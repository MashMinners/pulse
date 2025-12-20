<?php

declare(strict_types=1);

namespace Engine\Auth\Authorization;

use Engine\Auth\Config\Configurator;
use Engine\Auth\Services\SecretKeyService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(private Configurator $configurator){}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            if (!empty($request->getHeader('authorization'))){
                //Отрезаем из строки Bearer сам токен, который необходим для проверки

                /**
                 * Новый подход!
                 * Показываю и объясняю как он реализуется, для того чтоыбнельзя было воспользоваться укараденным Access
                 * токеном ибо он на половину валиден для аутентификации:
                 * Сам токен получает клиент полный и разделенный через конкатенацию на три части т.е.
                 * заголовок.нагрузка.подпись.
                 * Клиент делит токен на две части: заголовок.нагрузка и подпись
                 * заголовок.нагрузка => устанавливается в localStorage и к нему клиент свободно имеет доступ внезависимости
                 * от вкладок и тд.
                 * подпись => устанавливается в http-only куки и вылавливается здесь в этом мидлваре, после чего склеивается
                 * в полноценный токен через (.) конкатенацию и расшифровывается JWT классом
                 *
                 * Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2NvdW50SWQiOiIwOGUwYmI2OS0yZjQwLTRkMGQtYmE0Mi1kZDQwMjg2ZmI0MTYifQ.vtAWcAwOkAXMc8HUxa5EsY3QynwuUlGgqWVGzV1uBv8
                 */
                $token = mb_substr(($request->getHeader('authorization')[0]), 7); //токен состоящий из header.payload
                $signature = ($request->getHeader('signature')[0]); //http only содержащий signature
                [$header, $payload] = explode('.', $token); // получаем пэйлоад для поулчения id пользователя
                /**
                 * здесь можно вместо base64 использовать свой алгоритм или рандомный алгоритм, главное чтобы алгоритм этот
                 * совпадал с тем которым был зашифрован $payload на момент создания токена
                 * Имя этого рандомного алгоритма можно брать их файла SecretKey куда помимо самог осекретно ключа может
                 * быть записана так же информация по алгоритму которым был зашифрован $payload а так же создан секретный ключ
                 * Алгоритмы эти можно создавать самому и помещать в отдельный для этого созданный файл algorithm.php
                 * Внутри этого файла эти алгоритмы реализованы в виде функций, который раондомно выбираются
                 * шифруют $payload и генерируют secret key
                 */
                $accountId = json_decode(base64_decode($payload))->accountId;
                $header= json_decode(base64_decode($header));
                //Имеет смысл передавать по ссылке именно этот объект конфигуратора, с загруженными в него параметрами
                $secretKey = (new SecretKeyService($this->configurator))->getSecretKey($accountId, $this->configurator->hard);
                if ($secretKey){
                    /**
                     * Так как сейчас использую Постман я не буду сильно заморачиваться и резать токен в клиенте на две части
                     * пусть постман генерирует полный, дальше когда буду дорабатывать клиент само собой строка $accessToken = $token;
                     * будет удалена, а строка $accessToken = $token.'.'.$signature; будет делать склейку двух заголовков в полноценный токен
                     */
                    //$accessToken = $token.'.'.$signature;
                    $accessToken = $token;
                    $decoded = (JWT::decode($accessToken, new Key($secretKey, $header->alg)));
                    $request = $request->withAttribute('AccountId', $decoded->accountId);
                    $request = $request->withAttribute('AccountPermissions', $decoded->accountPermissions);
                    $response = $handler->handle($request);
                    return $response;
                }else{
                    return new JsonResponse('Не найден файл секретного ключа для расшифровки', 406);
                }
            }
            else{
                return new JsonResponse('Access токен не найден в заголовке Authorization', 401);
            }
        }
        catch (\Exception $e){
            $response = new JsonResponse('Access токен '.$accessToken.' не валиден '.$e->getMessage(), 400);
            return $response;
        }
    }
}
<?php

declare(strict_types=1);

namespace Engine\Auth\Services;

use Engine\Auth\Account\Account;
use Engine\Auth\Config\Configurator;
use Engine\Auth\Config\JWTPayload;
use Engine\Database\IConnector;
use Engine\DTO\StructuredResponse;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

class TokenService
{
    public function __construct(private Configurator $configurator, IConnector $connector){
        $this->pdo = $connector::connect();
    }

    private function getToken(string $token) : array
    {
        $query = ("SELECT refresh_tokens_account_id AS accountId, refresh_tokens_token AS refreshToken, 
                          refresh_tokens_created AS created, refresh_tokens_expires AS expires
                   FROM refresh_tokens WHERE refresh_tokens_token = :token ");
        $result = $this->pdo->prepare($query);
        $result->execute(['token' => $token]);
        return $result->fetch() ?: [];
    }

    /**
     * Устанавливаем новый токен
     * @param string $token
     * @param string $accountId
     * @return void
     */
    private function insertRefreshToken(string $token, string $accountId): void
    {
        $query = ("INSERT INTO `refresh_tokens` (refresh_tokens_token, refresh_tokens_account_id, refresh_tokens_created, refresh_tokens_expires)
                   VALUES (:refreshToken, :accountId, :created, :expires)");
        $result = $this->pdo->prepare($query);
        $result->execute([
            'accountId' => $accountId,
            'refreshToken' => $token,
            'created' => time(),
            'expires' => $this->configurator->refreshParams['expires']
        ]);
    }

    /**
     * Удалить старый токен
     * @param string $accountId
     * @return void
     */
    private function deleteRefreshToken(string $accountId): void
    {
        $query = ("DELETE FROM `refresh_tokens` WHERE `refresh_tokens_account_id` = :accountId");
        $result = $this->pdo->prepare($query);
        $result->execute(['accountId' => $accountId]);
    }

    private function validateRefreshToken(string $token) : string|bool
    {
        /**
         * 1 - найти токен в БД
         * - если токена нет в БД для данного пользователя, то отправить ответ со ссылкой на аутентификацию
         * - если токен есть, то проверить его время годности
         * -- если время годности закончено, то отправить ответ со ссылкой на аутентификацию
         * -- если время годности не закончено то:
         * 2 - создать пару из аксесc и рефреш токена и отправить их в ответ
         */
        if ($result = $this->getToken($token)){
            $currentTime = time();
            if ($currentTime < $result['expires']){
                return $result['accountId'];
            }
            return false;
        }
        return false;
    }

    /**
     * Токен служит для запроса обновленной пары refresh/access
     * Как то сильно его шифровать или связывать с access нет смысла ибо украденный рефреш токен если он просрочен,
     * просто не подойдет, а если он актуальный он запросит новую пару и оригинальный пользователь пробудет в системе
     * до окончания access, далее запросит новую пару тем самым лишив доступа к системе того кто завладел рефрешем
     * @param string $accessToken
     * @return string
     */
    public function generateRefreshToken(string $account) {
        $this->deleteRefreshToken($account);
        $token = Uuid::uuid4()->toString();
        $this->insertRefreshToken($token, $account);
        return $token;
    }

    /**
     * Данный токен при получении клиента делится на вде части: [header, payload] и signature
     * Первая часть записывается в открытое хранилище localStorage/sessionStorage
     * Вторая часть записывается в http only cookie.
     * На моменте авторизации в AuthorizationMiddleware они склеиваются в единый токен и дальше валидируются JWT классом
     * @param Account $account
     * @return string
     */
    public function generateAccessToken(string $account) {
        $tokenId = Uuid::uuid4()->toString();
        /**
         * Сделать генерацию через фабрику, тогда можно создавать не базовый, а расширенный класс со своими полями и
         * методами. Объявив это в DI
         * Понятное дело, что наполнение расширенных свойств делается уже в пользовательском коде, например в контроллере
         */
        $jwtPayload = new JWTPayload($this->configurator->jwtPayload);
        $jwtPayload->sub = $account;
        $jwtPayload->jti = $tokenId;
        $secretKey = (new SecretKeyService($this->configurator))->getSecretKey($account);
        $accessToken = JWT::encode((array)$jwtPayload, $secretKey, $this->configurator->jwtHeaderAlgorithm);
        return $accessToken;
    }

    public function refresh(string $token){
        if ($accountId = $this->validateRefreshToken($token)){
            if($accountId){
                $accessToken = $this->generateAccessToken($accountId);
                $refreshToken = $this->generateRefreshToken($accountId);
                return new StructuredResponse(200, 'Токены созданы', ['AccessToken' => $accessToken, 'RefreshToken' => $refreshToken]);
            }
            return new StructuredResponse(401, 'Такой учетной записи не существует в системе');
        }
        return new StructuredResponse(401, 'Токен не подходит. Вам необходима аутентификация');
    }

}
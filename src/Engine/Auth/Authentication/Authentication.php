<?php

declare(strict_types=1);

namespace Engine\Auth\Authentication;

use Engine\Auth\Services\SecretKeyService;
use Engine\Database\IConnector;
use Engine\Auth\Config\Configurator;
use Engine\Auth\Identification\Identification;
use Engine\DTO\StructuredResponse;

/**
 * Этот класс не используется для проверки авторизации по JWT токенам, этим занимается класс AuthorizationMiddleware
 */
class Authentication
{
    private \PDO $db;

    public function __construct(IConnector $connector, private Identification $identification, private Configurator $configurator){
        $this->db = $connector::connect();
    }

    private function rehash(string $password, string $accountId, string $algo, array $options) : string {
        $newHash = password_hash($password, $algo, $options);
        //Обновить хаш в таблице БД
        $query = ("UPDATE user_accounts SET user_accounts_password_hash = :newHash WHERE user_accounts_id = :accountId");
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'newHash' => $newHash,
            'accountId' => $accountId
        ]);
        return $newHash;
    }

    /**
     * Используется для того, чтобы определить, подошла ли пара логин и пароль для входа в систему
     * Далее там где метод был вызван происходят уже другие события:
     * либо это генерация токена, либо это создание сессии и запись данных в нее
     * Токены метод не должен возвращать, все что он должен возвращать это true/false об том
     * прошла ли аутентификация или нет на основе введеных данных и вернуть это в метод который
     * иницировал запрос, там уже в том методе возвращать либо ответ, либо инициировать генерацию токенов
     * или создавать сессию и записывать данные в нее и тд.
     * @param string $accountName
     * @param string $accountPassword
     * @param bool $hard
     * @return bool
     */
    public function authenticate(Credentials $credentials) : StructuredResponse {
        //Проверяем наличие учетной записи
        if ($account = $this->identification->identify($credentials->userName)){
            //Проверяем пароль
            if (password_verify($credentials->userPassword, $account->passwordHash)){
                $hashParams = $this->configurator->passwordHashParams;
                if (password_needs_rehash($account->passwordHash, $hashParams['algo'],$hashParams['options'])){
                    $this->rehash($credentials->userPassword, $account->id, $hashParams['algo'], $hashParams['options']);
                }
                /**
                 * Установим оригинальный секретный ключ для пользователя, при усиленной аутентификации,
                 * То есть каждый раз когда пользователь делает логин, генерируется на него случайный секретный ключ
                 * Если украдут токены, пользователь вынужден будет заново войти в систему по окончанию своего access token'a,
                 * так как его refresh токен будет заменен злоумышленником, и если будет скомпрометирован секретный ключ
                 * то:
                 * 1) он будет скомпрометирован для одного пользователя
                 * 2) он будет обновлен при первой же аутентификации
                 */
                if ($this->configurator->hard){
                    (new SecretKeyService($this->configurator))->generateSecretKey($account);
                }
                /**
                 * Заменить StructuredResponse на JsonResponse?
                 * Не думаю что это нужно, так как данное ДТО более удобно для работы внутри бизнес логики домена/модели
                 * JsonResponse имеет смысл использовать в Middleware и Controllers
                 */
                return new StructuredResponse(200, 'Пользователь аутентифицирован', $account);//
            }
            return new StructuredResponse(401, 'Пароль не верен');
        }
        return new StructuredResponse(404, 'Такой учетной записи не существует в системе');
    }

}
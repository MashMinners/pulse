<?php

declare(strict_types=1);

namespace Engine\Auth\Services;

use Engine\Auth\Account\Account;
use Engine\Auth\Config\Configurator;
use Ramsey\Uuid\Uuid;

class SecretKeyService
{
    public function __construct(private Configurator $configurator){

    }

    /**
     * Каждый раз при создании токена, для пользователя будет генерироваться секретный ключ
     * Ключ записывается в хранилище в специальный файл.
     * Имя файла это уникальный идентификатор пользователя.
     * Теперь каждый раз, когда нужно расшифровать токен, ключ будет браться из файла.
     * Если на момент создания токена, уже существует файл с секретным ключем, он пересоздается.
     *
     * Создание и хранение файлов секретных ключей решате проблему подделки токенов:
     * 1) Алгоритм создания токена очень простой. Используя его токен можно создать  в любой php песочнице
     * 2) Но для расшифровки сигнатуры этого токена, может быть использован только секретный ключ, который создается и
     * хранится на сервере.
     * 3) Ключ этот уникален на основе uuid4 и подделать его не имеется возможности, так же на сервер нет возможности
     * из вне отправить поддельный файл ключа для расшифровки этого токена, а значит если на сервер
     * будет отправлен подделаный токен, он просто не расшифруется серверным ключем(конкретного пользователя)
     * И тем самым не даст возможность получить неавторизованному лицу контроль.
     */
    public function generateSecretKey(Account $account) : string
    {
        $uuid = Uuid::uuid4()->toString();
        $shuffledChars = str_shuffle($this->configurator->permittedChars);
        //Просто склеиваю ключ из различных символов, uuid и имени пользователя
        $secretKey = hash('sha1',$uuid.$account->userName.$shuffledChars, false);
        //Создаю файл куда запишу сгенерированный ключ
        $keyPath = $this->configurator->keyStorage.'/'.$account->id;
        file_put_contents($keyPath, $secretKey);
        return $secretKey;
    }

    /**
     * В случае если используется усиленная авторизация, то ключ берется из файла конфигурируемого для каждого пользователя
     * В случае если обычная - то используется общий для всех ключ, который задается в конфигурационном файле auth.php
     * @param string $accountId
     * @param bool $hard
     * @return string|bool
     */
    public function getSecretKey(string $accountId) : string|bool {
        if ($this->configurator->hard){
            $keyFile = $this->configurator->keyStorage.'/'.$accountId;
            if (file_exists($keyFile)){
                $secretKey = file_get_contents($keyFile);
                return $secretKey;
            }
            return false;
        }
        return $this->configurator->secretKey;
    }
}
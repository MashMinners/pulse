<?php

declare(strict_types=1);

namespace Engine\Auth\Config;

use Engine\Exceptions\UnknownPropertyException;

class JWTPayload implements \JsonSerializable
{
    private string $iss; //Тот, кто выпустил токен (например, домен)
    private string $sub; //Основной субъект (пользователь), к которому относится токен (например, идентификатор пользователя)
    private string $aud; //Предполагаемые получатели/пользователи токена
    private int $exp; //Метка времени Unix, когда истекает срок действия токена
    private int $nbf; //Метка времени Unix, до которой токен недействителен
    private int $iat; //Метка времени Unix, когда был создан токен
    private string $jti; //Уникальный идентификатор токена, полезный для аннулирования
    private array $userData = [];

    public function __construct(array $payload){
        $this->init($payload);
    }

    private function init(array $tokenConfigs) : void
    {
        foreach ($tokenConfigs as $key => $value){
            if (property_exists($this, $property = $key)){
                $this->$property = $value;
            }else{
                throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
            }
        }
    }

    public function __get($name) : mixed
    {
        if (property_exists($this, $property = $name)){
            return $this->$property;
        }
        throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
    }

    public function __set($name, $value){
        if (property_exists($this, $property = ($name))){
            $this->$property = $value;
        }else{
            throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
        }
    }

    public function setUserData($key, $value){
        $this->userData[$key] = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'Issuer' => $this->iss,
            'Subject' => $this->sub,
            'Audience' => $this->aud,
            'Expired' => $this->exp,
            'NotBefore' => $this->nbf,
            'Issued' => $this->iat,
            'jwtID' => $this->jti,
            'userData' => $this->userData
        ];
    }
}
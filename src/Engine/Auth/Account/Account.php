<?php

declare(strict_types=1);

namespace Engine\Auth\Account;

use Engine\Exceptions\UnknownPropertyException;

class Account
{
    private $id;
    private $userName;
    private $passwordHash;
    private $secretKey;
    private $createDate;
    private $updateDate;

    public function __get($name){
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

}
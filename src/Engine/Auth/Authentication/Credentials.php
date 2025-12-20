<?php

declare(strict_types=1);

namespace Engine\Auth\Authentication;

use Engine\Exceptions\UnknownPropertyException;

class Credentials
{
    private string $userName;
    private string $userPassword;

    public function __construct() {
        $json = file_get_contents('php://input');
        $credentials = json_decode($json);
        $this->userName = $credentials->userName;
        $this->userPassword = $credentials->userPassword;
    }

    public function __get($name)
    {
        if (property_exists($this, $property = $name)){
            return $this->$property;
        }
        throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
    }

}
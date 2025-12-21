<?php

declare(strict_types=1);

namespace Engine\Auth\Config;

use Engine\Exceptions\UnknownPropertyException;

class Configurator
{
    private string $keyStorage;
    private string $secretKey;
    private string $algorithm; //
    private string $permittedChars;
    private string $jwtHeaderAlgorithm;
    private array $jwtPayload;
    private array $passwordHashParams;
    private array $refreshParams;

    public function __construct(private string $folder, private string $file, private bool $hard = false){
        if (file_exists($folder.'/'.$file.'.php')){
            $configs = require $this->folder.'/'.$this->file.'.php';
            $this->keyStorage =  $configs['keysStorage'];
            $this->secretKey = $configs['secretKey'];
            $this->permittedChars = $configs['permittedChars'];
            $this->jwtHeaderAlgorithm = $configs['jwtHeaderAlgorithm'];
            $this->jwtPayload = $configs['jwtPayload'];
            $this->passwordHashParams = $configs['passwordHashParams'];
            $this->refreshParams = $configs['refreshParams'];
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    public function __get($name){
        if (property_exists($this, $property = $name)){
            return $this->$property;
        }
        throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
    }

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws UnknownPropertyException
     */
    public function __set($name, $value){
        if (property_exists($this, $property = ($name))){
            $this->$property = $value;
        }else{
            throw new UnknownPropertyException("Свойство ".$property." не найдено в классе ".get_class($this));
        }
    }

}
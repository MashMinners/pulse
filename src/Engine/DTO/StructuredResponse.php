<?php

declare(strict_types=1);

namespace Engine\DTO;

class StructuredResponse implements \JsonSerializable
{
    public function __construct(private $code = 204, private $message = null, private $body = []){

    }

    public function __get($name){
        return $this->$name;
    }
    public function __set($name, $value){
        $this->$name = $value;
    }

    public function setBody($key, $value){
        $this->body[$key] = $value;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'Code' => $this->code,
            'Message' => $this->message,
            'Body' => $this->body
        ];
    }

}
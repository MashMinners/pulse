<?php

namespace Application\ReviewCollector\Models;

use Engine\DTO\BaseDTO;

class Review extends BaseDTO implements \JsonSerializable
{
    protected string|null $reviewId;
    protected string|null $reviewType;
    public function __construct(string $json){
        $this->init($json);
    }

    public function __get(string $name){
        return $this->$name;
    }

    public function jsonSerialize()
    {
        $properties = get_object_vars($this);
        return $properties;
    }
}
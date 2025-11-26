<?php

namespace Application\ReviewCollector\Models;

use Engine\DTO\BaseDTO;

class Review extends BaseDTO implements \JsonSerializable
{
    protected string|null $reviewId;
    protected string|null $reviewEmployeeId;
    protected int|null $reviewStatus;
    protected string|null $reviewText;
    protected string|null $reviewDate;
    public function __construct(string $json){
        /**
         * Заполнить все свойства null
         */
        $properties = get_class_vars(self::class);
        foreach ($properties as $name => $value){
            $this->$name = $value;
        }
        //Проинициализировать значениями
        $this->init($json);
    }

    public function __get(string $name){
        return $this->$name;
    }

    public function jsonSerialize() : mixed
    {
        $properties = get_object_vars($this);
        return $properties;
    }
}
<?php

namespace Application\ReviewCollector\Models;

use Engine\DTO\BaseDTO;

class Review implements \JsonSerializable
{
    protected string|null $reviewId;
    protected string|null $reviewEmployeeId;
    protected int|null $reviewStatus;
    protected string|null $reviewText;
    protected string|null $reviewDate;
    protected string|null $reviewPacient;
    protected string|null $reviewTelephone;

    public function __construct(string $json){
        $data = json_decode($json);
        foreach ($data AS $dataKey => $dataValue){
            $this->$dataKey = $dataValue;
        }
    }

    public function __get(string $name){
        return $this->$name;
    }

    public function __set($name, $value){
        $this->$name = $value;
    }

    public function jsonSerialize() : mixed
    {
        $properties = get_object_vars($this);
        return $properties;
    }
}
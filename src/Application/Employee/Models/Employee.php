<?php

namespace Application\Employee\Models;

use Engine\DTO\BaseDTO;

class Employee extends BaseDTO implements \JsonSerializable
{
    protected string|null $employeeId;
    public function __construct(array|string $data){
        $properties = get_class_vars(self::class);
        foreach ($properties as $name => $value){
            $this->$name = $value;
        }
        $this->init($data);
    }

    public function __get(string $name) : string|int|null {
        return $this->$name;
    }

    public function jsonSerialize() : mixed {
        $properties = get_object_vars($this);
        return $properties;
    }


}
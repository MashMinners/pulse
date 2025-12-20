<?php

declare(strict_types=1);

namespace Engine\Auth\Authentication;

class AuthErrors implements \JsonSerializable
{
    private array $_errors = [
        'incorrectCharacters' => [],
        'emptyFields' => []
    ];

    public function incorrectCharacters(string $field) {
        $this->_errors['incorrectCharacters'][] = $field;
    }

    public function emptyField(string $field){
        $this->_errors['emptyFields'][] = $field;
    }

    public function hasErrors(){
        if (!empty($this->_errors['incorrectCharacters']) OR !empty($this->_errors['emptyFields'])){
            return true;
        }
        return false;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4
     */
    public function jsonSerialize(): mixed
    {
        return [
            'IncorrectCharacters' => $this->_errors['incorrectCharacters'],
            'EmptyFields' => $this->_errors['emptyFields']
        ];
    }

}
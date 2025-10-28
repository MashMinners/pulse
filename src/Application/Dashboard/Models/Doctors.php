<?php

namespace Application\Dashboard\Models;

use Engine\Database\IConnector;

class Doctors
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }
    public function get(string $id){

    }

    /**
     * Будет возвращать список всех врачей с:
     * -ФИО
     * -Фото
     * -Инфрмацией (которая отображается на странице двух кнопок
     * @param array $IDs
     * @return void
     */
    public function getAll(array $IDs){

    }

    /**
     * Создаем врача в систему, вернет uuid этой записи
     * @return void
     */
    public function add() : string{

    }

    /**
     * В случае если нужно отредактировать врача, допустим стаж, ФИО, или текст
     * @param string $id
     * @return void
     */
    public function edit(string $id){

    }

    /**
     * Удаление врача из системы, но сделано как массовое, так как я уже это проходил и иногда нужно по несколько за раз
     * удалять
     * @param array $IDs
     * @return void
     */
    public function delete(array $IDs){

    }

}
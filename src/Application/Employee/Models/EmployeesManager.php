<?php

declare(strict_types=1);

namespace Application\Employee\Models;

use Engine\Database\IConnector;

class EmployeesManager
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

    public function get(string $id){
        $query = ("SELECT * FROM employees WHERE employees_employee_id = :id");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id' => $id
        ]);
        $result = $stmt->fetch();
    }

    public function create() : string{

    }

    public function edit(string $id) {

    }

    public function delete(array $IDs){

    }

}
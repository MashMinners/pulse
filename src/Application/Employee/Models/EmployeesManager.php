<?php

declare(strict_types=1);

namespace Application\Employee\Models;

use Engine\Database\IConnector;

class EmployeesManager
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

    public function getAll(){
        $query = ("SELECT * FROM employees");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(string $employeeId){
        $query = ("SELECT * FROM employees WHERE employees_employee_id = :employeeId");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'employeeId'=>$employeeId
        ]);
        $result = $stmt->fetch();
        return $result;
    }

    public function getByFullName(string $search) : EmployeesCollection {
        $query = ("SELECT * FROM employees 
                   WHERE CONCAT(employee_surname, ' ', employee_first_name) LIKE '%$search%'");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll();
        $collection = new EmployeesCollection();
        foreach ($results as $result){
            $collection->add(new Employee($result));
        }
        return $collection;
    }

    public function create() : string {

    }

    public function edit(string $id) {

    }

    public function delete(string $json) : bool {
    $std = json_decode($json);
    $query = ("DELETE FROM employees WHERE employees_employee_id = :employeeId");
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['employeeId'=>$std->employeeId]);
    return true;
}

}
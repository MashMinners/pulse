<?php

namespace Application\Dashboard\Models;

use Application\Employee\Models\EmployeesManager;
use Application\ReviewCollector\Models\ReviewCollector;
use Engine\Database\MySQLDbConnector;

class DashboardMain
{
    public function getAllWithRating(){
        $employeesWithRating = [];
        $connector = new MySQLDbConnector();
        $employeesManager = new EmployeesManager($connector);
        $employees = $employeesManager->getAll();
        $reviewsCollector = new ReviewCollector($connector);
        $reviews = $reviewsCollector->getAll();
        foreach ($employees AS $key => $value){
            if (array_key_exists($value['employees_employee_id'], $reviews)){
                if (array_key_exists(1, $reviews[$value['employees_employee_id']])){
                    $reviewsCount[$value['employees_employee_id']]['good'] = count($reviews[$value['employees_employee_id']][1]);
                }
                if (array_key_exists(0, $reviews[$value['employees_employee_id']])){
                    $reviewsCount[$value['employees_employee_id']]['bad'] = count($reviews[$value['employees_employee_id']][0]);
                }
            }
        }
        foreach ($employees AS $employee){
            $employeesWithRating[$employee['employees_employee_id']]['employeeId'] = $employee['employees_employee_id'];
            $employeesWithRating[$employee['employees_employee_id']]['employeeFullName'] = $employee['employees_employee_surname'].' '.$employee['employees_employee_first_name'].' '.$employee['employees_employee_second_name'];
            $employeesWithRating[$employee['employees_employee_id']]['employeePhoto'] = $employee['employees_employee_photo'];
            if (array_key_exists($employee['employees_employee_id'], $reviewsCount)){
                if (array_key_exists('good', $reviewsCount[$employee['employees_employee_id']])){
                    $employeesWithRating[$employee['employees_employee_id']]['employeePositiveRatingCount'] = $reviewsCount[$employee['employees_employee_id']]['good'];
                }
                else{
                    $employeesWithRating[$employee['employees_employee_id']]['employeePositiveRatingCount'] = 0;
                }
                if (array_key_exists('bad', $reviewsCount[$employee['employees_employee_id']])){
                    $employeesWithRating[$employee['employees_employee_id']]['employeeNegativeRatingCount'] = $reviewsCount[$employee['employees_employee_id']]['bad'];
                }
                else{
                    $employeesWithRating[$employee['employees_employee_id']]['employeeNegativeRatingCount'] = 0;
                }
            }else{
                $employeesWithRating[$employee['employees_employee_id']]['employeePositiveRatingCount'] = 0;
                $employeesWithRating[$employee['employees_employee_id']]['employeeNegativeRatingCount'] = 0;
            }
        }
        return $employeesWithRating;
    }

}
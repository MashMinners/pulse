<?php

declare(strict_types=1);

namespace Application\Employee\Controllers;

use Application\Employee\Models\EmployeesManager;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EmployeeController
{
    public function __construct(private EmployeesManager $manager){

    }

    public function getEmployee(ServerRequestInterface $request) : ResponseInterface {
        $employeeId = $request->getQueryParams()['employeeId'];
        $result = $this->manager->getById($employeeId);
        $response = (new JsonResponse($result));
        return $response;
    }

    public function remove(ServerRequestInterface $request) : ResponseInterface {
        $json = file_get_contents('php://input');
        $result = $this->manager->delete($json);
        $response = (new JsonResponse($result));
        return $response;
    }

}
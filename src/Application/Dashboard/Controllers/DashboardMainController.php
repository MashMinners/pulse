<?php

namespace Application\Dashboard\Controllers;

use Application\Dashboard\Models\DashboardMain;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardMainController
{
    public function __construct(private DashboardMain $dashboard){

    }

    public function showEmployeesWithRating(ServerRequestInterface $request) : ResponseInterface{
        $result = $this->dashboard->getAllWithRating();
        return new JsonResponse($result);
    }

}
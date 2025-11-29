<?php

declare(strict_types=1);

namespace Application\Dashboard\Controllers;

use Application\Dashboard\Models\DashboardReviews;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DashboardReviewsController
{
    public function __construct(private DashboardReviews $reviews){

    }

    public function showPositiveReviewsByEmployee(ServerRequestInterface $request) : ResponseInterface{
        $employeeId = $request->getQueryParams()['employeeId'];
        $result = $this->reviews->getReviewsByEmployee($employeeId,1);
        return new JsonResponse($result);
    }

    public function showNegativeReviewsByEmployee(ServerRequestInterface $request) : ResponseInterface{
        $employeeId = $request->getQueryParams()['employeeId'];
        $result = $this->reviews->getReviewsByEmployee($employeeId,0);
        return new JsonResponse($result);
    }

}
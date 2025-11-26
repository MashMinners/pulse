<?php

declare(strict_types=1);

namespace Application\ReviewCollector\Controllers;

use Application\ReviewCollector\Models\Review;
use Application\ReviewCollector\Models\ReviewCollector;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ReviewCollectorController
{
    public function __construct(private ReviewCollector $collector){

    }

    public function getByEmployee(ServerRequestInterface $request) : ResponseInterface{
        $data = file_get_contents('php://input');
        $result = $this->collector->getByEmployee($data);
        return new JsonResponse($result);
    }

    public function add(ServerRequestInterface $request) : ResponseInterface{
        $json = file_get_contents('php://input');
        $reviewId = $this->collector->insert(new Review($json));
        return new JsonResponse($reviewId);
    }

    public function remove(ServerRequestInterface $request) : ResponseInterface{
        $json = file_get_contents('php://input');
        $result = $this->collector->delete($json);
        $response = (new JsonResponse($result));
        return $response;
    }

}
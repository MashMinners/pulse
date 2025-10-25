<?php

declare(strict_types=1);

namespace Application\ReviewCollector\Models;

use Engine\Database\IConnector;

class ReviewCollector
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

}
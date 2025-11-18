<?php

declare(strict_types=1);

namespace Application\ReviewCollector\Models;

use Engine\Database\IConnector;
use Ramsey\Uuid\Uuid;

class ReviewCollector
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

    /**
     * Вставляет в базу данных отзыв
     * @param string $type - good/bad
     * @return void
     */
    public function insert(Review $review) : string{
        $query = (""); //Insert Query
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'reviewId' => $reviewId = Uuid::uuid4()->toString(),
            'reviewType' => $review->type
        ]);
        return $reviewId;
    }

    public function delete(string $json) : bool {
        $std = json_decode($json);
        $query = ("");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['employeeId'=>$std->employeeId]);
        return true;
    }

}
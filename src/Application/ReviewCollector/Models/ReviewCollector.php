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
     * Выбрать отзывы за период
     * @return void
     */
    public function getByDate(){

    }

    /**
     * Выбирает отзывы по пользователю за 30 дней
     * @return void
     */
    public function getByEmployee(){
        $query = ("SELECT * FROM reviews WHERE reviews_employee_id = :id AND ");

    }

    /**
     * Вставляет в базу данных отзыв
     * @param string $type - good/bad
     * @return void
     */
    public function insert(Review $review) : string{
        $query = ("INSERT INTO pulse.reviews (reviews_review_id, reviews_employee_id, reviews_review_status, reviews_review_text, reviews_review_date) 
                   VALUES (:reviewId, :employeeId, :reviewStatus, :reviewText, :reviewDate)"); //Insert Query
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'reviewId' => $reviewId = Uuid::uuid4()->toString(),
            'employeeId'=> $review->reviewEmployeeId,
            'reviewStatus' => $review->reviewStatus,
            'reviewText' => $review->reviewText,
            'reviewDate' => strtotime($review->reviewDate)
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
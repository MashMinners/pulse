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

    public function getAll(){
        $query = ("SELECT * FROM reviews");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        while ($data = $stmt->fetch()){
            $result[$data['reviews_employee_id']][$data['reviews_review_status']][] = $data;
        }
        return $result;
    }

    /**
     * Выбирает отзывы по пользователю за 30 дней
     * @return array|false
     */
    public function getByEmployee(string $data){
        //Получить ID сотрудника
        $employeeId = (json_decode($data))->review_employee_id;
        //Получаю дату на момент запроса
        $currentDate = strtotime(date('Y-m-d'));
        //Получаю дату от текущей на 30 дней назад. От нее и буду отталкиваться в поиске
        $reviewDate = $currentDate - 2592000;
        $query = ("SELECT * FROM reviews WHERE reviews_employee_id = :employeeId AND reviews_review_date > :reviewDate");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'employeeId' => $employeeId,
            'reviewDate' => $reviewDate
        ]);
        $result = $stmt->fetchAll();
        return $result;
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
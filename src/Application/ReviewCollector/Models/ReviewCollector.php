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
     * Вернет все отзывы с таблицы отзывов отсордитрованные по единственному логичному для этого полю Дате
     * @param $OrderBy
     * @return array|false
     */
    private function getAllReviews($OrderBy='DESC') : array{
        $query = ("SELECT * FROM reviews ORDER BY reviews_review_date $OrderBy");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Вернет массив данных, отсортированный по id сотрудника, а внутри отсортированный по статусу: хороший/отрицательный
     * @return array
     */
    public function getAllGroupedByEmployeeAnStatus() : array{
        $query = ("SELECT * FROM reviews");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        while ($data = $stmt->fetch()){
            $result[$data['reviews_employee_id']][$data['reviews_review_status']][] = $data;
        }
        return $result;
    }

    /**
     * Выбирает отзывы по пользователю за 30 дней, по умолчанию положительные
     * @return array|false
     */
    public function getByEmployee(string $data, $reviewStatus=1){
        //Получить ID сотрудника
        $employeeId = (json_decode($data))->review_employee_id;
        //Получаю дату на момент запроса
        $currentDate = strtotime(date('Y-m-d'));
        //Получаю дату от текущей на 30 дней назад. От нее и буду отталкиваться в поиске
        $reviewDate = $currentDate - 2592000;
        $query = ("SELECT * FROM reviews 
                   WHERE reviews_employee_id = :employeeId 
                   AND reviews_review_date > :reviewDate
                   AND reviews_review_status = :reviewStatus
                  ");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'employeeId' => $employeeId,
            'reviewDate' => $reviewDate,
            'reviewStatus' => $reviewStatus
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
        $query = ("INSERT INTO pulse.reviews (reviews_review_id, reviews_employee_id, reviews_review_status, 
                           reviews_review_text, reviews_review_date, reviews_review_pacient, reviews_review_pacient_telephone) 
                   VALUES (:reviewId, :employeeId, :reviewStatus, :reviewText, :reviewDate, :reviewPacient, :reviewTelephone)"); //Insert Query
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'reviewId' => $reviewId = Uuid::uuid4()->toString(),
            'employeeId'=> $review->reviewEmployeeId,
            'reviewStatus' => $review->reviewStatus,
            'reviewText' => $review->reviewText,
            'reviewDate' => strtotime(date("Y-m-d")),
            'reviewPacient' => $review->reviewPacient,
            'reviewTelephone' => $review->reviewTelephone

        ]);
        return $reviewId;
    }

}
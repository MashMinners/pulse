<?php

declare(strict_types=1);

namespace Application\Dashboard\Models;

use Engine\Database\IConnector;

class DashboardReviews
{
    public function __construct(IConnector $connector){
        $this->pdo = $connector::connect();
    }

    public function getReviewsByEmployee($employeeId, $reviewStatus=1){
        //Получаю дату на момент запроса
        $currentDate = strtotime(date('Y-m-d'));
        //Получаю дату от текущей на 30 дней назад. От нее и буду отталкиваться в поиске
        $reviewDate = $currentDate - 2592000;
        $query = ("SELECT * FROM reviews
                   INNER JOIN employees e on reviews.reviews_employee_id = e.employees_employee_id
                   WHERE reviews_employee_id = :employeeId 
                   AND reviews_review_date > :reviewDate
                   AND reviews_review_status = :reviewStatus
                   ORDER BY reviews_review_date DESC");
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'employeeId' => $employeeId,
            'reviewDate' => $reviewDate,
            'reviewStatus' => $reviewStatus
        ]);
        $result = $stmt->fetchAll();
        $i = 0;
        $finalResult = [];
        foreach ($result AS $single) {
            $single['reviews_review_date'] = date('d.m.Y H:i', $single['reviews_review_date']);
            $single['id'] = $i;
            $i++;
            $finalResult[] = $single;
        }
        return $finalResult ;

    }

}
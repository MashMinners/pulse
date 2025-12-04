<?php
#СБОР ОТЗЫВОВ В ПРИЛОЖЕНИИ
$this->get('app/employee/get', 'Application\Employee\Controllers\EmployeeController::getEmployee');

#СБОР ДАННЫХ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ДАШБОРДА
$this->get('dashboard/main/employees', 'Application\Dashboard\Controllers\DashboardMainController::showEmployeesWithRating');
#СБОР ДАННЫХ ДЛЯ СТРАНИЦЫ ОТЗЫВОВ
$this->get('dashboard/reviews/positive', 'Application\Dashboard\Controllers\DashboardReviewsController::showPositiveReviewsByEmployee');
$this->get('dashboard/reviews/negative', 'Application\Dashboard\Controllers\DashboardReviewsController::showNegativeReviewsByEmployee');

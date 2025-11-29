<?php
#СБОР ОТЗЫВОВ
$this->get('review/get-by/employee', '\Application\ReviewCollector\Controllers\ReviewCollectorController::getByEmployee');
$this->post('review/add', '\Application\ReviewCollector\Controllers\ReviewCollectorController::add');
$this->delete('review/delete', '\Application\ReviewCollector\Controllers\ReviewCollectorController::delete');

#СБОР ДАННЫХ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ДАШБОРДА
$this->get('dashboard/main/employees', 'Application\Dashboard\Controllers\DashboardMainController::showEmployeesWithRating');
#СБОР ДАННЫХ ДЛЯ СТРАНИЦЫ ОТЗЫВОВ
$this->get('dashboard/reviews/positive', 'Application\Dashboard\Controllers\DashboardReviewsController::showPositiveReviewsByEmployee');
$this->get('dashboard/reviews/negative', 'Application\Dashboard\Controllers\DashboardReviewsController::showNegativeReviewsByEmployee');

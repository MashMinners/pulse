<?php
#СБОР ОТЗЫВОВ В ПРИЛОЖЕНИИ
$this->get('app/employee/get', 'Application\Employee\Controllers\EmployeeController::getEmployee');
$this->post('app/review/add', 'Application\ReviewCollector\Controllers\ReviewCollectorController::addReview');

#СБОР ДАННЫХ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ДАШБОРДА
$this->get('dashboard/main/employees', 'Application\Dashboard\Controllers\DashboardMainController::showEmployeesWithRating');
    //->lazyMiddleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);
#СБОР ДАННЫХ ДЛЯ СТРАНИЦЫ ОТЗЫВОВ
$this->get('dashboard/reviews/positive', 'Application\Dashboard\Controllers\DashboardReviewsController::showPositiveReviewsByEmployee');
$this->get('dashboard/reviews/negative', 'Application\Dashboard\Controllers\DashboardReviewsController::showNegativeReviewsByEmployee');
#АВТОРИЗАЦИЯ
$this->post('auth/doAuth', 'Application\Auth\Controllers\AuthController::auth')
    ->lazyMiddlewares([
        \Engine\Auth\Authentication\CredentialsValidatorMiddleware::class,
        //\Engine\Auth\Authorization\AuthorizationMiddleware::class
    ]);
$this->post('auth/refresh', 'Application\Auth\Controllers\AuthController::refresh')
    ->lazyMiddlewares([
        \Engine\Auth\Authorization\AuthorizationMiddleware::class
    ]);
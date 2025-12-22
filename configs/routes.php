<?php
#СБОР ОТЗЫВОВ В ПРИЛОЖЕНИИ
$this->get('app/employee/get', 'Application\Employee\Controllers\EmployeeController::getEmployee')
    ->middleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);
$this->post('app/review/add/positive', 'Application\ReviewCollector\Controllers\ReviewCollectorController::addReview')
    ->middleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);

#СБОР ДАННЫХ ДЛЯ ГЛАВНОЙ СТРАНИЦЫ ДАШБОРДА
$this->get('dashboard/main/employees', 'Application\Dashboard\Controllers\DashboardMainController::showEmployeesWithRating')
    ->lazyMiddleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);
#СБОР ДАННЫХ ДЛЯ СТРАНИЦЫ ОТЗЫВОВ
$this->get('dashboard/reviews/positive', 'Application\Dashboard\Controllers\DashboardReviewsController::showPositiveReviewsByEmployee')
    ->middleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);
$this->get('dashboard/reviews/negative', 'Application\Dashboard\Controllers\DashboardReviewsController::showNegativeReviewsByEmployee')
    ->middleware(\Engine\Auth\Authorization\AuthorizationMiddleware::class);
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
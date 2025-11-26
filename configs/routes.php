<?php
#СБОР ОТЗЫВОВ
$this->get('review/get-by/employee', '\Application\ReviewCollector\Controllers\ReviewCollectorController::getByEmployee');
$this->post('review/add', '\Application\ReviewCollector\Controllers\ReviewCollectorController::add');
$this->delete('review/delete', '\Application\ReviewCollector\Controllers\ReviewCollectorController::delete');
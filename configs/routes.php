<?php
#СБОР ОТЗЫВОВ
$this->post('review/add', '\Application\ReviewCollector\Controllers\ReviewCollectorController::add');
$this->delete('review/delete', '\Application\ReviewCollector\Controllers\ReviewCollectorController::delete');
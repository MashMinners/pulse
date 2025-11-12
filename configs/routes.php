<?php
#СБОР ОТЗЫВОВ
$this->post('review/good', '\Application\ReviewCollector\Controllers\ReviewCollectorController::good');
$this->get('review/bad', '\Application\ReviewCollector\Controllers\ReviewCollectorController::bad');
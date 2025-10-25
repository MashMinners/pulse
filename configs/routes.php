<?php
#СБОР ОТЗЫВОВ
$this->get('review/good', '\Application\ReviewCollector\Controllers\ReviewCollectorController::good');
$this->get('review/bad', '\Application\ReviewCollector\Controllers\ReviewCollectorController::bad');
<?php
require(__DIR__ . "/../vendor/autoload.php");

use ChaosWD\Controller\SystemController;

SystemController::setup();

$request = new ChaosWD\Controller\RequestController();
$request->request($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

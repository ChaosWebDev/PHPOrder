<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define("ROOT_PATH", __DIR__ . "\\..\\..");
define("PRIVATE_PATH", ROOT_PATH . "\\private");
define("PUBLIC_PATH", ROOT_PATH . "\\public_html");

define("SRC_PATH", PRIVATE_PATH . "\\src");

define("CONFIG_PATH", PRIVATE_PATH . "\\config");
define("TEMPLATE_PATH", PRIVATE_PATH . "\\templates");

define("CONTROLLERS_PATH", SRC_PATH . "\\controllers");

define("ASSETS_PATH", PUBLIC_PATH . "\\assets");

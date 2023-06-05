<?php

namespace ChaosWD\Controller;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use ChaosWD\Controller\Environment;

class SystemController
{
    public static function setup()
    {
        // ! STANDARD SETUP ! //
        EnvironmentController::load();
        $_SESSION['TIMEZONE'] = $_ENV['TIMEZONE'] ?? $_COOKIE['TIMEZONE'] ?? "America/Denver";
        date_default_timezone_set($_SESSION['TIMEZONE']);
    }

    public static function searchDirectory($targetName)
    {
        $directory = ROOT_PATH;
        $dirIterator = new RecursiveDirectoryIterator($directory);
        $iterator = new RecursiveIteratorIterator($dirIterator, RecursiveIteratorIterator::SELF_FIRST);

        $files = [];

        foreach ($iterator as $file) {
            if ($file->getFilename() === $targetName) {
                array_push($files, $file->getPath() . "\\" . $file->getFilename());
            }
        }

        return $files;
    }
}

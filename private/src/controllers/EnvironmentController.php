<?php

namespace ChaosWD\Controller;

use ChaosWD\Controller\SystemController;


class EnvironmentController
{
    public static function load()
    {
        $files = SystemController::searchDirectory(".env");
        foreach ($files as $file) {
            $envData = file_get_contents($file);
            if (strpos($envData, "\r\n") === false && strpos($envData, "\n") !== false) {
                $envData = str_replace("\n", "\r\n", $envData);
            }

            $lines = explode(PHP_EOL, $envData);

            foreach ($lines as $line) {
                if (empty($line) || strpos($line, '#') === 0 || $line == "\r\n") continue;

                $matches = array();
                if (preg_match('/^([^=]+)=(.*)$/', $line, $matches)) {
                    $key = trim($matches[1]);
                    $value = trim($matches[2]);

                    $value = trim($value, "'\"");

                    $_ENV[$key] = $value;
                }
            }
        }
    }

    public static function set($key, $value)
    {
        $_ENV[e($key)] = e($value);
    }
}

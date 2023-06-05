<?php

namespace ChaosWD\Controller;

use fileHandler;
use stdClass;

class LogController
{
    use fileHandler;

    public $fileName = "generalLog";
    public $path;

    public function __construct($fileName)
    {
        $this->path = PRIVATE_PATH . "\\" . $_ENV['LOG_PATH'] ?? SRC_PATH . "\\logs";
        $this->fileName = $fileName;
        $this->checkFile($this->path, $this->fileName);
    }

    protected function checkFile($path, $fileName)
    {
        if (!is_dir($path)) :
            mkdir($path, 0775, true);
        endif;

        if (!file_exists($path . "\\{$fileName}.log")) :
            $fh = fopen($path . "\\{$fileName}.log", "w");
            $timestamp = date("m/d/Y H:i:s", time());
            fwrite($fh, "[Time: {$timestamp}][Message: Initialization]\r\n");
            fclose($fh);
        endif;

        return;
    }

    public function add($obj)
    {
        $timestamp = date("m/d/Y H:i:s", time());
        $html = "[Time: {$timestamp}]";

        if (!isset($obj->reason)) {
            $errorObj = new stdClass();
            $str = '{"reason":"ErrorLog","message":"Reason Left Null","Data":[{"attemptedMessage":"' . $obj->message ?? "BLANK" . '},{"attemptedData":"' . $obj->data ?? "NULL" . '"]';
            $errorObj = json_decode($str, false);
            $this->add($errorObj);
            return;
        }

        $html .= "[Reason: {$obj->reason}][Message: {$obj->message}]";

        if (isset($obj->data)) {
            if (is_array($obj->data)) :
                $data = json_encode($obj->data);
            elseif (is_object($obj->data)) :
                $data = json_encode($obj->data);
            else :
                $data = $obj->data;
            endif;
            $html .= "[Data: {$data}]";
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $html .= "[IP: {$ip}]";
        $html .= "\r\n";

        $this->setFileContents($this->path . "\\{$this->fileName}.log", $html, FILE_APPEND);
    }
}

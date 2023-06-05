<?php

namespace ChaosWD\Controller;

use ChaosWD\Controller\UserController;
use stdClass;

class TemplateController
{
    public $data;

    public function getView($arr = [])
    {
        $uri = $_SERVER['REQUEST_URI'];
        $dir = TEMPLATE_PATH;

        if (!isset($_ENV['HOME_URI'])) $_ENV['HOME_URI'] = "index";

        if ($uri == "" || $uri == "/") {
            $uri = "/{$_ENV['HOME_URI']}";
        }

        if (isset($_ENV['AUTHORIZATION_EXCLUSIONS'])) {
            $exclusion = explode(";", $_ENV['AUTHORIZATION_EXCLUSIONS']);

            if (!in_array($uri, $exclusion)) {
                $user = new UserController();
                $valid = (bool) $user->isUserValidated();

                if ($valid != true) {
                    setcookie('message', "Please log in.", time() + 30);
                    exit(header("Location: /" . $_ENV['LOGIN_URL'] ?? '/login'));
                }
            }
        }

        if (file_exists($dir . "{$uri}.php")) {
            $content = file_get_contents($dir . "{$uri}.php");
        } else {
            $errorLog = new LogController("errorLog");
            $obj = new stdClass();
            $obj->reason = "ErrorLog";
            $obj->message = "Template {$uri}.php does not exist.";
            $errorLog->add($obj);
        }

        $this->data = [
            'site_title' => $_ENV['SITE_TITLE'],
            'index' => "TESTTESTTEST"
        ];

        if (isset($arr) && $arr !== null) {
            array_push($this->data, $arr);
        }

        $filteredContent = $this->filterContent($content, $this->data);
        $output = $this->filterPHP($filteredContent, $this->data);
        $this->render($output);
    }

    protected function filterContent($content, $data)
    {
        extract($data);

        $pattern = '/\{(\w+)\}/';
        $replacement = '<?php echo e($$1); ?>';

        $compiled = preg_replace($pattern, $replacement, $content);

        ob_start();
        eval("?>" . $compiled . "<?php ");
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    protected function filterPHP($content, $data)
    {
        extract($data);
        $output = "";

        $lines = explode(PHP_EOL, $content);
        foreach ($lines as $line) {
            if (empty($line) || $line == "\r\n") {
                continue;
            }

            $line = trim($line);

            if (strpos($line, '#') === 0) {
                $phpCode = substr($line, 1);
                $output .= '<?php ' . $phpCode . '; ?>' . PHP_EOL;
            } else {
                $output .= $line . PHP_EOL;
            }
        }

        ob_start();
        eval("?>" . $output . "<?php ");
        $filteredOutput = ob_get_contents();
        ob_end_clean();

        return $filteredOutput;
    }

    protected function render($content)
    {
        $file = file_get_contents(TEMPLATE_PATH . "\\template.php");
        $compiled = preg_replace('/\{template\}/', $content, $file);
        $compiled = preg_replace('/\{site_title\}/', $_ENV['SITE_TITLE'], $compiled);

        echo $compiled;
    }
}

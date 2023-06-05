<?php

namespace ChaosWD\Controller;

class FormController
{

    public function process($var, $expectedVarType)
    {
        $var = $this->validate($var, $expectedVarType);
        $var = $this->sanitize($var, $expectedVarType);
        return $var;
    }

    public function validate($var, $expectedVarType)
    {
        $isFunction = 'is_' . $expectedVarType;
        if (function_exists($isFunction) && !call_user_func($isFunction, $var)) {
            return null;
        }
        return $var;
    }

    public function sanitize($var, $inputContext = null)
    {
        switch ($inputContext) {
            case 'html':
                $var = $this->sanitizeHTML($var);
                break;
            case 'url':
                $var = $this->sanitizeURL($var);
                break;
            case 'email':
                $var = $this->sanitizeEmail($var);
                break;
            case 'int':
            case 'float':
                $var = $this->sanitizeNumeric($var);
                break;
            default:
                $this->sanitizeGeneral($var);
        }

        return $var;
    }

    private function sanitizeHTML($var)
    {
        return strip_tags($var);
    }

    private function sanitizeURL($var)
    {
        if (filter_var($var, FILTER_VALIDATE_URL)) return filter_var($var, FILTER_SANITIZE_URL);
        return $var;
    }

    private function sanitizeEmail($var)
    {
        if (filter_var($var, FILTER_VALIDATE_EMAIL)) return filter_var($var, FILTER_SANITIZE_EMAIL);
        return $var;
    }

    private function sanitizeNumeric($var)
    {
        if (filter_var($var, FILTER_VALIDATE_INT)) return filter_var($var, FILTER_SANITIZE_NUMBER_INT);
        if (filter_var($var, FILTER_VALIDATE_FLOAT)) return filter_var($var, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return $var;
    }

    private function sanitizeGeneral($var)
    {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }

    public function generateToken()
    {
        $token = bin2hex(random_bytes(16));
        $_SESSION['token'] = $token;
        return $token;
    }

    protected function validateToken($token)
    {
        if (!isset($_SESSION['token'])) {
            setcookie('message', 'Unable to validate form.', time() + 30, '', '', false, 'lax');
            $this->redirectToForm();
        }

        if ($_SESSION['token'] !== $token) {
            setcookie('message', 'Unable to validate form.', time() + 30, '', '', false, 'lax');
            $this->redirectToForm();
        }

        return;
    }

    public function redirectToForm()
    {
        exit(header("Location: " . $_SERVER['HTTP_REFERER']));
    }
}

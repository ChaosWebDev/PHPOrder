<?php

namespace ChaosWD\Controller;

use ChaosWD\Controller\ChaosToken;
use ChaosWD\Controller\FormController;
use stdClass;

class UserController
{
    use \database;
    protected $userData = [];
    protected $userSubmittedInfo = [];

    public function isUserValidated()
    {
        return (bool) $_SESSION['userIsValidated'] ?? false;
    }

    public function validateUser()
    {
        if (!isset($_POST['username']) || !isset($_POST['password'])) $this->verificationFailed("Form did not submit");

        $form = new FormController();

        $this->userSubmittedInfo['username'] = $form->process($_POST['username'], "string");
        $this->userSubmittedInfo['password'] = $form->process($_POST['password'], "string");

        $this->userData = $this->execute("SELECT * FROM `users` WHERE `username` = ? LIMIT 1", [$this->userSubmittedInfo['username']]);
        $this->verification();
    }

    protected function verification()
    {

        if (empty($this->userData)) {
            $this->verificationFailed("Username not found.");
        }

        if (!password_verify($this->userSubmittedInfo['password'], $this->userData['password'])) {
            $this->verificationFailed("Password does not match.");
        }

        if (isset($_ENV['USING_USER_STATUS']) && $_ENV['USING_USER_STATUS'] == 'true') {
            if ($this->userData['status'] >= $_ENV['BLOCK_STATUS'] ?? 3) {
                $this->verificationFailed("Account blocked.");
            }
        }

        if (isset($_ENV['USING_MAX_ATTEMPTS']) && $_ENV['USING_MAX_ATTEMPTS'] == 'true') {
            if ($this->userData['failedAttempts'] >= (int) $_ENV['MAX_ATTEMPTS']) {
                $this->verificationFailed("Too many failed attempts.");
            }
        }

        $this->verificationSucceeded();
    }

    protected function verificationFailed($reason)
    {
        $userLog = new LogController("userLog");
        $obj = new stdClass();
        $obj->reason = "UserAccess";
        $obj->message = $reason;
        $obj->data = $this->userData;
        $userLog->add($obj);

        $params = [];
        $sql = "UPDATE `users` SET `failedAttempts` = :failedAttempts";
        $failedAttempts = $this->userData['failedAttempts'] + 1;
        $params['failedAttempts'] = $failedAttempts;

        if ($failedAttempts >= $_ENV['MAX_ATTEMPTS'] ?? 3) {
            $status = $_ENV['BLOCK_STATUS'] ?? 3;
            $sql .= ", `status` = :status";
            $params['status'] = $status;
        }

        $sql .= " WHERE `username` = :username LIMIT 1";
        $params['username'] = $this->userSubmittedInfo['username'];

        $this->execute($sql, $params);

        setcookie('message', $reason, time() + 30);

        if (isset($_COOKIE['chaos_token'])) {
            setcookie('chaos_token', '', time() - 3600);
        }

        exit(header("Location: /" . $_ENV['LOGIN_URL']));
    }

    protected function verificationSucceeded()
    {
        $userLog = new LogController("userLog");
        $obj = new stdClass();
        $exclude = ['password', 'status', 'failedAttempts', 'lastLogin', 'resetKey', 'resetExpiration'];
        foreach ($this->userData as $key => $value) :
            if (!in_array($key, $exclude)) :
                $_SESSION['userInfo'][$key] = $value;
            endif;
        endforeach;

        $_SESSION['userIsValidated'] = true;

        $params = array(
            "lastLogin" => time(),
            "username" => $this->userData['username']
        );

        $sql = "UPDATE `users` 
            SET `failedAttempts` = 0, `lastLogin` = :lastLogin, `resetKey` = null, `resetExpiration` = null
            WHERE `username` = :username 
            LIMIT 1";

        $this->execute($sql, $params);

        setcookie('message', "Logged in successfully - " . time(), time() + 30);

        $obj->reason = "UserAccess";
        $obj->message = "User Logged In Successfully";
        $obj->data = ["username" => $_COOKIE['username'] ?? $_SESSION['userInfo']['username']];
        $userLog->add($obj);

        exit(header("Location: /" . $_ENV['HOME_URL']));
    }

    public function logout()
    {
        $userLog = new LogController("userLog");
        $obj = new stdClass();
        $obj->reason = "UserAccess";
        $obj->message = "User Logged Out Successfully";
        $obj->data = ["username" => $_COOKIE['username'] ?? $_SESSION['username']];
        $userLog->add($obj);

        if (session_status() !== PHP_SESSION_NONE) {
            @session_destroy();
        }

        foreach ($_COOKIE as $key => $value) :
            setcookie($key, '', time() - 1);
        endforeach;

        setcookie('message', "Logged out successfully.", time() + 30);

        exit(header("Location: /" . $_ENV['LOGIN_URL']));
    }
}

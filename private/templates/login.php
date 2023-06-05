<?php

use ChaosWD\Controller\FormController;

$fc = new FormController;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChaosWD\Order Framework</title>
    <link rel="stylesheet" href="/assets/styles.css">
    <link rel="manifest" href="/assets/favicons/manifest.json">
    <link rel="shortcut icon" href="/assets/favicons/favicon.ico" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
</head>

<body>
    <?php
    if (isset($_COOKIE['message'])) {
        echo "<dvi>{$_COOKIE['message']}</dvi>";
        setcookie('message', "", time() - 1);
    }
    ?>
    <form action="/loginVerification" method="POST">
        <input type='text' style='display:none' name='token' id='token' value='<?= $fc->generateToken() ?>'>
        <input type="text" name="username" id="username" value='jpgerber'>
        <input type="text" name="password" id="password" value='pizza'>
        <input type="submit" value="Log In">
        <a href="/logout">Logout</a>
    </form>
</body>

</html>
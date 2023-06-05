<?php
function e($var)
{
    return htmlentities($var, ENT_QUOTES, 'UTF-8');
}

function pr($var)
{
    echo "<pre>";
    print_r($var);
    echo "</pre>";
}

function vd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
}

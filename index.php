<?
require_once (__DIR__ . '/dao/Usuario.php');
$usr = Usuario::restoreFromSession();
$to = isset($usr) ? './main.php' : './login.php';
header('Location: ' . $to );
<?
require_once (__DIR__ . '/dao/Usuario.php');
$diretorio = dirname($_SERVER['PHP_SELF']) . '/';


$usr = Usuario::restoreFromSession();
$to = isset($usr) ? './main.php' : './login.php';
$addr = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
header("Location: $addr$diretorio$to" );
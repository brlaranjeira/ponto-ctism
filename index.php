<?
require_once (__DIR__ . '/dao/Usuario.php');
$diretorio = dirname($_SERVER['PHP_SELF']) . '/';


$usr = Usuario::restoreFromSession();
$to = isset($usr) ? './main.php' : './login.php';
header("Location: $diretorio$to" );
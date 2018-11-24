<?
require_once (__DIR__ . '/dao/Usuario.php');
$diretorio = dirname($_SERVER['PHP_SELF']) . '/';


$usr = Usuario::restoreFromSession();
$to = isset($usr) ? './main.php' : './login.php';
$addr = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
$proto = isset($_SERVER['HTTPS']) ? 'https' : 'http';

$redir = $addr.$diretorio.$to;
header("Location: $proto://$redir" );
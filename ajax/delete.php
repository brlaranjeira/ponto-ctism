<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/14/17
 * Time: 10:50 AM
 */

session_start();
require_once (__DIR__ . '/../dao/Usuario.php');
$usr = Usuario::restoreFromSession();

if (!isset($usr)) {
	$proto = isset($_SERVER['HTTPS']) ? 'https' : 'http';
	$addr = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
	$diretorio = dirname($_SERVER['PHP_SELF']) . '/';
	$dparts = array_filter(explode('/',$diretorio));
	array_pop($dparts);
	$diretorio = '/' . implode('/',$dparts) . '/';
	$to = 'login.php';
	$redir = "$proto://$addr$diretorio$to";
	http_response_code(302);
	echo '{"href": "' . $redir . '"}';
} else {
	require_once (__DIR__ . '/../dao/Ponto.php');
	$ponto = Ponto::getById($_POST['cod']);
	$deletou = $ponto->delete();
	if ( !$deletou ) {
		http_response_code(500);
		echo '{"message": "Erro interno.\nCaso o erro persista, contate o setor responsável."}';
	} else {
		http_response_code(200);
		echo '{"message": "ok"}';
	}
}

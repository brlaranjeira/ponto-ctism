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
	include '../fragment/redirlogin.php';
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

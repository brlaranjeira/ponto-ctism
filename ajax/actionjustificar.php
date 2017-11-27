<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/24/17
 * Time: 7:07 PM
 */

session_start();
require_once (__DIR__ . '/../dao/Usuario.php');
require_once (__DIR__ . '/../dao/Ponto.php');
$usr = Usuario::restoreFromSession();

if (!isset($usr) || !$usr->hasGroup(Usuario::GRUPO_BOLSISTAS)) {
	include '../fragment/redirlogin.php';
}

$tsParts = explode(' ' , $_POST['registrodthr'] );
$dtParts = explode('/' , $tsParts[0] );
$dt = $dtParts[2] . '-' . $dtParts[1] . '-' . $dtParts[0];
$hr = $tsParts[1] . ':00';

$ponto = new Ponto();
$ponto->setIp();
$ponto->setUsuario($usr);
$ponto->setEvent($_REQUEST['registroevt']);
$ponto->setTimestamp("$dt $hr");
$ponto->setJust($_POST['registromotivo']);
if ($ponto->save()) {
	http_response_code(200);
	echo '{"message": "Justificativa registrada."}';
} else {
	http_response_code(500);
	echo '{"message": "Erro interno.\nCaso o erro persista, contate o setor responsável."}';
}


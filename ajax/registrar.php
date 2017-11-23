<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/23/17
 * Time: 4:39 PM
 */



require_once (__DIR__ . '/../dao/Ponto.php');
require_once (__DIR__ . '/../dao/Usuario.php');


if (isset($_POST) && !empty($_POST)) {
	$usuario = Usuario::restoreFromSession();
	if (!$usuario->hasGroup(Usuario::GRUPO_BOLSISTAS)) {
		header('Location: ./main.php');
		die();
	}
	$ponto = new Ponto();
	$ponto->setIp();
	$ponto->setUsuario($usuario);
	$ponto->setEvent($_POST['evt']);
	if ($ponto->save()) {
		echo '{"message": "Registro adicionado!"}';
	} else {
		echo '{"message": "Erro interno.\nCaso o erro persista, contate o setor responsável."}';
	}
	
}

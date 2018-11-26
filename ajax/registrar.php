<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/23/17
 * Time: 4:39 PM
 */



require_once (__DIR__ . '/../dao/Ponto.php');
require_once (__DIR__ . '/../dao/Usuario.php');
require_once (__DIR__ . '/../lib/ConfigClass.php');

$ip = $_SERVER['REMOTE_ADDR'];
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}
$ipsPermitidos = ConfigClass::ipsInternos;
$ip = '200.132.24.48';
$allow = true;
foreach ($ipsPermitidos as $x) {
    $pattern = "/^$x$/";
    if (preg_match($pattern,$ip) == 1){
        $allow = true;
        break;
    }
}
if (!$allow) {
    http_response_code(403);
    die('{"message":"Computador sem permissão."}');
}

if (isset($_POST) && !empty($_POST)) {
	$usuario = Usuario::restoreFromSession();
	if (!$usuario->hasGroup(Usuario::GRUPO_BOLSISTAS)) {
        die('{"message":"Usuário sem permissão."}');
	}
	$ponto = new Ponto();
	$ponto->setIp();
	$ponto->setUsuario($usuario);
	$ponto->setEvent($_POST['evt']);
	$ponto->setJust("");
	/*if ($ponto->save(null,true,array('deferido','usuarioDeferidor'))) {*/
	if ($ponto->save()) {
		echo '{"message": "Registro adicionado! <a href=\"./consultar.php\">Clique aqui para ver seus horários</a>"}';
	} else {
		echo '{"message": "Erro interno.\nCaso o erro persista, contate o setor responsável."}';
	}
	
}

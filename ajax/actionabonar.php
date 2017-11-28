<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/28/17
 * Time: 9:45 AM
 */

if (!empty($_POST)) {
	require_once (__DIR__ . '/../dao/Ponto.php');
	require_once (__DIR__ . '/../dao/Usuario.php');
	$usuario = Usuario::restoreFromSession();
	if ( !$usuario->hasGroup(Usuario::GRUPO_BOLSISTAS) ) {
		include '../fragment/redirlogin.php';
		die();
	}
	$ponto = new Ponto();
	$ponto->setIp();
	$ponto->setUsuario($usuario);
	$ponto->setEvent(Ponto::PONTO_ABONO);
	$dtParts = explode('/',$_POST['dataabono']);
	$data = $dtParts[2].'-'.$dtParts[1].'-'.$dtParts[0];
	$hora = str_pad($_POST['horasabono'],2,'0',STR_PAD_LEFT).':'.str_pad($_POST['minutosabono'],2,'0',STR_PAD_LEFT).':00';
	$ponto->setTimestamp("$data $hora");
	$ponto->setJust($_POST['motivoabono']);
	http_response_code(200);
	if ( $ponto->save() ) {
		$json = [
			'message' => "Abono solicitado. Deseja enviar e-mail para avisar o(a) coordenador(a) da bolsa?",
			'html' => "
<div class='container-fluid'>
	<div class='row'>
		<div class='col-xs-12'>
			<div class='form-group'>
				<label for='mail-coord'>Digite o endereço de e-mail</label><input id='mail-coord' name='mail-coord' class='form-control' type='text' />
			</div>
		</div>
	</div>
</div>
"
		];
		echo json_encode($json);
		
//		echo '{"message": "Abono solicitado.","html": "' . $mailForm . '"}';
	}
	
}
<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 8/31/17
 * Time: 10:33 AM
 */

require_once (__DIR__ . '/dao/Usuario.php');
$usr = Usuario::restoreFromSession();
$to = './login.php';
if (isset($usr)) {
	//TODO: ver grupos
	require_once (__DIR__ . '/lib/ConfigClass.php');
	$paginas = array_keys($usr->getPaginasPermitidas());
	
	$to = './' . $paginas[0] . '.php';
}
header('Location: ' . $to);
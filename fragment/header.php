<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 8/31/17
 * Time: 10:40 AM
 */
require_once (__DIR__ . '/../dao/Usuario.php');
$usuario = Usuario::restoreFromSession();
if (!isset($usuario)) {
    header('Location: ' . './login.php');
}
?>
<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="./main.php">Ponto Bolsistas</a>
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="active"><a href="consultar.php">Consultar</a></li>
				<li class="active"><a href="registrar.php">Registrar</a></li>
				<li class="active"><a href="justificar.php">Justificar</a></li>
				<li class="active"><a href="abonar.php">Abonar horas</a></li>
			</ul>
		</div>
	</div>
</nav>
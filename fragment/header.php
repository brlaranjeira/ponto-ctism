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
    die();
}
$current = explode('.',basename($_SERVER['PHP_SELF']))[0];
if (!$usuario->verificaPermissao($current)) {
	header('Location: ' . './main.php');
	die();
}
$paginas = $usuario->getPaginasPermitidas();
?>
<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="main.php">Ponto Bolsistas</a>
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div>
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
                <?
                    foreach ($paginas as $k => $v) {
                        $clActive = $current == $k ? 'class="active"' : '';
                        ?><li <?=$clActive?>><a href="<?=$k?>.php"><?=$v['nome']?></a> </li><?
                    }
                ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a><?=$usuario->getFullName()?></a></li>
                <li><a>|</a></li>
				<li><a href="logout.php">Sair</a></li>
			</ul>
		</div>
	</div>
</nav>

<div id="div-alert" class="fade alert">
    <strong id="alert-title">Success!</strong><span id="alert-message">Indicates a successful or positive action.</span>
</div>

<div id="modal-confirm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirma&ccedil;&atilde;o</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <!--<div class="btn-group"> -->
                <button type="button" id="btn-cancela-ok" class="btn btn-danger">N&atilde;o</button>
                <button type="button" id="btn-confirm-ok" class="btn btn-success">Sim</button>
                <!-- </div> -->
            </div>
        </div>
    </div>
</div>

<div id="modal-custom" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Mensagem
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div id="modal-mensagem-body" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
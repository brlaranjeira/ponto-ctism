<?php
/**
 * Created by PhpStorm.
 * User: Desktop-153157
 * Date: 22/08/2017
 * Time: 11:20
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ponto Bolsistas CTISM">
    <meta name="author" content="Noronha">
    <link rel="icon" href="img/CTISM.ico">
    <title>Ponto Bolsistas</title>
</head>
<body>

<?
include "./fragment/header.php";
if (!empty($_POST)) {
    require_once (__DIR__ . '/dao/Ponto.php');
    $ponto = new Ponto();
	$ponto->setIp($_SERVER['REMOTE_ADDR']);
	$ponto->setUsuario($usuario);
	$ponto->setEvent(Ponto::PONTO_ABONO);
	$dtParts = explode('/',$_POST['dataabono']);
	$data = $dtParts[2].'-'.$dtParts[1].'-'.$dtParts[0];
	$hora = str_pad($_POST['horasabono'],2,'0',STR_PAD_LEFT).':'.str_pad($_POST['minutosabono'],2,'0',STR_PAD_LEFT).':00';
	$ponto->setTimestamp("$data $hora");
	$ponto->setJust($_POST['motivoabono']);
	if ( $ponto->save() ) {
	    $msg = 'Abono registrado/solicitado.';
    }
 
}


?>

<div class="container">
    <?=isset($msg) ? $msg : ''?>
    <form action="" method="post">
        <div class="row">
            <div class="form-group col-xs-12 col-md-4">
                <label for="dataabono">Data</label>
                <input class="form-control input-data" type="text" name="dataabono" id="dataabono" placeholder="dd/mm/aaaa" maxlength="10">
            </div>
            <div class="form-group col-xs-12 col-md-4">
                <label for="horasbono">Horas</label>
                <input class="form-control" type="text" name="horasabono" id="horasabono"  maxlength="2">
            </div>
            <div class="form-group col-xs-12 col-md-4">
                <label for="minutosabono">Minutos</label>
                <input class="form-control" type="text" name="minutosabono" id="minutosabono" maxlength="2">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <label for="motivoabono">Motivo</label>
                    <textarea class="form-control" name="motivoabono" id="motivoabono"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <button type="submit" class="form-control btn btn-info">Enviar</button>
            </div>
        </div>
    </form>
    
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.mask.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/abonar.js"></script>
</html>
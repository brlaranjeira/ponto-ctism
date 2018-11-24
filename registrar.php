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
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/registrar.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ponto Bolsistas CTISM">
    <meta name="author" content="Noronha">
    <link rel="icon" href="img/CTISM.ico">
    <title>Ponto Bolsistas</title>
</head>
<body>

<?
require_once (__DIR__ . '/dao/Usuario.php');
require_once (__DIR__ . '/dao/Ponto.php');
$usuario = Usuario::restoreFromSession();

include "./fragment/header.php";

?>

<div class="container">
    <?
        $uidNumber = $usuario->getUidNumber();
        
        $ultimo = Ponto::getByAttr(
            array('usuario','event'),
            array ($uidNumber,Ponto::PONTO_ABONO),
            array('=','<>'),
            'timestamp',
            'DESC',
            1
        );
        $hlEntrada = true;
        if (isset($ultimo) && !empty($ultimo) && $ultimo[0]->getTimestamp(Ponto::TS_DATA) == date('d/m/Y')) {
            $hlEntrada = $ultimo[0]->getEvent() == Ponto::PONTO_SAIDA;
        }

        if (isset($msg) && !empty($msg)) {
            echo "<span class=\"hidden\" id=\"span-mensagem\">$msg</span>";
        }
    ?>
    <div class="row">
        <div class="col-xs-12">
            <div id="div-horario">
                <h1 id="horario" hrinicial="<?=time()?>">
                    <?=date('H:i:s', time());?>
                </h1>
            </div>
        </div>
    </div>
    <div id="div-registrar">
        <div class="row">
            <div class="col-xs-12">
                <button evt="<?=Ponto::PONTO_ENTRADA?>" class="btn btn-lg btn-info btn-block btn-registro <?=$hlEntrada?'btn-hl':''?> " type="submit">Entrada</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button evt="<?=Ponto::PONTO_SAIDA?>" class="btn btn-lg btn-info btn-block btn-registro <?=$hlEntrada?'':'btn-hl'?> " type="submit">Saída</button>
            </div>
        </div>
    </div>
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.mask.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/main.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/registrar.js"></script>
</html>
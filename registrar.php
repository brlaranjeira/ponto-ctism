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

require_once (__DIR__ . '/dao/Ponto.php');
require_once (__DIR__ . '/dao/Usuario.php');


if (isset($_POST) && !empty($_POST)) {
	$usuario = Usuario::restoreFromSession();
    $ponto = new Ponto();
	$ponto->setIp();
	$ponto->setUsuario($usuario);
    $ponto->setEvent($_POST['evt']);
    if ($ponto->save()) {
        $msg = 'Evento registrado!';
    }
    
}



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

    ?>
    <?=isset($msg)?$msg:''?>
    <form method="post" action="">
        <input type="hidden" name="evt" value="<?=Ponto::PONTO_ENTRADA?>">
        <button class="btn btn-lg btn-info btn-block <?=$hlEntrada?'btn-hl':''?> " type="submit">Entrada</button>
    </form>
    <form method="post" action="">
        <input type="hidden" name="evt" value="<?=Ponto::PONTO_SAIDA?>">
        <button class="btn btn-lg btn-info btn-block <?=$hlEntrada?'':'btn-hl'?> " type="submit">Saída</button>
    </form>
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.mask.min.js"></script>
</html>
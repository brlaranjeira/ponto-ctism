<?php
/**
 * Created by PhpStorm.
 * User: Desktop-153157
 * Date: 22/08/2017
 * Time: 11:23
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
require_once (__DIR__ . '/dao/Ponto.php');
?>

<div class="container">
    <form action="" method="post">
        <div class="row">
            <div class="col-md-6 col-xs-12 form-group">
                <label for="registro-evt">Evento</label>
                <select name="registro-evt" id="registro-evt" class="form-control">
                    <option value="<?=Ponto::PONTO_ENTRADA?>">Entrada</option>
                    <option value="<?=Ponto::PONTO_SAIDA?>">Sa&iacute;da</option>
                </select>
            </div>
            <div class="col-md-6 col-xs-12 form-group">
                <label for="registro-dthr">Data e Hora</label>
                <input name="registro-dthr" id="registro-dthr" type="text" class="form-control input-data" maxlength="16" placeholder="DD/MM/AAAA hh:mm">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                <button type="submit" class="btn form-control btn-info">Enviar justificativa</button>
            </div>
        </div>
    </form>
</div>

</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.mask.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/justificar.js"></script>
</html>

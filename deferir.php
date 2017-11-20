<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 8/31/17
 * Time: 10:40 AM
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
<?
require_once (__DIR__ . '/dao/Ponto.php');
require_once (__DIR__ . '/dao/Usuario.php');
 include './fragment/header.php';
?>
<body>


<div class="container">
        <label for="registro-evt"><font size="5"><b>Bolsista</b></font></label>
        <?
        $bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
        $pontos = Ponto::getByAttr('deferido',0,'=');
        $bolsistaSelecionado = isset($_GET['bolsista']) ? new Usuario($_GET['bolsista']) : $bolsistas[0];
        ?>
        <br/>
        <div id= "div-deferir" class=" col-xs- form-group">
            <?
            foreach ($pontos as $ponto) {
                echo '<div class="row">';


                echo '<div class="col-xs-3">';
                echo '<font size="4">';
                echo $ponto->getUsuario()->getFullname();
                echo '</font>';
                echo '</div>';

                echo '<div class="col-xs-3">';
                echo '<font size="4">';
                echo $ponto->getTimestamp();
                echo '</font>';
                echo '</div>';

                echo '<div class="col-xs-3">';
                echo '<font size="3">';
                echo '<b>';
                echo '[';
                echo $ponto->getEvent();
                echo '] ';
                echo '</b>';
                echo $ponto->getJust();
                echo '</font>';
                echo '</div>';

                echo '<div class="col-xs-3">';
                    echo '<div class="btn-group">';
                        $codigo=$ponto->getId();
                        echo '<button cod="' . $codigo . '" class="btn btn-md btn-success btn-deferir">Deferir</button>';
                        echo '<button cod="' . $codigo . '" class="btn btn-md btn-danger btn-indeferir">Não deferir</button>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                ?>

                <?
                echo    '</div>';
                }?>


</div>
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.mask.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/deferir.js"></script>
</html>




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



        <label for="registro-evt">Bolsista</label>
        <?
        $bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
        $pontos = Ponto::getByAttr('deferido',0,'=');
        $bolsistaSelecionado = isset($_GET['bolsista']) ? new Usuario($_GET['bolsista']) : $bolsistas[0];
        ?>
        <br/>
        <div class=" col-xs- form-group">
            <?
            foreach ($pontos as $ponto) {
                echo '<div class="row">';


                // nome do bolsista
                echo '<div class="col-xs-3">';
              //  echo 'NOME';
                echo '<br/>';
                echo $ponto->getUsuario()->getFullname();

                echo '</div>';

                // data e hora
                echo '<div class="col-xs-3">';
             //  echo 'DATA E HORA';
                echo '<br/>';
                echo $ponto->getTimestamp();
                echo '</div>';

                // [entrada/saida/abono] motivo
                echo '<div class="col-xs-3">';
                echo 'MOTIVO';
                echo '<br/>';
                echo '[';
                echo $ponto->getEvent();
                echo '] ';
                echo $ponto->getJust();
                echo '</div>';

                // [botao aceita/botao cancela]
                echo '<div class="col-xs-3">';
              //  echo 'botoes';
                echo '<br/>';
                echo '<input type="button" name="Deferir" value="Deferir">';
                echo "  ";
                echo '<input type="button" name="Não Deferir" value="Não Deferir">';
                echo '</div>';


                echo '</div>';
                ?>



                <?







                echo    '</div>';

                }?>


</div>




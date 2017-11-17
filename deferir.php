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


<div class="row">
    <div class="col-md-3 col-xs-6 form-group">
        <label for="registro-evt">Bolsista</label>
        <?
        $bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
        $bolsistaSelecionado = isset($_GET['bolsista']) ? new Usuario($_GET['bolsista']) : $bolsistas[0];
        ?>
        <br/>
        <div class="col-md-3 col-xs-6 form-group">
            <?
            foreach ($bolsistas as $bolsista) {
                $bolsista->getUid()?> <?=$bolsista->getFullName();

                }?>

        </div>
    </div>
</div>




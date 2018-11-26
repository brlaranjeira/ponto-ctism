<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 26/11/18
 * Time: 18:59
 */

require_once __DIR__. '/./dao/Usuario.php';

$bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
foreach ( $bolsistas as $bolsista ) {
    echo $bolsista->getFullName() . ' (calcular horas)' . '<br/>';
}
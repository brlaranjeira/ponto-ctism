<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/20/17
 * Time: 2:14 PM
 */

require_once (__DIR__ . '/dao/Usuario.php');
Usuario::destroySession();
header('Location: ./login.php');
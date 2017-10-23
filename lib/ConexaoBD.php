<?php

/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 16/08/16
 * Time: 09:31
 */
class ConexaoBD {

    public static function getConexao() {
        require_once ( __DIR__ . "/./ConfigClass.php");
        try {
            return new PDO('mysql:host=' . ConfigClass::bdHost . ';dbname=' . ConfigClass::bdName . ';charset=' . ConfigClass::bdCharset,ConfigClass::bdUser,ConfigClass::bdPasswd);
        } catch (Exception $e) {
            die ($e->getMessage());
        }
    }

}
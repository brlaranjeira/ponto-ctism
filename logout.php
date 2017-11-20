<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/20/17
 * Time: 2:14 PM
 */

session_start();
session_destroy();
session_commit();
header('Location: ./login.php');
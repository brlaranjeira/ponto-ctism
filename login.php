<!DOCTYPE html>


<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ponto Bolsistas CTISM">
    <meta name="author" content="Noronha">
    <link rel="icon" href="img/CTISM.ico">

    <title>Ponto Bolsistas</title>
    <link href="css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/signin.css" rel="stylesheet">


</head>

<body>
<? include './fragment/modais.php';?>
<div class="container">
<?
require_once (__DIR__ . '/dao/Usuario.php');
if ( isset($_POST) == !empty($_POST) ) {
    $usuario = Usuario::auth($_POST['usuario'] , $_POST['senha'] );
    if ($usuario != null ) {
        $usuario->saveToSession();
        header("Location: ./consultar.php");
    } else {
        $msg = "Usuario não encontrado";
    }
}

?>
    <form id="form-signin" class="form-signin" method="POST" action="">
        <h2 class="form-signin-heading text-center">Ponto Bolsistas<br /><br /></h2>
        <label for="usuario" class="sr-only">Usuário</label>

        <input id="login-usr" type="text" name="usuario" class="form-control" placeholder="Digite seu usuário" autofocus><br />

        <label for="senha" class="sr-only">Senha</label>
        <input id="login-pw" type="password" name="senha" class="form-control" placeholder="Digite sua senha"  >

        <button class="btn btn-lg btn-primary btn-block" type="submit">Acessar</button><br /><br /><br />
    </form>

</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/main.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/login.js"></script>
<?
if (isset($msg)) {
    echo '';
    ?>
    <script>
        showMessage('PONTO ELETRÔNICO]','<?=$msg?>','danger');
    </script>
    <?
}
?>
</html>
<?
/**
 * Created by PhpStorm.
 * User: Desktop-153157
 * Date: 21/08/2017
 * Time: 08:55
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/consultar.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ponto Bolsistas CTISM">
    <meta name="author" content="Noronha">
    <link rel="icon" href="img/CTISM.ico">
    <title>Ponto Bolsistas</title>
</head>
<body>

    <? include './fragment/header.php' ?>

<div class="container">
    <h3>
        <?
            $meses = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
            
            $mesSelecionado = isset($_GET['mes']) ? $_GET['mes'] : date('m');
            $anoSelecionado = isset($_GET['ano']) ? $_GET['ano'] : date('Y');
            
            require_once (__DIR__ . '/dao/Usuario.php');
            
            $usuario = Usuario::restoreFromSession();
            if ($usuario->hasGroup(array(Usuario::GRUPO_PROFESSORES,Usuario::GRUPO_FUNCIONARIOS,Usuario::GRUPO_SSI))) {
	            $bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
            } else {
                $bolsistas = array ($usuario);
            }
            $bolsistaSelecionado = isset($_GET['bolsista']) ? new Usuario($_GET['bolsista']) : $bolsistas[0];
            
        ?>
        <form method="get" action="">
            <div class="row">
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="select-ano">Ano</label>
                    <select id="select-ano" name="ano" class="form-control">
                        <? for ($i=date('Y');$i>=2013;$i--) {
                            $selStr = ($i == $anoSelecionado) ? 'selected' : '';
                            ?><option value="<?=$i?>" <?=$selStr?> ><?=$i?></option><?
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="select-mes">M&ecirc;s</label>
                    <select id="select-mes" name="mes" class="form-control">
                        <? for ($i=0; $i<sizeof($meses);$i++) {
                            $vl = str_pad(strval($i+1),2,0,STR_PAD_LEFT);
                            $selStr = ($vl == $mesSelecionado) ? 'selected' : '';
                            ?><option value="<?=$vl?>" <?=$selStr?> ><?=$meses[$i]?></option><?
                        } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="bolsista">Bolsista</label>
                    <select id="select-bolsista" name="bolsista" class="form-control">
                        <? foreach ($bolsistas as $bolsista) {
                            $selStr = $bolsista->getUid() == $bolsistaSelecionado->getUid() ? 'selected' : '';
                            ?><option value="<?=$bolsista->getUid()?>" <?=$selStr?>><?=$bolsista->getFullName()?></option><?
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 form-group">
                    <button type="submit" class="btn btn-block btn-info"><span class="glyphicon glyphicon-search">&nbsp;Buscar</span></button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-xs-12">
                <table id="tb-registros" class="table table-striped">
                    <thead>
                    <tr>
                        <th width="10%" class="td-left">Dia</th>
                        <th width="30%">Entrada</th>
                        <th width="30%">Saída</th>
                        <th width="30%" class="td-right">Horas</th>
                    </tr>
                    <tr>
                        <th colspan="4" id="td-carregando" class="hidden" ></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </h3>
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/egg/egg.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/main.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/consultar.js"></script>

</html>
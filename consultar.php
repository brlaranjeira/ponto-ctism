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
            $bolsistas = Usuario::getAllFromGroup(Usuario::GRUPO_BOLSISTAS);
            $bolsistaSelecionado = isset($_GET['bolsista']) ? new Usuario($_GET['bolsista']) : $bolsistas[0];
            
        ?>
        <form method="get" action="">
            <div class="row">
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="select-mes">Ano</label>
                    <select name="ano" class="form-control">
                        <? for ($i=date('Y');$i>=2013;$i--) {
                            $selStr = ($i == $anoSelecionado) ? 'selected' : '';
                            ?><option value="<?=$i?>" <?=$selStr?> ><?=$i?></option><?
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="select-mes">M&ecirc;s</label>
                    <select name="mes" class="form-control">
                        <? for ($i=0; $i<sizeof($meses);$i++) {
                            $vl = str_pad(strval($i+1),2,0,STR_PAD_LEFT);
                            $selStr = ($vl == $mesSelecionado) ? 'selected' : '';
                            ?><option value="<?=$vl?>" <?=$selStr?> ><?=$meses[$i]?></option><?
                        } ?>
                    </select>
                </div>
                <div class="col-xs-12 col-md-4 form-group">
                    <label for="bolsista">Bolsista</label>
                    <select name="bolsista" class="form-control">
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
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Entrada</th>
                        <th>Saída</th>
                        <th>Horas</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?
                    require_once (__DIR__ . '/dao/Ponto.php');
                    $dtIni = "$anoSelecionado-$mesSelecionado-01 00:00:00";
                    $anoFim = ($mesSelecionado != '12') ? $anoSelecionado : intval($anoSelecionado)+1;
                    $mesFim = ($mesSelecionado != '12') ? str_pad(intval($mesSelecionado)+1,2,0,STR_PAD_LEFT) : '01';
                    $dtFim = "$anoFim-$mesFim-01 00:00:00";
                    
                    $pontos = Ponto::getByAttr(
                            array('usuario','timestamp','timestamp'),
                            array($bolsistaSelecionado->getUidNumber(),$dtIni,$dtFim),
                            array('=','>=','<'),
                            array(
                                    'DATE(' . Ponto::getColumnName('timestamp') . ')',
	                            Ponto::getColumnName('event') . '= \'' . Ponto::PONTO_ABONO . '\'',
	                            'TIME('. Ponto::getColumnName('timestamp'). ')'));
                    if (empty($pontos)) {
                        echo '<tr><td colspan="4">Nenhum registro encontrado</td></tr>';
                    }
                    $anterior = null;
                    foreach ( $pontos as $ponto ) {
                        switch ($ponto->getEvent()) {
                            case Ponto::PONTO_ENTRADA:
                                ?><tr><td><?=$ponto->getTimestamp(Ponto::TS_DATA)?></td>
                                <td><?=$ponto->getTimestamp(Ponto::TS_HORARIO)?></td><?
                                break;
                            case Ponto::PONTO_SAIDA:
                                ?><td><?=$ponto->getTimestamp(Ponto::TS_HORARIO)?></td><?
                                ?><td><?='diff'?></td></tr><?
                                break;
                            default: //abono
                                break;
                        }
                        $anterior = $ponto;
                    } ?>
                    
                    </tbody>
                </table>
            </div>
        </div>
    </h3>
</div>
</body>
<script type="application/ecmascript" language="ecmascript" src="js/jquery/jquery.min.js"></script>
<script type="application/ecmascript" language="ecmascript" src="js/bootstrap/bootstrap.min.js"></script>
</html>
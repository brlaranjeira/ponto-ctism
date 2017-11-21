<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/16/17
 * Time: 10:58 AM
 */

require_once (__DIR__.'/../dao/Ponto.php');
require_once (__DIR__.'/../dao/Usuario.php');
require_once (__DIR__ . '/../lib/Utils.php');
$usr = Usuario::restoreFromSession();
$bolsista = $_REQUEST['bolsista'];
$bolsista = (new Usuario($bolsista))->getUidnumber();
$ano = $_REQUEST['ano'];
$mes = $_REQUEST['mes'];

function linkJustificativa( $bolsista , $evt , $dt='' ) {
	$usr = Usuario::restoreFromSession();
	$usr = $usr->getUidNumber();
	if ($usr == $bolsista)  {
		$dt = explode('-',explode(' ',$dt)[0]);
		$dt = $dt[2] . $dt[1] . $dt[0];
		return "<a class='link-justificativa' href=\"./justificar.php?dt=$dt&evt=$evt\">Adicionar Justificativa</a>";
	} else {
		return 'Registro ausente';
	}
	
}

function buildTooltip ( $ponto ) {
	$just = $ponto->getJust();
	if ($ponto->getDeferido() == 0) {
		$just = "$just
Aguardando deferimento";
	}
	if ( isset($just) ) {
		return "<a data-toggle=\"tooltip\" title=\"$just\">
					<small>
						<span class=\"fa fa-info-circle\" aria-hidden=\"true\"></span>
					</small>
				</a>";
	}
	return '';
}


function buildTrashBtn ( $usr,  $ponto ) {
	if ($ponto->getUsuario()->getUid() == $usr->getUid()) {
		return '<button class="btn-delete btn btn-danger btn-apagar btn-sm"' . 'cod="' . $ponto->getId() . '"><span class="fa fa-trash-o" aria-hidden="true"></span></button>';
	} else {
		return '';
	}
}

$dtIni= "$ano-$mes-01 00:00:00";
$anoFim = ($mes != '12') ? $ano : intval($ano)+1;
$mesFim = ($mes != '12') ? str_pad(intval($mes)+1,2,0,STR_PAD_LEFT) : '01';
$dtFim = "$anoFim-$mesFim-01 00:00:00";


$pontos = Ponto::getByAttr (
	array('usuario','timestamp','timestamp'),
	array($bolsista,$dtIni,$dtFim),
	array('=','>=','<'),
	array(
		'DATE(' . Ponto::getColumnName('timestamp') . ')',
		Ponto::getColumnName('event') . '= \'' . Ponto::PONTO_ABONO . '\'',
		'TIME('. Ponto::getColumnName('timestamp'). ')'));

if (empty($pontos)) {
	echo '<tr><td colspan="4">Nenhum registro encontrado</td></tr>';
}

if (!$usr->hasGroup(array(Usuario::GRUPO_PROFESSORES,Usuario::GRUPO_FUNCIONARIOS,Usuario::GRUPO_SSI))) {
	if ($usr->getUidNumber() != $bolsista) {
		http_response_code(400);
		die();
	}
}



$anterior = null;
$totalTrab = 0;
$totalAbono = 0;
foreach ( $pontos as $ponto ) {
	
	if ($deferido = $ponto->getDeferido() == 1) {
		$hora = '<span>' . $ponto->getTimestamp( Ponto::TS_HORARIO ) . '</span>';
	} else {
		$hora = '<span class="nao-deferido">' . $ponto->getTimestamp( Ponto::TS_HORARIO ) . '</span>';
	}
	$tooltip = buildTooltip($ponto);
	$btnDelete = buildTrashBtn($usr, $ponto);
	$hora .= " $btnDelete ";
	if ($ponto->getEvent() != Ponto::PONTO_ABONO ) {
		$hora .= " $tooltip";
	}
	
	$data = $ponto->getTimestamp( Ponto::TS_DATA );
	
	
	if ( $ponto->getEvent() == Ponto::PONTO_ENTRADA ) {
		if ( ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA ) )   {
			echo '<td>' . linkJustificativa($bolsista,Ponto::PONTO_SAIDA,$anterior->getTimestamp()) . '</td><td class="td-right">Impossível calcular</td></tr>';
		}
		echo "<tr><td>$data</td><td>$hora</td>";
	} elseif ( $ponto->getEvent() == Ponto::PONTO_SAIDA ) {
		$pendencia = false;
		if ( !isset( $anterior ) || $anterior->getEvent() == Ponto::PONTO_SAIDA ) {
			echo '<tr><td>'.$data.'</td><td>' . linkJustificativa($bolsista,Ponto::PONTO_ENTRADA,$ponto->getTimestamp()) . '</td>';
			$pendencia = true;
		} elseif ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA && $ponto->getTimestamp( Ponto::TS_DATA ) != $anterior->getTimestamp( Ponto::TS_DATA ) ) {
			echo '<td>' . linkJustificativa($bolsista,Ponto::PONTO_SAIDA,$anterior->getTimestamp()) . '</td><td class="td-right">Impossível calcular</td></tr>';
			echo '<tr><td>'.$data.'</td><td>' . linkJustificativa($bolsista,Ponto::PONTO_ENTRADA,$ponto->getTimestamp()) .'</td>';
			$pendencia = true;
		}
		echo "<td>$hora</td>";
		if ($pendencia) {
			echo '<td class="td-right">Impossível calcular</td>';
		} else {
			
			$diff = Utils::timeDiff($ponto->getTimestamp(Ponto::TS_HORARIO),$anterior->getTimestamp(Ponto::TS_HORARIO));
			$totalTrab += $diff;
			$tempo = Utils::secondsToStrtime($diff);
			echo "<td class='td-right'>$tempo</td>";
		}
		echo '</tr>';
		$anterior = $ponto;
	} elseif ($ponto->getEvent() == Ponto::PONTO_ABONO) {
		if ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA ) {
			echo '<td>' . linkJustificativa( $bolsista,Ponto::PONTO_SAIDA , $anterior->getTimestamp() ) . '</td>';
			echo '<td class="td-right">Impossível calcular</td></tr>';
		}
		echo '<tr><td>' . $data . '</td><td colspan="2">Abono de horas' . $tooltip . '</td>';
		echo '<td class="td-right">' . $hora . '</td></tr>';
		$horaParts = explode( ':' , $hora );
		$totalAbono += $horaParts[ 0 ] * 3600 + $horaParts[ 1 ] * 60 + $horaParts[ 2 ];
	}
	$anterior = $ponto->getEvent() == Ponto::PONTO_ABONO ? $anterior : $ponto;
}
echo '<tr class="bottom"><td colspan="3">Total de horas trabalhadas</td><td class="td-right">'. Utils::secondsToStrtime($totalTrab) . '</td></tr>';
echo '<tr><td colspan="3">Total de horas abonada</td><td class="td-right">'. Utils::secondsToStrtime($totalAbono) . '</td></tr>';
echo '<tr><td colspan="3">Total (Trabalhadas + Abonadas)</td><td class="td-right">'. Utils::secondsToStrtime($totalTrab + $totalAbono) . '</td></tr>';

?>
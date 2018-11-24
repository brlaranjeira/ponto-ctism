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
	if ( isset($just) && !empty($just) ) {
		return "<span class='x' data-toggle=\"tooltip\" title=\"$just\">
					<small>
						<span class=\"fa fa-info-circle\" aria-hidden=\"true\"></span>
					</small>
				</span>";
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
$pendenciaAbonos = false;
$pendenciaTrab = false;
foreach ( $pontos as $ponto ) {
	if ($deferido = $ponto->getDeferido() == 1) {
		$hora = '<span>' . $ponto->getTimestamp( Ponto::TS_HORARIO ) . '</span>';
	} else {
		$hora = '<span class="nao-deferido">' . $ponto->getTimestamp( Ponto::TS_HORARIO ) . '</span>';
	}
	$tooltip = buildTooltip($ponto);
	$withtooltip = empty($tooltip) ? '' : 'td-with-tooltip';
	$btnDelete = buildTrashBtn($usr, $ponto);
	$hora .= " $btnDelete ";
	if ($ponto->getEvent() != Ponto::PONTO_ABONO ) {
		$hora .= " $tooltip";
	}
	
	$data = $ponto->getTimestamp( Ponto::TS_DATA );
	
	
	if ( $ponto->getEvent() == Ponto::PONTO_ENTRADA ) {
		$ponto->getDeferido() == 0 and $pendenciaTrab = true;
		if ( ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA ) )   {
			echo '<td>' . linkJustificativa($bolsista,Ponto::PONTO_SAIDA,$anterior->getTimestamp()) . '</td><td class="td-right">Impossível calcular</td></tr>';
			$pendenciaTrab = true;
		}
		echo "<tr><td class='td-left'>$data</td><td class='$withtooltip'>$hora</td>";
	} elseif ( $ponto->getEvent() == Ponto::PONTO_SAIDA ) {
		$ponto->getDeferido() == 0 and $pendenciaTrab = true;
		$pendencia = false;
		if ( !isset( $anterior ) || $anterior->getEvent() == Ponto::PONTO_SAIDA ) {
			echo '<tr><td>'.$data.'</td><td>' . linkJustificativa($bolsista,Ponto::PONTO_ENTRADA,$ponto->getTimestamp()) . '</td>';
			$pendencia = true;
		} elseif ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA && $ponto->getTimestamp( Ponto::TS_DATA ) != $anterior->getTimestamp( Ponto::TS_DATA ) ) {
			echo '<td>' . linkJustificativa($bolsista,Ponto::PONTO_SAIDA,$anterior->getTimestamp()) . '</td><td class="td-right">Impossível calcular</td></tr>';
			echo '<tr><td>'.$data.'</td><td>' . linkJustificativa($bolsista,Ponto::PONTO_ENTRADA,$ponto->getTimestamp()) .'</td>';
			$pendencia = true;
			$pendenciaTrab = true;
		}
		echo "<td class='$withtooltip'>$hora</td>";
		
		if ($pendencia) {
			echo '<td class="td-right">Impossível calcular</td>';
			$pendenciaTrab = true;
		} else {
			
			$diff = Utils::timeDiff($ponto->getTimestamp(Ponto::TS_HORARIO),$anterior->getTimestamp(Ponto::TS_HORARIO));
			$totalTrab += $diff;
			$tempo = Utils::secondsToStrtime($diff);
			$naoDef = ($ponto->getDeferido() == 0 || $anterior->getDeferido() == 0) ? 'nao-deferido' : '';
			echo "<td class='$naoDef td-right'>$tempo</td>";
		}
		echo '</tr>';
		$anterior = $ponto;
	} elseif ($ponto->getEvent() == Ponto::PONTO_ABONO) {
		if ( isset( $anterior ) && $anterior->getEvent() == Ponto::PONTO_ENTRADA ) {
			echo '<td>' . linkJustificativa( $bolsista,Ponto::PONTO_SAIDA , $anterior->getTimestamp() ) . '</td>';
			echo '<td class="td-right">Impossível calcular</td></tr>';
			$pendenciaTrab = true;
		}
		$ponto->getDeferido() == 0 and $pendenciaAbonos = true;
		echo '<tr><td>' . $data . '</td><td colspan="2">Abono de horas' . $tooltip . '</td>';
		echo '<td class="td-right">' . $hora . '</td></tr>';
		$horaParts = explode( ':' , $ponto->getTimestamp(Ponto::TS_HORARIO) );
		$totalAbono += $horaParts[ 0 ] * 3600 + $horaParts[ 1 ] * 60 + $horaParts[ 2 ];
	}
	$anterior = $ponto->getEvent() == Ponto::PONTO_ABONO ? $anterior : $ponto;
}

$clsSpanHoras = $pendenciaTrab ? 'class="nao-deferido"' : '';
$clsSpanAbono = $pendenciaAbonos ? 'class="nao-deferido"' : '';
$clsSpanTotal = ( $pendenciaTrab || $pendenciaAbonos ) ? 'class="nao-deferido"' : '';

echo '<tr class="bottom"><td class="td-left" colspan="3">Total de horas trabalhadas</td><td class="td-right"><span ' . $clsSpanHoras . '>' . Utils::secondsToStrtime($totalTrab) . '</span></td></tr>';
echo '<tr><td class="td-left" colspan="3">Total de horas abonada</td><td class="td-right"><span ' . $clsSpanAbono . '>'. Utils::secondsToStrtime($totalAbono) . '</span></td></tr>';
echo '<tr><td class="td-left" colspan="3">Total (Trabalhadas + Abonadas)</td><td class="td-right"><span ' . $clsSpanTotal . '>'. Utils::secondsToStrtime($totalTrab + $totalAbono) . '</span></td></tr>';

?>
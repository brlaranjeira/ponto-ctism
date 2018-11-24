<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 11/14/17
 * Time: 2:04 PM
 */

class Utils {
	
	public static function timeDiff( $t1 , $t2 ) {
		$partsFim = explode(':' , $t1);
		$partsIni = explode(':' , $t2);
		return (intval($partsFim[0]) - intval($partsIni[0])) * 3600 +
			(intval($partsFim[1]) - intval($partsIni[1])) * 60 +
			intval($partsFim[2]) - intval($partsIni[2]);
	}
	
	public static function secondsToStrtime( $s ) {
		$diffHoras = floor($s / 3600);
		$s -= $diffHoras * 3600;
		$diffMinutos = floor($s / 60);
		$s -= $diffMinutos * 60;
		$tempo = str_pad($diffHoras,2,0,STR_PAD_LEFT) .
			':' . str_pad($diffMinutos,2,0,STR_PAD_LEFT) .
			':' . str_pad($s,2,0,STR_PAD_LEFT);
		return $tempo;
	}
	
}
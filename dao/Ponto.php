<?php
/**
 * Created by PhpStorm.
 * User: brlaranjeira
 * Date: 10/7/17
 * Time: 10:50 AM
 */

require_once (__DIR__ . '/EntidadeAbstrata.php');
class Ponto extends EntidadeAbstrata {
	
	/**
	 * @var string
	 */
	private $ip;
	/**
	 *@var Usuario
	 */
	private $usuario;
	/**
	 * @var string
	 */
	private $event;
	/**
	 * @var string
	 */
	private $timestamp;
	/**
	 * @var string
	 */
	private $just;
	/**
	 * @var boolean
	 */
	private $deferido;
	
	/**
	 * @var Usuario
	 */
	private $usuarioDeferidor;
	
	const TS_DATA = 0;
	const TS_HORARIO = 1;
	
	const PONTO_ENTRADA = 'Entrada';
	const PONTO_SAIDA = 'Saida';
	const PONTO_ABONO = 'Abono';
	
	/**
	 * @var Usuario
	 */
	//private $usuario;
	
	protected static $tbName = 'ponto';
	protected static $dicionario = [
		'ip' => 'ip',
		'usuario' => 'uid',
		'event' => 'evt',
		'timestamp' => 'dthr',
		'just' => 'just',
		'deferido' => 'deferido',
		'usuarioDeferidor' => 'uid_deferidor'
	];
	protected static $getters = [
		'usuario' => 'getUsrUidNumber',
		'usuarioDeferidor' => 'getUsrDeferidorUidNumber'
	];
	
	protected static $idName = 'ID';
	
	/**
	 * @return mixed
	 */
	public function getIp() {
		return $this->ip;
	}
	
	/**
	 * @param mixed $ip
	 */
	public function setIp( $ip=null ) {
		$this->ip = isset($ip) ? $ip : $_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * @return mixed
	 */
	public function getUsuario() {
		return $this->usuario;
	}
	
	/**
	 * @param mixed $usuario
	 */
	public function setUsuario( $usuario ) {
		require_once (__DIR__ . '/Usuario.php');
		$this->usuario = is_object( $usuario ) ? $usuario : Usuario::getByUidNumber($usuario);
	}
	
	public function getUsrUidNumber() {
		require_once (__DIR__ . '/Usuario.php');
		return $this->usuario->getUidNumber();
	}
	
	/**
	 * @return mixed
	 */
	public function getEvent() {
		return $this->event;
	}
	
	/**
	 * @param mixed $event
	 */
	public function setEvent( $event ) {
		$this->event = $event;
	}
	
	/**
	 * @return mixed
	 */
	public function getTimestamp( $part=null ) {
		if (!isset($part)) {
			return $this->timestamp;
		}
		if ($part == self::TS_DATA) {
			$parts = explode('-',explode(' ',$this->timestamp)[0]);
			$dt = $parts[2] . '/' . $parts[1] . '/' . $parts[0];
			return $dt;
		}
		$h = explode(' ',$this->timestamp)[1];
		return $h;
	}
	
	/**
	 * @param mixed $timestamp
	 */
	public function setTimestamp( $timestamp ) {
		$this->timestamp = $timestamp;
	}
	
	/**
	 * @return mixed
	 */
	public function getJust() {
		return $this->just;
	}
	
	/**
	 * @param mixed $just
	 */
	public function setJust( $just ) {
		$this->just = $just;
	}
	
	/**
	 * @return bool
	 */
	public function getDeferido() {
		return $this->deferido;
	}
	
	/**
	 * @param void $deferido
	 */
	public function setDeferido( $deferido ) {
		$this->deferido = $deferido;
	}
	
	/**
	 * @return Usuario
	 */
	public function getUsuarioDeferidor() {
		return $this->usuarioDeferidor;
	}
	
	/**
	 * @param Usuario $usuarioDeferidor
	 */
	public function setUsuarioDeferidor( $usuarioDeferidor ) {
		if (isset($usuarioDeferidor)) {
			require_once (__DIR__ . '/Usuario.php');
			$this->usuarioDeferidor = is_object( $usuarioDeferidor ) ? $usuarioDeferidor : Usuario::getByUidNumber($usuarioDeferidor);
		}
	}
	
	public function getUsrDeferidorUidNumber() {
		require_once (__DIR__ . '/Usuario.php');
		if (isset($this->usuarioDeferidor)) {
			if (!is_object($this->usuarioDeferidor)) {
				$this->setUsuarioDeferidor($this->usuarioDeferidor);
			}
			return $this->usuarioDeferidor->getUidNumber();
		}
		return null;
	}
	
	
	
	
	
	
}
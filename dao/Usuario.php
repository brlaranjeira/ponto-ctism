<?php

/**
 * Created by PhpStorm.
 * User: SSI-Bruno
 * Date: 25/04/2016
 * Time: 11:04
 */
class Usuario implements Serializable {
	
	
	const GRUPO_PROFESSORES = '10001';
	const GRUPO_FUNCIONARIOS = '10002';
	const GRUPO_BOLSISTAS = '10003';
	
	
	/**
	 * @var string id do usuario
	 */
	private $uid;
	/**
	 * @var string uidNumber do usuario
	 */
	private $uidNumber;
	/**
	 * @var array grupos a que o usuario pertence
	 */
	private $grupos;
	/**
	 * @var string nome completo
	 */
	private $fullName;

    /**
     * @var string email
     */
	private $email;

	
	/**
	 * Usuario constructor.
	 * @param string $uid
	 * @param bool $loadGrupos
	 * @param string $fullName
	 * @param string $uidNumber
	 */
	public function __construct($uid, $loadGrupos = false, $fullName = null, $uidNumber = null, $email=null ) {
		$this->uid = $uid;
		$this->fullName = $fullName;
        $this->uidNumber = $uidNumber;
        $this->email = $email;
        $attrs = array();
        if (!isset($fullName)) {
            $attrs[] = 'sn';
            $attrs[] = 'givenname';
        }
        if (!isset($uidNumber)) {
            $attrs[] = 'uidnumber';
        }
        if (!isset($email)) {
            $attrs[] = 'mail';
        }
        if (!empty($attrs)) {
            require_once(__DIR__."/../lib/LDAP/ldap.php");
            $ldap = new ldap();
            $parts = $ldap->getXbyY($attrs,'uid',$this->uid);
            if (!isset($fullName)) {
                $this->fullName = $parts['givenname'] . ' ' . $parts['sn'];
            }
            if (!isset($uidNumber)) {
                $this->uidNumber = $parts['uidnumber'];
            }
            if (!isset($email)) {
                $this->email= $parts['mail'];
            }
        }
		$loadGrupos and $this->loadGrupos();
	}
	
	public static function getByUidNumber ( $uidNumber ) {
		require_once(__DIR__."/../lib/LDAP/ldap.php");
		$ldap = new ldap();
		$parts = $ldap->getXbyY(['uid','sn','givenname','mail'],'uidnumber',$uidNumber);
		return new Usuario($parts['uid'],true,$parts['givenname'] . ' ' . $parts['sn'], $uidNumber, $parts['mail']);
	}
	
	
	
	/**
	 *
	 */
	public function loadGrupos() {
		$this->grupos = array();
		require_once(__DIR__."/../lib/LDAP/ldap.php");
		$ldap = new ldap();
		$allGroups = $ldap->getXbyY('gidNumber', 'cn', '*', LDAP_GROUPS_BASE);
		foreach ($allGroups as $gid) {
			if ($ldap->isMembroDoGrupo($this->uid, $gid)) {
				$this->grupos[] = $gid;
			}
		}
	}

	/**
	 *
	 */
	public function loadUidNumber() {
		require_once(__DIR__."/../lib/LDAP/ldap.php");
		$ldap = new ldap();
		$this->uidNumber = $ldap->getXbyY('uidnumber','uid',$this->uid);
	}
	
	/**
	 *
	 */
	public function loadFullName() {
		require_once(__DIR__."/../lib/LDAP/ldap.php");
		$ldap = new ldap();
		//$this->fullName = $ldap->getXbyY('cn','uid',$this->uid);
		$parts = $ldap->getXbyY(array ('givenname','sn'),'uid',$this->uid);
		$this->fullName = $parts['givenname'] . ' ' . $parts['sn'];
	}

    /**
     *
     */
	public function loadEmail () {
        require_once(__DIR__."/../lib/LDAP/ldap.php");
        $ldap = new ldap();
	    $this->email = $ldap->getXbyY('mail','uid',$this->uid);
    }
	
	/**
	 * @param $gid
	 *
	 * @return Usuario[]
	 */
	public static function getAllFromGroup($gid) {
		require_once(__DIR__."/../lib/LDAP/ldap.php");
		$ldap = new ldap();
		$ret = array();
		$gid = is_array($gid) ? $gid : array($gid);
		foreach ($gid as $g) {
			$curr = array_map(function($usr) {
				return new Usuario($usr,false);
			}, array_merge($ldap->getXbyY('uid','gidnumber',$g), $ldap->getXbyY('memberuid','gidnumber',$g,LDAP_GROUPS_BASE)));
			$ret = array_merge($ret,$curr);
		}
		usort($ret, function($usrx,$usry) {
			return strcmp(strtolower($usrx->fullName),strtolower($usry->fullName));
		});
		$last = '';
		$remove = array();
		for ($i = 0; $i < sizeof($ret); $i++) {
			$uidNumber = $ret[$i]->uidNumber;
			if ( $uidNumber == $last ) {
				$remove[] = $i;
			}
			$last = $uidNumber;
		}
		foreach ($remove as $rm) {
			unset($ret[$rm]);
		}
		
		return $ret;
	}
	
	/**
	 * @param $gId
	 * @return bool
	 */
	public function hasGroup($gId,$and=false) {
		if (!isset($this->grupos)) {
			$this->loadGrupos();
		}
		if (is_array($gId)) {
			foreach ($gId as $grupo) {
				$achou = in_array($grupo,$this->grupos);
				if ($and && !$achou) {
					return false;
				} elseif (!$and && $achou) {
					return true;
				}
			}
			return $and;
		}
		return in_array($gId,$this->grupos);
	}
	
	/**
	 * @return mixed
	 */
	public function getUid() {
		return $this->uid;
	}
	
	/**
	 * @return null
	 */
	public function getUidNumber() {
		return $this->uidNumber;
	}
	
	/**
	 * @return mixed
	 */
	public function getGrupos() {
		!isset($this->grupos) and $this->loadGrupos();
		return $this->grupos;
	}
	
	/**
	 * @return null
	 */
	public function getFullName() {
		return $this->fullName;
	}

    /**
     * @return string email
     */
	public function getEmail () {
	   return $this->email;
    }
	
	public function serialize() {
		$delim = ' ||| ';
		$str =  'uid:=' . serialize($this->uid) . $delim;
		$str .= 'uidNumber:=' . serialize($this->uidNumber) . $delim;
		$str .= 'grupos:=' . serialize($this->grupos) . $delim;
		$str .= 'fullName:=' . serialize($this->fullName);
		return $str;
	}
	
	public function unserialize($serialized) {
		$serialized = strstr($serialized,'{');
		$serialized = substr($serialized,1,strlen($serialized)-2);
		$delim = ' ||| ';
		$partes = explode($delim,$serialized);
		foreach ($partes as $parte) {
			list($k,$v) = explode(':=',$parte);
			$$k = unserialize($v);
		}
		$usr = new Usuario($uid,false,$fullName,$uidNumber);
		$usr->grupos = $grupos;
		return $usr;
	}
	
	function __toString() {
		$ret = '{';
		$ret .= '"uid":"' . $this->uid . '",';
		$ret .= '"uidNumber":"' . $this->uidNumber . '",';
		$ret .= '"grupos":[';
		$primeiro = true;
		foreach ( $this->getGrupos() as $grupo ) {
			$ret .= !$primeiro ? ',' : '';
			$primeiro = false;
			$ret .= '"' . $grupo . '"';
		}
		$ret .= '],';
		$ret .= '"fullName":"' . $this->fullName . '"';
		$ret .= '}';
		return $ret;
	}
	
	public static function auth($usr,$pw) {
		require_once (__DIR__ . '/../lib/LDAP/ldap.php');
		$ldap = new ldap();
		if ($ldap->auth($usr,$pw)) {
			return new Usuario($usr);
		}
		return null;
		
	}
	
	public function saveToSession() {
		session_start();
		$_SESSION['ctism_user'] = serialize($this);
	}

	public static function destroySession() {
        session_start();
        session_destroy();
        session_commit();
    }

	public static function restoreFromSession() {
		session_start();
		
		return isset($_SESSION['ctism_user'])
			? Usuario::unserialize($_SESSION['ctism_user'])
			: null;
	}

	
}
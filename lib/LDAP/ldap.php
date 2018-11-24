<?php

require_once dirname(__FILE__) . "/config/config.php";

class ldap {

    private $connection = NULL;

    public function __construct($host = NULL, $port = NULL) {
        $host = ($host == NULL) ? LDAP_SERVER : $host;
        $port = ($port == NULL) ? LDAP_PORT : $port;
        $conn = ldap_connect($host, $port);
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, LDAP_VERSION);
        if ($conn) {
            $this->connection = $conn;
        } else {
            throw new Exception('Server Error', 500);
        }
    }

    public function __destruct() {
        $c = $this->connection;
        if ($c != NULL) {
            ldap_close($c);
        }
    }

    public function bind($user, $pass) {
        $c = $this->connection;
        if ($c == NULL) {
            return FALSE;
        }
        return @ldap_bind($c, $user, $pass);
    }

    public function auth($user, $pass) {
        if (!$this->bind(LDAP_AUTH_LOGIN, LDAP_AUTH_PASSWD)) {
            return FALSE;
        }
        $cn = $this->getXbyY('cn', 'uid', $user);
        return $this->bind("cn=$cn," . LDAP_PEOPLE_BASE, $pass);
    }

    public function getConnection() {
        return $this->connection;
    }

    public function getArrayPassword($usuario, $newpass){
        require_once dirname(__FILE__) . "/../Crypt/md5.php";
        require_once dirname(__FILE__) . "/../Crypt/password.php";
        $new_data['userPassword']=posixPassword($newpass, "SSHA");
        $new_data['sambaLMPassword']=lmPassword($newpass);
        $new_data['sambaNTPassword']=ntPassword($newpass);
        $md5 = new md5();
        $carlicense = $md5->getenc($newpass);
        $new_data['carLicense']=$carlicense;
        $new_data['sambaPwdLastSet']= 1284467565;
        return $new_data;
    }
    
    public function changeUserPassword($usuario, $newpass) {
        $cn = $this->getXbyY("cn", "uid", $usuario);
        if(!$cn) {
            return false;
        }
        $new_data = $this->getArrayPassword($usuario, $newpass);
        if(ldap_mod_replace($this->connection, "cn=".$cn.','.LDAP_PEOPLE_BASE, $new_data)){
            $resultado = true;
        } else {
            $resultado = false;
        }
        return $resultado;
    }

    public function getXbyY($x, $y, $value, $dn = LDAP_PEOPLE_BASE) {
        $this->bind(LDAP_AUTH_LOGIN, LDAP_AUTH_PASSWD);
        if ($this->connection == NULL) {
            return FALSE;
        }
        $filter = "(|($y=$value))";
        $fields = is_array($x) ? $x : array($x);
        $result = ldap_search($this->connection, $dn, $filter, $fields);
        //ldap_sort($this->connection, $result, "cn");
        $count = ldap_count_entries($this->connection, $result);
        $entry = ldap_get_entries($this->connection, $result);
        $array = array();
        if ($value == '*' || $y == "gidnumber") {
            $entry = ldap_first_entry($this->connection, $result);
            while ($entry) {
                $attrs = ldap_get_attributes($this->connection, $entry);

                for ($i = 0; $i < $attrs["count"]; $i++) {
                    $attr_name = $attrs[$i];
                    for ($j = 0; $j < $attrs[$attr_name]["count"]; $j++) {
                        array_push($array, $attrs["$attr_name"][$j]);
                    }
                }
                $entry = ldap_next_entry($this->connection, $entry);
            }
        } else {
        	#if (sizeof($fields) == 1) {
        	if (!is_array($x) == 1) {
        		//$x = is_array($x) ? $x[0] : $x;
		        if (isset($entry[0]["$x"][1])) {
			        $res_array = $entry[0]["$x"];
			        unset($res_array["count"]);
			        @$array = $res_array;
		        } else {
			        @$array = $entry[0]["$x"][0];
		        }
	        } else {
		        for ( $i = 0; $i < $entry[ 'count' ]; $i++ ) {
		            $current = array();
			        for ($j = 0; $j < sizeof($fields); $j++) {
			        	$fieldName = $fields[$j];
				        $current[$fieldName] = $entry[$i][$fieldName][0];
			        }
			        if ($entry['count'] == 1) {
			        	return $current;
			        }
			        $array[] = $current;
	            }
	        }
        	
        }
	        return $array;
    }

    public function isMembroDoGrupo($usuario, $grupo) {
        $this->bind(LDAP_AUTH_LOGIN, LDAP_AUTH_PASSWD);
        if ($this->connection == NULL) {
            return false;
        }
        if(is_numeric($usuario)){
            $usuario = $this->getXbyY('uid', 'uidnumber', $usuario);
        }
        if (is_numeric($grupo)) {
            $by = 'gidnumber';
            if($grupo == $this->getXbyY($by, 'uid', $usuario)){
                return true;
            }
        } else {
            $gidNumber = $this->getXbyY('gidnumber', 'cn', $grupo, LDAP_GROUPS_BASE);
            if((is_array($gidNumber) && in_array($this->getXbyY('gidnumber', 'uid', $usuario),$gidNumber)) || ($gidNumber == $this->getXbyY('gidnumber', 'uid', $usuario))){
                return true;
            }
            $by = "cn";
        }
        return (@in_array($usuario, $this->getXbyY('memberuid', $by, $grupo, LDAP_GROUPS_BASE)) ? true : false);
    }

    public function isMembroAlgumGrupo($usuario, $grupos) {
        foreach ($grupos as $grupo) {
            if ($this->isMembroDoGrupo($usuario, $grupo) === true) {
                return true;
            }
        }
        return false;
    }

    public function isMembroTodosGrupos($usuario, $grupos) {
        $n = 0;
        foreach ($grupos as $grupo) {
            if ($this->isMembroDoGrupo($usuario, $grupo) === true) {
                $n++;
            }
        }
        return n == count($grupos);
    }
    
    public function getNextUidNumber() {
        $this->bind(LDAP_AUTH_LOGIN, LDAP_AUTH_PASSWD);
        if ($this->connection == NULL) {
            return false;
        }
        $filter="(|(uidNumber=*))";
        $showfields = array("uidNumber");
        $search_result = ldap_search($this->connection, LDAP_PEOPLE_BASE, $filter, $showfields);

        ldap_sort($this->connection, $search_result, "uidnumber");
        $entries = ldap_get_entries($this->connection, $search_result);
        
        @$last_entrie = end($entries);
        @$uidNumber = $last_entrie["uidnumber"][0];
        
        return $uidNumber + 1;
    }
    
    function addUser($nome, $sobrenome, $data_nascimento, $matricula, $vinculo, $curso, $telefone, $email, $login, $userPassword, $sambaNTPassword, $sambaLMPassword, $carLicense, $sambaPwdLastSet) {
        $this->bind(LDAP_AUTH_LOGIN, LDAP_AUTH_PASSWD);
        if ($this->connection == NULL) {
            return false;
        }

        //PREPARA DADOS PARA INSER��O
	//dados do individuo
	$cn = $nome. " " . $sobrenome;
	$dn = "cn=".$cn.",".LDAP_PEOPLE_BASE;

        $attr["givenName"] = "$nome";
	$attr["cn"] = "$cn";
	$attr["sn"] = "$sobrenome";
	$attr["uid"] = $login;

	//configuracao do ambinete.....
	$attr["sambaPwdLastSet"] = $sambaPwdLastSet;
	$attr["loginShell"] = "/bin/bash";
	$attr["homeDirectory"] = "/storage/home/$login";

	$attr["sambaPrimaryGroupSID"] = LDAP_SAMBA_GROUP_SID;
	if ($vinculo == 'Professor') {
            $attr["gidNumber"] = 10001;
	} elseif ($vinculo == 'Funcion�rio') {
            $attr["gidNumber"] = 10002;
	} elseif ($vinculo == 'Aluno') {
            $attr["gidNumber"] = 10000;
	}else {
            $attr["gidNumber"] = 10003; //Bolsistas 
	}

	/* INFORMACOES ADICIONAIS UNUSED */
	//$attr["dataNascimento"] = $data_nascimento; 

	if (!$curso) {
            $curso = "-";
	}

        $attr["departmentNumber"] = $curso;
	$attr["mail"] = $email;
	$attr["employeeNumber"]= $matricula;
	$attr["mobile"]= $telefone;
	
	//senhas... usar funcoes hash pra isso...
	$attr["sambaNTPassword"] = $sambaNTPassword;
	$attr["sambaLMPassword"] = $sambaLMPassword;
	$attr["userPassword"] = $userPassword;
	$attr["carLicense"] = $carLicense; // Senha usada pelo radius
		
	$attr['sambaPwdLastSet']= $sambaPwdLastSet;

	//classes do objeto inserido
	$attr["objectclass"][0] = "inetOrgPerson";
	$attr["objectclass"][1] = "sambaSamAccount";
	$attr["objectclass"][2] = "posixAccount";

	$attr["sambaSID"] = LDAP_SAMBA_SID;
	
	$attr["uidNumber"]=$this->getNextUidNumber();

        $result = ldap_add($this->connection, $dn, $attr);
	return $result;
    }    
}
?>
<?php

/**
 * Created by PhpStorm.
 * User: Camila
 * Date: 05/12/2016
 * Time: 16:51
 */
abstract class EntidadeAbstrata {

    const BLACK_LIST = 0;
    const WHITE_LIST = 1;
    /**
     * @var array mapeando atributos para colunas (necessariamente nesta ordem)
     */
    protected static $dicionario = array();

    /**
     * @var array elementos necessarios em cada elemento:
     *   tbRelName (nome da tabela intermediaria),
     *   tbRelCurrentId (nome da coluna que identifica esta tabela na tabela intermediaria),
     *   tbRelOtherId (nome da coluna que identifica a outra tabela na tabela intermediaria),
     *   tbRelDicionario (mapa das colunas da tabela intermediaria para os atributos da outra classe),
     *   clEntityName (nome da classe que representa a outra tabela)
     */
    protected static $manyToMany = array();

    /**
     * @var array elementos necessarios em cada elemento:
    */
    protected static $hasMany = array();
    /**
     * @var array elementos necessarios em cada elemento
     *   clEntityName ( nome da classe da outra entidade ),
     *   tbForeignKey ( id que representa a outra tabela nesta tabela ( foreign key )
     */
    protected static $hasOne = array();
    /**
     * @var array nomes dos metodos getters para os objetos
     */
    protected static $getters = array();

    /**
     * @var array nomes dos metodos setters para os objetos
     */
    protected static $setters = array();
    /**
     * @var string nome da coluna do id da tabela
     */
    protected static $idName;
    /**
     * @var integer valor do id do elemento
     */
    protected $id;
    /**
     * @var string nome da tabela correspondente
     */
    protected static $tbName;



    public function save( $conexao=null , $doCommit=true , $attrList=array() , $listType=self::BLACK_LIST ) {
        /**
         * inicializa
         */
        require_once (__DIR__ . "/../lib/ConexaoBD.php");
        $clazz = get_called_class();
        if ($listType==self::BLACK_LIST) {
            $subDicionario = $clazz::$dicionario;
            foreach ($attrList as $attr ) {
                unset($subDicionario[$attr]);
            }
            foreach ($clazz::$hasOne as $k => $v) {
                if (!array_key_exists($k,$attrList)) {
                    $subDicionario[$k] = $v['tbForeignKey'];
                }
            }
        } else {
            foreach ($attrList as $attr) {
                $subDicionario[$attr] = $clazz::$dicionario[$attr];
            }
            foreach ($clazz::$hasOne as $k => $v) {
                if (array_key_exists($k, $attrList)) {
                    $subDicionario[$k] = $v['tbForeignKey'];
                }
            }
        }
        $conexao = isset($conexao) ? $conexao : ConexaoBD::getConexao();

        /**
         * has one
         */
        foreach ( $clazz::$hasOne as $k => $v ) {
            $getter = self::getGetter($k);
            $setter = self::getSetter($k);
            $obj = $this->$getter();
            if ($obj != null && !$obj->save($conexao,false)) {
                $conexao->rollBack();
            }
            $this->$setter($obj);
        }

        /**
         * tabela em si
         */

        /**
         * primeiro monta o sql
         */
        if (!isset($this->id)) { //vai inserir, pois nao tem id ainda
            $sql = 'INSERT INTO ' . $clazz::$tbName . ' (' . implode(',',array_values($subDicionario)) . ') VALUES ( ';
            for ($i=0;$i<sizeof($subDicionario);$i++) {
                $sql .= $i == 0 ? ' ? ' : ', ? ';
            }
            $sql .= ' )';
        } else { //vai atualizar, pois ja tem id
            $sql = 'UPDATE ' . $clazz::$tbName . ' SET ';
            $colunas = array_values($subDicionario);
            for ($i=0; $i < sizeof($subDicionario); $i++) {
                $sql .= $i == 0 ? ' ' : ' , ';
                $sql .= $colunas[$i] . ' = ? ';
            }
            $sql .= 'WHERE ';
            $sql .= isset($clazz::$idName) ? $clazz::$idName : 'id';
            $sql .= ' = ? ';
        }

        /**
         * agora preenche os valores
         */
        $osvalores = array();
        foreach ($subDicionario as $attr => $col) {
            #$metodo = 'get' . strtoupper($attr[0]) . substr($attr,1);
            #$valor = $this->$metodo();
            $getter = self::getGetter($attr);
            $valor = $this->$getter();
            if (is_object($valor)) {
                $valor = $valor->getId();
            }
            $osvalores[] = $valor;
        }
        if (isset($this->id)) {
            $osvalores[] = $this->id;
        }
        if (!$conexao->inTransaction()) {
            $conexao->beginTransaction();
        }
        $statement = $conexao->prepare($sql);
        $execOk = $statement->execute($osvalores);
        $idInserido = isset($this->id) ? $this->id : $conexao->lastInsertId();
        if (!$execOk) {
            $conexao->rollBack();
            return null;
        }

        /**
         * many to many
         */
        foreach ($clazz::$manyToMany as $k => $v) {
            $getter = self::getGetter( $k );
            $arr = $this->$getter();
            $ids = array(); //os que nao estiverem aqui serao deletados
            foreach ($arr as $elm) {
                if (!$elm->save($conexao, false)) {
                    $conexao->rollBack();
                    return null;
                }
                $ids[] = $elm->getId();
                //update ou insert da tabela intermediaria
                $colunas = implode(',',array_keys($v['tbRelDicionario']));
                /**
                 * recupera o que já tem na tabela intermediaria, para verificar o que tem de diferente do objeto
                 */
                $sql = 'SELECT ' . $colunas . ' FROM produto_tem_propriedade WHERE id_produto = ? and id_propriedade = ?';
                $queryConn = ConexaoBD::getConexao();
                $stmt = $queryConn->prepare($sql);
                $stmt->execute(array($this->id,$elm->getId()));
                $relValues = $stmt->fetchObject();
                if ($relValues) { //atualiza os dados, caso estejam diferente
                    $colunasDiferentes = '';
                    $valoresNovos = array();
                    foreach ( $v['tbRelDicionario'] as $coluna => $atributo ) {
                        $getter = $v['clEntityName']::getGetter($atributo);
                        $attrValue = $elm->$getter();
                        $bdValue = $relValues->$coluna;
                        if ($attrValue != $bdValue) {
                            $colunasDiferentes .= strlen($colunasDiferentes) > 0 ? ' , ' : ' ';
                            $colunasDiferentes .= $coluna . ' = ? ';
                            $valoresNovos[] = $attrValue;
                        }
                    }
                    if ( sizeof($valoresNovos) > 0 ) {
                        $sql = 'UPDATE ' . $v['tbRelName'] . ' SET ' . $colunasDiferentes . ' WHERE ' . $v['tbRelCurrentId'] . ' = ? AND ' . $v['tbRelOtherId'] . ' = ?';
                        $valoresNovos[] = $this->id;
                        $valoresNovos[] = $elm->getId();
                        $stmt = $conexao->prepare($sql);
                        $inseriu = $stmt->execute($valoresNovos);
                        if (!$inseriu) {
                            $conexao->rollBack();
                            return null;
                        }
                    }
                } else {//insere, caso nao exista a tabela intermediaria
                    $map = $v['tbRelDicionario'];
                    $sql = 'INSERT INTO ' . $v['tbRelName'] . ' (';
                        $primeiro = true;
                    $osvalores = array();
                    foreach ($map as $coluna => $atributo) {
                        $sql .= $primeiro ? $coluna : ' , ' . $coluna;
                        $getter = $v['clEntityName']::getGetter( $atributo );
                        $osvalores[] = $elm->$getter();
                    }
                    $sql .= ' , ' . $v['tbRelCurrentId'] . ' , ' . $v['tbRelOtherId'] . ') VALUES ( ? ';
                    $sql .= str_repeat(' , ? ', sizeof($map) + 1) . ' )';
                    $osvalores[] = $this->id;
                    $osvalores[] = $elm->getId();

                    $stmt = $conexao->prepare($sql);
                    $execOk = $stmt->execute($osvalores);
                }
            }
            if ( sizeof($ids) > 0 ) {
                $sql = 'DELETE FROM ' . $v['tbRelName'] . ' WHERE ' . $v['tbRelCurrentId'] . ' = ? AND ' . $v['tbRelOtherId'] . ' NOT IN ( ?';
                $sql .= str_repeat(' , ? ' , sizeof($ids)-1) . ' )';
                $stmt = $conexao->prepare($sql);
                array_unshift($ids,$this->id);
                $stmt->execute($ids);
            }
        }


        /**
         * has many
         */
        foreach ( $clazz::$hasMany as $k => $v ) {
            $getter = self::getGetter( $k );
            $objects = $this->$getter();
            $ids = array();
            foreach ( $objects as $obj ) {
                $execOk = $obj->save();
                if (!$execOk) {
                    $conexao->rollBack();
                    return null;
                }
                $ids[] = $obj->getId();
            }
            if ( sizeof($ids) > 0) {
                $sql = 'DELETE FROM ' . $v['clEntityName']::$tbName . ' WHERE ' . $v['clEntityName']::$dicionario[$v['clCurrentId']] . ' = ? AND ' . $v['clEntityName']::getIdColumn() . ' NOT IN ( ?';
                $sql .= str_repeat(' , ? ' , sizeof($ids)-1) . ' )';
                array_unshift($ids, $this->id);
                $stmt = $conexao->prepare($sql);
                $execOk = $stmt->execute($ids);
                if ( !$execOk ) {
                    $conexao->rollBack();
                    return null;
                }
            }
        }

        /**
         * commit
         */
        if ($doCommit && !$conexao->commit() ) {
            $conexao->rollBack();
            return null;
        }
        $ret = isset($this->id) ? true : $idInserido;
        $this->id = $idInserido;
        return $ret;
    }
    
    public function delete() {
    	$clazz = get_called_class();
	    $cname = (isset($clazz::$idName)) ? $clazz::$idName : 'id';
    	$sql = "DELETE FROM " . $clazz::$tbName . ' WHERE ' . $cname . ' = ?';
    	$conn = ConexaoBD::getConexao();
    	$conn->beginTransaction();
    	$stmt = $conn->prepare($sql);
    	$execOk = $stmt->execute(array ($this->id));
    	if ( $execOk ) {
    		$conn->commit();
	    } else {
    		$conn->rollBack();
	    }
    	return $execOk;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return EntidadeAbstrata[]
     */
    public static function getAll() {
        require_once (__DIR__ . "/../lib/ConexaoBD.php");
        $clazz = get_called_class();
        $sql = 'SELECT * FROM ' . $clazz::$tbName;
        $statement = ConexaoBD::getConexao()->prepare($sql);
        $statement->execute();
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = self::rowToObject( $row, $clazz );
        }
        return $objects;
    }

    public static function getById( $id ) {
        $ret = self::getByAttr('id' , $id );
        return $ret[0];
    }
	
	
	/**
	 * @param $attrs
	 * @param $values
	 * @param string $operators
	 * @param string $orderBy
	 * @param string $orderType
	 * @param int $limit
	 * @return Ponto[]
	 */
    public static function getByAttr($attrs , $values, $operators = '=', $orderBy = '', $orderType='ASC', $limit=null ) {
        require_once (__DIR__ . "/../lib/ConexaoBD.php");
        $operators = is_array($operators) ? $operators : array($operators);
        $clazz = get_called_class();
        $sql = 'SELECT * FROM ' . $clazz::$tbName;
        $attrs = is_array($attrs) ? $attrs : array($attrs);
        $values = is_array($values) ? $values : array($values);
        
        
        $len = min(sizeof($attrs),sizeof($values));
        for ( $i = 0; $i < $len; $i ++ ) {
	        $colName = ($attrs[$i] != 'id') ? ($clazz::$dicionario[$attrs[$i]]) : ( (isset($clazz::$idName)) ? $clazz::$idName : 'id');
	        $op = isset($operators[$i]) ? $operators[$i] : '=';
	        $op = (isset($operators) && isset($operators[$i])) ? $operators[$i] : '=';
	        $sql .= $i != 0 ? ' AND ' : ' WHERE ';
	        $sql .= $colName . ' ' . $op . ' ? ';
        }
        
	    if (!empty($orderType) && !empty($orderBy)) {
		    $orderBy = is_array($orderBy) ? $orderBy : array ($orderBy);
		    $orderType = is_array($orderType) ? $orderType : array ($orderType);
	        for ($i=0; $i< sizeof($orderBy); $i++ ) {
		        $sql .= ($i == 0) ? ' ORDER BY ' : ' , ';
		        $colName = $clazz::getColumnName($orderBy[$i]);
		        $sql .= (isset($colName)) ? $colName : $orderBy[$i];
		        $sql .= (isset($orderType[$i])) ? ' ' . $orderType[$i] . ' ' : '';
	        }
        }
        
        if (isset($limit)) {
        	$sql .= " LIMIT $limit";
        }

        $statement = ConexaoBD::getConexao()->prepare($sql);
        $statement->execute(array_slice($values,0,$len));
        $rows = $statement->fetchAll();
        $objects = array();
        foreach ($rows as $row) {
            $objects[] = self::rowToObject( $row, $clazz );
        }
        return sizeof($objects) > 0 ? $objects : array ();
    }

    private static function rowToObject( $row, $clazz ) {
        $object = new $clazz();

        /**
         * tabela em si
         */
        foreach ( $clazz::$dicionario as $key => $value ) {
            $attrVal = $row[$value];
            $setter = self::getSetter( $key );
            /*if (array_key_exists($key,$clazz::$setters)) {
                $setter = $clazz::$setters[$key];
            }*/
            $object->$setter($attrVal);
        }

        /**
         * id
         */
        $setter = 'setId';
        $id = isset($row[$clazz::$idName]) ? $row[$clazz::$idName] : $row['id'];
        $object->$setter($id);

        /**
         * many to many
         */
        foreach ($clazz::$manyToMany as $k => $v) {
            $sql = 'SELECT * FROM ' . $v['tbRelName'] . ' WHERE ' . $v['tbRelCurrentId'] . ' = ?';
            $statement = ConexaoBD::getConexao()->prepare($sql);
            $statement->execute(array($id));
            $linhas = $statement->fetchAll();
            $objArray = array();
            foreach ($linhas as $linha) {
                $cls = $v['clEntityName'];
                $current = $cls::getById($linha[$v['tbRelOtherId']]);
                foreach ($v['tbRelDicionario'] as $kk => $vv) {
                    $setter = self::getSetter( $vv );
                    $current->$setter($linha[$kk]);
                }
                $objArray[] = $current;
                echo 'a';
            }
            $setter = self::getSetter( $k );
            $object->$setter($objArray);
        }

        /**
         * has many
         */
        foreach ( $clazz::$hasMany as $k => $v ) {
            $cls = $v['clEntityName'];
            $objArray = $cls::getByAttr($v['clCurrentId'],$id);
            $setter = self::getSetter( $k );
            $object->$setter($objArray);
            echo '';
        }

        /**
         * has one
         */
        foreach ( $clazz::$hasOne as $k => $v ) {
            $cls = $v['clEntityName'];
            require_once (__DIR__ . '/' . $cls . '.php');
            $obj = $cls::getById($row[$v['tbForeignKey']]);
            $setter = self::getSetter($k);
            $object->$setter($obj);
        }




        return $object;
    }

    public function asJSON( $extraAttrs =array()) {
        $clazz = get_called_class();
        $json = '{ "id":"'.$this->id.'"';


        /**
         * tabela em si
         */
        foreach ( array_keys($clazz::$dicionario) as $item ) {
            $getter = self::getGetter($item);
            $json .= ',"' . $item . '":';
            $attr = $this->$getter();
            if (is_object($attr)) {
                $json .= $attr->asJSON();
            } else {
                $json .= '"' . $attr . '"';
            }
        }

        /**
         * many to many
         */
        foreach ( $clazz::$manyToMany as $k => $v ) {
            $json .= ', "' . $k . '": [';
            $getter = self::getGetter($k);
            $objects = $this->$getter();
            for ( $i = 0; $i < sizeof($objects); $i++ ) {
                $json .= $i == 0 ? '' : ' , ';
                $json .= $objects[$i]->asJSON(array_values($v['tbRelDicionario']));
            }
            $json .= ']';
        }
        /**
         * extraAttrs (usado nos many to many)
         */
        for ( $i = 0 ; $i < sizeof( $extraAttrs ); $i++ ) {
            $attrName = $extraAttrs[$i];
            $getter = self::getGetter($attrName);
            $attrValue = $this->$getter();
            $json .= ',"' . $attrName . '":';
            if ( is_object($attrValue) ) {
                $json .= $attrValue->asJSON();
            } else {
                $json .= '"' . $attrValue . '"';
            }
        }

        /**
         * has many
         */
        foreach ($clazz::$hasMany as $attrName => $attrInfo ) {
            $getter = self::getGetter($attrName);
            $objects = $this->$getter();
            $json .= ', "' . $attrName . '": [';
                for ( $i = 0; $i < sizeof($objects); $i++ ) {
                    $json .= $i == 0 ? '' : ' , ';
                    $json .= $objects[$i]->asJSON();
                }
            $json .= ']';
        }

        /**
         * has one
         */
        foreach ($clazz::$hasOne as $attrName => $attrInfo ) {
            $getter = self::getGetter($attrName);
            $obj = $this->$getter();
            $objJSON = $obj->asJSON();
            $json .= ",\"$attrName\":$objJSON";


            echo ';';
        }


        $json .= '}';
        return $json;
    }
	
	public static function getColumnName ( $pname ) {
		$clazz = get_called_class();
		$x = isset($clazz::$dicionario[$pname]) ? $clazz::$dicionario[$pname] : null;
		return $x;
	}

    private static function getSetter( $pname ) {
        $clazz = get_called_class();
        if (isset($clazz::$setters) && array_key_exists($pname,$clazz::$setters)) {
            return $clazz::$setters[$pname];
        }
        return 'set' . strtoupper($pname[0]) . substr($pname,1);
    }
    private static function getGetter( $pname ) {
        $clazz = get_called_class();
        if (isset($clazz::$getters) && array_key_exists($pname,$clazz::$getters)) {
            return $clazz::$getters[$pname];
        }
        return 'get' . strtoupper($pname[0]) . substr($pname,1);
    }
    private static function getIdColumn( $pname ) {
        $clazz = get_called_class();
        return isset($clazz::$idName) ? $clazz::$idName : 'id';
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: bruno
 * Date: 16/08/16
 * Time: 09:31
 */
require_once ( __DIR__ . '/../dao/Usuario.php');
class ConfigClass {

    const bdHost = 'localhost';

    const bdPort = '3306';

    const bdName = 'ponto';

    const bdUser = 'root';

    const bdCharset = 'utf8';

    const bdPasswd = '';
    
    /**
     * PERMISSOES DE ACESSO ÀS PÁGINAS
     */
    const paginas = [
        'registrar' => [
            'nome' => 'Registrar',
            'permissoes'=> array(Usuario::GRUPO_BOLSISTAS)
        ], 'consultar' => [
    		'nome' => 'Consultar',
		    'permissoes'=> '*'
        ], 'justificar' => [
		    'nome' => 'Justificar',
		    'permissoes'=> array(Usuario::GRUPO_BOLSISTAS)
	    ], 'abonar' => [
		    'nome' => 'Abonar',
		    'permissoes'=> array(Usuario::GRUPO_BOLSISTAS)
	    ], 'deferir' => [
		    'nome' => 'Deferir',
		    'permissoes'=> array(Usuario::GRUPO_FUNCIONARIOS,Usuario::GRUPO_PROFESSORES)
	    ], 'relatorio' => [
	        'nome' => 'Relatório',
            'permissoes' => array(Usuario::GRUPO_SSI)
        ]

    ];

    const ipsInternos = [
        '172.17.*.*',
        '200.132.24.4[7,8]'
    ];

}
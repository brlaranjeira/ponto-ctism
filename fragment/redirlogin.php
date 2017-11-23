<?php
$proto = isset($_SERVER['HTTPS']) ? 'https' : 'http';
$addr = $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
$diretorio = dirname($_SERVER['PHP_SELF']) . '/';
$dparts = array_filter(explode('/',$diretorio));
array_pop($dparts);
$diretorio = '/' . implode('/',$dparts) . '/';
$to = 'login.php';
$redir = "$proto://$addr$diretorio$to";
http_response_code(302);
echo '{"href": "' . $redir . '"}';
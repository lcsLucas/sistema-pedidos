<?php

define( 'ABSPATH', $_SERVER['DOCUMENT_ROOT']);

define( 'PATH_VIEWS', $_SERVER['DOCUMENT_ROOT']."/public/views/");

define( 'PATH_CSS', $_SERVER['DOCUMENT_ROOT']."/public/css/");

define( 'PATH_JS', $_SERVER['DOCUMENT_ROOT']."/public/js/");

define( 'PATH_PEDIDOS', $_SERVER['DOCUMENT_ROOT']."/public/arquivos/pedidos/");

define( 'PATH_TMP_PEDIDOS', $_SERVER['DOCUMENT_ROOT']."/public/arquivos/tmp/");

define( 'PATH_PDFS', $_SERVER['DOCUMENT_ROOT']."/public/arquivos/pdfs/");

define( 'PATH_VIEWS_ADM', $_SERVER['DOCUMENT_ROOT']."/public/views/Admin/");

define('DB_HOST','localhost');

define('DB_NAME','health2');

define('DB_USER','root');

define('DB_PASSWORD','');

define('DB_CHARSET', 'utf8' );

define('DEBUG', true );

require_once ABSPATH.'/app/util/loader.php';
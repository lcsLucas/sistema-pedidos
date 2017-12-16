<?php

//Não permitir acesso direto no arquivo
if (! defined('ABSPATH')){
    header("Location: /");
    exit();
}

date_default_timezone_set("Brazil/East");

/*inicia sessão*/
session_name(md5('seg' . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
session_start();

//configura para o cookie da sessão ser acessado somente por HTTP
//impedindo acesso via javascript
ini_set('session.cookie_httponly',1);
ini_set('session.use_only_cookies',1);

/*configura debug*/
if (! defined('DEBUG') || DEBUG === false ) :
	 // Esconde todos os erros
	 error_reporting(0);
	 ini_set("display_errors", 0);
else :
	 // Mostra todos os erros
	 error_reporting(E_ALL);
	 ini_set("display_errors", 1);
endif;
ini_set("log_errors", 1);
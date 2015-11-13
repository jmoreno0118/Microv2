<?php

function get_site(){
	$script = explode('/', $_SERVER['SCRIPT_NAME']);
	return '/'.$script[1].'/';
}

define("url", 'http://'.$_SERVER['HTTP_HOST'].get_site() );

define("direction", $_SERVER['DOCUMENT_ROOT'].get_site() );

define("views", 'includes/views/');

define("libs", 'includes/libs/');

define("modules", 'includes/modules/');

define("functions", 'includes/functions/');

define("images", 'includes/img/');
?>
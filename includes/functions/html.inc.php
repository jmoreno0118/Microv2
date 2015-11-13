<?php

function open_html_head($title){
	include direction.views.'openhtml.html.php';
}

function load_jquery(){
	include direction.views.'jquery.html.php';
}

function load_jqueryvalidation(){
	include direction.views.'jqueryvalidation.html.php';
}

function load_datatables(){
	include direction.views.'datatables.html.php';
}

function close_head_open_body(){
	include direction.views.'openbody.html.php';
}

function get_header(){
	include direction.views.'header.html.php';
}

function close_html_body_footer(){
	include direction.views.'footer.html.php';
}

function get_menu(){
	include direction.views.'menu.html.php';
}

function get_principal_view(){
	include direction.views.'temas.html.php';
}

?>
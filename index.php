<?php

include_once 'conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';
include_once direction.functions.'html.inc.php';

if (!usuarioRegistrado())
{
  include 'registro.html.php';
  exit();
}
if (isset($_SESSION['idot'])){
  unset($_SESSION['idot']);
}
if (isset($_SESSION['quien'])){
  unset($_SESSION['quien']);
}
include 'index.html.php';
exit();
?>
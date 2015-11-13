
<?php
 /********** Norma 001 **********/
include_once '../../../conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';

if (!usuarioRegistrado())
{
  header('Location: '.url);
  exit();
}
if (!usuarioConPermiso('Administra OT'))
{
  $mensaje='Solo el Admistrador de ordenes tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/* Ir a formulario de nueva planta */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try
{
	$sql='SELECT Razon_Social FROM clientestbl WHERE Numero_Cliente =:id';
	$s=$pdo->prepare($sql);
	$s->bindValue(':id', $_POST['idcliente']);
	$s->execute();
	$plantas = $s->fetch();
}
catch (PDOException $e)
{
   $mensaje='Hubo un error tratando de obtener la informacion del cliente';
   throwError($mensaje);
}
$pestanapag ='Agrega planta';
$titulopagina ='Agregar una nueva planta al cliente ';
$razonsocial = $plantas['Razon_Social'];
$boton = 'Guardar Planta';
include 'formaplanta.html.php';

?>
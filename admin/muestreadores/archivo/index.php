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
if ( !(usuarioConPermiso('Supervisor') OR usuarioConPermiso('Captura')) )
{
  $mensaje='Solo el Supervisor y Capturista tienen acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}
 limpiasession();

/**************************************************************************************************/
/* Subir documento */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='subir')
{
	include direction.functions.'conectadb.inc.php';
	// verifica que el archivo se haya subido
	if (!is_uploaded_file($_FILES['archivo']['tmp_name']))
	{
		$mensaje='Hubo un error tratando de subir el archivo.  Favor de revisar la conexión a internet y que el archivo sea menor a 2Mb e intenta de nevo.';
		throwError($mensaje);
	}

	$archivotipo=exif_imagetype($_FILES['archivo']['tmp_name']);
	$tiposaceptados=array(IMAGETYPE_PNG);
	if (!in_array($archivotipo,$tiposaceptados))
	{
		$mensaje='Las firmas solo pueden ser formato PNG';
		$errorlink = url.'admin/muestreadores';
		$errornav = 'Volver a muestreadores';
		throwError($mensaje, $errorlink, $errornav);
	}

	// se verifica que el nombre del archivo solo contenga caracteres validos
	$nombrearch=preg_replace('/[^A-Z0-9._-]/i','_',$_FILES['archivo']['name']);
	$partes=pathinfo($nombrearch);
	$extension=$partes['extension'];
	$nombrearchivar=$_POST['id'].'_'.$_POST['hora'].'.'.$extension;
	$nombre=$partes['filename'];

	//se guarda el archivo en la carpeta deseada
	$semovio=move_uploaded_file($_FILES['archivo']['tmp_name'],
	direction.'admin/muestreadores/archivo/'.$nombrearchivar);
	if (!$semovio)
	{
		$mensaje='Vuelva a intentar de nuevo.  Hubo un error tratando de guardar el archivo';
		$errorlink = url.'admin/muestreadores';
		$errornav = 'Volver a muestreadores';
		throwError($mensaje, $errorlink, $errornav);
	}
	// Colocar los permisos de lectura al archivo
	chmod (direction.'admin/muestreadores/archivo/'.$nombrearchivar, 0777);
	guardadocumento($nombrearchivar, $nombre, $_POST['id']);

	$_SESSION['post'] = $_POST;
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Acción default */
/**************************************************************************************************/
if(isset($_POST['id'])){
	$post = $_POST;
}
else
{
	$post = $_SESSION['post'];
	if(isset($post))
	{
		//$_SESSION['post'] = '';
		//unset($_SESSION['post']);
	}
}
include direction.functions.'conectadb.inc.php';
$id = $post['id'];
$nombremuestreador = getMuestreador();
$documentos = datostabla();
include 'formafirma.html.php';
exit();

/**************************************************************************************************/
/* Obtener el nombre de la orden */
/**************************************************************************************************/
function getMuestreador()
{
	global $pdo, $id; 
	try
	{
		$sql='SELECT nombre, apellido
			FROM usuariostbl
			WHERE id = :id';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id', $id);
		$s->execute();
		$nombre = $s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error extrayendo la información del muestreador.';
		$errorlink = url.'admin/muestreadores';
		$errornav = 'Volver a muestreadores';
		throwError($mensaje, $errorlink, $errornav);
	}
	return $nombre['nombre'].' '.$nombre['apellido'];
}

/**************************************************************************************************/
/* Obtener la lista de documentos */
/**************************************************************************************************/
function datostabla()
{
	global $id, $pdo; 
	try
	{
		$sql='SELECT firma, firmaarchivar FROM usuariostbl
		WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de mostrar los planos existentes.';
		$errorlink = url.'admin/muestreadores';
		$errornav = 'Volver a muestreadores';
		throwError($mensaje, $errorlink, $errornav);
	}

	if($docs = $s->fetch())
	{
		return $docs;
	}
	return '';
}

/**************************************************************************************************/
/* Guanrdar documento en la base de datos */
/**************************************************************************************************/
function guardadocumento($nombrearchivar='', $nombre='', $id='')
{
  global $pdo;
  try
  {
	  $sql='UPDATE usuariostbl SET
			firma=:firma,
			firmaarchivar=:firmaarchivar
			WHERE id=:id';
	  $s=$pdo->prepare($sql);
	  $s->bindValue(':firma',$nombre);
	  $s->bindValue(':firmaarchivar',$nombrearchivar);
	  $s->bindValue(':id', $id);
	  $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error al tratar de guardar la informacion del plano. Intentar nuevamente y avisar de este error a sistemas.'.$e;
    $errorlink = url.'admin/muestreadores';
	$errornav = 'Volver a muestreadores';
    throwError($mensaje, $errorlink, $errornav);
  }
}
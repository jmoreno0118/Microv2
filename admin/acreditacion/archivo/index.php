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
if (!usuarioConPermiso('Supervisor'))
{
  $mensaje='Solo el Supervisor tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}
limpiasession();

/**************************************************************************************************/
/* Borrar documento */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='borraplano')
{
	include direction.functions.'conectadb.inc.php';
	$sql='SELECT nombrearchivado FROM acredimgtbl WHERE id=:id';
	$s=$pdo->prepare($sql);
	$s->bindValue(':id',$_POST['iddoc']);
	$s->execute();
	$nombre=$s->fetch();
	$arch=$_SERVER['DOCUMENT_ROOT'].'/reportes/acreditacion/archivo/'.$nombre['nombrearchivado'];

	if (file_exists($arch))
	{
		try
		{	
			$sql='DELETE FROM acredimgtbl WHERE id=:id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':id', $_POST['iddoc']);
			$s->execute();
		}
		catch (PDOException $e)
		{
		   $mensaje='Lo sentimos, no se pudo borrar el plano.  Favor de intentar nuevamente';
		   throwError($mensaje, $errorlink, $errornav);
		}   
	   unlink($arch);
	}
	else
	{
	   $mensaje='Lo sentimos, no se encontro el archivo que se desea borrar.  Favor de avisar a sistemas de este error';
	   throwError($mensaje, $errorlink, $errornav);
	}

	$_SESSION['post'] = $_POST;
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Subir documento */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='subir')
{
	include direction.functions.'conectadb.inc.php';
	// verifica que el archivo se haya subido
	if (!is_uploaded_file($_FILES['archivo']['tmp_name']))
	{
		$mensaje = 'Hubo un error tratando de subir el archivo.  Favor de revisar la conexión a internet y que el archivo sea menor a 2Mb e intenta de nevo.';
		throwError($mensaje, $errorlink, $errornav);
	}

	$archivotipo=exif_imagetype($_FILES['archivo']['tmp_name']);
	$tiposaceptados=array(IMAGETYPE_GIF,IMAGETYPE_JPEG,IMAGETYPE_PNG,IMAGETYPE_BMP);
	if (!in_array($archivotipo,$tiposaceptados))
	{
		$mensaje = 'Los croquis sólo pueden ser imagen, ej. GIF, JPEG, PNG O BMP';
		$errorlink = url.'admin/acreditacion';
		$errornav = 'Volver a acreditaciones';
		throwError($mensaje, $errorlink, $errornav);
	}

	// se verifica que el nombre del archivo solo contenga caracteres validos
	$nombrearch = preg_replace('/[^A-Z0-9._-]/i','_',$_FILES['archivo']['name']);
	$partes = pathinfo($nombrearch);
	$extension = $partes['extension'];
	$nombrearchivar = $_POST['id'].'_'.$_POST['hora'].'.'.$extension;
	$nombre = $partes['filename'];

	//se guarda el archivo en la carpeta deseada
	$semovio = move_uploaded_file($_FILES['archivo']['tmp_name'],
	direction.'admin/acreditacion/archivo/'.$nombrearchivar);
	if (!$semovio)
	{
		$mensaje = 'Vuelva a intentar de nuevo.  Hubo un error tratando de guardar el archivo';
		throwError($mensaje, $errorlink, $errornav);
	}
	// Colocar los permisos de lectura al archivo
	chmod (direction.'admin/acreditacion/archivo/'.$nombrearchivar, 0777);
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
$nombreacred = getAcred();
$documentos = datostabla();
include 'formacroquis.html.php';
exit();

/**************************************************************************************************/
/* Obtener el nombre de la orden */
/**************************************************************************************************/
function getAcred()
{
	global $pdo, $id; 
	try
	{
		$sql='SELECT nombre
			FROM acreditaciontbl
			WHERE id = :id';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id', $id);
		$s->execute();
		$nombreacred = $s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error extrayendo la información de adicionales.';
		throwError($mensaje);
	}
	return $nombreacred['nombre'];
}

/**************************************************************************************************/
/* Obtener la lista de documentos */
/**************************************************************************************************/
function datostabla()
{
	global $id, $pdo; 
	try
	{
		$sql='SELECT id, nombre, nombrearchivado FROM acredimgtbl
		WHERE acreditacionidfk=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de mostrar los planos existentes.';
		throwError($mensaje);
	}

	if($docs = $s->fetchAll())
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
	  $sql='INSERT INTO acredimgtbl SET
			nombre=:nombre,
			nombrearchivado=:nombrearchivar,
			acreditacionidfk=:id';
	  $s=$pdo->prepare($sql);
	  $s->bindValue(':nombre',$nombre);
	  $s->bindValue(':nombrearchivar',$nombrearchivar);
	  $s->bindValue(':id', $id);
	  $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error al tratar de guardar la informacion del plano.  intentar nuevamente y avisar de este error a sistemas.'.$e;
    throwError($mensaje);
  }
}

<?php
 /********** Norma 001 **********/
include_once '../../conf.php';
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

/**************************************************************************************************/
/* Ir a formulario de nueva planta */
/**************************************************************************************************/
/*if(isset($_POST['accion']) and $_POST['accion']=='Nueva')
{
	$pestanapag ='Agrega acreditación';
	$titulopagina ='Agregar una nueva acreditación';
	$boton = 'Guardar Acreditación';
	include 'formacaptura.html.php';
	exit();
}*/

/**************************************************************************************************/
/* Guardar nueva planta */
/**************************************************************************************************/
/*if(isset($_POST['accion']) and $_POST['accion']=='Guardar Acreditación')
{
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		$sql='INSERT INTO acreditaciontbl SET
		nombre = :nombre,
		fecha = :fecha';
		$s=$pdo->prepare($sql);
		$s->bindValue(':nombre', trim($_POST['nombre']) );
		$s->bindValue(':fecha', trim($_POST['fecha']) );
		$s->execute();

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar la planta. Favor de intentar nuevamente.'.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	header('Location: .');
	exit();
}*/

/**************************************************************************************************/
/* Entrar a edición de una planta */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar')
{
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try
	{
		$sql='SELECT * FROM acreditaciontbl WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$id);
		$s->execute();
		$valores = $s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de insertar la planta. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	$pestanapag ='Editar acreditación';
	$titulopagina ='Editar una acreditación';
	$boton = 'Salvar Acreditación';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Salvar edición de una planta */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar Acreditación')
{
	include direction.functions.'conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		$sql='UPDATE acreditaciontbl SET
			nombre=:nombre,
			fecha=:fecha
			WHERE id = :id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':nombre', trim($_POST['nombre']) );
		$s->bindValue(':fecha', trim($_POST['fecha']) );
		$s->bindValue(':id', $_POST['id']);
		$s->execute();

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar la planta. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Borrar una planta */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Borrar')
{
	include direction.functions.'conectadb.inc.php';
	$id=$_POST['id'];
	try
	{
		$sql='SELECT * FROM acreditaciontbl WHERE id=:id';
		$s= $pdo->prepare($sql);
		$s->bindValue(':id',$id); 
		$s->execute();
		$valores=$s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo extraer información de la planta'.$e;
		throwError($mensaje);
	}
	include 'formaconfirmaplanta.html.php';
	exit();
}

/**************************************************************************************************/
/* Confirmar borrado de una planta */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Continuar')
{
	include direction.functions.'conectadb.inc.php';
	$id=$_POST['id'];
	try
	{
		$sql='DELETE FROM acreditaciontbl WHERE id=:id';
		$s= $pdo->prepare($sql);
		$s->bindValue(':id',$id); 
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo hacer la confirmacion de eliminación de la planta'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Confirmar borrado de una planta */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Archivo')
{
	$_SESSION['post'] = $_POST;
	header('Location: '.url.'admin/acreditacion/archivo');
	exit();
}

/**************************************************************************************************/
/* Ver tabla de acreditaciones */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try   
{
	$sql='SELECT *
	    FROM acreditaciontbl
	    ORDER BY fecha ASC';
	$s=$pdo->prepare($sql); 
	$s->execute();
	$acreditaciones = $s->fetchAll();
}
catch (PDOException $e)
{
	$mensaje='Hubo un error extrayendo la lista de ordenes de agua.'.$e;
  	throwError($mensaje);
}
include 'formaacreds.html.php';
exit();
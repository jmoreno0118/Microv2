
<?php

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
/* Ir a formulario de nueva metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Nuevo')
{
	$pestanapag ='Agrega parametro';
	$titulopagina ='Agregar un nuevo parametro';
	$boton = 'Guardar Parametro';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Guardar nueva metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar Parametro')
{
	include direction.functions.'conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		$sql='INSERT INTO aparametrostbl SET
			clave=:clave,
			parametro=:parametro,
			unidades=:unidades,
			metodo=:metodo,
			LD=:LD,
			LC=:LC';
		$s=$pdo->prepare($sql);
		$s->bindValue(':clave', trim($_POST['clave']) );
		$s->bindValue(':parametro', trim($_POST['parametro']) );
		$s->bindValue(':unidades', trim($_POST['unidades']) );
		$s->bindValue(':metodo', trim($_POST['metodo']) );
		$s->bindValue(':LD', trim($_POST['LD']) );
		$s->bindValue(':LC', trim($_POST['LC']) );
		$s->execute();

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar la metodo. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Entrar a edición de una metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar')
{
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try
	{
		$sql='SELECT * FROM aparametrostbl WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$id);
		$s->execute();
		$valores = $s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de insertar la metodo. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	$pestanapag ='Editar parametro';
	$titulopagina ='Editar un parametro';
	$boton = 'Salvar Parametro';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Salvar edición de un metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar Parametro')
{
	include direction.functions.'conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		$sql='UPDATE aparametrostbl SET
			clave=:clave,
			parametro=:parametro,
			unidades=:unidades,
			metodo=:metodo,
			LD=:LD,
			LC=:LC
			WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':clave', trim($_POST['clave']) );
		$s->bindValue(':parametro', trim($_POST['parametro']) );
		$s->bindValue(':unidades', trim($_POST['unidades']) );
		$s->bindValue(':metodo', trim($_POST['metodo']) );
		$s->bindValue(':LD', trim($_POST['LD']) );
		$s->bindValue(':LC', trim($_POST['LC']) );
		$s->bindValue(':id', $_POST['id']);
		$s->execute();

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar la metodo. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Borrar un metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Borrar')
{
	include direction.functions.'conectadb.inc.php'; 
	$id=$_POST['id'];
	try
	{
		$sql='SELECT * FROM aparametrostbl WHERE id=:id';
		$s= $pdo->prepare($sql);
		$s->bindValue(':id',$id); 
		$s->execute();
		$valores=$s->fetch();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo extraer información de la metodo'.$e;
		throwError($mensaje);
	}
	include 'formaconfirma.html.php';
	exit();
}

/**************************************************************************************************/
/* Confirmar borrado de un metodo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Continuar')
{
	include direction.functions.'conectadb.inc.php'; 
	$id=$_POST['id'];
	try
	{
		$sql='DELETE FROM aparametrostbl WHERE id=:id';
		$s= $pdo->prepare($sql);
		$s->bindValue(':id',$id); 
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo hacer la confirmacion de eliminación de la metodo'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Ver tabla de acreditaciones */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try   
{
	$sql='SELECT * FROM aparametrostbl';
	$s=$pdo->prepare($sql); 
	$s->execute();
	$parametros = $s->fetchAll();
}
catch (PDOException $e)
{
	$mensaje='Hubo un error extrayendo la lista de ordenes de agua.'.$e;
  	throwError($mensaje);
}
include 'formaparametros.html.php';
exit();
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
/* Ir a formulario de nueva norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Nuevo')
{
	$pestanapag ='Agrega norma';
	$titulopagina ='Agregar una nueva norma';
	$boton = 'Guardar Norma';
	$parametros = getParametros();
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Guardar nueva norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar Norma')
{
	include direction.functions.'conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		$sql='INSERT INTO normastbl SET
			norma=:norma';
		$s=$pdo->prepare($sql);
		$s->bindValue(':norma', trim($_POST['nombre']) );
		$s->execute();
		$id=$pdo->lastInsertid();

		setParametros($id, array_unique($_POST['parametro']));

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar el parametro. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Entrar a edición de una norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar')
{
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try
	{
		$sql='SELECT * FROM normastbl WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$valores = $s->fetch();

		$sql='SELECT * FROM paramnormas WHERE normaidfk=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$nparams = array();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $value) {
			$nparams[] = $value['parametroidfk'];
		}
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo extraer información del parametro. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	$parametros = getParametros();
	$pestanapag ='Editar norma';
	$titulopagina ='Editar una norma';
	$boton = 'Salvar Norma';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Salvar edición de una norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar Norma')
{
	include direction.functions.'conectadb.inc.php';
	try
	{
		$pdo->beginTransaction();

		setParametros($_POST['id'], array_unique($_POST['parametro']));

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar el parametro. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	header('Location: .');
	exit();
}

/**************************************************************************************************/
/* Ir a formulario de nueva norma */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try   
{
	$sql='SELECT * FROM normastbl';
	$s=$pdo->prepare($sql); 
	$s->execute();
	$normas = $s->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e)
{
	$mensaje='Hubo un error extrayendo la lista de parametros.'.$e;
  	throwError($mensaje);
}
include 'formanormas.html.php';
exit();

/**************************************************************************************************/
/* Ir a formulario de nueva parametro */
/**************************************************************************************************/
function getParametros(){
	include direction.functions.'conectadb.inc.php';
	try
	{
		$sql='SELECT * FROM aparametrostbl';
		$s=$pdo->prepare($sql);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo extraer información del parametro. Favor de intentar nuevamente.'.$e;
		throwError($mensaje);
	}
	foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $resultado)
	{
		$parametros[$resultado['id']] = $resultado['parametro'];
	}
	return $parametros;
}

/**************************************************************************************************/
/* Ir a formulario de nueva parametro */
/**************************************************************************************************/
function setParametros($norma, $parametros){
	global $pdo;
	foreach ($parametros as $value) {
		$sql='INSERT INTO paramnormas SET
			parametroidfk=:parametroidfk,
			normaidfk=:normaidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':parametroidfk', $value);
		$s->bindValue(':normaidfk', $norma);
		$s->execute();
	}
}
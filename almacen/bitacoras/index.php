<?php

include_once '../../conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';

if (!usuarioRegistrado())
{
  header('Location: '.url);
  exit();
}
if (!usuarioConPermiso('Captura'))
{
  $mensaje='Solo el Capturista tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/* Nueva bitacora */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Nueva'){
	$pestanapag = 'Nueva Bitacora';
	$titulopagina = 'Nueva Bitacora';
	$boton = 'Guardar';
	include 'formabitacora.html.php';
	exit();
}

/**************************************************************************************************/
/* Guardar bitacora */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar'){
	include direction.functions.'conectadb.inc.php';
	try
	{
		$sql='INSERT INTO bitacoratbl SET
                muestreadoridfk=:muestreadoridfk,
                ot=:ot,
                fechainicio=:fechainicio,
                entrego=:entrego,
                observaciones=:observaciones,
                fechafin=:fechafin,
                agentesevaluados=:agentesevaluados,
                metodosreferencia=:metodosreferencia,
                procedimiento=:procedimiento,
                actividadesrealizadas=:actividadesrealizadas,
                problemas=:problemas';
		$s=$pdo->prepare($sql);
		$s->bindValue(':muestreadoridfk', getUsuarioid($_SESSION['usuario']) );
		$s->bindValue(':ot', $_POST['ot']);
		$s->bindValue(':fechainicio', $_POST['fechainicio']);
		$s->bindValue(':entrego', $_POST['entrego']);
		$s->bindValue(':observaciones', $_POST['observaciones']);
		$s->bindValue(':agentesevaluados', $_POST['agentesevaluados']);
		$s->bindValue(':metodosreferencia', $_POST['metodosreferencia']);
		$s->bindValue(':procedimiento', $_POST['procedimiento']);
		$s->bindValue(':actividadesrealizadas', $_POST['actividadesrealizadas']);
		$s->bindValue(':problemas', $_POST['problemas']);
		if( isset($_POST['fechafin']) AND strcmp($_POST['fechafin'], '') !== 0  ){
			$s->bindValue(':fechafin', $_POST['fechafin']);
		}else{
			$s->bindValue(':fechafin', NULL, PDO::PARAM_INT);
		}
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		throwError($mensaje);
	}
}

/**************************************************************************************************/
/* Editar bitacora */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar'){
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try{
		$sql='SELECT * FROM bitacoratbl
				WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$valores = $s->fetch(PDO::FETCH_ASSOC);
		if( strcmp($valores['fechafin'], '0000-00-00 00:00:00') === 0 ){
			$valores['fechafin'] = '';
		}
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de obtener los datos de la bitacora. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
	$pestanapag = 'Editar bitacora';
	$titulopagina = 'Editar bitacora';
	$boton = 'Salvar';
	include 'formabitacora.html.php';
	exit();
}

/**************************************************************************************************/
/* Salvar bitacora */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar'){
	include direction.functions.'conectadb.inc.php';
	try
	{
		$sql='UPDATE bitacoratbl SET
                muestreadoridfk=:muestreadoridfk,
                ot=:ot,
                fechainicio=:fechainicio,
                entrego=:entrego,
                observaciones=:observaciones,
                fechafin=:fechafin,
                agentesevaluados=:agentesevaluados,
                metodosreferencia=:metodosreferencia,
                procedimiento=:procedimiento,
                actividadesrealizadas=:actividadesrealizadas,
                problemas=:problemas
                WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':muestreadoridfk', getUsuarioid($_SESSION['usuario']) );
		$s->bindValue(':ot', $_POST['ot']);
		$s->bindValue(':fechainicio', $_POST['fechainicio']);
		$s->bindValue(':entrego', $_POST['entrego']);
		$s->bindValue(':observaciones', $_POST['observaciones']);
		$s->bindValue(':agentesevaluados', $_POST['agentesevaluados']);
		$s->bindValue(':metodosreferencia', $_POST['metodosreferencia']);
		$s->bindValue(':procedimiento', $_POST['procedimiento']);
		$s->bindValue(':actividadesrealizadas', $_POST['actividadesrealizadas']);
		$s->bindValue(':problemas', $_POST['problemas']);
		if( isset($_POST['fechafin'])  AND strcmp($_POST['fechafin'], '') !== 0 ){
			$s->bindValue(':fechafin', $_POST['fechafin']);
		}else{
			$s->bindValue(':fechafin', NULL, PDO::PARAM_INT);
		}
		$s->bindValue(':id', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		throwError($mensaje);
	}
}

/**************************************************************************************************/
/* Salvar bitacora */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Equipos'){
	$_SESSION['bitacoraid'] = $_POST['id'];
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/bitacoras/equipos');
	exit();
}

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
if(isset($_SESSION['bitacoraid']))
	unset($_SESSION['bitacoraid']);
try{
	if(usuarioConPermiso('Supervisor')){
		$sql='SELECT * FROM bitacoratbl';
		$s=$pdo->prepare($sql);
	}else{
		$sql='SELECT * FROM bitacoratbl WHERE muestreadoridfk=:muestreadoridfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':muestreadoridfk', getUsuarioid($_SESSION['usuario']) );
	}
	$s->execute();
	$bitacoras = $s->fetchAll(PDO::FETCH_ASSOC);
	
}catch (PDOException $e){
	$mensaje='Hubo un error al tratar de obtener los equipos. Favor de intentar nuevamente. '.$e;
	throwError($mensaje);
}
include 'formabitacoras.html.php';
exit();

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
function getUsuarioid($usuario){
	global $pdo;
	try{
		$sql='SELECT id FROM usuariostbl WHERE usuario=:usuario';
		$s=$pdo->prepare($sql);
		$s->bindValue(':usuario', $usuario);
		$s->execute();
		$usuario = $s->fetch(PDO::FETCH_ASSOC);
		return $usuario['id'];
	}catch (PDOException $e){
		return '';
	}
}
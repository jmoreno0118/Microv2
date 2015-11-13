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
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Nuevo'){
	$pestanapag = 'Nuevo equipo';
	$titulopagina = 'Nuevo equipo';
	$boton = 'Guardar';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Guardar equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar'){
	include direction.functions.'conectadb.inc.php';
	try
	{
		$sql='INSERT INTO equipostbl SET
                inventario=:inventario,
                marca=:marca,
                modelo=:modelo,
                serie=:serie,
                tipo=:tipo,
                descripcion=:descripcion,
                fechaalta=:fechaalta,
                estudio=:estudio,
                estado=:estado,
                fechabaja=:fechabaja,
                causabaja=:causabaja,
                responsable=:responsable,
                notas=:notas,
                periodo_cal_externo=:periodo_cal_externo,
                periodo_manto_interno=:periodo_manto_interno';
		$s=$pdo->prepare($sql);
		$s->bindValue(':inventario', $_POST['inventario']);
		$s->bindValue(':marca', $_POST['marca']);
		$s->bindValue(':modelo', $_POST['modelo']);
		$s->bindValue(':serie', $_POST['serie']);
		$s->bindValue(':tipo', $_POST['tipo']);
		$s->bindValue(':descripcion', $_POST['descripcion']);
		$s->bindValue(':fechaalta', $_POST['fechaalta']);
		$s->bindValue(':estudio', $_POST['estudio']);
		$s->bindValue(':estado', $_POST['estado']);

		if( isset($_POST['fechabaja']) ){
			$s->bindValue(':fechabaja', $_POST['fechabaja']);
		}else{
			$s->bindValue(':fechabaja', NULL, PDO::PARAM_INT);
		}

		if( isset($_POST['causabaja']) ){
			$s->bindValue(':causabaja', $_POST['causabaja']);
		}else{
			$s->bindValue(':causabaja', NULL, PDO::PARAM_INT);
		}
		$s->bindValue(':responsable', $_POST['responsable']);
		$s->bindValue(':notas', $_POST['notas']);
		$s->bindValue(':periodo_cal_externo', $_POST['periodo_cal_externo']);
		$s->bindValue(':periodo_manto_interno', $_POST['periodo_manto_interno']);
		$s->execute();
		$id=$pdo->lastInsertid();

		eqparametros($id, $_POST['parametros']);
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		throwError($mensaje);
	}
}

/**************************************************************************************************/
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar'){
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try{
		$sql='SELECT * FROM equipostbl
				WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$valores = $s->fetch(PDO::FETCH_ASSOC);

		$sql='SELECT * FROM eqparametrostbl
				WHERE equipoidfk=:equipoidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $id);
		$s->execute();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $value) {
			$valores['parametros'][] = $value;
		}
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de obtener los datos del equipo. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
	$pestanapag = 'Editar equipo';
	$titulopagina = 'Editar equipo';
	$boton = 'Salvar';
	include 'formacaptura.html.php';
	exit();
}

/**************************************************************************************************/
/* Salvar equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar'){
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try
	{
		$sql='UPDATE equipostbl SET
                inventario=:inventario,
                marca=:marca,
                modelo=:modelo,
                serie=:serie,
                tipo=:tipo,
                descripcion=:descripcion,
                fechaalta=:fechaalta,
                estudio=:estudio,
                estado=:estado,
                fechabaja=:fechabaja,
                causabaja=:causabaja,
                responsable=:responsable,
                notas=:notas,
                periodo_cal_externo=:periodo_cal_externo,
                periodo_manto_interno=:periodo_manto_interno
                WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':inventario', $_POST['inventario']);
		$s->bindValue(':marca', $_POST['marca']);
		$s->bindValue(':modelo', $_POST['modelo']);
		$s->bindValue(':serie', $_POST['serie']);
		$s->bindValue(':tipo', $_POST['tipo']);
		$s->bindValue(':descripcion', $_POST['descripcion']);
		$s->bindValue(':fechaalta', $_POST['fechaalta']);
		$s->bindValue(':estudio', $_POST['estudio']);
		$s->bindValue(':estado', $_POST['estado']);

		if( isset($_POST['fechabaja']) ){
			$s->bindValue(':fechabaja', $_POST['fechabaja']);
		}else{
			$s->bindValue(':fechabaja', NULL, PDO::PARAM_INT);
		}

		if( isset($_POST['causabaja']) ){
			$s->bindValue(':causabaja', $_POST['causabaja']);
		}else{
			$s->bindValue(':causabaja', NULL, PDO::PARAM_INT);
		}
		$s->bindValue(':responsable', $_POST['responsable']);
		$s->bindValue(':notas', $_POST['notas']);
		$s->bindValue(':periodo_cal_externo', $_POST['periodo_cal_externo']);
		$s->bindValue(':periodo_manto_interno', $_POST['periodo_manto_interno']);
		$s->bindValue(':id', $id);
		$s->execute();

		deleteeqparametros($id);

		eqparametros($id, $_POST['parametros']);
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		throwError($mensaje);
	}
}

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try{
	$sql='SELECT * FROM equipostbl';
	$s=$pdo->prepare($sql);
	$s->execute();
	$equipos = $s->fetchAll(PDO::FETCH_ASSOC);
}catch (PDOException $e){
	$mensaje='Hubo un error al tratar de obtener los equipos. Favor de intentar nuevamente. '.$e;
	throwError($mensaje);
}
include 'formaequipos.html.php';
exit();

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
function eqparametros($id, $parametros){
	global $pdo;
	foreach ($parametros as $key => $value) {
		$sql='INSERT INTO eqparametrostbl SET
                equipoidfk=:equipoidfk,
                parametro=:parametro,
                refesperada1=:refesperada1,
                refesperada2=:refesperada2,
                refesperada3=:refesperada3,
                unidades=:unidades';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $id);
		$s->bindValue(':parametro', $value['parametro']);
		$s->bindValue(':refesperada1', $value['refesperada1']);
		$s->bindValue(':refesperada2', $value['refesperada2']);
		$s->bindValue(':refesperada3', $value['refesperada3']);
		$s->bindValue(':unidades', $value['unidades']);
		$s->execute();
	}
}

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
function deleteeqparametros($id){
	global $pdo;
	$sql='DELETE FROM eqparametrostbl
		WHERE equipoidfk=:equipoidfk';
	$s=$pdo->prepare($sql);
	$s->bindValue(':equipoidfk', $id);
	$s->execute();
}
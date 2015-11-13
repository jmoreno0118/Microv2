<?php
 //********** iluminacion **********
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';


 if (!usuarioRegistrado())
 {
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/direccionaregistro.inc.php';
  exit();
 }
 if (!usuarioConPermiso('Captura'))
 {
  $mensaje='Solo el Capturista tiene acceso a esta parte del programa';
  include '../accesonegado.html.php';
  exit();
 }

/**************************************************************************************************/
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Agregar'){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id = $_POST['id'];
	try
	{
		foreach ($_POST['equipos'] as $key => $value) {
			$sql='INSERT INTO bitacoraeqtbl SET
                bitacoraidfk=:bitacoraidfk,
                equipoidfk=:equipoidfk';
			$s=$pdo->prepare($sql);
			$s->bindValue(':bitacoraidfk', $id);
			$s->bindValue(':equipoidfk', $value);
			$s->execute();

			$sql='UPDATE equipostbl SET
				estado = "Campo"
				WHERE id=:id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':id', $value);
			$s->execute();
		}
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
}

/**************************************************************************************************/
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Borrar'){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try
	{
		$sql='DELETE FROM bitacoraeqtbl
			WHERE equipoidfk=:equipoidfk
			AND bitacoraidfk=:bitacoraidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $_POST['equipoid']);
		$s->bindValue(':bitacoraidfk', $_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
}

/**************************************************************************************************/
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Ver'){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id = $_POST['id'];
	$equipoid = $_POST['equipoid'];
	try
	{
		$sql='SELECT * FROM equipostbl WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $equipoid);
		$s->execute();
		$valores = $s->fetch(PDO::FETCH_ASSOC);

		$sql='SELECT * FROM eqparametrostbl WHERE equipoidfk=:equipoidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $equipoid);
		$s->execute();
		$valores['parametros'] = $s->fetchAll(PDO::FETCH_ASSOC);

		$sql='SELECT *, id as "bitacoraeqid" FROM bitacoraeqtbl WHERE bitacoraidfk=:bitacoraidfk AND equipoidfk=:equipoidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':bitacoraidfk', $id);
		$s->bindValue(':equipoidfk', $equipoid);
		$s->execute();
		$valores = array_merge($valores, $s->fetch(PDO::FETCH_ASSOC));
		if( strcmp($valores['fechahoradevolucion'], '0000-00-00 00:00:00') === 0 ){
			$valores['fechahoradevolucion'] = '';
		}

		$sql='SELECT * FROM lecturastbl WHERE bitacoraeqidfk=:bitacoraeqidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':bitacoraeqidfk', $valores['bitacoraeqid']);
		$s->execute();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $key => $value) {
			$refesperadas = array('refesperada1' => $value['calref1'],
									'refesperada2' => $value['calref2'],
									'refesperada3' => $value['calref3']
				);
			$valores['parametros'][$value['paramnum']-1] = array_replace($valores['parametros'][$value['paramnum']-1], $value, $refesperadas);
		}

	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$pestanapag = 'Equipo de la bitacora';
	$titulopagina = 'Equipo de la bitacora ';
	$boton = 'Guardar';
	include 'formaequipo.html.php';
	exit();
}

/**************************************************************************************************/
/* Nuevo equipo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar'){
	//var_dump($_POST);
	//exit();
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id = $_POST['id'];
	$equipoid = $_POST['equipoid'];
	$bitacoraeqid = $_POST['bitacoraeqid'];
	try
	{
		$sql='UPDATE bitacoraeqtbl SET
                fechahoraentrega=:fechahoraentrega,
                fechahoradevolucion=:fechahoradevolucion,
                observacionsalida=:observacionsalida,
                comprobacionsalida=:comprobacionsalida,
                observacionregreso=:observacionregreso,
                comprobacionregreso=:comprobacionregreso,
                reviso=:reviso
                WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':fechahoraentrega', $_POST['fechahoraentrega'], PDO::PARAM_STR);

		if( isset($_POST['fechahoradevolucion']) AND strcmp($_POST['fechahoradevolucion'], '') !== 0 ){
			$s->bindValue(':fechahoradevolucion', $_POST['fechahoradevolucion'], PDO::PARAM_STR);
		}else{
			$s->bindValue(':fechahoradevolucion', NULL, PDO::PARAM_INT);
		}

		$s->bindValue(':observacionsalida', $_POST['observacionsalida']);
		if( isset($_POST['comprobacionsalida']) AND strcmp($_POST['comprobacionsalida'], '') !== 0 ){
			$s->bindValue(':comprobacionsalida', $_POST['comprobacionsalida']);
		}else{
			$s->bindValue(':comprobacionsalida', NULL, PDO::PARAM_INT);
		}

		if( isset($_POST['observacionregreso']) AND strcmp($_POST['observacionregreso'], '') !== 0 ){
			$s->bindValue(':observacionregreso', $_POST['observacionregreso']);
		}else{
			$s->bindValue(':observacionregreso', NULL, PDO::PARAM_INT);
		}

		if( isset($_POST['comprobacionregreso']) AND strcmp($_POST['comprobacionregreso'], '') !== 0 ){
			$s->bindValue(':comprobacionregreso', $_POST['comprobacionregreso']);
		}else{
			$s->bindValue(':comprobacionregreso', NULL, PDO::PARAM_INT);
		}

		$s->bindValue(':reviso', $_POST['reviso']);
		$s->bindValue(':id', $bitacoraeqid);
		$s->execute();

		if( isset($_POST['fechahoradevolucion']) AND strcmp($_POST['fechahoradevolucion'], '') !== 0 ){
			$sql='UPDATE equipostbl SET
				estado = "Almacen"
				WHERE id=:id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':id', $equipoid);
			$s->execute();
		}
		
		foreach ($_POST['lectura'] as $key => $value) {
			if( (strcmp($value['calini1'], '') === 0 AND
				strcmp($value['calini2'], '') === 0 AND 
				strcmp($value['calini3'], '') === 0) OR
				(strcmp($value['calfin1'], '') === 0 AND 
				strcmp($value['calfin2'], '') === 0 AND
				strcmp($value['calfin3'], '') === 0) ){
				continue;
			}
			$sql = 'SELECT id FROM lecturastbl WHERE bitacoraeqidfk=:bitacoraeqidfk AND paramnum=:paramnum';
			$s=$pdo->prepare($sql);
			$s->bindValue(':bitacoraeqidfk', $bitacoraeqid);
			$s->bindValue(':paramnum', $key+1);
			$s->execute();
			$lectura = $s->fetch();

			if($lectura){
				$sql='UPDATE lecturastbl SET
	                calref1=:calref1,
	                calref2=:calref2,
	                calref3=:calref3,
	                calini1=:calini1,
	                calini2=:calini2,
	                calini3=:calini3,
	                calfin1=:calfin1,
	                calfin2=:calfin2,
	                calfin3=:calfin3,
	                notas=:notas
	                WHERE id=:id';
				$s=$pdo->prepare($sql);
				$s->bindValue(':id', $lectura['id']);
			}else{
				$sql='INSERT INTO lecturastbl SET
	                bitacoraeqidfk=:bitacoraeqidfk,
	                paramnum=:paramnum,
	                calref1=:calref1,
	                calref2=:calref2,
	                calref3=:calref3,
	                calini1=:calini1,
	                calini2=:calini2,
	                calini3=:calini3,
	                calfin1=:calfin1,
	                calfin2=:calfin2,
	                calfin3=:calfin3,
	                notas=:notas';
				$s=$pdo->prepare($sql);
				$s->bindValue(':bitacoraeqidfk', $bitacoraeqid);
				$s->bindValue(':paramnum', $key+1);
			}
			$s->bindValue(':calref1', $value['calref1']);
			$s->bindValue(':calref2', $value['calref2']);
			$s->bindValue(':calref3', $value['calref3']);
			$s->bindValue(':calini1', $value['calini1']);
			$s->bindValue(':calini2', $value['calini2']);
			$s->bindValue(':calini3', $value['calini3']);
			$s->bindValue(':calfin1', $value['calfin1']);
			$s->bindValue(':calfin2', $value['calfin2']);
			$s->bindValue(':calfin3', $value['calfin3']);
			$s->bindValue(':notas', $value['notas']);
			$s->execute();
		}
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error insertando el equipo. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}

}

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
$id = (isset($_POST['id']))? $_POST['id'] : $_SESSION['bitacoraid'];
try{
	$sql='SELECT * FROM bitacoraeqtbl WHERE bitacoraidfk=:bitacoraidfk';
	$s=$pdo->prepare($sql);
	$s->bindValue(':bitacoraidfk', $id);
	$s->execute();
	$equiposel = $s->fetchAll(PDO::FETCH_ASSOC);

	//var_dump($equiposel);

	foreach ($equiposel as $key => $value) {
		$sql='SELECT id, inventario, descripcion FROM equipostbl WHERE id=:equipoidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $value['equipoidfk']);
		$s->execute();
		$equiposel[$key] = $s->fetch(PDO::FETCH_ASSOC);

		$sql='SELECT * FROM eqparametrostbl WHERE equipoidfk=:equipoidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':equipoidfk', $value['equipoidfk']);
		$s->execute();
		$equiposel[$key]['parametros'] = $s->fetchAll(PDO::FETCH_ASSOC);
	}

	$sql='SELECT equipostbl.*
		FROM equipostbl
		WHERE estado = "Almacen"';
	$s=$pdo->prepare($sql);
	$s->execute();
	$equipos = $s->fetchAll(PDO::FETCH_ASSOC);

	$sql='SELECT ot, fechainicio FROM bitacoratbl WHERE id=:id';
	$s=$pdo->prepare($sql);
	$s->bindValue(':id', $id);
	$s->execute();
	$bitacora = $s->fetch(PDO::FETCH_ASSOC);
}catch (PDOException $e){
	$mensaje='Hubo un error al tratar de obtener los equipos. Favor de intentar nuevamente. '.$e;
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
	exit();
}
$pestanapag = 'Equipos de la bitacora';
$titulopagina = 'Equipos de la orden '.$bitacora['ot'];
$boton = 'Agregar';
include 'formaequipos.html.php';
exit();
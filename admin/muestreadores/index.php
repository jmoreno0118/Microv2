<?php
include_once '../../conf.php';
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

$estudios = array('Iluminacion',
				'Nivel sonoro equivalente',
			 	'Dosis de ruido',
			 	'temperaturas extremas/abatidas',
			 	'Radiaciones NO ionizantes',
			 	'Vibraciones mano-brazo',
			 	'Vibraciones cuerpo completo',
			 	'Radiaciones ionizantes',
			 	'NOM 001',
			 	'NOM 002',
			 	'NOM 003',
			 	'Fuentes fijas',
			 	'Ruido periferico',
			 	'Suelos',
			 	'CRETIB'
 );

$estudiossig = array('Iluminacion',
				'Nivel sonoro equivalente',
			 	'Dosis de ruido',
			 	'temperaturas extremas/abatidas',
			 	'Radiaciones NO ionizantes',
			 	'Vibraciones mano-brazo',
			 	'Vibraciones cuerpo completo',
			 	'Radiaciones ionizantes',
			 	'NOM 001',
			 	'NOM 002',
			 	'NOM 003',
			 	'Fuentes fijas',
			 	'Ruido periferico',
			 	'Suelos',
			 	'CRETIB',
			 	'Laboratorio'
 );

/**************************************************************************************************/
/* Salvar muestreador */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar'){
	include direction.functions.'conectadb.inc.php';
	$id = $_POST['id'];
	try{
		$pdo->beginTransaction();

        borrarEstudios($id);

        if(isset($_POST['estudiosmuestreador'])){
        	insertarEstudios($id, $_POST['estudiosmuestreador']);
		}

        if(isset($_POST['estudiossignatarios'])){
	        insertarEstudiosSignatario($id, $_POST['estudiossignatarios']);
    	}

		$pdo->commit();
	}catch (PDOException $e){
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de obtener los muestreadores. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
	verMuestreadores();
}

/**************************************************************************************************/
/* Editar muestreador */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar'){
	include direction.functions.'conectadb.inc.php';
	$pestanapag='Editar muestreador';
	$boton='Salvar';
	$id = $_POST['id'];
	try{
		$sql='SELECT * FROM usuariostbl
				WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$valores = $s->fetch();

		$sql='SELECT actividfk FROM usuarioactivtbl
				WHERE usuarioidfk=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $value) {
			$valores['permisos'][] = $value['actividfk'];
		}

		$sql='SELECT estudio FROM usuarioestudiostbl
				WHERE usuarioidfk=:id AND usuariotipo="M"';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $value) {
			$valores['estudiosmuestreador'][] = $value['estudio'];
		}
		//$valores['estudiosmuestreador'] = $s->fetchAll(PDO::FETCH_ASSOC);
		
		$sql='SELECT estudio FROM usuarioestudiostbl
				WHERE usuarioidfk=:id AND usuariotipo="S"';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		foreach ($s->fetchAll(PDO::FETCH_ASSOC) as $value) {
			$valores['estudiossignatarios'][] = $value['estudio'];
		}
		//$valores['estudiossignatarios'] = $s->fetchAll(PDO::FETCH_ASSOC);

	}catch (PDOException $e){
		$mensaje='Hubo un error al tratar de obtener los datos del muestreador. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
	$titulopagina='Editar '.$valores['nombre'].' '.$valores['apellido'];
	include 'formacapturar.html.php';
	exit();
}

/**************************************************************************************************/
/* Borrar muestreador */
/**************************************************************************************************/
/*if(isset($_POST['accion']) and $_POST['accion']=='Borrar'){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id = $_POST['id'];
	try{
		$sql='SELECT * FROM usuariostbl
				WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $id);
		$s->execute();
		$valores = $s->fetch();

		$nombre = $valores['nombre'];
		$ap = $valores['ap'];
		$am = $valores['am'];

	}catch (PDOException $e){
		$mensaje='Hubo un error al tratar de obtener los datos del muestreador. Favor de intentar nuevamente. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	include 'formaconfirma.html.php';
	exit();
}

/**************************************************************************************************/
/* Confirmar borrado del muestreador */
/**************************************************************************************************/
/*if(isset($_POST['accion']) and $_POST['accion']=='Continuar borrando'){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id = $_POST['id'];
	try{
		$sql='UPDATE usuariostbl SET
              estado=:estado
              WHERE id=:id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':estado', 0);
        $s->bindValue(':id', $id);
        $s->execute();
	}catch (PDOException $e){
		$mensaje='Hubo un error al tratar de obtener los datos del muestreador. Favor de intentar nuevamente. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	verMuestreadores();
}*/

/**************************************************************************************************/
/* Ir a archivos de un muestreador */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Firma')
{
	$_SESSION['post'] = $_POST;
	header('Location: '.url.'admin/muestreadores/archivo');
	exit();
}

/**************************************************************************************************/
/* Acción por default */
/**************************************************************************************************/
verMuestreadores();

/**************************************************************************************************/
/* Función para ver tabla de muestreadores */
/**************************************************************************************************/
function verMuestreadores(){
	include direction.functions.'conectadb.inc.php';
	try{

		if(usuarioConPermiso('Supervisor')){
			$sql='SELECT * FROM usuariostbl
				INNER JOIN usuarioactivtbl ON usuariostbl.id = usuarioactivtbl.usuarioidfk
				WHERE estado = 1 AND (actividfk = "Muestreador" OR actividfk = "Signatario")
				GROUP BY usuariostbl.id';
			$s=$pdo->prepare($sql);
			$s->execute();
			$muestreadores = $s->fetchAll();
		}else{
			$sql='SELECT rep.* FROM 
					(SELECT usuariostbl.* FROM usuariostbl
					INNER JOIN usuarioreptbl ON usuariostbl.id = usuarioreptbl.usuarioidfk
					INNER JOIN (SELECT usuarioreptbl.representanteidfk as "id"
								FROM usuarioreptbl
								INNER JOIN usuariostbl  ON usuariostbl.id = usuarioreptbl.usuarioidfk
								WHERE usuariostbl.usuario=:usuario) s ON usuarioreptbl.representanteidfk = s.id) AS rep
					INNER JOIN 
					(SELECT usuariostbl.id FROM usuariostbl
					INNER JOIN usuarioactivtbl ON usuariostbl.id = usuarioactivtbl.usuarioidfk
					WHERE (actividfk = "Muestreador" OR actividfk = "Signatario")) AS act
					ON rep.id = act.id
					WHERe estado = 1
					GROUP BY id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':usuario', $_SESSION['usuario']);
			$s->execute();
			$muestreadores = $s->fetchAll();
		}
		
	}catch (PDOException $e){
		$mensaje='Hubo un error al tratar de obtener los muestreadores. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
	include 'formamuestreadores.html.php';
	exit();	
}

/**************************************************************************************************/
/* Función para borrar estudios de un muestreador */
/**************************************************************************************************/
function borrarEstudios($id){
	global $pdo; 
	$sql='DELETE FROM usuarioestudiostbl WHERE usuarioidfk = :id';
	$s=$pdo->prepare($sql);
	$s->bindValue(':id', $id);
	$s->execute();
}

/**************************************************************************************************/
/* Función para insertar estudios del signatario */
/**************************************************************************************************/
function insertarEstudiosSignatario($id, $estudios){
	global $pdo; 
	foreach ($estudios as $key => $value) {
    	$sql='INSERT INTO usuarioestudiostbl SET
			usuarioidfk=:id,
			estudio=:estudio,
			usuariotipo="S"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $id);
        $s->bindValue(':estudio', $value);
        $s->execute();
    }
}

/**************************************************************************************************/
/* Función para insertar estudios del muestreador */
/**************************************************************************************************/
function insertarEstudios($id, $estudios){
	global $pdo; 
	foreach ($estudios as $key => $value) {
        	$sql='INSERT INTO usuarioestudiostbl SET
				usuarioidfk=:id,
				estudio=:estudio,
				usuariotipo="M"';
	        $s=$pdo->prepare($sql);
	        $s->bindValue(':id', $id);
	        $s->bindValue(':estudio', $value);
	        $s->execute();
        }
}

?>
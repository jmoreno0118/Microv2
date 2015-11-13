<?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/funcioneshig.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';

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
/* Capturar reconocimientos iniciales de una orden de trabajo */
/**************************************************************************************************/
  if(isset($_GET['accion']) and $_GET['accion']=='capturarci')
  {
  	fijarAccionUrl('capturarci');

    $pestanapag='Agrega Reconocimiento Inicial';
    $titulopagina='Agregar un nuevo reconocimiento inicial';
    $boton = 'guardarrci';
    $idot=$_GET['idot'];
    $signatarios = getSignatarios('Iluminacion');
    if(isset($_POST['valores'])){
		$valores = json_decode($_POST['valores'],TRUE);
	}else{
		$valores = array("empresagiro" => getEGiro($idot),
						"signatario" => getSignatario($idot));
	}
	if(isset($_POST['puestos'])){
		$puestos = json_decode($_POST['puestos'],TRUE);
	}
  	include 'formacapturarci.html.php';
  	exit();
  }

/**************************************************************************************************/
/* Guarda reconocimiento inicial de una orden de trabajo */
/**************************************************************************************************/
  if(isset($_POST['accion']) and $_POST['accion']=='guardarrci')
  {
  	/*$mensaje='Error Forzado 1.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();*/

	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$idot=$_POST['idot'];
	try
	{
		$pdo->beginTransaction();

		setSignatario($_POST['signatario']);

		setGiroEmpresa($_POST['idot'], $_POST['empresagiro']);

		$sql='INSERT INTO recsilumtbl SET
			 ordenidfk=:ordenid,
		     fecha=:fecha,
			 largo=:largo,
			 ancho=:ancho,
			 alto=:alto,
			 tipolampara=:tipolampara,
			 potencialamp=:potencialamp,
			 numlamp=:numlamp,
			 alturalamp=:alturalamp,
			 techocolor=:techocolor,
			 paredcolor=:paredcolor,
			 pisocolor=:pisocolor,
			 influencia=:influencia,
			 percepcion=:percepcion,
			 mantenimiento=:mantenimiento';
		$s=$pdo->prepare($sql);
		$s->bindValue(':ordenid',$idot);
		$s->bindValue(':fecha',$_POST['fecha']);
		$s->bindValue(':largo',$_POST['largo']);
		$s->bindValue(':ancho',$_POST['ancho']);
		$s->bindValue(':alto',$_POST['alto']);
		$s->bindValue(':tipolampara',$_POST['tipolampara']);
		$s->bindValue(':potencialamp',$_POST['potencialamp']);
		$s->bindValue(':numlamp',$_POST['numlamp']);
		$s->bindValue(':alturalamp',$_POST['alturalamp']);
		$s->bindValue(':techocolor',$_POST['techocolor']);
		$s->bindValue(':paredcolor',$_POST['paredcolor']);
		$s->bindValue(':pisocolor',$_POST['pisocolor']);
		$s->bindValue(':influencia',$_POST['influencia']);
		$s->bindValue(':percepcion',$_POST['percepcion']);
		$s->bindValue(':mantenimiento',$_POST['mantenimiento']);
		$s->execute();
		$rcid=$pdo->lastInsertId();

		$sql='INSERT INTO deptostbl SET
		     departamento=:departamento,
			 area=:area,
			 descriproceso=:descriproceso';
		$s=$pdo->prepare($sql);
		$s->bindValue(':departamento',$_POST['departamento']);
		$s->bindValue(':area',$_POST['area']);
		$s->bindValue(':descriproceso',$_POST['descriproceso']);
		$s->execute();
		$deptoid=$pdo->lastInsertId();

		$sql='INSERT INTO deptorecilumtbl SET
		     deptoidfk=:deptoidfk,
			 recilumidfk=:recilumidfk';
		$s=$pdo->prepare($sql);
		$s->bindValue(':deptoidfk', $deptoid);
		$s->bindValue(':recilumidfk',$rcid);
		$s->execute();

		foreach ($_POST["puestos"] as $key => $value) {
			if($value["puesto"] != "" && $value["numtrabajadores"] != "" && $value["actividades"]!="")
			{
				$sql='INSERT INTO descripuestostbl SET
				     deptoidfk=:deptoidfk,
					 puesto=:puesto,
					 numtrabajadores=:numtrabajadores,
					 actividades=:actividades';
				$s=$pdo->prepare($sql);
				$s->bindValue(':deptoidfk', $deptoid);
				$s->bindValue(':puesto', $value["puesto"]);
				$s->bindValue(':numtrabajadores', $value["numtrabajadores"]);
				$s->bindValue(':actividades', $value["actividades"]);
				$s->execute();
			}
		}
		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de insertar el reconocimiento. Favor de intentar nuevamente.'.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$pestanapag='Agrega Reconocimiento Inicial';
	$titulopagina='Agregar un nuevo reconocimiento inicial';
	$boton = 'guardarrci';
	$signatarios = getSignatarios('Iluminacion');
	$valores = array("departamento" => $_POST["departamento"],
					"area" => $_POST["area"]);
	include 'formacapturarci.html.php';
	exit();
  }

/**************************************************************************************************/
/* Editar reconocimiento inicial de una orden de trabajo */
/**************************************************************************************************/
  if(isset($_POST['accion']) and $_POST['accion']=='editarci')
  {
  	fijarAccionUrl('editarci');

	$id = $_POST['id'];
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';

	if(isset($_POST['valores'])){
		$valores = json_decode($_POST['valores'],TRUE);
	}else{
		try{
			$sql='SELECT * FROM recsilumtbl
				   INNER JOIN deptorecilumtbl ON recsilumtbl.id=deptorecilumtbl.deptoidfk
				   INNER JOIN deptostbl ON deptorecilumtbl.deptoidfk=deptostbl.id
				   WHERE recsilumtbl.id = :id';
			$s=$pdo->prepare($sql); 
			$s->bindValue(':id',$_POST['id']);
			$s->execute();
		}catch (PDOException $e){
			$mensaje='Hubo un error extrayendo la información de reconocimiento inicial.';
			include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
			exit();
		}

		foreach ($s as $linea){
			$valores = array("empresagiro" => getEGiro( $_SESSION['idot'] ),
						"fecha" => $linea["fecha"],
						"departamento" => $linea["departamento"],
						"area" => $linea["area"],
						"descriproceso" => $linea["descriproceso"],
						"largo" => $linea["largo"],
						"ancho" => $linea["ancho"],
						"alto" => $linea["alto"],
						"tipolampara" => $linea["tipolampara"],
						"potencialamp" => $linea["potencialamp"],
						"numlamp" => $linea["numlamp"],
						"alturalamp" => $linea["alturalamp"],
						"techocolor" => $linea["techocolor"],
						"paredcolor" => $linea["paredcolor"],
						"pisocolor" => $linea["pisocolor"],
						"influencia" => $linea["influencia"],
						"percepcion" => $linea["percepcion"],
						"mantenimiento" => $linea["mantenimiento"],
						"nombresignatario" => getNombreSignatario($linea["ordenidfk"]),
                 		"signatario" => getSignatario($linea["ordenidfk"]));
			$idot=$linea["ordenidfk"];
		}
	}

	if(isset($_POST['puestos'])){
		$puestos = json_decode($_POST['puestos'],TRUE);
	}else{
		try{
			$sql='SELECT descripuestostbl.puesto, descripuestostbl.numtrabajadores, descripuestostbl.actividades
					FROM descripuestostbl
					INNER JOIN deptostbl ON descripuestostbl.deptoidfk = deptostbl.id
					INNER JOIN deptorecilumtbl ON deptostbl.id = deptorecilumtbl.deptoidfk
					WHERE deptorecilumtbl.recilumidfk = :id';
			$s=$pdo->prepare($sql); 
			$s->bindValue(':id',$_POST['id']);
			$s->execute();
		}catch (PDOException $e){
			$mensaje='Hubo un error extrayendo la información de reconocimiento inicial.';
			include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
			exit();
		}

		foreach($s as $linea){
			$puestos[] = array("puesto" => $linea["puesto"],
								"numtrabajadores" => $linea["numtrabajadores"],
								"actividades" => $linea["actividades"]);
		}
	}
	$signatarios = getSignatarios('Iluminacion');
	$pestanapag='Editar Reconocimiento Inicial';
	$titulopagina='Editar reconocimiento inicial';
	$boton = 'salvarci';
	include 'formacapturarci.html.php';
	exit();
  }

/**************************************************************************************************/
/* Guardar la edición de un reconocimiento inicial */
/**************************************************************************************************/
  if(isset($_POST['accion']) AND ($_POST['accion']=='salvarci' OR $_POST['accion']=='Continua cambio'))
  {
  	/*$mensaje='Error Forzado 2.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();*/

	$id = $_POST['id'];
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$pestanapag='Editar Reconocimiento Inicial';
	$titulopagina='Editar reconocimiento inicial';
	$boton = 'salvarci';


	if($_POST['accion']=='salvarci'){
		try
		{
			$sql='SELECT influencia FROM recsilumtbl where id=:id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':id',$id);
			$s->execute();
		}
		catch(PDOException $e)
		{
			$mensaje='Hubo un error al tratar de editar el reconocimiento inicial. Favor de intentar nuevamente.';
			include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
			exit();	
		}

		$influencianterior = $s->fetch();
		if ($influencianterior['influencia'] == TRUE AND $_POST['influencia'] == FALSE){
			desglosapost($_POST);
			include 'formaconfirmacambiorci.html.php';
			exit();
		}
	}

	if($_POST['accion']=='Continua cambio'){
		try
		{
			$pdo->beginTransaction(); 
			$sql='SELECT puntoidfk FROM puntorecilumtbl
					WHERE recilumidfk = :id';
			$s=$pdo->prepare($sql);
			$s->bindValue(':id',$_POST['id']);
			$s->execute();
			foreach ($s as $linea)
			{
				$sql='DELETE FROM medsilumtbl
					WHERE puntoidfk=:puntoid
					ORDER BY id DESC LIMIT 2';
				$s=$pdo->prepare($sql);
				$s->bindValue(':puntoid',$linea['puntoidfk']);
				$s->execute();
			}		
			$pdo->commit();
		}
		catch (PDOException $e)
		{
			$pdo->rollback();
			$mensaje='Hubo un error al seleccionar las mediciones que se borrarán. '.$e;
			include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
			exit();	
		}
	}

	try
	{
		setGiroEmpresa($_POST['idot'], $_POST['empresagiro']);

		if(strcmp($_POST['signatario'], '') !== 0)
		{
		setSignatario($_POST['signatario']);
		}

		$sql='UPDATE recsilumtbl SET
			fecha=:fecha,
			largo=:largo,
			ancho=:ancho,
			alto=:alto,
			tipolampara=:tipolampara,
			potencialamp=:potencialamp,
			numlamp=:numlamp,
			alturalamp=:alturalamp,
			techocolor=:techocolor,
			paredcolor=:paredcolor,
			pisocolor=:pisocolor,
			influencia=:influencia,
			percepcion=:percepcion,
			mantenimiento=:mantenimiento
			WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->bindValue(':fecha',$_POST['fecha']);
		$s->bindValue(':largo',$_POST['largo']);
		$s->bindValue(':ancho',$_POST['ancho']);
		$s->bindValue(':alto',$_POST['alto']);
		$s->bindValue(':tipolampara',$_POST['tipolampara']);
		$s->bindValue(':potencialamp',$_POST['potencialamp']);
		$s->bindValue(':numlamp',$_POST['numlamp']);
		$s->bindValue(':alturalamp',$_POST['alturalamp']);
		$s->bindValue(':techocolor',$_POST['techocolor']);
		$s->bindValue(':paredcolor',$_POST['paredcolor']);
		$s->bindValue(':pisocolor',$_POST['pisocolor']);
		$s->bindValue(':influencia',$_POST['influencia']);
		$s->bindValue(':percepcion',$_POST['percepcion']);
		$s->bindValue(':mantenimiento',$_POST['mantenimiento']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de editar el reconocimiento inicial. Favor de intentar nuevamente.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}

	try
	{
		$sql='UPDATE deptostbl 
			INNER JOIN deptorecilumtbl ON deptostbl.id=deptorecilumtbl.deptoidfk
			SET
			departamento=:departamento,
			area=:area,
			descriproceso=:descriproceso
			WHERE deptorecilumtbl.recilumidfk=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->bindValue(':departamento',$_POST['departamento']);
		$s->bindValue(':area',$_POST['area']);
		$s->bindValue(':descriproceso',$_POST['descriproceso']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de editar el departamento. Favor de intentar nuevamente.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	
	try
	{
		$sql='DELETE descripuestostbl FROM descripuestostbl
			INNER JOIN deptostbl ON descripuestostbl.deptoidfk = deptostbl.id
			INNER JOIN deptorecilumtbl ON deptostbl.id = deptorecilumtbl.deptoidfk
			WHERE deptorecilumtbl.recilumidfk = :id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de eliminar los puestos. Favor de intentar nuevamente.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}

	try
	{
		$sql='SELECT deptostbl.id FROM deptostbl 
		INNER JOIN deptorecilumtbl ON deptostbl.id = deptorecilumtbl.deptoidfk WHERE deptorecilumtbl.recilumidfk = :id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
		$resultado=$s->fetch();
		foreach ($_POST["puestos"] as $key => $value) {
			if($value["puesto"] != "" && $value["numtrabajadores"] != "" && $value["actividades"]!= "")
			{
				$sql='INSERT INTO descripuestostbl SET
					deptoidfk=:deptoidfk,
					puesto=:puesto,
					numtrabajadores=:numtrabajadores,
					actividades=:actividades';
				$s=$pdo->prepare($sql);
				$s->bindValue(':deptoidfk', $resultado["id"]);
				$s->bindValue(':puesto', $value["puesto"]);
				$s->bindValue(':numtrabajadores', $value["numtrabajadores"]);
				$s->bindValue(':actividades', $value["actividades"]);
				$s->execute();
			}
		}
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error al tratar de agregar los puestos. Favor de intentar nuevamente.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$idot=$_POST['idot'];
	verRecs($idot);
	exit();
  }

/**************************************************************************************************/
/* Borrar un reconocimiento inicial de una orden de trabajo */
/**************************************************************************************************/
  if (isset($_POST['accion']) and $_POST['accion']=='borrarci')
  {
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	$id=$_POST['id'];
	try
	{
		$sql='SELECT COUNT(*) as Puntos FROM puntorecilumtbl WHERE recilumidfk=:id';
		$s= $pdo->prepare($sql);
		$s->bindValue(':id',$id); 
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo hacer el conteo de puntos'.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$cuenta = $s->fetch();
	
	if($cuenta["Puntos"] > 0){
		$mensaje='Este reconocimiento inicial no puede ser borrado ya que tiene puntos. ';
		$errorlink = 'http://'.$_SERVER['HTTP_HOST'].'/reportes/iluminacion';
		$errornav = 'Volver a iluminación';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}else{
		$sql='SELECT recsilumtbl.fecha, deptostbl.departamento, deptostbl.area
		      FROM recsilumtbl
			  INNER JOIN deptorecilumtbl ON recsilumtbl.id = deptorecilumtbl.recilumidfk
			  INNER JOIN deptostbl ON deptorecilumtbl.deptoidfk = deptostbl.id
			  WHERE recsilumtbl.id = :id';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id',$id);
		$s->execute();
		$resultado=$s->fetch();

		$fecha=$resultado['fecha'];
		$departamento=$resultado['departamento'];
		$area=$resultado['area'];
		include 'formaconfirmarci.html.php';
		exit();
	  }
	}

/**************************************************************************************************/
/* Confirmación de borrado de un reconocimiento inicial */
/**************************************************************************************************/
  if (isset($_POST['accion']) and $_POST['accion']=='Continuar borrando')
  {
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try
	{
		// recupero el num. de ot para regresar a los reconocimientos
		$pdo->beginTransaction();
		$sql='SELECT id, ordenidfk FROM recsilumtbl
		  		WHERE  id=:id';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
		$resultado=$s->fetch();
		$ot=$resultado['ordenidfk'];

		$sql='DELETE FROM descripuestostbl WHERE deptoidfk IN (SELECT deptoidfk FROM deptorecilumtbl WHERE recilumidfk = :id)';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();

		$sql='DELETE FROM deptostbl WHERE id IN (SELECT deptoidfk FROM deptorecilumtbl WHERE recilumidfk = :id)';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();

		$sql='DELETE FROM deptorecilumtbl WHERE recilumidfk=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();

		$sql='DELETE FROM recsilumtbl WHERE id=:id';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['id']);
		$s->execute();
		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error borrando el reconocimiento. Intente de nuevo. '.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	verRecs($ot);
  }

/**************************************************************************************************/
/*  Se cancela cambio de influencia */
/**************************************************************************************************/
 if(isset($_POST['accion']) and $_POST['accion']=='Cancela cambio')
 {
   $pestanapag='Editar Reconocimiento Inicial';
   $titulopagina='Editar reconocimiento inicial';
   $boton = 'salvarci';
   $puestos=array();
   $id = $_POST['id'];
   $idot=$_POST['idot'];
   $signatarios = getSignatarios('Iluminacion');
   foreach($_POST['puestos'] as $linea){
   	$puestos[] = array("puesto" => $linea["puesto"],
   					   "numtrabajadores" => $linea["numtrabajadores"],
   					    "actividades" => $linea["actividades"]);
   }
   $valores = array("fecha" => $_POST["fecha"],
					"departamento" => $_POST["departamento"],
					"area" => $_POST["area"],
					"descriproceso" => $_POST["descriproceso"],
					"largo" => $_POST["largo"],
					"ancho" => $_POST["ancho"],
					"alto" => $_POST["alto"],
					"tipolampara" => $_POST["tipolampara"],
					"potencialamp" => $_POST["potencialamp"],
					"numlamp" => $_POST["numlamp"],
					"alturalamp" => $_POST["alturalamp"],
					"techocolor" => $_POST["techocolor"],
					"paredcolor" => $_POST["paredcolor"],
					"pisocolor" => $_POST["pisocolor"],
					"influencia" => 1,
					"percepcion" => $_POST["percepcion"],
					"mantenimiento" => $_POST["mantenimiento"],
					"nombresignatario" => $_POST["nombresignatario"],
                 	"signatario" => $_POST["signatario"]);
   include 'formacapturarci.html.php';
   exit();
 }
 
/**************************************************************************************************/
/* Ir a la opcion de puntos */
/**************************************************************************************************/
  if((isset($_POST['accion']) and $_POST['accion']=='puntos'))
  {
	$_SESSION['idrci']=$_POST['id'];
	header('Location: http://'.$_SERVER['HTTP_HOST'].str_replace('?','',$_SERVER['REQUEST_URI']).'puntos');
    exit();
  }

/**************************************************************************************************/
/* Dar por terminada una orden */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Enviar')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  if(isset($_POST['terminada']))
  {
      try
      {
        $sql='UPDATE estudiostbl SET
          fechafin = CURDATE()
          WHERE ordenidfk = :id AND nombre = "Iluminacion"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id',$_POST['ot']);
        $s->execute();
      }
      catch(PDOException $e)
      {
        $mensaje='Hubo un error al tratar de terminar la orden. Intentar nuevamente y avisar de este error a sistemas.';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit(); 
      }
  }else{
      try
      {
        $sql='UPDATE estudiostbl SET
          fechafin = NULL,
          fecharevision  = NULL
          WHERE ordenidfk = :id AND nombre = "Iluminacion"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id',$_POST['ot']);
        $s->execute();
      }
      catch(PDOException $e)
      {
        $mensaje='Hubo un error al tratar de terminar la orden. Intentar nuevamente y avisar de este error a sistemas.';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit(); 
      }
  }
  $_SESSION['ot'] = $_POST['ot'];
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/iluminacion/');
  exit();
}

/**************************************************************************************************/
/* Dar visto bueno a una orden */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Vo. Bo.')
{
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try
	{
		if (isset($_POST['comentario']) AND $_POST['comentario'] !== '')
		{
			setObservacion($pdo, 'Iluminacion', $_POST['ot'], $_POST['comentario']);
		}

		$sql='UPDATE estudiostbl SET
        fecharevision = CURDATE()
        WHERE ordenidfk = :id AND nombre = "Iluminacion"';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id',$_POST['ot']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$mensaje='Hubo un error al tratar de terminar la orden. Intentar nuevamente y avisar de este error a sistemas.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$_SESSION['ot'] = $_POST['ot'];
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/iluminacion/');
	exit();
}

/**************************************************************************************************/
/* Poner comentarios a una orden */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Comentar y Regresar Orden')
{
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try
	{
		if (isset($_POST['comentario']) AND $_POST['comentario'] !== '')
		{
			setObservacion($pdo, 'Iluminacion', $_POST['ot'], $_POST['comentario']);
		}

		$sql='UPDATE estudiostbl SET
            fechafin = NULL,
            fecharevision  = NULL
            WHERE ordenidfk = :id AND nombre = "Iluminacion"';
		$s=$pdo->prepare($sql);
		$s->bindValue(':id', $_POST['ot']);
		$s->execute();
	}
	catch(PDOException $e)
	{
		$mensaje='Hubo un error al tratar de terminar la orden. Intentar nuevamente y avisar de este error a sistemas.'.$e;
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}
	$_SESSION['ot'] = $_POST['ot'];
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/iluminacion/');
	exit();
}

/**************************************************************************************************/
/* Acción por defualt, llevar a búsqueda de ordenes */
/**************************************************************************************************/
include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
if(isset($_SESSION['idot']) and isset($_SESSION['quien']) and $_SESSION['quien']=='Iluminacion'){
	$idot=$_SESSION['idot'];
	if (isset($_SESSION['idrci']))
	{
		unset($_SESSION['idrci']);
	}
}
else {
	header('Location: http://'.$_SERVER['HTTP_HOST'].str_replace('rci/','',$_SERVER['REQUEST_URI']));
}
$ot=otdeordenes($idot);
$recsini=verRecs($idot);
include 'formarci.html.php';
exit();

/**************************************************************************************************/
/* Función para ver reconocimientos iniciales de una orden de trabajo */
/**************************************************************************************************/
function verRecs($id = ""){
	global $pdo;
	$idot=$id;
	$ot=otdeordenes($id);
	try   
	{
		$sql='SELECT ot, fechafin
			FROM ordenestbl
			INNER JOIN estudiostbl ON ordenestbl.id = estudiostbl.ordenidfk
			WHERE ordenestbl.id = :id AND estudiostbl.nombre = "Iluminacion"';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id', $idot);
		$s->execute();
		$nombreot = $s->fetch();


		$sql='SELECT recsilumtbl.id, deptostbl.departamento, deptostbl.area, deptostbl.descriproceso
				FROM recsilumtbl
				INNER JOIN ordenestbl ON ordenidfk=ordenestbl.id
				INNER JOIN deptorecilumtbl ON recsilumtbl.id=deptorecilumtbl.deptoidfk
				INNER JOIN deptostbl ON deptorecilumtbl.deptoidfk=deptostbl.id
				WHERE recsilumtbl.ordenidfk = :id';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id',$id);
		$s->execute();

		$sql='SELECT id, observacion, fecha, supervisor
				FROM observacionestbl
				WHERE ordenesidfk = :id
				AND estudio = "Iluminacion"
				ORDER BY id DESC';
		$c=$pdo->prepare($sql); 
		$c->bindValue(':id', $idot);
		$c->execute();
		$comentarios = $c->fetchAll();
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error extrayendo la lista de reconocimientos iniciales.';
		include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
		exit();
	}

	foreach ($s as $linea)
	{
		$recsini[]=array('id'=>$linea['id'],
						'departamento'=>$linea['departamento'],
						'area'=>$linea['area'],
						'descriproceso'=>$linea['descriproceso']);
	}

	include 'formarci.html.php';
	exit();
}

/**************************************************************************************************/
/* Acumulacion del post de editar, para pasarlo a la pantalla del cambio de influencia */
/**************************************************************************************************/
function desglosapost($post="")
{ 
	global $campos, $contenidos, $puestos, $numtrabajadores, $actividades;
	$campos=array();
	$contenidos=array();
	$puestos=array();
	$numtrabajadores=array();
	$actividades=array();
	foreach ($post as $campo=>$contenido)
	{
		if ($campo <> 'puestos' AND $campo <> 'accion' AND $campo <> 'boton')
		{
			$campos[]=$campo;
			$contenidos[]=$contenido;
		}
	}

	foreach ($post['puestos'] as $descrip)
	{
		if ($descrip['puesto'] != "")
		{
			$puestos[] = $descrip['puesto'];
			$numtrabajadores[] = $descrip['numtrabajadores'];
			$actividades[] = $descrip['actividades'];
		}
	}
}

/**************************************************************************************************/
/* Función para guardar el giro de la empresa */
/**************************************************************************************************/
function setGiroEmpresa($id, $giro){
    global $pdo;
    $sql='UPDATE clientestbl SET
          Giro_Empresa=:empresagiro
          WHERE Numero_Cliente = (SELECT clienteidfk
                                  FROM ordenestbl
                                  WHERE id = :id)';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id', $id);
    $s->bindValue(':empresagiro', $giro);
    $s->execute();
}

/**************************************************************************************************/
/* Función para obtener el giro de la empresa */
/**************************************************************************************************/
function getEGiro($ot){
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
	try   
	{
		$sql='SELECT Giro_Empresa
			FROM clientestbl
			WHERE Numero_Cliente = (SELECT clienteidfk
									FROM ordenestbl
									WHERE id = :id)';
		$s=$pdo->prepare($sql); 
		$s->bindValue(':id',$ot);
		$s->execute();
		$e = $s->fetch();
		return $e['Giro_Empresa'];
	}
  catch (PDOException $e)
  {
		return "";
	}
}
?>
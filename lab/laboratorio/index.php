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
/* Confirmar borrado de un parametro */
/**************************************************************************************************/
if(isset($_POST['accion']) AND $_POST['accion']=='Crear')
{
	$norma = $_POST['norma'];
	fijarAccionUrl('Crear');
	include direction.functions.'conectadb.inc.php';
	if($norma =='NOM 001' OR $norma =='NOM 002' OR $norma =='NOM 003'){
		$acred = "Aguas";
	}
	$params = getParams($norma);

	include direction.functions.'conectadb.inc.php'; 
	try
	{
		$sql='SELECT * FROM aparametrostbl ORDER BY id DESC';
		$s= $pdo->prepare($sql);
		$s->execute();
	}
	catch (PDOException $e)
	{
		$mensaje='No se pudo extraer información del parametro.'.$e;
		throwError($mensaje);
	}

	$adicionales = array();
	foreach ($s->fetchAll() as $key => $value) {
		if( strcmp($value['clave'], "GYA") === 0 )
		{
			$parametros[$value['clave']][$value['id']] = 'Metodo: '.$value['metodo'].', Unidad: '.$value['unidades'].', MC: '.$value['LC'];
		}
		elseif( strcmp($value['clave'], "NMPCF-AR1") === 0 )
		{
			$coliformes = true;
			$parametros[$value['clave']][$value['id']] = 'Metodo: '.$value['metodo'].', Unidad: '.$value['unidades'].', MC: '.$value['LC'];
		}
		elseif( !in_array($value['clave'], array_keys($params) ) )
		{
			$adicionales[$value['id']] = 'Parametro: '.$value['parametro'].', Metodo: '.$value['metodo'].', Unidad: '.$value['unidades'].', LD: '.$value['LD'].', LC: '.$value['LC'];
		}
		else
		{
			$parametros[$value['clave']][$value['id']] = 'Metodo: '.$value['metodo'].', Unidad: '.$value['unidades'].', LD: '.$value['LD'].', LC: '.$value['LC'];
		}
	}

	if(isset($_POST['valores']))
  	{
		$valores = json_decode($_POST['valores'],TRUE);
	}

	$clientes = getClientes();
	$muestreadores = getMuestreadores($norma, "M");
	$signatarios = getMuestreadores($norma, "S");
	$acreditaciones = getAcreditaciones($acred);

	include 'formacapturar.html.php';
	exit();
}

/**************************************************************************************************/
/* Confirmar borrado de un parametro */
/**************************************************************************************************/
if(isset($_POST['accion']) AND $_POST['accion']=='Guardar' )
{
	/*$mensaje='Error Forzado 1.';
	include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
	exit();*/

	/*echo '<pre>';
	var_dump($_POST);
	echo '</pre>';
	exit;*/

	include direction.functions.'conectadb.inc.php';

	try
	{
		$pdo->beginTransaction();

		$acred = getAcreditacion($_POST['acreditacion']);

		$sql='INSERT INTO infomelabtbl SET
			requerido=:requerido,
			Numero_Cliente=:para,
			ot=:ot,
			idep=:idep,
			fecharecepcion=:fecharecepcion,
			fechamui=:fechamui,
			fechamuf=:fechamuf,
			fechainforme=:fechainforme,
			fechani=:fechani,
			fechanf=:fechanf,
			acreditacionidfk=:acreditacionidfk,
			acrednombre=:acrednombre,
			acredfecha=:acredfecha,
			acredestudio=:acredestudio';
		$s=$pdo->prepare($sql);
		$s->bindValue(':requerido',$_POST['requerido']);
		$s->bindValue(':para',$_POST['para']);
		$s->bindValue(':ot',$_POST['ot']);
		$s->bindValue(':idep',$_POST['idep']);
		$s->bindValue(':fecharecepcion',$_POST['fecharecepcion']);
		$s->bindValue(':fechamui',$_POST['fechamuestreo']);
		if( strcmp($_POST['fechamuestreofin'], '') !== 0 ){
			$s->bindValue(':fechamuf', $_POST['fechamuestreofin']);
		}else{
			$s->bindValue(':fechamuf', NULL, PDO::PARAM_INT);
		}
		$s->bindValue(':fechainforme',$_POST['fechainforme']);
		$s->bindValue(':fechani',$_POST['fechaanalisis']);
		if( strcmp($_POST['fechaanalisisfin'], '') !== 0 ){
			$s->bindValue(':fechanf', $_POST['fechaanalisisfin']);
		}else{
			$s->bindValue(':fechanf', NULL, PDO::PARAM_INT);
		}
		$s->bindValue(':acreditacionidfk',$acred['id']);
		$s->bindValue(':acrednombre',$acred['nombre']);
		$s->bindValue(':acredfecha',$acred['fecha']);
		$s->bindValue(':acredestudio',$acred['estudio']);
		$s->execute();
		$id=$pdo->lastInsertid();

		insertMS($id, $_POST['signatario'], 'S');

		insertMS($id, $_POST['muestreador'], 'M');

		insertGyaColiformes($id, $_POST['pgya'], $_POST['gyac'], $_POST['gya']);

		insertGyaColiformes($id, $_POST['pcoliformes'], $_POST['gyac'], $_POST['coliformes']);
		
		insertParametros($id, $_POST['parametro'], $_POST['parametros']);

		insertParametros($id, $_POST['parametro'], $_POST['adicionales']);

		$pdo->commit();
	}
	catch (PDOException $e)
	{
		$pdo->rollback();
		$mensaje='Hubo un error al tratar de agregar la orden. Favor de intentar nuevamente. '.$e;
		throwError($mensaje);
	}
}

/**************************************************************************************************/
/* Confirmar borrado de un parametro */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try
{
	$s=$pdo->prepare('SELECT norma
					FROM normastbl
					JOIN paramnormas ON normastbl.id = paramnormas.normaidfk
					GROUP BY norma'); 
    $s->execute();
}
catch(PDOException $e)
{
	$mensaje='Hubo un error extrayendo las normas.';
    throwError($mensaje);
}
foreach ($s->fetchAll() as $key => $value) {
	$normas[] = $value['norma'];
}
include 'formalab.html.php';
exit();

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function getClientes(){
	global $pdo;
	try
	{
		$resultados = $pdo->query('SELECT Numero_Cliente, Razon_Social FROM clientestbl WHERE Razon_Social <> "" ORDER BY Razon_Social');
	}
	catch (PDOException $e)
	{
		$mensaje='Hubo un error tratando de obtener la informacion de los clientes'.$e;
		throwError($mensaje);
	}  
	foreach ($resultados as $resultado)
	{
		$clientes[$resultado['Numero_Cliente']] = $resultado['Razon_Social'];
	}
	return $clientes;
}

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function getMuestreadores($estudio, $tipo){
	global $pdo;
    if($tipo === "S"){
    	$actividad = "Signatario";
    	$laboratorio = 'INNER JOIN (SELECT * FROM usuarioestudiostbl WHERE estudio = "Laboratorio") L ON usuariostbl.id = L.usuarioidfk';
    }elseif($tipo === "M"){
    	$actividad = "Muestreador";
    	$laboratorio = '';
    }

    try
    {
        $sql='SELECT usuariostbl.id, nombre, apellido
          FROM usuariostbl
          INNER JOIN usuarioestudiostbl ON usuariostbl.id = usuarioestudiostbl.usuarioidfk
          INNER JOIN usuarioactivtbl ON usuariostbl.id = usuarioactivtbl.usuarioidfk
          INNER JOIN usuarioreptbl ON usuariostbl.id = usuarioreptbl.usuarioidfk
          '.$laboratorio.'
          WHERE actividfk = "'.$actividad.'" AND estado = 1 AND usuarioestudiostbl.estudio = "'.$estudio.'" AND
          usuarioestudiostbl.usuariotipo = "'.$tipo.'"';
        $s=$pdo->prepare($sql); 
        $s->execute();
        $signatarios = '';
        foreach ($s as $value) {
          $signatarios[$value['id']] = $value['nombre'].' '.$value['apellido'];
        }
        return $signatarios;
    }
    catch (PDOException $e)
    {
      return "";
    }
}

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function insertMS($id, $clientes, $bandera){
	global $pdo;
	   if($bandera === "S"){
    	$tabla = "labsignatariostbl";
    }elseif($bandera === "M"){
    	$tabla = "labmuestreadorestbl";
    }

	foreach ($clientes as $key => $value) {
		if( strcmp($value, '') !== 0 ){
			$sql='INSERT INTO '.$tabla.' SET
				usuarioidfk=:usuarioidfk,
				informelabtbl=:informelabtbl';
			$s=$pdo->prepare($sql);
			$s->bindValue(':usuarioidfk', $value);
			$s->bindValue(':informelabtbl', $id);
			$s->execute();
		}
	}
}

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function insertGyaColiformes($id, $parametro, $gyac, $resultados){
	global $pdo;
	foreach ($resultados as $key => $value) {
		if( strcmp($gyac[$key]['muestranum'], '') !== 0 AND strcmp($value['resultado'], '') !== 0 AND strcmp($gyac[$key]['identificacion'], '') !== 0){
			$sql='INSERT INTO parainformetbl SET
				informeidfk=:informeidfk,
				parametroidfk=:parametroidfk,
				muestranum=:muestranum,
				resultado=:resultado,
				identificacion=:identificacion';
			$s=$pdo->prepare($sql);
			$s->bindValue(':informeidfk', $id);
			$s->bindValue(':parametroidfk', $parametro);
			$s->bindValue(':muestranum', $gyac[$key]['muestranum']);
			$s->bindValue(':resultado', $value['resultado']);
			$s->bindValue(':identificacion', $gyac[$key]['identificacion']);
			$s->execute();
		}
	}
}

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function insertParametros($id, $parametro, $parametros){
	global $pdo;
	foreach ($parametros as $key => $value) {
		if( strcmp($value['parametro'], '') !== 0 AND strcmp($value['resultado'], '') !== 0 ){
			$sql='INSERT INTO parainformetbl SET
				informeidfk=:informeidfk,
				parametroidfk=:parametroidfk,
				muestranum=:muestranum,
				resultado=:resultado,
				identificacion=:identificacion';
			$s=$pdo->prepare($sql);
			$s->bindValue(':informeidfk', $id);
			$s->bindValue(':parametroidfk', $value['parametro']);
			$s->bindValue(':muestranum', $parametro['muestranum']);
			$s->bindValue(':resultado', $value['resultado']);
			$s->bindValue(':identificacion', $parametro['identificacion']);
			$s->execute();
		}
	}
}

/**************************************************************************************************/
/* Función para obtener la lista de acreditaciones */
/**************************************************************************************************/
function getAcreditaciones($estudio){
    include direction.functions.'conectadb.inc.php';
    try
    {
        $sql='SELECT * from acreditaciontbl WHERE estudio = :estudio ORDER BY id DESC';
        $s=$pdo->prepare($sql); 
        $s->bindValue(':estudio', $estudio);
        $s->execute();
        $signatarios = '';
        foreach ($s as $value) {
          $acreditaciones[$value['id']] = $value['nombre'];
        }
        return $acreditaciones;
    }
    catch (PDOException $e)
    {
      return "";
    }
}

/**************************************************************************************************/
/* Función para obtener la lista de acreditaciones */
/**************************************************************************************************/
function getAcreditacion($id){
    global $pdo;
    $sql='SELECT * from acreditaciontbl WHERE id = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $id);
    $s->execute();
    return $s->fetch(PDO::FETCH_ASSOC);
}

/**************************************************************************************************/
/* Función para obtener la lista de acreditaciones */
/**************************************************************************************************/
function getParams($norma){
	global $pdo;
	$sql = 'SELECT clave, parametro
			FROM normastbl
			JOIN paramnormas ON normastbl.id = paramnormas.normaidfk
			JOIN aparametrostbl ON paramnormas.parametroidfk = aparametrostbl.id
			WHERE norma = :norma AND clave <> "GYA" AND clave <> "NMPCF-AR1"';
	$s=$pdo->prepare($sql); 
	$s->bindValue(':norma', $norma);
    $s->execute();
    foreach ($s->fetchAll() as $key => $value) {
		$params[$value['clave']] = $value['parametro'];
	}
	return $params;
}
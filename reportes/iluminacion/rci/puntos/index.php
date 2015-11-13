<?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/funcioneshig.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';
 
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

global $rectbl;
global $recidfk;
$rectbl = 'puntorecilumtbl';
$recidfk = 'recilumidfk';

include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/reusapunto/reusarpuntos.php';

/**************************************************************************************************/
/* Crear un nuevo punto en una orden de trabajo */
/**************************************************************************************************/
if (isset($_GET['nuevopunto']))
{
  fijarAccionUrl('nuevopunto');

  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $sql='SELECT *, recsilumtbl.id  as "idrci" FROM puntostbl
          INNER JOIN puntorecilumtbl ON puntostbl.id=puntorecilumtbl.puntoidfk
          INNER JOIN recsilumtbl ON puntorecilumtbl.recilumidfk = recsilumtbl.id
          WHERE recsilumtbl.ordenidfk = :id
          ORDER BY medicion DESC limit 1';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $_SESSION['idot']);
    $s->execute();

    $punto = $s->fetch();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo la información del punto.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }

  if(isset($_POST['valores']))
  {
    $valores = json_decode($_POST['valores'],TRUE);
  }
  else
  {
      if( strcmp($punto['idrci'], $_SESSION['idrci']) === 0 ){
          $valores = array("nomedicion" => ($punto['medicion']+1),
                           "fecha" => $punto['fecha'],
                           "departamento" => $punto['departamento'],
                           "area" => $punto['area'],
                           "luxometro" => $punto['equiposidfk'],
                           "observaciones" => $punto['observaciones']);
      }
      else
      {
          try
          {
            $sql='SELECT * FROM recsilumtbl
             INNER JOIN deptorecilumtbl ON recsilumtbl.id=deptorecilumtbl.deptoidfk
             INNER JOIN deptostbl ON deptorecilumtbl.deptoidfk=deptostbl.id
             WHERE recsilumtbl.id = :id';
            $s=$pdo->prepare($sql); 
            $s->bindValue(':id', $_SESSION['idrci']);
            $s->execute();

            $rec = $s->fetch();
          }
          catch (PDOException $e)
          {
            $mensaje='Hubo un error extrayendo la información del punto.'.$e;
            include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
            exit();
          }

          $valores = array("nomedicion" => ($punto['medicion']+1),
                         "departamento" => $rec['departamento'],
                         "area" => $rec['area']);
      }   
  }

  $mediciones = '';
  if(isset($_POST['mediciones'])){
    $mediciones = json_decode($_POST['mediciones'],TRUE);
  }
  
  formularioPuntos('Agrega Punto', 'Agregar un nuevo punto', 'guardarpunto', $_SESSION['idrci'], "", $valores, $mediciones);	 
}

/**************************************************************************************************/
/* Guardar inf. y mediciones de un punto nuevo */
/**************************************************************************************************/
if(isset($_GET['guardarpunto']))
{
  /*$mensaje='Error Forzado 1.';
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
  exit();*/

  $idrci = $_POST['idrci'];
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $pdo->beginTransaction();
    $sql='INSERT INTO puntostbl SET
          medicion=:nomedicion,
          fecha=:fecha,
          departamento=:departamento,
          area=:area,
          ubicacion=:ubicacion,
          numestudios=0,
          ordenidfk=:ordenidfk';
    $s=$pdo->prepare($sql);
    $s->bindValue(':nomedicion',$_POST['nomedicion']);
    $s->bindValue(':fecha',$_POST['fecha']);
    $s->bindValue(':departamento',$_POST['departamento']);
    $s->bindValue(':area',$_POST['area']);
    $s->bindValue(':ubicacion',$_POST['ubicacion']);
    $s->bindValue(':ordenidfk',$_SESSION['idot']);
    $s->execute();
    $puntosid=$pdo->lastInsertId();

    $sql='INSERT INTO puntorecilumtbl SET
          puntoidfk=:puntoidfk,
          recilumidfk=:recilumidfk,
          nirm=:nirm,
          observaciones=:observaciones,
          identificacion=:identificacion,
          equiposidfk=:luxometro,
          correccion=:correccion';
    $s=$pdo->prepare($sql);
    $s->bindValue(':puntoidfk', $puntosid);
    $s->bindValue(':recilumidfk', $idrci);
    $s->bindValue(':nirm', $_POST['nirm']);
    $s->bindValue(':observaciones', $_POST['observaciones']);
    $s->bindValue(':identificacion', $_POST['identificacion']);
    $s->bindValue(':luxometro', $_POST["luxometro"]);
    $s->bindValue(':correccion', getCorreccion($_POST['luxometro']));
    $s->execute();

    foreach ($_POST["mediciones"] as $key => $value) {
      if($value["hora"] != "" && $value["e1plano"]!="" && $value["e2plano"]!="")
      {
        $sql='INSERT INTO medsilumtbl SET
                puntoidfk=:puntoidfk,
                hora=:hora,
                e1pared=:e1pared,
                e2pared=:e2pared,
                e1plano=:e1plano,
                e2plano=:e2plano';
        $s=$pdo->prepare($sql);
        $s->bindValue(':puntoidfk', $puntosid);
        $s->bindValue(':hora', $value["hora"]);
        $s->bindValue(':e1plano', $value["e1plano"]);
        $s->bindValue(':e2plano', $value["e2plano"]);
        $s->bindValue(':e1pared', trim($value["e1pared"]));
        $s->bindValue(':e2pared', trim($value["e2pared"]));
        $s->execute();
      }
    }
    $pdo->commit();
  }
  catch (PDOException $e)
  {
   $pdo->rollback();
   $mensaje='Hubo un error al tratar de agregar las mediciones. Favor de intentar nuevamente.'.$e;
   include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
   exit();
  }

  $idrci=$_POST['idrci'];
  $valores = array("nomedicion" => ($_POST['nomedicion']+1),
                   "fecha" => $_POST['fecha'],
                   "departamento" => $_POST['departamento'],
                   "area" => $_POST['area'],
                   "luxometro" => $_POST['luxometro'],
                   "observaciones" => $_POST['observaciones']
                   );
  formularioPuntos('Agrega Punto', 'Agregar un nuevo punto', 'guardarpunto', $idrci, '', $valores);
}

/**************************************************************************************************/
/* Editar un punto de vibraciones */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='editarpunto')
{
  fijarAccionUrl('nuevopunto');

  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  if(isset($_POST['valores'])){
    $valores = json_decode($_POST['valores'],TRUE);
  }else{
    
    try   
    {
      $sql='SELECT *
            FROM puntostbl
            INNER JOIN puntorecilumtbl ON puntostbl.id = puntorecilumtbl.puntoidfk
            WHERE puntostbl.id = :id';
      $s=$pdo->prepare($sql); 
      $s->bindValue(':id',$_POST['id']);
      $s->execute();
      $punto = $s->fetch();

      $sql="SELECT DATE_FORMAT(medsilumtbl.hora, '%H:%i') as 'hora', medsilumtbl.e1pared, medsilumtbl.e2pared, medsilumtbl.e1plano, medsilumtbl.e2plano
            FROM medsilumtbl
            INNER JOIN puntostbl ON medsilumtbl.puntoidfk = puntostbl.id
            WHERE puntostbl.id = :id";
      $s=$pdo->prepare($sql); 
      $s->bindValue(':id',$_POST['id']);
      $s->execute();
    }
    catch (PDOException $e) 
    {
      $mensaje='Hubo un error extrayendo la información del punto.';
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }

    $mediciones = array();
    foreach($s as $linea){
      $mediciones[] = array("hora" => $linea["hora"],
                           "e1pared" => $linea["e1pared"],
                           "e2pared" => $linea["e2pared"],
                           "e1plano" => $linea["e1plano"],
                           "e2plano" => $linea["e2plano"]);
    }
    $valores = array("nomedicion" => $punto['medicion'],
                     "fecha" => $punto['fecha'],
                     "departamento" => $punto['departamento'],
                     "area" => $punto['area'],
                     "ubicacion" => $punto['ubicacion'],
                     "identificacion" => $punto['identificacion'],
                     "observaciones" => $punto['observaciones'],
                     "nirm" => $punto['nirm'],
                     "luxometro" => $punto['equiposidfk']);
  }

  if(isset($_POST['mediciones'])){
    $mediciones = json_decode($_POST['mediciones'],TRUE);
  }

  $idrci=idrecdepuntos($_POST['id']);
  $idot=idotdeidrci($idrci);
  $ot=otdeordenes($idot);

  formularioPuntos('Editar Punto', 'Editar un punto de la OT. '.$ot, 'salvarpunto',  $idrci, $_POST['id'], $valores, $mediciones);
}

/**************************************************************************************************/
/* Guardar la edición de un punto de vibrsaciones */
/**************************************************************************************************/
if (isset($_GET['salvarpunto']))
{
  /*$mensaje='Error Forzado 2.';
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
  exit();*/

  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $pdo->beginTransaction();

    $sql='UPDATE puntostbl SET
          medicion=:nomedicion,
          fecha=:fecha,
          departamento=:departamento,
          area=:area,
          ubicacion=:ubicacion,
          ordenidfk=:ordenidfk
          WHERE id = :id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':nomedicion',$_POST['nomedicion']);
    $s->bindValue(':fecha',$_POST['fecha']);
    $s->bindValue(':departamento',$_POST['departamento']);
    $s->bindValue(':area',$_POST['area']);
    $s->bindValue(':ubicacion',$_POST['ubicacion']);  
    $s->bindValue(':id',$_POST['id']);
    $s->bindValue(':ordenidfk',$_SESSION['idot']);
    $s->execute();

    $sql='UPDATE puntorecilumtbl SET
        nirm=:nirm,
        observaciones=:observaciones,
        identificacion=:identificacion,
        equiposidfk=:luxometro,
        correccion=:correccion
        WHERE puntoidfk = :id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':nirm', $_POST['nirm']);
    $s->bindValue(':observaciones', $_POST['observaciones']);
    $s->bindValue(':identificacion', $_POST['identificacion']);
    $s->bindValue(':luxometro', $_POST["luxometro"]);
    $s->bindValue(':correccion', getCorreccion($_POST['luxometro']));
    $s->bindValue(':id',$_POST['id']);
    $s->execute();

    $sql="DELETE FROM medsilumtbl
          WHERE puntoidfk = :id";
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();

    foreach ($_POST["mediciones"] as $key => $value) {
      if($value["hora"] != "" && $value["e1plano"]!="" && $value["e2plano"]!="")
      {
        $sql='INSERT INTO medsilumtbl SET
              puntoidfk=:puntoidfk,
              hora=:hora,
              e1pared=:e1pared,
              e2pared=:e2pared,
              e1plano=:e1plano,
              e2plano=:e2plano';
        $s=$pdo->prepare($sql);
        $s->bindValue(':puntoidfk', $_POST['id']);
        $s->bindValue(':hora', $value["hora"]);
        $s->bindValue(':e1plano', $value["e1plano"]);
        $s->bindValue(':e2plano', $value["e2plano"]);
        $s->bindValue(':e1pared', trim($value["e1pared"]));
        $s->bindValue(':e2pared', trim($value["e2pared"]));
        $s->execute();
      }
    }
    $pdo->commit();
  }
  catch (PDOException $e)
  {
    $pdo->rollback();
    $mensaje='Hubo un error al tratar de actualizar punto. Favor de intentar nuevamente.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }

  verPuntos($_POST['id']);
}

/**************************************************************************************************/
/* Borrar un punto de un reconocimiento inicial de una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='borrarpunto')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php'; 
  try
  {
    $sql='SELECT .puntostbl.medicion, puntostbl.fecha, puntostbl.departamento, puntostbl.area, puntorecilumtbl.identificacion 
          FROM puntostbl 
          INNER JOIN puntorecilumtbl ON puntostbl.id = puntorecilumtbl.puntoidfk
          WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer al confirmacion de eliminación'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $id=$_POST['id'];
  $idrci=idrecdepuntos($_POST['id']);
  $resultado=$s->fetch();
  $medicion=$resultado['medicion'];
  $fecha=$resultado['fecha'];
  $departamento=$resultado['departamento'];
  $area=$resultado['area'];
  $identificacion=$resultado['identificacion'];
  include 'formaconfirmapuntos.html.php';
  exit();
}

/**************************************************************************************************/
/* Continuar borrado de un punto */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar borrando')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $pdo->beginTransaction();
    $sql='DELETE FROM puntorecilumtbl WHERE puntoidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();

    $sql='DELETE FROM medsilumtbl WHERE puntoidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();

    $sql='DELETE FROM puntostbl WHERE id=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
    $pdo->commit();
  }
  catch (PDOException $e)
  {
    $pdo->rollback();
    $mensaje='Hubo un error borrando el punto. Intente de nuevo. '.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  verPuntos($_POST['idrci']);
}

/**************************************************************************************************/
/* Acción por defualt, llevar a búsqueda de puntos */
/**************************************************************************************************/
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  if(isset($_SESSION['idot']) and isset($_SESSION['idrci']) and isset($_SESSION['quien']) and $_SESSION['quien']=='Iluminacion'){
    $idot=$_SESSION['idot'];
    $idrci=$_SESSION['idrci'];
  }
  else{
	  header('Location: http://'.$_SERVER['HTTP_HOST'].str_replace('puntos/','',$_SERVER['REQUEST_URI']));
  }
  $ot=otdeordenes($idot);
  $puntos=verpuntos($idrci);
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/formas/formapuntos.html.php';
  exit();

/**************************************************************************************************/
/* Función para ver puntos de un reconocimiento inicial */
/**************************************************************************************************/
function verPuntos($id = ""){
  global $pdo;
  try
  {
    $sql='SELECT puntostbl.id, puntostbl.medicion, puntostbl.departamento, puntostbl.area, puntorecilumtbl.identificacion
          FROM puntostbl
          INNER JOIN puntorecilumtbl ON puntostbl.id=puntorecilumtbl.puntoidfk
          WHERE puntorecilumtbl.recilumidfk = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id',$id);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo la lista de puntos.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php'.$e;
    exit();
  }

  foreach ($s as $linea)
  {
    $puntos[]=array('id'=>$linea['id'],
                    'medicion'=>$linea['medicion'],
                    'departamento'=>$linea['departamento'],
                    'area'=>$linea['area'],
                    'identificacion'=>$linea['identificacion']);
  }
  if(isset($puntos)){
    return $puntos;
  }else{
    return;
  }
}

/**************************************************************************************************/
/* Función para ir a formulario de puntos de un reconocimiento inicial */
/**************************************************************************************************/
function formularioPuntos($pestanapag="", $titulopagina="", $accion="", $idrci="", $id="", $valores="", $meds=""){
  global $pdo;
  try   
  {
    $sql='SELECT influencia FROM recsilumtbl
    WHERE recsilumtbl.id = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $idrci);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo la influencia.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $influencia = $s->fetch();
  $nmediciones = $influencia['influencia'] == 0 ? 1 : 3; 
  /*  echo 'para este estudio tantas mediciones '.$nmediciones.'<br>'.',fluencia '.$influencia['influencia']; exit(); */
  if($meds !== ""){
    $mediciones = $meds;
  }

  $luxometros = getEquipos("Luxometro", $_SESSION['idot']);

  $idot=idotdeidrci($idrci);
  include 'formacapturarpuntos.html.php';
  exit();
}

/**************************************************************************************************/
/* Función obtener el numero de OT a partir del id de recsilumtbl */
/**************************************************************************************************/
function otderecsilum($id="")
{
  global $pdo;
  try   
  {
    $sql='SELECT ot FROM ordenestbl
          INNER JOIN recsilumtbl on ordenidfk=ordenestbl.id
          WHERE recsilumtbl.id = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $id);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo el numero de OT.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $resultado = $s->fetch();
  return $resultado['ot']; 
}

/**************************************************************************************************/
/* Función obtener el numero de idot a partir de idrci */
/**************************************************************************************************/
function idotdeidrci($idrci="")
{
  global $pdo;
  try   
  {
    $sql='SELECT ordenidfk FROM recsilumtbl WHERE  id= :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $idrci);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo informacion de la orden.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $resultado = $s->fetch();
  return $resultado['ordenidfk']; 
}

/**************************************************************************************************/
/* Función obtener el numero de id de recsilumtbl a partir del id de puntos */
/**************************************************************************************************/
function idrecdepuntos($id="")
{
  global $pdo;
  try   
  {
    $sql='SELECT recilumidfk FROM puntorecilumtbl
    INNER JOIN puntostbl ON puntostbl.id=puntorecilumtbl.puntoidfk
    WHERE puntostbl.id = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id', $id);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo informacion del reconocimiento.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $resultado = $s->fetch();
  return $resultado['recilumidfk']; 
}
?>
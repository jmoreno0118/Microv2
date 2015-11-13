<?php
 /********** Norma 001 **********/
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/funcionesecol.inc.php';
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';
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
 limpiasession();

/**************************************************************************************************/
/* Guardar una nueva medición de una orden de trabajo */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='guardar')
{
  /*$mensaje='Error Forzado 1.';
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
  exit();*/

  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  $mcompuestas = "";
  if(isset($_POST['regreso']) AND $_POST['regreso'] === '2')
  {
    $cantidad = intval($_POST['cantidad']);
    $id = $_POST['id'];
    $mcompuestas = json_decode($_POST['mcompuestas'], TRUE);
    $muestreoid = $_POST['muestreoid'];
  }
  else
  {
    /*if( strcmp($_POST['termometro'], '') === 0){
      $mensaje='No se seleccionó termometro. Favor de verificar el dato.';
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }*/
    try
    {
      $pdo->beginTransaction();

      setAcreditacion($_POST['acreditacion']['id']);

      setGiroEmpresa($_POST['id'], $_POST['empresagiro']);

      setSignatario($_POST['signatario']);

      $sql='SELECT id FROM maximostbl WHERE descargaen =:descargaen AND uso=:uso';
      $s=$pdo->prepare($sql); 
      $s->bindValue(':descargaen', isset($_POST['descargaen'])? $_POST['descargaen'] : "");
      $s->bindValue(':uso', isset($_POST['uso'])? $_POST['uso'] : "");
      $s->execute();
      $nom01maximosidfk = $s->fetch();

      $sql='INSERT INTO generalesaguatbl SET
            ordenaguaidfk=:id,
            nom01maximosidfk=:nom01maximosidfk,
            numedicion=:numedicion,
            lugarmuestreo=:lugarmuestreo,
            descriproceso=:descriproceso,
            materiasusadas=:materiasusadas,
            tratamiento=:tratamiento,
            Caracdescarga=:Caracdescarga,
            estrategia=:estrategia,
            numuestras=:numuestras,
            observaciones=:observaciones,
            tipomediciones=:tipomediciones,
            tipodescarga=:tipodescarga,
            estudio = "nom002"';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id', $_POST['id']);
      $s->bindValue(':nom01maximosidfk', $nom01maximosidfk['id']);
      $s->bindValue(':numedicion', intval($_POST['numedicion']),PDO::PARAM_INT );
      $s->bindValue(':lugarmuestreo', $_POST['lugarmuestreo']);
      $s->bindValue(':descriproceso', $_POST['descriproceso']);
      $s->bindValue(':materiasusadas', $_POST['materiasusadas']);
      $s->bindValue(':tratamiento', $_POST['tratamiento']);
      $s->bindValue(':Caracdescarga', $_POST['Caracdescarga']);
      $s->bindValue(':estrategia', $_POST['estrategia']);
      $s->bindValue(':numuestras', $_POST['numuestras']);
      $s->bindValue(':observaciones', $_POST['observaciones']);
      $s->bindValue(':tipomediciones', $_POST['tipomediciones']);
      $s->bindValue(':tipodescarga', $_POST['tipodescarga']);
      $s->execute();
      $id=$pdo->lastInsertid();

      $correccion = getCorreccion($_POST['termometro']);
      $calibracion1 = getCalibracion1($correccion, $_POST['temperatura']);
      $calibracion2 = getCalibracion2($correccion, $_POST['temperatura']);

      /*$sql='INSERT INTO muestreosaguatbl SET
            generalaguaidfk=:generalaguaidfk,
            fechamuestreo=:fechamuestreo,
            identificacion=:identificacion,
            temperatura=:temperatura,
            caltermometro=:caltermometro,
            caltermometro2=:caltermometro2,
            pH=:pH,
            conductividad=:conductividad,
            mflotante=:mflotante,
            equipoidfk=:equipoidfk,
            correccionterm=:correccionterm';*/

      $sql='INSERT INTO muestreosaguatbl SET
            generalaguaidfk=:generalaguaidfk,
            fechamuestreo=:fechamuestreo,
            identificacion=:identificacion,
            temperatura=:temperatura,
            caltermometro=:caltermometro,
            caltermometro2=:caltermometro2,
            pH=:pH,
            conductividad=:conductividad,
            mflotante=:mflotante';
      $s=$pdo->prepare($sql);
      $s->bindValue(':generalaguaidfk', $id);
      $s->bindValue(':fechamuestreo', $_POST['fechamuestreo']);
      $s->bindValue(':identificacion', $_POST['identificacion']);
      $s->bindValue(':temperatura', $_POST['temperatura']);
      $s->bindValue(':caltermometro', $_POST['emtermometro']);
      $s->bindValue(':caltermometro2', 0);
      /*$s->bindValue(':caltermometro', $calibracion1);
      $s->bindValue(':caltermometro2', $calibracion2);*/
      $s->bindValue(':pH', $_POST['pH']);
      $s->bindValue(':conductividad', $_POST['conductividad']);
      $s->bindValue(':mflotante', $_POST['mflotante']);
      /*$s->bindValue(':equipoidfk', $_POST['termometro']);
      $s->bindValue(':correccionterm', $correccion);*/
      $s->execute();
      $muestreoid=$pdo->lastInsertid();

      setResponsables($id);

      if(isset($_POST['fechamuestreofin']) AND $_POST['fechamuestreofin'] !== "")
      {
        $sql='UPDATE muestreosaguatbl SET
            fechamuestreofin=:fechamuestreofin
            WHERE generalaguaidfk=:generalaguaidfk';
        $s=$pdo->prepare($sql);
        $s->bindValue(':generalaguaidfk', $muestreoid);
        $s->bindValue(':fechamuestreofin', $_POST['fechamuestreofin']);
        $s->execute();
      }

      $pdo->commit();
    }
    catch (PDOException $e)
    {
      $pdo->rollback();
      $mensaje='Hubo un error al tratar de insertar la medicion. Favor de intentar nuevamente.'.$e;
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }
    $cantidad = 1;
    if($_POST['tipomediciones'] === '4')
    {
        $cantidad = 2;
    }else if($_POST['tipomediciones'] === '8')
    {
        $cantidad = 4;
    }else if($_POST['tipomediciones'] === '12')
    {
        $cantidad = 6;
    }
  }
  fijarAccionUrl('guardar');

  if($cantidad === 1)
  {
    formularioParametros('nom002', $id, $muestreoid, $cantidad, "", "", "", "", 1);
  }
  formularioMediciones('nom002', $id, $muestreoid, $cantidad, $mcompuestas, 2);
}

/**************************************************************************************************/
/* Agregar una nueva medicion a una orden de trabajo */
/**************************************************************************************************/
if (isset($_GET['accion']) and $_GET['accion']=='capturar')
{
  fijarAccionUrl('capturar');

  $id = $_SESSION['ot'];
  $pestanapag = 'Agrega medición';
  $titulopagina = 'Agregar una nueva medición';
  $boton = 'guardar';
  iniciarAgua('nom002', 'NOM 002', $id, 'Aguas');
  if(isset($_POST['valores']))
  {
    $valores = json_decode($_POST['valores'],TRUE);
  }
  else
  {
    $valores = array("empresagiro" => getEGiro($id),
                     "descargaen" => "",
                     "uso" => "",
                     "signatario" => getSignatario($id),
                     "responsable" => getResponsables($id, '', 'nom002'),
                     "acreditacion" => getAcreditacion($id)
              );
  }
  include 'formacapturarmeds.html.php';
  exit();
}

/**************************************************************************************************/
/* Editar reconocimiento inicial de una orden de trabajo */
/**************************************************************************************************/
if((isset($_POST['accion']) and $_POST['accion']=='editar') OR (isset($_POST['accion']) and $_POST['accion']=='ver') OR (isset($_POST['accion']) AND $_POST['accion'] == 'volver' AND isset($_POST['meds'])))
{
  fijarAccionUrl('editar');

  $id = $_POST['id'];
  if(isset($_POST['valores']))
  {
    $valores = json_decode($_POST['valores'],TRUE);
  }
  else
  {
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try
    {
      $sql='SELECT * FROM generalesaguatbl
      INNER JOIN muestreosaguatbl ON generalesaguatbl.id=muestreosaguatbl.generalaguaidfk
      WHERE generalesaguatbl.id = :id';
      $s=$pdo->prepare($sql); 
      $s->bindValue(':id', $id);
      $s->execute();
      $linea = $s->fetch();

      $sql='SELECT descargaen, uso FROM maximostbl WHERE id=:id';
      $s=$pdo->prepare($sql); 
      $s->bindValue(':id', $linea["nom01maximosidfk"]);
      $s->execute();
      $nom01maximos = $s->fetch();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error extrayendo la información de reconocimiento inicial.';
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }

    $valores = array("empresagiro" => getEGiro($linea["ordenaguaidfk"]),
                 "descargaen" => $nom01maximos["descargaen"],
                 "uso" => $nom01maximos["uso"],
                 "numedicion" => $linea["numedicion"],
                 "lugarmuestreo" => $linea["lugarmuestreo"],
                 "descriproceso" => $linea["descriproceso"],
                 "tipomediciones" => $linea["tipomediciones"],
                 //"proposito" => $linea["proposito"],
                 "materiasusadas" => $linea["materiasusadas"],
                 "tratamiento" => $linea["tratamiento"],
                 "Caracdescarga" => $linea["Caracdescarga"],
                 "tipodescarga" => $linea["tipodescarga"],
                 "estrategia" => $linea["estrategia"],
                 "numuestras" => $linea["numuestras"],
                 "observaciones" => $linea["observaciones"],
                 "fechamuestreo" => $linea["fechamuestreo"],
                 "fechamuestreofin" => $linea["fechamuestreofin"],
                 "identificacion" => $linea["identificacion"],
                 "temperatura" => $linea["temperatura"],
                 "pH" => $linea["pH"],
                 "conductividad" => $linea["conductividad"],
                 "nombresignatario" => getNombreSignatario($linea["ordenaguaidfk"]),
                 "signatario" => getSignatario($linea["ordenaguaidfk"]),
                 "responsable" => getResponsables($linea["ordenaguaidfk"], $id, 'nom002'),
                 "mflotante" => $linea["mflotante"],
                 "acreditacion" => getAcreditacion($linea["ordenaguaidfk"]),
                 "termometro" => $linea["equipoidfk"],
                 "emtermometro" => $linea["caltermometro"]);
  }
  iniciarAgua('nom002', 'NOM 002', $_SESSION['ot'], 'Aguas');
  $pestanapag='Editar medicion';
  $titulopagina='Editar medicion';
  $boton = 'salvar';
  if($_POST['accion']=='ver')
    $boton = 'siguiente';
  $regreso = 1;
  include 'formacapturarmeds.html.php';
  exit();
}

/**************************************************************************************************/
/* Borrar una medición de una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='borrar')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php'; 
  try
  {
    $sql='SELECT generalesaguatbl.numedicion, generalesaguatbl.lugarmuestreo, generalesaguatbl.descriproceso, muestreosaguatbl.fechamuestreo, muestreosaguatbl.identificacion
      FROM generalesaguatbl
      INNER JOIN muestreosaguatbl ON generalesaguatbl.id = muestreosaguatbl.generalaguaidfk
      WHERE generalesaguatbl.id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer la confirmacion de eliminación'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $id=$_POST['id'];
  $resultado=$s->fetch();
  $nummedicion=$resultado['numedicion'];
  $lugarmuestreo=$resultado['lugarmuestreo'];
  $descriproceso=$resultado['descriproceso'];
  $fechamuestreo=$resultado['fechamuestreo'];
  $identificacion=$resultado['identificacion'];
  include 'formaconfirmamed.html.php';
  exit();
}

/**************************************************************************************************/
/* Confirmación de borrado de una medición de una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar borrando medicion')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
      $pdo->beginTransaction();
      $sql='DELETE FROM parametros2tbl WHERE parametroidfk IN (SELECT parametrostbl.id FROM parametrostbl INNER JOIN muestreosaguatbl ON muestreosaguatbl.id=parametrostbl.muestreoaguaidfk WHERE muestreosaguatbl.generalaguaidfk= :id)';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();

      $sql='DELETE FROM adicionalestbl WHERE parametroidfk IN (SELECT parametrostbl.id FROM parametrostbl INNER JOIN muestreosaguatbl ON muestreosaguatbl.id=parametrostbl.muestreoaguaidfk WHERE muestreosaguatbl.generalaguaidfk= :id)';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();

      $sql='DELETE FROM mcompuestastbl WHERE muestreoaguaidfk IN (SELECT id FROM muestreosaguatbl WHERE generalaguaidfk = :id)';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute(); 

      $sql='DELETE FROM parametrostbl WHERE muestreoaguaidfk IN (SELECT id FROM muestreosaguatbl WHERE generalaguaidfk = :id)';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute(); 

      $sql='DELETE FROM documentostbl WHERE generalaguaidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute(); 

      $sql='DELETE FROM responsables WHERE muestreoaguaidfk IN (SELECT id FROM muestreosaguatbl WHERE generalaguaidfk = :id)';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute(); 

      $sql='DELETE FROM muestreosaguatbl WHERE generalaguaidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();

      $sql='DELETE FROM generalesaguatbl WHERE id=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();

      $pdo->commit();
  }
  catch (PDOException $e)
  {
      $pdo->rollback();
      $errorlink = 'http://'.$_SERVER['HTTP_HOST'].'/reportes/nom002/generales';
      $errornav = 'Volver a norma 001';
      $mensaje='Hubo un error borrando la medición. Intente de nuevo. '.$e;
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
  }
  verMeds($_POST['ot']);
}

/**************************************************************************************************/
/* Guardar la edición de una orden de trabajo */
/**************************************************************************************************/
if((isset($_POST['accion']) AND $_POST['accion'] == 'salvar') OR (isset($_POST['accion']) AND $_POST['accion'] == 'siguiente') OR (isset($_POST['accion']) AND $_POST['accion'] == 'volver' AND isset($_POST['coms'])))
{
  /*$mensaje='Error Forzado 1.';
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
  exit();*/

  fijarAccionUrl('salvar');

  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  $id = $_POST['id'];
  if(!isset($_POST['regreso']))
  { /* If regreso */
      
      if($_POST['accion'] !== 'siguiente')
      { /* If siguiente */
          try
          {
            /*if( strcmp($_POST['termometro'], '') === 0){
              $mensaje='No se seleccionó termometro. Favor de verificar el dato.';
              include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
              exit();
            }*/

              $pdo->beginTransaction();
             
              setAcreditacion($_POST['acreditacion']['id']);

              setGiroEmpresa($_POST['id'], $_POST['empresagiro']);

              if(strcmp($_POST['signatario'], '') !== 0)
              {
                setSignatario($_POST['signatario']);
              }

              $sql='SELECT id FROM maximostbl WHERE descargaen =:descargaen AND uso=:uso';
              $s=$pdo->prepare($sql); 
              $s->bindValue(':descargaen',$_POST['descargaen']);
              $s->bindValue(':uso',$_POST['uso']);
              $s->execute();
              $nom01maximosidfk = $s->fetch();

              $sql='UPDATE generalesaguatbl SET
                    nom01maximosidfk=:nom01maximosidfk,
                    numedicion=:numedicion,
                    lugarmuestreo=:lugarmuestreo,
                    descriproceso=:descriproceso,
                    materiasusadas=:materiasusadas,
                    tratamiento=:tratamiento,
                    Caracdescarga=:Caracdescarga,
                    estrategia=:estrategia,
                    numuestras=:numuestras,
                    observaciones=:observaciones,
                    tipomediciones=:tipomediciones,
                    tipodescarga=:tipodescarga,
                    estudio = "nom002"
                    WHERE id=:id';
              $s=$pdo->prepare($sql);
              $s->bindValue(':id',$_POST['id']);
              $s->bindValue(':nom01maximosidfk', $nom01maximosidfk['id']);
              $s->bindValue(':numedicion', intval($_POST['numedicion']), PDO::PARAM_INT );
              $s->bindValue(':lugarmuestreo', $_POST['lugarmuestreo']);
              $s->bindValue(':descriproceso', $_POST['descriproceso']);
              $s->bindValue(':materiasusadas', $_POST['materiasusadas']);
              $s->bindValue(':tratamiento', $_POST['tratamiento']);
              $s->bindValue(':Caracdescarga', $_POST['Caracdescarga']);
              $s->bindValue(':estrategia', $_POST['estrategia']);
              $s->bindValue(':numuestras', $_POST['numuestras']);
              $s->bindValue(':observaciones', $_POST['observaciones']);
              $s->bindValue(':tipomediciones', $_POST['tipomediciones']);
              $s->bindValue(':tipodescarga', $_POST['tipodescarga']);
              $s->execute();

              /*$correccion = getCorreccion($_POST['termometro']);
              $calibracion1 = getCalibracion1($correccion, $_POST['temperatura']);
              $calibracion2 = getCalibracion2($correccion, $_POST['temperatura']);*/

              /*$sql='UPDATE muestreosaguatbl SET
                    fechamuestreo=:fechamuestreo,
                    identificacion=:identificacion,
                    temperatura=:temperatura,
                    caltermometro=:caltermometro,
                    caltermometro2=:caltermometro2,
                    pH=:pH,
                    conductividad=:conductividad,
                    mflotante=:mflotante,
                    equipoidfk=:equipoidfk,
                    correccionterm=:correccionterm
                    WHERE generalaguaidfk=:generalaguaidfk';*/

              $sql='UPDATE muestreosaguatbl SET
                    fechamuestreo=:fechamuestreo,
                    identificacion=:identificacion,
                    temperatura=:temperatura,
                    caltermometro=:caltermometro,
                    caltermometro2=:caltermometro2,
                    pH=:pH,
                    conductividad=:conductividad,
                    mflotante=:mflotante
                    WHERE generalaguaidfk=:generalaguaidfk';
              $s=$pdo->prepare($sql);
              $s->bindValue(':generalaguaidfk', $_POST['id']);
              $s->bindValue(':fechamuestreo', $_POST['fechamuestreo']);
              $s->bindValue(':identificacion', $_POST['identificacion']);
              $s->bindValue(':temperatura', $_POST['temperatura']);
              $s->bindValue(':caltermometro', $_POST['emtermometro']);
              $s->bindValue(':caltermometro2', 0);
              /*$s->bindValue(':caltermometro', $calibracion1);
              $s->bindValue(':caltermometro2', $calibracion2);*/
              $s->bindValue(':pH', $_POST['pH']);
              $s->bindValue(':conductividad', $_POST['conductividad']);
              $s->bindValue(':mflotante', $_POST['mflotante']);
              /*$s->bindValue(':equipoidfk', $_POST['termometro']);
              $s->bindValue(':correccionterm', $correccion);*/
              $s->execute();

              setResponsables($_POST['id']);

              if(isset($_POST['fechamuestreofin']) AND $_POST['fechamuestreofin'] !== "")
              {
                $sql='UPDATE muestreosaguatbl SET
                    fechamuestreofin=:fechamuestreofin
                    WHERE generalaguaidfk=:generalaguaidfk';
                $s=$pdo->prepare($sql);
                $s->bindValue(':generalaguaidfk', $_POST['id']);
                $s->bindValue(':fechamuestreofin', $_POST['fechamuestreofin']);
                $s->execute();
              }

              $pdo->commit();
          }
          catch (PDOException $e)
          {
            $pdo->rollback();
            $mensaje='Hubo un error al tratar de actulizar la medicion. Favor de intentar nuevamente.'.$e;
            include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
            exit();
          }
      } /* If siguiente */

      $cantidad = 1;
      if($_POST['tipomediciones'] === '4')
      {
          $cantidad = 2;
      }else if($_POST['tipomediciones'] === '8')
      {
          $cantidad = 4;
      }else if($_POST['tipomediciones'] === '12')
      {
          $cantidad = 6;
      }
  }  /* If regreso */
  else
  { // cierre de if(!isset($_POST['regreso']))
    $cantidad = intval($_POST['cantidad']);
  }
  //var_dump($cantidad);
  //exit();
  if($cantidad === 1)
  { /* If cantidad = 1 */
      if($_POST['accion'] == 'volver' AND isset($_POST['coms']))
      {  /* If volvercoms */
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
        try
        {
          $sql='SELECT *, muestreosaguatbl.id as "muestreoid" FROM generalesaguatbl
             INNER JOIN muestreosaguatbl ON generalesaguatbl.id=muestreosaguatbl.generalaguaidfk
             WHERE generalesaguatbl.id = :id';
          $s=$pdo->prepare($sql); 
          $s->bindValue(':id',$_POST['id']);
          $s->execute();
          $linea = $s->fetch();

          $sql='SELECT descargaen, uso FROM maximostbl WHERE id=:id';
          $s=$pdo->prepare($sql); 
          $s->bindValue(':id', $linea["nom01maximosidfk"]);
          $s->execute();
          $nom01maximos = $s->fetch();
        }
        catch (PDOException $e)
        {
          $mensaje='Hubo un error extrayendo la información de reconocimiento inicial.';
          include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
          exit();
        }
       
        iniciarAgua('nom002', 'NOM 002', $_SESSION['ot'], 'Aguas');
        $egiro = getEGiro($linea["ordenaguaidfk"]);
        $valores = array("empresagiro" => getEGiro($linea["ordenaguaidfk"]),
                   "descargaen" => $nom01maximos["descargaen"],
                   "uso" => $nom01maximos["uso"],
                   "numedicion" => $linea["numedicion"],
                   "lugarmuestreo" => $linea["lugarmuestreo"],
                   "descriproceso" => $linea["descriproceso"],
                   "tipomediciones" => $linea["tipomediciones"],
                   "materiasusadas" => $linea["materiasusadas"],
                   "tratamiento" => $linea["tratamiento"],
                   "Caracdescarga" => $linea["Caracdescarga"],
                   "tipodescarga" => $linea["tipodescarga"],
                   "estrategia" => $linea["estrategia"],
                   "numuestras" => $linea["numuestras"],
                   "observaciones" => $linea["observaciones"],
                   "fechamuestreo" => $linea["fechamuestreo"],
                   "fechamuestreofin" => $linea["fechamuestreofin"],
                   "identificacion" => $linea["identificacion"],
                   "temperatura" => $linea["temperatura"],
                   "pH" => $linea["pH"],
                   "conductividad" => $linea["conductividad"],
                   "nombresignatario" => getNombreSignatario($linea["ordenaguaidfk"]),
                   "signatario" => getSignatario($linea["ordenaguaidfk"]),
                   "responsable" => getResponsables($linea["ordenaguaidfk"], $id, 'nom002'),
                   "mflotante" => $linea["mflotante"],
                   "acreditacion" => getAcreditacion($linea["ordenaguaidfk"]),
                   "termometro" => $linea["equipoidfk"],
                   "emtermometro" => $linea["caltermometro"]
                   );
        $pestanapag='Editar medicion';
        $titulopagina='Editar medicion';
        $accion='';
        $boton = 'salvar';
        $regreso = 1;
        include 'formacapturarmeds.html.php';
        exit();
      } /* If volvercoms */

      if(isset($_POST['regreso']) AND $_POST['regreso'] === '2')
      {
        formularioParametros('nom002', $_POST['id'], $_POST['muestreoid'], intval($_POST['cantidad']), $_POST['idparametro'], json_decode($_POST['valores'], TRUE), json_decode($_POST['parametros'], TRUE), json_decode($_POST['adicionales'], TRUE), 1);
      }
      formularioParametros('nom002', $_POST['id'], $linea["muestreoid"], $cantidad, "", "", "", "", 1);
  } /* If cantidad = 1 */
  else
  { /* Else cantidad != 1 */
    if(isset($_POST['regreso']) AND $_POST['regreso'] === '2')
    {
      $mcompuestas = json_decode($_POST['mcompuestas'], TRUE);
      formularioMediciones('nom002', $id, $_POST['muestreoid'], $cantidad, $mcompuestas, 1);
    }
    else
    {
      try
      {
        $sql='SELECT muestreosaguatbl.id as "muestreoid"
              FROM muestreosaguatbl
              WHERE generalaguaidfk = :id';
        $s=$pdo->prepare($sql); 
        $s->bindValue(':id',$_POST['id']);
        $s->execute();
        $linea = $s->fetch();
      }
      catch (PDOException $e)
      {
        $mensaje='Hubo un error extrayendo la información de reconocimiento inicial.';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
      }

      formularioMediciones('nom002', $id, $linea["muestreoid"], $cantidad, "", 1);
    }
  } /* Else cantidad != 1 */
  exit();
}

/**************************************************************************************************/
/* Formulario de parametros de una medicion una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='parametros')
{
  fijarAccionUrl('parametros');

  if(isset($_POST['regreso']) AND $_POST['regreso'] === '2')
  {
    formularioParametros('nom002', $_POST['id'], $_POST['muestreoid'], $_POST['cantidad'], $_POST['idparametro'], json_decode($_POST['valores'],TRUE), json_decode($_POST['parametros'],TRUE), json_decode($_POST['adicionales'],TRUE), $_POST['regreso'], $_POST['accionparam']);
  }else{
      $cantidad = 1;
      if($_POST['tipomedicion'] === '4')
      {
          $cantidad = 2;
      }
      else if($_POST['tipomedicion'] === '8')
      {
          $cantidad = 4;
      }
      else if($_POST['tipomedicion'] === '12')
      {
          $cantidad = 6;
      }
  }
  if(isset($_POST['idparametro']) AND $_POST['idparametro'] !== "")
  {
    formularioParametros('nom002', $_POST['id'], $_POST['muestreoid'], $cantidad, $_POST['idparametro']);
  }
  else
  {
    formularioParametros('nom002', $_POST['id'], $_POST['muestreoid'], $cantidad);
  }
}

/**************************************************************************************************/
/* Formulario de parametros de una medicion una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='captura siralab')
{
  fijarAccionUrl('captura siralab');

  if(isset($_POST['regreso']) AND $_POST['regreso'] === '2')
  {
    formularioSiralab('nom002', $_POST['id'], json_decode($_POST['valores'], TRUE), json_decode($_POST['mcompuestas'], TRUE), $_POST['cantidad'], $_POST['regreso'], $_POST['accion']);
  }
  else
  {
      $cantidad = 1;
      if($_POST['tipomedicion'] === '4')
      {
          $cantidad = 2;
      }else if($_POST['tipomedicion'] === '8')
      {
          $cantidad = 4;
      }else if($_POST['tipomedicion'] === '12')
      {
          $cantidad = 6;
      }
  }
  formularioSiralab('nom002', $_POST['muestreoid'], '', '', $cantidad, 0);
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
          WHERE ordenidfk = :id AND nombre = "NOM 002"';
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
          WHERE ordenidfk = :id AND nombre = "NOM 002"';
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
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/nom002/');
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
        setObservacion($pdo, 'NOM 002', $_POST['ot'], $_POST['comentario']);
      }
      
      $sql='UPDATE estudiostbl SET
        fecharevision = CURDATE()
        WHERE ordenidfk = :id AND nombre = "NOM 002"';
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
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/nom002/');
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
        setObservacion($pdo, 'NOM 002', $_POST['ot'], $_POST['comentario']);
      }

      $sql='UPDATE estudiostbl SET
            fechafin = NULL,
            fecharevision  = NULL
            WHERE ordenidfk = :id AND nombre = "NOM 002"';
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
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/nom002/');
  exit();
}

/**************************************************************************************************/
/* Ir a subir documentos */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='documentos')
{
  $_SESSION['post'] = $_POST;
  header('Location: http://'.$_SERVER['HTTP_HOST'].'/reportes/nom002/documentos');
  exit();
}

/**************************************************************************************************/
/* Función para ver mediciones de una orden de trabajo */
/**************************************************************************************************/
verMeds($_SESSION['ot'], 'nom002' , 'NOM 002');
<?php
 /********** Norma 001 **********/
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/magicquotes.inc.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';

if (!usuarioRegistrado())
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/direccionaregistro.inc.php';
  exit();
}
if (!usuarioConPermiso('Supervisor'))
{
  $mensaje='Solo el Supervisor tiene acceso a esta parte del programa';
  include '../../accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/* Agregar un nuevo máximo a la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='capturar')
{
  $pestanapag='Agregar máximo';
  $titulopagina='Agregar una nuevo máximo';
  $boton = 'guardar';
  include 'formacapturamaximo.html.php';
  exit();
}

/**************************************************************************************************/
/* Guardar un nuevo máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='guardar')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try   
  {
      $sql='INSERT INTO maximostbl SET
            descargaen = :descargaen,
            uso = :uso,
            GyA = :GyA,
            ssedimentables = :ssedimentables,
            ssuspendidos = :ssuspendidos,
            dbo = :dbo,
            arsenico = :arsenico,
            cadmio = :cadmio,
            cianuros = :cianuros,
            cobre = :cobre,
            cromo = :cromo,
            mercurio = :mercurio,
            niquel = :niquel,
            plomo = :plomo,
            zinc = :zinc,
            temperatura = :temperatura,
            mflotante = :mflotante,
            estudio = "nom002"';
      $s=$pdo->prepare($sql);
      $s->bindValue(':descargaen', strcmp(trim($_POST['descargaen']),'') !== 0 ? $_POST['descargaen'] : '---' );
      $s->bindValue(':uso', strcmp(trim($_POST['uso']),'') !== 0 ? $_POST['uso'] : '---' );
      $s->bindValue(':GyA', strcmp(trim($_POST['GyA']),'') !== 0 ? $_POST['GyA'] : '---' );
      $s->bindValue(':ssedimentables', strcmp(trim($_POST['ssedimentables']),'') !== 0 ? $_POST['ssedimentables'] : '---' );
      $s->bindValue(':ssuspendidos', strcmp(trim($_POST['ssuspendidos']),'') !== 0 ? $_POST['ssuspendidos'] : '---' );
      $s->bindValue(':dbo', strcmp(trim($_POST['dbo']),'') !== 0 ? $_POST['dbo'] : '---' );
      $s->bindValue(':arsenico', strcmp(trim($_POST['arsenico']),'') !== 0 ? $_POST['arsenico'] : '---' );
      $s->bindValue(':cadmio', strcmp(trim($_POST['cadmio']),'') !== 0 ? $_POST['cadmio'] : '---' );
      $s->bindValue(':cianuros', strcmp(trim($_POST['cianuros']),'') !== 0 ? $_POST['cianuros'] : '---' );
      $s->bindValue(':cobre', strcmp(trim($_POST['cobre']),'') !== 0 ? $_POST['cobre'] : '---' );
      $s->bindValue(':cromo', strcmp(trim($_POST['cromo']),'') !== 0 ? $_POST['cromo'] : '---' );
      $s->bindValue(':mercurio', strcmp(trim($_POST['mercurio']),'') !== 0 ? $_POST['mercurio'] : '---' );
      $s->bindValue(':niquel', strcmp(trim($_POST['niquel']),'') !== 0 ? $_POST['niquel'] : '---' );
      $s->bindValue(':plomo', strcmp(trim($_POST['plomo']),'') !== 0 ? $_POST['plomo'] : '---' );
      $s->bindValue(':zinc', strcmp(trim($_POST['zinc']),'') !== 0 ? $_POST['zinc'] : '---' );
      $s->bindValue(':temperatura', $_POST['temperatura']);
      $s->bindValue(':mflotante', $_POST['mflotante']);
      $s->execute();
      $id = $pdo->lastInsertid();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error insertando los máximos.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  verMaximos();
}

/**************************************************************************************************/
/* Editar un máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='editar')
{
  editarMaximos($_POST['id']);
}

/**************************************************************************************************/
/* Salvar edición de un máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='salvar')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try   
  {
    $sql='UPDATE maximostbl SET
          descargaen = :descargaen,
          uso = :uso,
          GyA = :GyA,
          ssedimentables = :ssedimentables,
          ssuspendidos = :ssuspendidos,
          dbo = :dbo,
          arsenico = :arsenico,
          cadmio = :cadmio,
          cianuros = :cianuros,
          cobre = :cobre,
          cromo = :cromo,
          mercurio = :mercurio,
          niquel = :niquel,
          plomo = :plomo,
          zinc = :zinc,
          temperatura = :temperatura,
          mflotante = :mflotante,
          estudio = "nom002"
          WHERE id = :id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id', $_POST['id']);
    $s->bindValue(':descargaen', strcmp(trim($_POST['descargaen']),'') !== 0 ? $_POST['descargaen'] : '---' );
    $s->bindValue(':uso', strcmp(trim($_POST['uso']),'') !== 0 ? $_POST['uso'] : '---' );
    $s->bindValue(':GyA', strcmp(trim($_POST['GyA']),'') !== 0 ? $_POST['GyA'] : '---' );
    $s->bindValue(':ssedimentables', strcmp(trim($_POST['ssedimentables']),'') !== 0 ? $_POST['ssedimentables'] : '---' );
    $s->bindValue(':ssuspendidos', strcmp(trim($_POST['ssuspendidos']),'') !== 0 ? $_POST['ssuspendidos'] : '---' );
    $s->bindValue(':dbo', strcmp(trim($_POST['dbo']),'') !== 0 ? $_POST['dbo'] : '---' );
    $s->bindValue(':arsenico', strcmp(trim($_POST['arsenico']),'') !== 0 ? $_POST['arsenico'] : '---' );
    $s->bindValue(':cadmio', strcmp(trim($_POST['cadmio']),'') !== 0 ? $_POST['cadmio'] : '---' );
    $s->bindValue(':cianuros', strcmp(trim($_POST['cianuros']),'') !== 0 ? $_POST['cianuros'] : '---' );
    $s->bindValue(':cobre', strcmp(trim($_POST['cobre']),'') !== 0 ? $_POST['cobre'] : '---' );
    $s->bindValue(':cromo', strcmp(trim($_POST['cromo']),'') !== 0 ? $_POST['cromo'] : '---' );
    $s->bindValue(':mercurio', strcmp(trim($_POST['mercurio']),'') !== 0 ? $_POST['mercurio'] : '---' );
    $s->bindValue(':niquel', strcmp(trim($_POST['niquel']),'') !== 0 ? $_POST['niquel'] : '---' );
    $s->bindValue(':plomo', strcmp(trim($_POST['plomo']),'') !== 0 ? $_POST['plomo'] : '---' );
    $s->bindValue(':zinc', strcmp(trim($_POST['zinc']),'') !== 0 ? $_POST['zinc'] : '---' );
    $s->bindValue(':temperatura', $_POST['temperatura']);
    $s->bindValue(':mflotante', $_POST['mflotante']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo los máximos.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  verMaximos();
}

/**************************************************************************************************/
/* Agregar un nuevo máximo a la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='borrar')
{
  $id = $_POST['id'];
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $sql='SELECT identificacion FROM maximostbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$id); 
    $s->execute();
    $resultado = $s->fetch();
    $identificacion = $resultado['identificacion'];
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer la confirmacion de eliminación'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  include 'formaconfirmaximo.html.php';
  exit();
}

/**************************************************************************************************/
/* Confirmación de borrado de una medición de una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar borrando')
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try
  {
    $sql='DELETE FROM maximostbl WHERE id = :id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error borrando el máximo. Intente de nuevo. '.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  verMaximos();
}

/**************************************************************************************************/
/* Ver maximos de la norma 001 */
/**************************************************************************************************/
verMaximos();
	
/**************************************************************************************************/
/* Función para obtener valores máximos */
/**************************************************************************************************/
function verMaximos(){
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try   
  {
    $s=$pdo->prepare('SELECT id, descargaen, uso FROM maximostbl WHERE estudio = "nom002"');
    $s->execute();
    $e = $s->fetchAll();
    foreach ($e as $value) {
      $maximos[] = array("id" => $value['id'],
                         "descargaen" => $value['descargaen'],
                         "uso" => $value['uso']);
    }
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo los maximos.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  include 'formamax.html.php';
  exit();
}

/**************************************************************************************************/
/* Función para obtener valores máximos */
/**************************************************************************************************/
function editarMaximos($id){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  try   
  {
    $sql='SELECT * FROM maximostbl WHERE id = :id';
    $s=$pdo->prepare($sql); 
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error actualizando los máximos.'.$e;
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }
  $valor = $s->fetch();
  $valores = array("descargaen" => $valor["descargaen"],
           "uso" => $valor["uso"],
  	    	 "GyA" => $valor["GyA"],
  				 "ssedimentables" => $valor["ssedimentables"],
           "ssuspendidos" => $valor["ssuspendidos"],
           "dbo" => $valor["dbo"],
           "arsenico" => $valor["arsenico"],
           "cadmio" => $valor["cadmio"],
           "cianuros" => $valor["cianuros"],
           "cobre" => $valor["cobre"],
           "cromo" => $valor["cromo"],
           "mercurio" => $valor["mercurio"],
           "niquel" => $valor["niquel"],
           "plomo" =>$valor["plomo"],
           "zinc" => $valor["zinc"],
           "temperatura" => $valor["temperatura"],
           "mflotante" => $valor["mflotante"]);
  $pestanapag='Editar máximo';
  $titulopagina='Editar un máximo';
  $boton = 'salvar';
  include 'formacapturamaximo.html.php';
  exit();
}
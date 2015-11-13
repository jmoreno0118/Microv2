<?php

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
/* Agregar un nuevo máximo a la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Capturar')
{
  $pestanapag='Límites '.$GLOBALS['normatitulo'];
  $titulopagina='Agregar una nuevo máximo';
  $boton = 'Guardar';
  include direction.modules.'maxnom/formacapturamaximo.html.php';
  exit();
}

/**************************************************************************************************/
/* Guardar un nuevo máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Guardar')
{
  include direction.functions.'conectadb.inc.php';
  try   
  {
    $sql='INSERT INTO maximostbl SET
          descargaen = :descargaen,
          uso = :uso,
          GyA = :GyA,
          coliformes = :coliformes,
          ssedimentables = :ssedimentables,
          ssuspendidos = :ssuspendidos,
          dbo = :dbo,
          nitrogeno = :nitrogeno,
          fosforo = :fosforo,
          arsenico = :arsenico,
          cadmio = :cadmio,
          cianuros = :cianuros,
          cobre = :cobre,
          cromo = :cromo,
          mercurio = :mercurio,
          niquel = :niquel,
          plomo = :plomo,
          zinc = :zinc,
          hdehelminto = :hdehelminto,
          temperatura = :temperatura,
          mflotante = :mflotante,
          estudio = "'.$GLOBALS['norma'].'"';
    $s=$pdo->prepare($sql);
    $s->bindValue(':descargaen',     strcmp(trim($_POST['descargaen']),'')     !== 0 ? trim($_POST['descargaen'])     : '---' );
    $s->bindValue(':uso',            strcmp(trim($_POST['uso']),'')            !== 0 ? trim($_POST['uso'])            : '---' );
    $s->bindValue(':GyA',            strcmp(trim($_POST['GyA']),'')            !== 0 ? trim($_POST['GyA'])            : '---' );
    $s->bindValue(':coliformes',     strcmp(trim($_POST['coliformes']),'')     !== 0 ? trim($_POST['coliformes'])     : '---' );
    $s->bindValue(':ssedimentables', strcmp(trim($_POST['ssedimentables']),'') !== 0 ? trim($_POST['ssedimentables']) : '---' );
    $s->bindValue(':ssuspendidos',   strcmp(trim($_POST['ssuspendidos']),'')   !== 0 ? trim($_POST['ssuspendidos'])   : '---' );
    $s->bindValue(':dbo',            strcmp(trim($_POST['dbo']),'')            !== 0 ? trim($_POST['dbo'])            : '---' );
    $s->bindValue(':nitrogeno',      strcmp(trim($_POST['nitrogeno']),'')      !== 0 ? trim($_POST['nitrogeno'])      : '---' );
    $s->bindValue(':fosforo',        strcmp(trim($_POST['fosforo']),'')        !== 0 ? trim($_POST['fosforo'])        : '---' );
    $s->bindValue(':arsenico',       strcmp(trim($_POST['arsenico']),'')       !== 0 ? trim($_POST['arsenico'])       : '---' );
    $s->bindValue(':cadmio',         strcmp(trim($_POST['cadmio']),'')         !== 0 ? trim($_POST['cadmio'])         : '---' );
    $s->bindValue(':cianuros',       strcmp(trim($_POST['cianuros']),'')       !== 0 ? trim($_POST['cianuros'])       : '---' );
    $s->bindValue(':cobre',          strcmp(trim($_POST['cobre']),'')          !== 0 ? trim($_POST['cobre'])          : '---' );
    $s->bindValue(':cromo',          strcmp(trim($_POST['cromo']),'')          !== 0 ? trim($_POST['cromo'])          : '---' );
    $s->bindValue(':mercurio',       strcmp(trim($_POST['mercurio']),'')       !== 0 ? trim($_POST['mercurio'])       : '---' );
    $s->bindValue(':niquel',         strcmp(trim($_POST['niquel']),'')         !== 0 ? trim($_POST['niquel'])         : '---' );
    $s->bindValue(':plomo',          strcmp(trim($_POST['plomo']),'')          !== 0 ? trim($_POST['plomo'])          : '---' );
    $s->bindValue(':zinc',           strcmp(trim($_POST['zinc']),'')           !== 0 ? trim($_POST['zinc'])           : '---' );
    $s->bindValue(':hdehelminto',    strcmp(trim($_POST['hdehelminto']),'')    !== 0 ? trim($_POST['hdehelminto'])    : '---' );
    $s->bindValue(':temperatura',    strcmp(trim($_POST['temperatura']),'')    !== 0 ? trim($_POST['temperatura'])    : '---' );
    $s->bindValue(':mflotante',      strcmp(trim($_POST['mflotante']),'')      !== 0 ? trim($_POST['mflotante'])      : '---' );
    $s->execute();
    $id = $pdo->lastInsertid();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error insertando los máximos.'.$e;
    throwError($mensaje);
  }
  verMaximos();
}

/**************************************************************************************************/
/* Editar un máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Editar')
{
  editarMaximos($_POST['id']);
}

/**************************************************************************************************/
/* Salvar edición de un máximo de la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Salvar')
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='UPDATE maximostbl SET
          descargaen = :descargaen,
          uso = :uso,
          GyA = :GyA,
          coliformes = :coliformes,
          ssedimentables = :ssedimentables,
          ssuspendidos = :ssuspendidos,
          dbo = :dbo,
          nitrogeno = :nitrogeno,
          fosforo = :fosforo,
          arsenico = :arsenico,
          cadmio = :cadmio,
          cianuros = :cianuros,
          cobre = :cobre,
          cromo = :cromo,
          mercurio = :mercurio,
          niquel = :niquel,
          plomo = :plomo,
          zinc = :zinc,
          hdehelminto = :hdehelminto,
          temperatura = :temperatura,
          mflotante = :mflotante,
          estudio =  "'.$GLOBALS['norma'].'"
          WHERE id = :id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id', $_POST['id']);
    $s->bindValue(':descargaen',     strcmp(trim($_POST['descargaen']),'')     !== 0 ? trim($_POST['descargaen'])     : '---' );
    $s->bindValue(':uso',            strcmp(trim($_POST['uso']),'')            !== 0 ? trim($_POST['uso'])            : '---' );
    $s->bindValue(':GyA',            strcmp(trim($_POST['GyA']),'')            !== 0 ? trim($_POST['GyA'])            : '---' );
    $s->bindValue(':coliformes',     strcmp(trim($_POST['coliformes']),'')     !== 0 ? trim($_POST['coliformes'])     : '---' );
    $s->bindValue(':ssedimentables', strcmp(trim($_POST['ssedimentables']),'') !== 0 ? trim($_POST['ssedimentables']) : '---' );
    $s->bindValue(':ssuspendidos',   strcmp(trim($_POST['ssuspendidos']),'')   !== 0 ? trim($_POST['ssuspendidos'])   : '---' );
    $s->bindValue(':dbo',            strcmp(trim($_POST['dbo']),'')            !== 0 ? trim($_POST['dbo'])            : '---' );
    $s->bindValue(':nitrogeno',      strcmp(trim($_POST['nitrogeno']),'')      !== 0 ? trim($_POST['nitrogeno'])      : '---' );
    $s->bindValue(':fosforo',        strcmp(trim($_POST['fosforo']),'')        !== 0 ? trim($_POST['fosforo'])        : '---' );
    $s->bindValue(':arsenico',       strcmp(trim($_POST['arsenico']),'')       !== 0 ? trim($_POST['arsenico'])       : '---' );
    $s->bindValue(':cadmio',         strcmp(trim($_POST['cadmio']),'')         !== 0 ? trim($_POST['cadmio'])         : '---' );
    $s->bindValue(':cianuros',       strcmp(trim($_POST['cianuros']),'')       !== 0 ? trim($_POST['cianuros'])       : '---' );
    $s->bindValue(':cobre',          strcmp(trim($_POST['cobre']),'')          !== 0 ? trim($_POST['cobre'])          : '---' );
    $s->bindValue(':cromo',          strcmp(trim($_POST['cromo']),'')          !== 0 ? trim($_POST['cromo'])          : '---' );
    $s->bindValue(':mercurio',       strcmp(trim($_POST['mercurio']),'')       !== 0 ? trim($_POST['mercurio'])       : '---' );
    $s->bindValue(':niquel',         strcmp(trim($_POST['niquel']),'')         !== 0 ? trim($_POST['niquel'])         : '---' );
    $s->bindValue(':plomo',          strcmp(trim($_POST['plomo']),'')          !== 0 ? trim($_POST['plomo'])          : '---' );
    $s->bindValue(':zinc',           strcmp(trim($_POST['zinc']),'')           !== 0 ? trim($_POST['zinc'])           : '---' );
    $s->bindValue(':hdehelminto',    strcmp(trim($_POST['hdehelminto']),'')    !== 0 ? trim($_POST['hdehelminto'])    : '---' );
    $s->bindValue(':temperatura',    strcmp(trim($_POST['temperatura']),'')    !== 0 ? trim($_POST['temperatura'])    : '---' );
    $s->bindValue(':mflotante',      strcmp(trim($_POST['mflotante']),'')      !== 0 ? trim($_POST['mflotante'])      : '---' );
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo los máximos.'.$e;
    throwError($mensaje);
  }
  verMaximos();
}

/**************************************************************************************************/
/* Agregar un nuevo máximo a la norma */
/**************************************************************************************************/
if(isset($_POST['accion']) and $_POST['accion']=='Borrar')
{
  $id = $_POST['id'];
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='SELECT descargaen, uso FROM maximostbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$id); 
    $s->execute();
    $resultado = $s->fetch();
    $descargaen = $resultado['descargaen'];
    $uso = $resultado['uso'];
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer la confirmacion de eliminación'.$e;
    throwError($mensaje);
  }
  include direction.modules.'maxnom/formaconfirmaximo.html.php';
  exit();
}

/**************************************************************************************************/
/* Confirmación de borrado de una medición de una orden de trabajo */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar')
{
  include direction.functions.'conectadb.inc.php';
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
    throwError($mensaje);
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
  include direction.functions.'conectadb.inc.php';
  try
  {
    $s=$pdo->prepare('SELECT id, descargaen, uso FROM maximostbl WHERE estudio = "'.$GLOBALS['norma'].'"');
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
    throwError($mensaje);
  }
  $pestanapag='Maximos '.$GLOBALS['normatitulo'];
  $titulopagina='Maximos '.$GLOBALS['normatitulo'];
  include direction.modules.'maxnom/formamax.html.php';
  exit();
}

/**************************************************************************************************/
/* Función para obtener valores máximos */
/**************************************************************************************************/
function editarMaximos($id){
  include direction.functions.'conectadb.inc.php';
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
    throwError($mensaje);
  }
  $valor = $s->fetch();
  $valores = array("descargaen" => $valor["descargaen"],
           "uso" => $valor["uso"],
  	    	 "GyA" => $valor["GyA"],
  	    	 "coliformes" => $valor["coliformes"],
  				 "ssedimentables" => $valor["ssedimentables"],
           "ssuspendidos" => $valor["ssuspendidos"],
           "dbo" => $valor["dbo"],
           "nitrogeno" => $valor["nitrogeno"],
           "fosforo" => $valor["fosforo"],
           "arsenico" => $valor["arsenico"],
           "cadmio" => $valor["cadmio"],
           "cianuros" => $valor["cianuros"],
           "cobre" => $valor["cobre"],
           "cromo" => $valor["cromo"],
           "mercurio" => $valor["mercurio"],
           "niquel" => $valor["niquel"],
           "plomo" =>$valor["plomo"],
           "zinc" => $valor["zinc"],
           "hdehelminto" => $valor["hdehelminto"],
           "temperatura" => $valor["temperatura"],
           "mflotante" => $valor["mflotante"]);
  $pestanapag='Límites '.$GLOBALS['normatitulo'];
  $titulopagina='Editar un máximo';
  $boton = 'Salvar';
  include direction.modules.'maxnom/formacapturamaximo.html.php';
  exit();
}
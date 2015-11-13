<?php

function html($texto)
{
  return htmlspecialchars($texto,ENT_QUOTES,'UTF-8');
}

function htmlout($texto)
{
  echo html($texto);
}

function htmldecode($texto){
  return html_entity_decode($texto, ENT_COMPAT, 'UTF-8');
}

// otra referencia puede ser http://michelf.com/projects/php-markdown/
function markdown2html($texto)
{
  $texto=html($texto);

  // coloca negrillas
  $texto=preg_replace('/__(.+?)__/s','<strong>$1</strong>',$texto);
  $texto=preg_replace('/\*\*(.+?)\*\*/s','<strong>$1</strong>',$texto);
  // coloca italicas (enfasis)
  $texto=preg_replace('/_([^_]+)_/','<em>$1</em>',$texto);
  $texto=preg_replace('/\*([^\*]+)\*/','<em>$1</em>',$texto);
  //convierte de windows a unix
  $texto=str_replace("\r\n","\n",$texto);
  //convierte de macintoch a unix
  $texto=str_replace("\r","\n",$texto);
  //genera el parrafo
  $texto='<p>'.str_replace("\n\n",'</p><p>',$texto).'</p>';
  // genera line breaks
  $texto=str_replace("\n",'<br>',$texto);
  // genera el codigo para [texto](dereccion URL)
  $texto=preg_replace('/\[([^\]]+)]\(([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\)/i',
        '<a href="$2">$1></a>',$texto);
  return $texto;   
}
  
function markdownout($texto)
{
  echo markdown2html($texto);
}

function rastrea($texto)
{
  $texto=htmlout($texto);
  exit();
}

/* **************************************************************** */
/* ***** Limpia los valores de idot, idrci, idpunto y quien ******* */
/* **************************************************************** */
function limpiasession(){
  if (isset($_SESSION['idot'])){
    unset($_SESSION['idot']);
  }
  if (isset($_SESSION['quien'])){
    unset($_SESSION['quien']);
  } 
}

/**************************************************************************************************/
/* Función para arrojar el error */
/**************************************************************************************************/
function throwError($mensaje, $errorlink = '', $errornav = ''){
    include direction.views.'error.html.php';
    exit();
}

function fijarAccionUrl($accion){
  $_SESSION['accion'] = $accion;
  $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
  $host     = $_SERVER['HTTP_HOST'];
  $script   = $_SERVER['SCRIPT_NAME'];
  $params   = $_SERVER['QUERY_STRING'];
  $currentUrl = $protocol . '://' . $host . $script . '?' . $params;
  $_SESSION['url'] = $currentUrl;
}

/**************************************************************************************************/
/* Función para obtener la acreditacion de la orden */
/**************************************************************************************************/
  function getCorreccion($idequipo){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try{

      $sql='SELECT correccion
          FROM calibracionestbl
          WHERE equipoidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id', $idequipo);
      $s->execute();

      if(!$correccion = $s->fetch()){

        //var_dump($correccion);
        //exit();

        return "";
        exit();

        $mensaje='Hubo un error getCorreccion';
        include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
        exit();
      }

      return $correccion['correccion'];
    }catch (PDOException $e){
      //return "";
      $mensaje='Hubo un error getCorreccion '.$e;
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }
  }

/**************************************************************************************************/
/* Función para obtener la acreditacion de la orden */
/**************************************************************************************************/
function getEquipos($tipo, $ot){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try
    {
        $sql='SELECT equipos.ID_Equipo, Descripcion, Marca, Modelo, Numero_Inventario
              FROM  equipos
              WHERE equipos.Tipo = "'.$tipo.'"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':ot', $ot);
        $s->execute();

        $equipos = '';
        $error = '';
        if($eqbd = $s->fetchAll()){
          foreach ($eqbd as $value){
            $sql='SELECT *
              FROM  calibracionestbl
              WHERE equipoidfk=:id';
            $s=$pdo->prepare($sql);
            $s->bindValue(':id', $value['ID_Equipo']);
            $s->execute();

            if(!$s->fetch()){
              $error.=$value['Numero_Inventario'].',';
              continue;
            }

            $equipos[$value['ID_Equipo']] = $value['Descripcion'].' '.$value['Marca'].' '.$value['Modelo'].' '.$value['Numero_Inventario'];
          }
        }

        /*$sql='SELECT equipos.ID_Equipo, Descripcion, Marca, Modelo, Numero_Inventario
              FROM  equipos
              INNER JOIN bitacora_uso ON equipos.ID_Equipo = bitacora_uso.ID_Equipo
              INNER JOIN bitacora_personal ON bitacora_uso.ID_Personal = bitacora_personal.ID_Personal
              WHERE bitacora_personal.Numero_Orden_Muestreo IN (SELECT ot FROM ordenestbl WHERE id = :ot)
              AND equipos.Tipo = "'.$tipo.'"';
        $s=$pdo->prepare($sql);
        $s->bindValue(':ot', $ot);
        $s->execute();

        $equipos = '';
        $error = '';
        if($eqbd = $s->fetchAll()){
            foreach ($eqbd as $value){
              $sql='SELECT *
                FROM  calibracionestbl
                WHERE equipoidfk=:id';
              $s=$pdo->prepare($sql);
              $s->bindValue(':id', $value['ID_Equipo']);
              $s->execute();

              if(!$s->fetch()){
                $error.=$value['Numero_Inventario'].',';
              }

              $equipos[$value['ID_Equipo']] = $value['Descripcion'].' '.$value['Marca'].' '.$value['Modelo'].' '.$value['Numero_Inventario'];
            }
        }*/
        
        /*if(strcmp($error, '') !== 0){
          $mensaje = 'Les hace falta capturar correccion a los equipos: '.rtrim($error, ',');
          include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
          exit();
        }*/

      return $equipos;
    }catch (PDOException $e){
      return "";
    }
}

/**************************************************************************************************/
/* Función para obtener la acreditacion de la orden */
/**************************************************************************************************/
  function setObservacion($pdo, $estudio, $ot, $observacion){
    try
    {
        $sql= 'SELECT nombre, apellido FROM usuariostbl
                WHERE usuario = :usuario';
        $s = $pdo->prepare($sql);
        $s->bindValue(':usuario', $_SESSION['usuario']);
        $s->execute();
        $e = $s->fetch();

        $sql='INSERT INTO  observacionestbl SET
          ordenesidfk = :id,
          observacion = :observacion,
          fecha = CURDATE(),
          supervisor = :supervisor,
          estudio = :estudio';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $ot);
        $s->bindValue(':observacion', $observacion);
        $s->bindValue(':supervisor', $e['nombre'].' '.$e['apellido']);
        $s->bindValue(':estudio', $estudio);
        $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error getCorreccion '.$e;
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }
  }

/**************************************************************************************************/
/* Función para obtener el listado de signatarios */
/**************************************************************************************************/
function getSignatarios($estudio){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try
    {
        $sql='SELECT usuariostbl.id, nombre, apellido
          FROM usuariostbl
          INNER JOIN usuarioestudiostbl ON usuariostbl.id = usuarioestudiostbl.usuarioidfk
          INNER JOIN usuarioactivtbl ON usuariostbl.id = usuarioactivtbl.usuarioidfk
          INNER JOIN usuarioreptbl ON usuariostbl.id = usuarioreptbl.usuarioidfk
          WHERE actividfk = "Signatario" AND estado = 1 AND estudio = "'.$estudio.'" AND
          usuariotipo = "S" AND usuarioreptbl.representanteidfk = (SELECT representanteidfk FROM ordenestbl WHERE id = :id)';
        $s=$pdo->prepare($sql); 
        if(isset($_SESSION['ot'])){
          $s->bindValue(':id', $_SESSION['ot']);
        }elseif(isset($_SESSION['idot'])){
          $s->bindValue(':id', $_SESSION['idot']);
        }
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
/* Función para obtener el signatario de la orden */
/**************************************************************************************************/
function getNombreSignatario($ot){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try
    {
        $sql='SELECT nombre, apellido, firmaarchivar
      FROM  usuariostbl
      INNER JOIN ordenestbl ON usuariostbl.id = ordenestbl.signatarioidfk
      WHERE ordenestbl.id=:id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $ot);
        $s->execute();
        $value = $s->fetch();

        return $value['nombre'].' '.$value['apellido'];
    }
    catch (PDOException $e)
    {
      return "";
    }
}

/**************************************************************************************************/
/* Función para obtener el signatario de la orden */
/**************************************************************************************************/
function getSignatario($ot){
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try
    {
        $sql='SELECT signatarioidfk
          FROM  ordenestbl
          WHERE id=:id';
        $s=$pdo->prepare($sql);
        $s->bindValue(':id', $ot);
        $s->execute();
        $value = $s->fetch();

        return $value['signatarioidfk'];
    }
    catch (PDOException $e)
    {
      return "";
    }
}

/**************************************************************************************************/
/* Función para obtener el signatario de la orden */
/**************************************************************************************************/
function setSignatario($signatarioid){
    global $pdo;
    $sql='UPDATE ordenestbl SET
          signatarioidfk = :signatarioidfk
          WHERE id = :id';
    $s=$pdo->prepare($sql);
    if(isset($_SESSION['ot'])){
      $s->bindValue(':id', $_SESSION['ot']);
    }elseif(isset($_SESSION['idot'])){
      $s->bindValue(':id', $_SESSION['idot']);
    }
    $s->bindValue(':signatarioidfk', $signatarioid);
    $s->execute();
}
?>
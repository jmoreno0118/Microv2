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
/* Editar un máximo de la norma */
/**************************************************************************************************/
if((isset($_POST['accion']) and $_POST['accion']=='guardar') OR (isset($_POST['accion']) and $_POST['accion']=='salvar'))
{
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
    try   
    {
        $sql='INSERT INTO limitestbl SET
        GyA=:GyA,
        ssedimentables=:ssedimentables,
        ssuspendidos=:ssuspendidos,
        dbo=:dbo,
        arsenico=:arsenico,
        cadmio=:cadmio,
        cianuros=:cianuros,
        cobre=:cobre,
        cromo=:cromo,
        mercurio=:mercurio,
        niquel=:niquel,
        plomo=:plomo,
        zinc=:zinc,
        fecha=CURDATE()';
        $s=$pdo->prepare($sql);
        $s->bindValue(':GyA', $_POST['GyA']);
        $s->bindValue(':ssedimentables', $_POST['ssedimentables']);
        $s->bindValue(':ssuspendidos', $_POST['ssuspendidos']);
        $s->bindValue(':dbo', $_POST['dbo']);
        $s->bindValue(':arsenico', $_POST['arsenico']);
        $s->bindValue(':cadmio', $_POST['cadmio']);
        $s->bindValue(':cianuros', $_POST['cianuros']);
        $s->bindValue(':cobre', $_POST['cobre']);
        $s->bindValue(':cromo', $_POST['cromo']);
        $s->bindValue(':mercurio', $_POST['mercurio']);
        $s->bindValue(':niquel', $_POST['niquel']);
        $s->bindValue(':plomo', $_POST['plomo']);
        $s->bindValue(':zinc', $_POST['zinc']);
        $s->execute();
        $id=$pdo->lastInsertid();
    }
    catch(PDOException $e)
    {
      $mensaje='Hubo un error actualizando los máximos.'.$e;
      include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
      exit();
    }
    header('Location: ../..');
}

/**************************************************************************************************/
/* Acción default */
/**************************************************************************************************/
include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php'; 
try   
{
    $s=$pdo->prepare('SELECT * FROM limitestbl ORDER BY id DESC'); 
    $s->execute();
}
catch (PDOException $e)
{
    $mensaje='Hubo un error extrayendo la información de limites.';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
}
if($param1 = $s->fetch())
{
    $valores = array("GyA" => $param1["GyA"],
                    "ssedimentables" => $param1["ssedimentables"],
                    "ssuspendidos" => $param1["ssuspendidos"],
                    "dbo" => $param1["dbo"],
                    "arsenico" => $param1["arsenico"],
                    "cadmio" => $param1["cadmio"],
                    "cianuros" => $param1["cianuros"],
                    "cobre" => $param1["cobre"],
                    "cromo" => $param1["cromo"],
                    "mercurio" => $param1["mercurio"],
                    "niquel" => $param1["niquel"],
                    "plomo" =>$param1["plomo"],
                    "zinc" => $param1["zinc"],
                    "fecha" => $param1["fecha"]);
    formularioLimites($valores, "salvar");
}
else
{
  formularioLimites("", "guardar");
}

/**************************************************************************************************/
/* Función para ver formulario de parametros de una medicion de una orden de trabajo */
/**************************************************************************************************/
function formularioLimites($valor = "", $boton = ""){
  $pestanapag='Límites';
  $titulopagina='Límites';
  if($valor !== "")
  {
    $valores = $valor;
  }
  include 'formacapturalimite.html.php';
  exit();
}
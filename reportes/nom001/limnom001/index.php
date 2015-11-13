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
        coliformes=:coliformes,
        ssedimentables=:ssedimentables,
        ssuspendidos=:ssuspendidos,
        dbo=:dbo,
        nkjedahl=:nkjedahl,
        nitritos=:nitritos,
        nitratos=:nitratos,
        fosforo=:fosforo,
        arsenico=:arsenico,
        cadmio=:cadmio,
        cianuros=:cianuros,
        cobre=:cobre,
        cromo=:cromo,
        mercurio=:mercurio,
        niquel=:niquel,
        plomo=:plomo,
        zinc=:zinc,
        hdehelminto=:hdehelminto,
        fecha=CURDATE()';
        $s=$pdo->prepare($sql);
        $s->bindValue(':GyA', trim($_POST['GyA']) );
        $s->bindValue(':coliformes', trim($_POST['coliformes']) );
        $s->bindValue(':ssedimentables', trim($_POST['ssedimentables']) );
        $s->bindValue(':ssuspendidos', trim($_POST['ssuspendidos']) );
        $s->bindValue(':dbo', trim($_POST['dbo']) );
        $s->bindValue(':nkjedahl', trim($_POST['nkjedahl']) );
        $s->bindValue(':nitritos', trim($_POST['nitritos']) );
        $s->bindValue(':nitratos', trim($_POST['nitratos']) );
        $s->bindValue(':fosforo', trim($_POST['fosforo']) );
        $s->bindValue(':arsenico', trim($_POST['arsenico']) );
        $s->bindValue(':cadmio', trim($_POST['cadmio']) );
        $s->bindValue(':cianuros', trim($_POST['cianuros']) );
        $s->bindValue(':cobre', trim($_POST['cobre']) );
        $s->bindValue(':cromo', trim($_POST['cromo']) );
        $s->bindValue(':mercurio', trim($_POST['mercurio']) );
        $s->bindValue(':niquel', trim($_POST['niquel']) );
        $s->bindValue(':plomo', trim($_POST['plomo']) );
        $s->bindValue(':zinc', trim($_POST['zinc']) );
        $s->bindValue(':hdehelminto', trim($_POST['hdehelminto']) );
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
                    "coliformes" => $param1["coliformes"],
                    "ssedimentables" => $param1["ssedimentables"],
                    "ssuspendidos" => $param1["ssuspendidos"],
                    "dbo" => $param1["dbo"],
                    "nkjedahl" => $param1["nkjedahl"],
                    "nitritos" => $param1["nitritos"],
                    "nitratos" => $param1["nitratos"],
                    "fosforo" => $param1["fosforo"],
                    "arsenico" => $param1["arsenico"],
                    "cadmio" => $param1["cadmio"],
                    "cianuros" => $param1["cianuros"],
                    "cobre" => $param1["cobre"],
                    "cromo" => $param1["cromo"],
                    "mercurio" => $param1["mercurio"],
                    "niquel" => $param1["niquel"],
                    "plomo" => $param1["plomo"],
                    "zinc" => $param1["zinc"],
                    "hdehelminto" => $param1["hdehelminto"],
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
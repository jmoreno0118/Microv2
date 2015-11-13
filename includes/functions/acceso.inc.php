<?php
function usuarioRegistrado()
{
   if (isset($_POST['accion']) and $_POST['accion'] == 'Entrar')
   {
      if (!isset($_POST['loginusuario']) or $_POST['loginusuario'] == '' or
          !isset($_POST['loginclave']) or $_POST['loginclave'] == '')
      {
         $GLOBALS['loginError'] = 'Verifique que la información de los 2 campos este llena';
         return FALSE;
      }
      $clave = md5($_POST['loginclave'] . 'ravol');
      if (BDcontieneAutor($_POST['loginusuario'], $clave))
      {
         session_start();
         $_SESSION['registrado'] = TRUE;
         $_SESSION['usuario'] = $_POST['loginusuario'];
         $_SESSION['clave'] = $clave;
         return TRUE;
      }
      else
      {
         session_start();
         unset($_SESSION['registrado']);
         unset($_SESSION['usuario']);
         unset($_SESSION['clave']);
         $GLOBALS['loginError'] =
             'El usuario o la clave es incorrecta.';
         return FALSE;
      }
   }
   if (isset($_POST['accion']) and $_POST['accion'] == 'Salir')
   {
      session_start();
      unset($_SESSION['registrado']);
      unset($_SESSION['usuario']);
      unset($_SESSION['clave']);
      header('Location: '.url);
      exit();
   }
   session_start();
   if (isset($_SESSION['registrado']))
   {
      return BDcontieneAutor($_SESSION['usuario'], $_SESSION['clave']);
   }
}


function BDcontieneAutor($usuario, $clave)
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql = 'SELECT COUNT(*) FROM usuariostbl
            WHERE usuario = :usuario AND clave = :clave';
    $s = $pdo->prepare($sql);
    $s->bindValue(':usuario', $usuario);
    $s->bindValue(':clave', $clave);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $error = 'Existe un error localizando los datos del autor.  Favor de volver a intentar.';
    include 'error.html.php';
    exit();
  }
  $linea = $s->fetch();
  if ($linea[0] > 0)
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}


function usuarioConPermiso($actividad)
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql = "SELECT COUNT(*) FROM usuariostbl
            INNER JOIN usuarioactivtbl ON usuariostbl.id = usuarioidfk
            INNER JOIN actividadestbl ON actividfk = actividadestbl.id
            WHERE usuario = :usuario AND actividadestbl.id = :actividad";
    $s = $pdo->prepare($sql);
    $s->bindValue(':usuario', $_SESSION['usuario']);
    $s->bindValue(':actividad', $actividad);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $error = 'Se tuvo un error verificando las actividades permitidas.';
    include 'error.html.php';
    exit();
  }
  $linea = $s->fetch();
  if ($linea[0] > 0)
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}
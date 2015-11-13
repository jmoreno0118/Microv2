<?php
 //********** USUARIOS **********
include_once '../../conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';

if (!usuarioRegistrado())
{
  header('Location: '.url);
  exit();
}
if (!usuarioConPermiso('Administra usuarios'))
{
  $mensaje='Solo el Admistrador de usuarios tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_GET['usuarionuevo']))
{
  include direction.functions.'conectadb.inc.php';
  $pestanapag='Agrega usuario';
  $titulopagina='Agrega a un nuevo usuario';
  $accion='agregausuario';
  $usuario='';
  $nombre='';
  $apellido='';
  $correo='';
  $id='';
  $boton='Agrega usuario';

  // Construye lista de actividades y de representantes a las que tiene aceeso el usuario
  try
  {
    $resultado=$pdo->query('SELECT id, descripcion FROM actividadestbl');	
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo las actividades.';
    throwError($mensaje);
  }

  foreach ($resultado as $linea)
  {
    $actividades[]=array('id'=>$linea['id'],
                        'descripcion'=>$linea['descripcion'],
                        'seleccionada'=>FALSE);
  }

  try
  {
    $resultado=$pdo->query('SELECT id, nombre FROM representantestbl');		
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error extrayendo los representantes.';
    throwError($mensaje);
  }

  foreach ($resultado as $linea)
  {
    $representantes[]=array('id'=>$linea['id'],
                            'nombre'=>$linea['nombre'],
                            'seleccionada'=>FALSE);
  }
  include 'formacaptura.html.php';
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_GET['agregausuario']))
{
  if ($_POST['clave']!='')
  {
    $clave=md5($_POST['clave'].'ravol');
    include direction.functions.'conectadb.inc.php';
    try
    {
      $sql='INSERT INTO usuariostbl SET
            usuario=:usuario,
            nombre=:nombre,
            apellido=:apellido,
            correo=:correo,
            clave=:clave,
            estado=1';
      $s=$pdo->prepare($sql);
      $s->bindValue(':usuario',$_POST['usuario']);
      $s->bindValue(':nombre',$_POST['nombre']);
      $s->bindValue(':apellido',$_POST['apellido']);
      $s->bindValue(':correo',$_POST['correo']);
      $s->bindValue(':clave',$clave);
      $s->execute();	
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error al tratar de agregar al usuarrio. Favor de intentar nuevamente.';
      throwError($mensaje);
    }
    $usuarioid=$pdo->lastInsertid();
  }
  else
  {
    $mensaje='Se requiere de una clave de acceso para poder dar de alta al usuario';
    throwError($mensaje);
  }

  if (isset($_POST['actividades']))
  {
    insertActividades($_POST['actividades'], $usuarioid);
  }

  if (isset($_POST['representantes']))
  {
    insertRepresentantes($_POST['representantes'], $usuarioid);
  }
  header('Location: .');
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Editar')
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='SELECT id, usuario, clave, nombre, apellido, correo FROM usuariostbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error obtenindo la informacion del usuario';
    throwError($mensaje);
  }   
  $resultado=$s->fetch();
  $pestanapag='Edita usuario';
  $titulopagina='Edición de la información del usuario';
  $accion='editausuario';
  $id=$resultado['id'];
  $usuario=$resultado['usuario'];
  $nombre=$resultado['nombre'];
  $apellido=$resultado['apellido'];
  $correo=$resultado['correo'];
  $boton='Salva cambios';
  // Trae la lista de actividades asignadas al usuario
  try
  {
    $sql='SELECT actividfk FROM usuarioactivtbl WHERE usuarioidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un errror tratando de leer las actividades permitidas';
    throwError($mensaje);
  }
  $actividadesSelec=array();
  foreach ($s as $linea)
  {
    $actividadesSelec[]=$linea['actividfk'];
  }
  // construiremos la lista de actividades
  try
  {
    $resultado=$pdo->query('SELECT id, descripcion FROM actividadestbl');
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error obteniendo la base de datos de las actividades';
    throwError($mensaje);
  }
  foreach ($resultado as $linea)
  {
    $actividades[]=array('id'=>$linea['id'],
                        'descripcion'=>$linea['descripcion'],
                        'seleccionada'=>in_array($linea['id'],$actividadesSelec)
     );
  }
  // Trae la lista de representantes asignadas al usuario
  try
  {
    $sql='SELECT representanteidfk FROM usuarioreptbl WHERE usuarioidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un errror tratando de leer los representantes a los que tiene acceso';
    throwError($mensaje);
  }
  $representantesSelec=array();
  foreach ($s as $linea)
  {
    $representantesSelec[]=$linea['representanteidfk'];
  }
  // construiremos la lista de representantes
  try
  {
    $resultado=$pdo->query('SELECT id, nombre FROM representantestbl');
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error obteniendo la base de datos de las actividades';
    throwError($mensaje);
  }
  foreach ($resultado as $linea)
  {
    $representantes[]=array('id'=>$linea['id'],
                            'nombre'=>$linea['nombre'],
                            'seleccionada'=>in_array($linea['id'],$representantesSelec)
     );
  }
  include 'formacaptura.html.php';
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_GET['editausuario']))
{ 
  include direction.functions.'conectadb.inc.php';
  try
  { 
    $sql='UPDATE usuariostbl SET
          usuario=:usuario,
          nombre=:nombre,
          apellido=:apellido,
          correo=:correo WHERE id=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->bindValue(':usuario',$_POST['usuario']);
    $s->bindValue(':nombre',$_POST['nombre']);
    $s->bindValue(':apellido',$_POST['apellido']);
    $s->bindValue(':correo',$_POST['correo']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error en la actalización del usuario';
    throwError($mensaje);
  }

  if ($_POST['clave']!='')
  {
    $clave=md5($_POST['clave'].'ravol');
    try
    {
      $sql='UPDATE usuariostbl SET
            clave=:clave
            WHERE id=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':clave',$clave);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error y no se pudo guardar la nueva clave.';
      throwError($mensaje);
    }
  }
  //guardando las nuevas actividades
  try
  {
    $sql='DELETE FROM usuarioactivtbl WHERE usuarioidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error no se pudieron eliminar las actividades previas.  Intentar de nuevo.';
    throwError($mensaje);
  }
  if (isset($_POST['actividades']))
  {
    insertActividades($_POST['actividades'], $_POST['id']);
  }
  //guardando los nuevos representantes
  try
  {
    $sql='DELETE FROM usuarioreptbl WHERE usuarioidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error no se pudieron eliminar los representantes previos.  Intentar de nuevo.';
    throwError($mensaje);
  }
  if (isset($_POST['representantes']))
  {
    insertRepresentantes($_POST['representantes'], $_POST['id']);
  }

  header ('Location: .');
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Borrar')
{
  include direction.functions.'conectadb.inc.php'; 
  try
  {
    $sql='SELECT id, usuario, nombre, apellido FROM usuariostbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer al confirmacion de eliminación';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  $id=$resultado['id'];
  $usuario=$resultado['usuario'];
  $nombre=$resultado['nombre'];
  $apellido=$resultado['apellido'];
  include 'formaconfirma.html.php';
  exit();
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar')
{
  $id = $_POST['id'];
  include direction.functions.'conectadb.inc.php';
  
  try
  {
    $sql='SELECT count(*) FROM usuarioactivtbl WHERE usuarioidfk=:id AND (actividfk = "Signatario" OR actividfk = "Muestreador")';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id', $id); 
    $s->execute();
    $tipo = $s->fetch();
  }
  catch (PDOException $e)
  {
    $mensaje='No se pudo hacer al confirmacion de eliminación';
    throwError($mensaje);
  }

  try
  {
    $pdo->beginTransaction();

    if($tipo[0] > 0)
    {
      $sql='UPDATE usuariostbl SET
            estado=0
            WHERE id=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id', $id);
      $s->execute();
    }
    else
    {
      $sql='DELETE FROM usuarioactivtbl WHERE usuarioidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$id);
      $s->execute();

      $sql='DELETE FROM usuarioreptbl WHERE usuarioidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$id);
      $s->execute();

      $sql='DELETE FROM usuariostbl where id=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$id);
      $s->execute();
    }
    $pdo->commit();
  }
  catch (PDOException $e)
  {
    $pdo->rollback();
    $mensaje='Hubo un error borrando el usuario'.$e;
    throwError($mensaje);
  }

  header('Location: .');
  exit();
}
  
/**************************************************************************************************/
/*   */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try
{
  $resultado=$pdo->query('SELECT id, nombre, apellido FROM usuariostbl');
}
catch (PDOException $e)
{
  $mensaje='Error en la recuperación de la base de datos de usuarios';
  throwError($mensaje);
}

foreach ($resultado as $linea)
{
  $usuarios[]=array('id'=>$linea['id'], 
                    'nombre'=>$linea['nombre'],
                    'apellido'=>$linea['apellido']);
}
include 'formausuarios.html.php';

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
function insertActividades($actividades, $usuarioid){
  global $pdo;
  foreach ($actividades as $actividad)
  {
    try
    {
      $sql='INSERT INTO usuarioactivtbl SET
            usuarioidfk=:usuarioid,
            actividfk=:actividadid';
      $s=$pdo->prepare($sql);
      $s->bindValue(':usuarioid',$usuarioid);
      $s->bindValue(':actividadid',$actividad);
      $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error tratando de guardar las actividades';
      throwError($mensaje);
    }
  }
}

/**************************************************************************************************/
/*   */
/**************************************************************************************************/
function insertRepresentantes($representantes, $usuarioid){
  global $pdo;
  foreach ($representantes as $representante)
  {
    try
    {
      $sql='INSERT INTO usuarioreptbl SET
            usuarioidfk=:usuarioid,
            representanteidfk=:representanteid';
      $s=$pdo->prepare($sql);
      $s->bindValue(':usuarioid',$usuarioid);
      $s->bindValue(':representanteid',$representante);
      $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error tratando de guardar los representantes';
      throwError($mensaje);
    }
  }
}

?>
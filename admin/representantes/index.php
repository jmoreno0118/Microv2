<?php
 //********** REPRESENTANES **********
include_once '../../conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';

$estados= array('Aguascalientes','Baja California','Baja California Sur','Campeche','Coahuila de Zaragoza','Colima','Chiapas','Chihuahua','Distrito Federal','Durango','Guanajuato','Guerrero','Hidalgo','Jalisco','México','Michoacán','Morelos','Nayarit','Nuevo León','Oaxaca','Puebla','Querétaro','Quintana Roo','San Luis Potosí','Sinaloa','Sonora','Tabasco','Tamaulipas','Tlaxcala','Veracruz','Yucatán','Zacatecas');


if (!usuarioRegistrado())
{
  header('Location: '.url);
  exit();
}
if (!usuarioConPermiso('Administra representantes'))
{
  $mensaje='Solo el Admistrador de representantes tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/* Crear un nuevo representante */
/**************************************************************************************************/
if (isset($_GET['representantenuevo']))
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectadb.inc.php';
  $pestanapag='Agraga representante';
  $titulopagina='Agraga a un nuevo representante';
  $accion='agregarepresentante';
  $nombre='';
  $estado='';
  $tel='';
  $id='';
  $boton='Agrega representnte';
  include 'formacapturarep.html.php';
  exit();
}

/**************************************************************************************************/
/* Insertar nuevo representante */
/**************************************************************************************************/
if (isset($_GET['agregarepresentante']))
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='INSERT INTO representantestbl SET
          nombre=:nombre,
          estado=:estado,
          tel=:tel';
    $s=$pdo->prepare($sql);
    $s->bindValue(':nombre',$_POST['nombre']);
    $s->bindValue(':estado',$_POST['estado']);
    $s->bindValue(':tel',$_POST['tel']);
    $s->execute();	
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error al tratar de agregar al representante. Favor de intentar nuevamente.';
    throwError($mensaje);
  }
  header('Location: .');
  exit();
}

/**************************************************************************************************/
/* Editar un representante */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Editar')
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='SELECT id, nombre, estado, tel FROM representantestbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error obtenindo la informacion del representante';
    throwError($mensaje);
  }   
  $resultado=$s->fetch();
  $pestanapag='Edita representante';
  $titulopagina='Edición de la información del representante';
  $accion='editarepresentante';
  $id=$resultado['id'];
  $nombre=$resultado['nombre'];
  $estado=$resultado['estado'];
  $tel=$resultado['tel'];
  $boton='Salva cambios';

  include 'formacapturarep.html.php';;
  exit();  
}

/**************************************************************************************************/
/* Salvar edición de un representante */
/**************************************************************************************************/
if (isset($_GET['editarepresentante']))
{ 
  include direction.functions.'conectadb.inc.php';
  try
  { 
    $sql='UPDATE representantestbl SET
    nombre=:nombre,
    estado=:estado,
    tel=:tel WHERE id=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->bindValue(':nombre',$_POST['nombre']);
    $s->bindValue(':estado',$_POST['estado']);
    $s->bindValue(':tel',$_POST['tel']);
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error en la actalización del representante';
    throwError($mensaje);
  }   
  header ('Location: .');
  exit();
}

/**************************************************************************************************/
/* Borrar un representante */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Borrar')
{
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='SELECT id, nombre, estado FROM representantestbl WHERE id=:id';
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
  $nombre=$resultado['nombre'];
  $estado=$resultado['estado'];
  include 'formaconfirma.html.php';
  exit();
}

/**************************************************************************************************/
/* Continuar borrado de un representante */
/**************************************************************************************************/
if (isset($_POST['accion']) and $_POST['accion']=='Continuar')
{
  include direction.functions.'conectadb.inc.php';
  // verifica que no tenga ordenes abiertas
  echo 'entro a verificacion de ordenes';
  try
  {
    $sql='SELECT COUNT(*) FROM ordenestbl
          WHERE representanteidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();
  }
  catch (PDOexception $e)
  {
    $mensaje='Existe un error localizando los datos de las ordenes de trabajo del representante.  Favor de volver a intentar.';
    throwError($mensaje);
  }
  echo ' salgo de verificacion de ordenes'; 
  $linea=$s->fetch();
  if ($linea[0]>0)
  {
    echo ' entro al if de que encontro ordenes'; 
    $mensaje='Lo sentimos no se pude dar de baja a este representante por tener ordenes asociadas a el';
    throwError($mensaje);
  }
  else
  {
    try
    {
      $sql='DELETE FROM representantestbl WHERE id=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['id']);
      $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error borrando al representante. Intente de nuevo. '.$e;
      throwError($mensaje);
    }
  }  
  header('Location: .');
  exit();
}
  
/**************************************************************************************************/
/* Ver tabla de representantes */
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
try
{
  $resultado=$pdo->query('SELECT id, nombre, estado FROM representantestbl');
}
catch (PDOException $e)
{
  $mensaje='Error en la recuperación de la base de datos de representantes';
  throwError($mensaje);
}

foreach ($resultado as $linea)
{
  $representantes[]=array('id'=>$linea['id'], 
                          'nombre'=>$linea['nombre'],
                          'estado'=>$linea['estado']);
}
include 'formarepresentantes.html.php';
?> 
    
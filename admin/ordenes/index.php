<?php
//********** ORDENES **********
include_once '../../conf.php';
include_once direction.functions.'ayudas.inc.php';
include_once direction.functions.'acceso.inc.php';

$especialidades= array('MH','ME','Medicas');
$higiene= array('Iluminacion','Nivel sonoro equivalente','Dosis de ruido', 'temperaturas extremas/abatidas','Radiaciones NO ionizantes','Vibraciones mano-brazo','Vibraciones cuerpo completo','Radiaciones ionizantes');
$ecologia=array('NOM 001','NOM 002', 'NOM 003','Fuentes fijas','Ruido periferico','Suelos',' CRETIB');

if (!usuarioRegistrado())
{
  header('Location: '.url);
  exit();
}
if (!usuarioConPermiso('Administra OT'))
{
  $mensaje='Solo el Admistrador de ordenes tiene acceso a esta parte del programa';
  include direction.views.'accesonegado.html.php';
  exit();
}

/**************************************************************************************************/
/* Crear nueva orden */
/**************************************************************************************************/
if (isset($_GET['ordenueva']))
{   
  include 'formanuevaorden.html.php';
  exit();
}

/**************************************************************************************************/
/* Continuar creando la orden nueva */
/**************************************************************************************************/
if (isset($_GET['contordenueva']))
{
  //include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectaclientedb.inc.php';
  include direction.functions.'conectadb.inc.php';
  if (!validanuevaorden($_POST['ot']))
  {
    include 'formanuevaorden.html.php';
    exit();	 
  }
  // extae los datos fechalta, clienteid y especialidad de las ordenes administrativas
  inforden($_POST['ot']);
  // se extae de Clientes los datos de la persona a quien se dirije el estudio
  infocontacto($clienteid);
  setRazonSocial($clienteid);
  $pestanapag='Agrega OT';
  $titulopagina='Agrega una nueva orden de trabajo';
  $accion='agregaot';
  $ot=$_POST['ot'];
  $representanteid='';
  $id='';
  $boton='Agrega orden';
  $otant='';
  $planta = 0;
  // genera las listas de clientes, representantes,higiene y ecologia.
  //$cliente=getcliente(clienteid);
  $representantes=listarepresentantes();
  listahig_ecol();
  include 'formacapturaorden.html.php';
  exit();
}

/**************************************************************************************************/
/* Guardar la orden nueva */
/**************************************************************************************************/
if (isset($_GET['agregaot']))
{
  include direction.functions.'conectadb.inc.php';

  if(!isset($_POST['planta'])){
    try
    {
      $sql='SELECT * FROM clientestbl WHERE Numero_Cliente=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$_POST['cliente']);
      $s->execute();
      $cliente=$s->fetch();

      $sql='SELECT * FROM plantastbl WHERE
            razonsocial=:razonsocial AND
            planta=:planta AND
            calle=:calle AND
            colonia=:colonia AND
            ciudad=:ciudad AND
            estado=:estado AND
            cp=:cp AND
            rfc=:rfc AND
            Numero_Clienteidfk=:cliente';
      $s=$pdo->prepare($sql);
      $s->bindValue(':razonsocial', $cliente['Razon_Social']);
      $s->bindValue(':planta', $cliente['Razon_Social']);
      $s->bindValue(':calle', $cliente['Calle_Numero']);
      $s->bindValue(':colonia', $cliente['Colonia']);
      $s->bindValue(':ciudad', $cliente['Ciudad']);
      $s->bindValue(':estado', $cliente['Estado']);
      $s->bindValue(':cp', $cliente['Codigo_Postal']);
      $s->bindValue(':rfc', $cliente['RFC']);
      $s->bindValue(':cliente', $cliente['Numero_Cliente']);
      $s->execute();
      $existe=$s->fetch();

      if(!$existe)
      {
        $sql='INSERT INTO plantastbl SET
              razonsocial=:razonsocial,
              planta=:planta,
              calle=:calle,
              colonia=:colonia,
              ciudad=:ciudad,
              estado=:estado,
              cp=:cp,
              rfc=:rfc,
              Numero_Clienteidfk=:cliente';
        $s=$pdo->prepare($sql);
        $s->bindValue(':razonsocial', $cliente['Razon_Social']);
        $s->bindValue(':planta', $cliente['Razon_Social']);
        $s->bindValue(':calle', $cliente['Calle_Numero']);
        $s->bindValue(':colonia', $cliente['Colonia']);
        $s->bindValue(':ciudad', $cliente['Ciudad']);
        $s->bindValue(':estado', $cliente['Estado']);
        $s->bindValue(':cp', $cliente['Codigo_Postal']);
        $s->bindValue(':rfc', $cliente['RFC']);
        $s->bindValue(':cliente', $cliente['Numero_Cliente']);
        $s->execute();
        $_POST['planta']=$pdo->lastInsertid();
      }
      else
      {
        $_POST['planta']=$existe['id'];
      }
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error al tratar de agregar la planta. Favor de intentar nuevamente. '.$e;
      throwError($mensaje);
    }
  }

  try
  {
    $sql='INSERT INTO ordenestbl SET
          ot=:ot,
          fechalta=:fechalta,
          representanteidfk=:represetanteid,
          clienteidfk=:clienteid,
          tipo=:tipo,
          atencion=:atencion,
          atenciontel=:atenciontel,
          atencioncorreo=:atencioncorreo,
          plantaidfk=:plantaidfk';
    $s=$pdo->prepare($sql);
    $s->bindValue(':ot',$_POST['ot']);
    $s->bindValue(':fechalta',$_POST['fechalta']);
    $s->bindValue(':represetanteid',$_POST['representante']);
    $s->bindValue(':clienteid',$_POST['cliente']);
    $s->bindValue(':tipo',$_POST['tipo']);
    $s->bindValue(':atencion',$_POST['atencion']);
    $s->bindValue(':atenciontel',$_POST['atenciontel']);
    $s->bindValue(':atencioncorreo',$_POST['atencioncorreo']);
    $s->bindValue(':plantaidfk',$_POST['planta']);
    $s->execute();	
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error al tratar de agregar la orden. Favor de intentar nuevamente. '.$e;
    throwError($mensaje);
  }
  $ordenid=$pdo->lastInsertId();
  try
  {
    $pdo->beginTransaction();
    if (isset($_POST['higienestudios']) AND isset($_POST['tipo']) AND $_POST['tipo']=='MH')
    {
      $sql='INSERT INTO estudiostbl SET
            nombre=:nombre,
            ordenidfk=:ordenid';
      $s=$pdo->prepare($sql);
      foreach ($_POST['higienestudios'] as $estudio)
      {
        $s->bindValue(':nombre',$estudio);
        $s->bindValue(':ordenid',$ordenid);
        $s->execute();
      }   
    }
    if (isset($_POST['ecologiaestudios']) AND isset($_POST['tipo']) AND $_POST['tipo']=='ME')
    {
      $sql='INSERT INTO estudiostbl SET
            nombre=:nombre,
            ordenidfk=:ordenid';
      $s=$pdo->prepare($sql);
      foreach ($_POST['ecologiaestudios'] as $estudio)
      {
        $s->bindValue(':nombre',$estudio);
        $s->bindValue(':ordenid',$ordenid);
        $s->execute();
      }	   
    }
    $pdo->commit();
  }
  catch (PDOException $e)
  {
    $pdo->rollback();
    $mensaje='Hubo un error al tratar de guardar los estudios. Favor de intentar nuevamente.';
    throwError($mensaje);
  } 
  header('Location: .');
  exit();
} 

/**************************************************************************************************/
/* Editar una orden */
/**************************************************************************************************/
  if (isset($_POST['accion']) and $_POST['accion']=='Editar')
  {
  include direction.functions.'conectadb.inc.php';
  try
  {
    $sql='SELECT id, ot, clienteidfk, representanteidfk, tipo, fechalta, atencion, atenciontel, atencioncorreo, plantaidfk FROM ordenestbl WHERE id=:id';
    $s= $pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']); 
    $s->execute();
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error obtenindo la informacion de las ordenes de trabajo';
    throwError($mensaje);
  }   
  $resultado = $s->fetch();
  $pestanapag = 'Edita OT';
  $titulopagina = 'Edición de la información de la orden de trabajo';
  $accion = 'editaorden';
  $id = $resultado['id'];
  $ot = $resultado['ot'];
  $clienteid = $resultado['clienteidfk'];
  setRazonSocial($resultado['clienteidfk']);
  $fechalta = $resultado['fechalta'];
  $representanteid = $resultado['representanteidfk'];
  $especialidad = $resultado['tipo'];
  $atencion = $resultado['atencion'];
  $atenciontel = $resultado['atenciontel'];
  $atencioncorreo = $resultado['atencioncorreo'];
  $planta = (isset($resultado['plantaidfk']))?$resultado['plantaidfk']:0;
  $boton = 'Salva cambios';
  $otant = $ot; 
  // genera las listas de clientes, representantes,higiene y ecologia.
  //$clientes = listaclientes();
  $representantes = listarepresentantes();
  listahig_ecol($id);
  include 'formacapturaorden.html.php';
  exit();  
}
   

/**************************************************************************************************/
/* Salvar  orden */
/**************************************************************************************************/ 
 //************************************************************************************
 //*nota.- se requiere adicionar si se acepta el cambio o                             *
 //* para aceptar el cambio esta orden no debe de tener ningun estudio capturado de lo*
 //*que se desea cambiar                                        *
 //************************************************************************************
if (isset($_GET['editaorden']))
{
  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/conectaclientedb.inc.php';
  include direction.functions.'conectadb.inc.php';
  if ($_POST['otant']<>$_POST['ot'])
  {
    if (!validanuevaorden($_POST['ot']))
    {
      cargavalores();
      include 'formacapturaorden.html.php';
      exit();	 
    }
  }	 
  try
  {
    $pdo->beginTransaction();
    $sql='UPDATE ordenestbl SET
          ot=:ot,
          clienteidfk=:clienteid,
          representanteidfk=:representanteid,
          tipo=:tipo,
          atencion=:atencion,
          atenciontel=:atenciontel,
          atencioncorreo=:atencioncorreo,
          plantaidfk=:plantaidfk
          WHERE id=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->bindValue(':ot',$_POST['ot']);
    $s->bindValue(':clienteid',$_POST['cliente']);
    $s->bindValue(':representanteid',$_POST['representante']);
    $s->bindValue(':tipo',$_POST['tipo']);
    $s->bindValue(':atencion',$_POST['atencion']);
    $s->bindValue(':atenciontel',$_POST['atenciontel']);
    $s->bindValue(':atencioncorreo',$_POST['atencioncorreo']);
    $s->bindValue(':plantaidfk',$_POST['planta']);
    $s->execute();

    //borrra los estudios anteriores y garda los nuevos
    $sql='DELETE FROM estudiostbl WHERE ordenidfk=:id';
    $s=$pdo->prepare($sql);
    $s->bindValue(':id',$_POST['id']);
    $s->execute();

    if (isset($_POST['higienestudios']) AND isset($_POST['tipo']) AND $_POST['tipo']=='MH')
    {
      $sql='INSERT INTO estudiostbl SET 
            nombre=:estudio,
            ordenidfk=:ordenid';
      $s=$pdo->prepare($sql);
      foreach ($_POST['higienestudios'] AS $estudio)
      {
        $s->bindValue(':estudio',$estudio);
        $s->bindValue(':ordenid',$_POST['id']);
        $s->execute();
      }
    }

    if (isset($_POST['ecologiaestudios']) AND isset($_POST['tipo']) AND $_POST['tipo']=='ME')
    {
      $sql='INSERT INTO estudiostbl SET  
            nombre=:estudio,
            ordenidfk=:ordenid';
      $s=$pdo->prepare($sql);
      foreach ($_POST['ecologiaestudios'] as $estudio)
      {
        $s->bindValue(':estudio',$estudio);
        $s->bindValue(':ordenid',$_POST['id']);
        $s->execute();
      }
    }		 
    $pdo->commit();
  }
  catch (PDOException $e)
  {
    $pdo->rollback();
    $mensaje='Hubo un error en la actualización de la orden.  Favor de intentarlo nuevamente'.$e;
    throwError($mensaje);
  }   
  header ('Location: .');
  exit();
}

/**************************************************************************************************/
/* Buscar una orden */
/**************************************************************************************************/
if (isset($_GET['accion']) and $_GET['accion']=='Buscar')
{
  include direction.functions.'conectadb.inc.php';
  // instruccion basica
  $select='SELECT ordenestbl.id, ot, tipo, fechalta, clienteidfk, representanteidfk, nombre, Razon_Social, plantaidfk
          FROM ordenestbl
          INNER JOIN representantestbl ON representanteidfk=representantestbl.id
          INNER JOIN clientestbl ON clienteidfk=Numero_Cliente';
  $where='';
  $placeholders=array();
  // si se selecciona ot
  if ($_GET['ot']!='')
  {
    $where .= " AND ot LIKE :ot";
    $placeholders[':ot']='%'.$_GET['ot'].'%';
  }
  // si se selecciona fecha inicio y/o fecha fin
  if ($_GET['fechaini']!='')
  {
    $where .= " AND fechalta > :fechaini";
    $placeholders[':fechaini']=$_GET['fechaini'];
  }
  if ($_GET['fechafin']!='')
  {
    $where .= " AND fechalta < :fechafin";
    $placeholders[':fechafin']=$_GET['fechafin'];
  }   
  // si se selecciona tipo
  if ($_GET['tipo']!='')
  {
    $where .= " AND tipo = :tipo";
    $placeholders[':tipo']=$_GET['tipo'];
  }
  // si se selecciono un representante
  if ($_GET['representante'] !='')
  {
    $where .= " AND representanteidfk= :representante";
    $placeholders[':representante']=$_GET['representante'];
  }
  // se hace la busqueda en donde s->execute($placeholders) sustituye la funcion bindValue pag 218
  try
  {
    $sql=$select.$where;
    $s=$pdo->prepare($sql);
    $s->execute($placeholders);
  }
  catch (PDOException $e)
  {
    $mensaje='hubo un error obteneindo a los clientes';
    throwError($mensaje);
  }
  foreach ($s as $linea)
  {	
    $ordenes[]=array(
                    'id' => $linea['id'],
                    'ot' => $linea['ot'],
                    'cliente' => $linea['Razon_Social'],
                    'representante' => $linea['nombre'],
                    'tipo' => $linea['tipo'],
                    'fechalta' => $linea['fechalta'],
                    'planta' => $linea['plantaidfk']
                );
  }
  include 'formaordenes.html.php';
  exit();
}

/**************************************************************************************************/
/* Acción default
/**************************************************************************************************/
include direction.functions.'conectadb.inc.php';
$representantes=listarepresentantes();
include 'formabuscaorden.html.php';
exit();
  


/**************************************************************************************************/
/* Verifica que la orden que se desea dar de alta no exista */
/**************************************************************************************************/
function repetidaot($ot='')
{
  global $pdo;
  try
  {
    $sql='SELECT count(*)
          FROM ordenestbl
          WHERE ot=:ot';
    $s=$pdo->prepare($sql);
    $s->bindValue(':ot',$ot);
    $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error leyendo las bases de datos de las ordenes.  Favor de intentarlo nuevamente';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  if ($resultado[0]>0)
  {
    return true;
  }
  else
  {
    return false;
  }
}

/**************************************************************************************************/
/* Verificar que exista la orden */
/**************************************************************************************************/
function existeotadmin($ot='')
{
  global $cpdo;
  try
  {
    $sql='SELECT count(*)
          FROM ordenes_muestreo 
          WHERE Numero_Orden_Muestreo=:ot';
    $s=$cpdo->prepare($sql);
    $s->bindValue(':ot',$ot);
    $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error leyendo las bases de datos de las ordenes en la sección administrativa.  Favor de intentarlo nuevamente';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  if ($resultado[0]>0)
  {
    return true;
  }
  else 
  {
    return false;
  }
}

/**************************************************************************************************/
/* Valida que el campo orden no sea vacío, que exista la orden administrativa y no este repetida */
/**************************************************************************************************/
function validanuevaorden()
{
  global $mensaje;
  if (!isset($_POST['ot']) or $_POST['ot']==''){
    $mensaje='Para continuar es necesario que se coloque un número de orden de trabajo';
    return false;
  }	
  else{
    if (!existeotadmin($_POST['ot'])){
      $mensaje='El numero de orden de trabajo ingresado no esta dado de alta administrativamente. Favor de verificar la información e intentar nuevamente.';
      return false;
    }
    else
    {
      if (repetidaot($_POST['ot']))
      {
        $mensaje='El numero de orden de trabajo ingresado esta asignado a otra orden.  Favor de verificar la información e intentar nuevamente.';
        return false;
      }
      else
      {
        return true;
      }
    }
  }
}

/**************************************************************************************************/
/* Extrae la informacion de fechalta, especialidad y cliente de la orden */
/**************************************************************************************************/
function inforden($ot='')
{
  global $cpdo, $fechalta, $clienteid, $especialidad;
  try
  {
    $sql='SELECT Clave_Empresa_Muestreo, Fecha_Registro_Orden, Numero_Cliente_Informe
    FROM ordenes_muestreo
    INNER JOIN cotizaciones ON ordenes_muestreo.Numero_Solicitud_orden = cotizaciones.Numero_Cotizacion
    WHERE Numero_Orden_Muestreo=:ot';
    $s=$cpdo->prepare($sql);
    $s->bindValue(':ot',$ot);
    $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error leyendo las bases de datos de las ordenes en la sección administrativa.  Favor de intentarlo nuevamente';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  $fechalta=$resultado['Fecha_Registro_Orden'];
  $clienteid=$resultado['Numero_Cliente_Informe'];
  $especialidad=$resultado['Clave_Empresa_Muestreo'];
}


/**************************************************************************************************/
/* Extrae los datos del contacto de la base de clientes */
/**************************************************************************************************/
function infocontacto($clienteid='')
{
  global $pdo,$atencion, $atenciontel, $atencioncorreo;
  try
  {
    $sql='SELECT Nombre_Usuario, Telefono_Usuario, Email_Usuario
          FROM clientestbl
          WHERE Numero_Cliente=:clienteid';
    $s=$pdo->prepare($sql);
    $s->bindValue(':clienteid',$clienteid);
    $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error tratando de extraer los datos del usuario.  favor de intentar nuevamente.';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  $atencion=$resultado['Nombre_Usuario'];
  $atenciontel=$resultado['Telefono_Usuario'];
  $atencioncorreo=$resultado['Email_Usuario'];
}

/**************************************************************************************************/
/* Extrae los datos del contacto de la base de clientes */
/**************************************************************************************************/
function setRazonSocial($clienteid='')
{
  global $pdo, $razonsocial;
  try
  {
    $sql='SELECT Razon_Social
          FROM clientestbl
          WHERE Numero_Cliente=:clienteid';
    $s=$pdo->prepare($sql);
    $s->bindValue(':clienteid',$clienteid);
    $s->execute();
  }
  catch(PDOException $e)
  {
    $mensaje='Hubo un error tratando de extraer los datos del usuario.  favor de intentar nuevamente.';
    throwError($mensaje);
  }
  $resultado=$s->fetch();
  $razonsocial=$resultado['Razon_Social'];
}


/**************************************************************************************************/
/* Genera las listas de los clientes */
/**************************************************************************************************/
/*function listaclientes()
{
  global $pdo;
  try
  {
    $resultados=$pdo->query('SELECT Numero_Cliente, Razon_Social FROM clientestbl ORDER BY Razon_Social');
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error tratando de obtener la informacion de los representantes';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/error.html.php';
    exit();
  }  
  foreach ($resultados as $resultado)
  {
    $clientes[]=array('id'=>$resultado['Numero_Cliente'],
                      'nombre'=>$resultado['Razon_Social']);
  }
  return $clientes;
}*/


/**************************************************************************************************/
/* Construye lista de representantes */
/**************************************************************************************************/
function listarepresentantes()
{
  global $pdo; 
  // 
  try
  {
    $resultados=$pdo->query('SELECT id, nombre FROM representantestbl');
  }
  catch (PDOException $e)
  {
    $mensaje='Hubo un error tratando de obtener la informacion de los representantes';
    throwError($mensaje);
  }  
  foreach ($resultados as $resultado)
  {
    $representantes[]=array('id'=>$resultado['id'],
                            'nombre'=>$resultado['nombre']);
  }
  return $representantes;  
}



/**************************************************************************************************/
/* Genera la lista de los estudios para ecologia e higiene */
/**************************************************************************************************/
function listahig_ecol($id='')
{
  global $higiene, $ecologia,
  $pdo,
  $higienestudios, $ecologiaestudios;
  $estudioSelec=array();
  if ($id!=''){
    try
    {
      $sql='SELECT id, nombre
            FROM estudiostbl
            WHERE ordenidfk=:id';
      $s=$pdo->prepare($sql);
      $s->bindValue(':id',$id);
      $s->execute();
    }
    catch (PDOException $e)
    {
      $mensaje='Hubo un error tratando de obtener la informacion de los estudios solicitados';
      throwError($mensaje);
    } 
    foreach ($s as $resultado)
    {
      $estudioSelec[]=$resultado['nombre'];
    }
  } 

  for ($x=0; $x<count($higiene); $x++)
  {
    $higienestudios[]=array('id'=>$x,
                            'nombre'=>$higiene[$x],
                            'seleccionada'=>in_array($higiene[$x],$estudioSelec));
  }
  for ($x=0; $x<count($ecologia); $x++)
  {
    $ecologiaestudios[]=array('id'=>$x,
                              'nombre'=>$ecologia[$x],
                              'seleccionada'=>in_array($ecologia[$x],$estudioSelec));
  }
  return;
}



/**************************************************************************************************/
/* Asigna los valores a variables que se muestran en la hoja de captura orden */
/**************************************************************************************************/
function cargavalores()
{
  global $higiene,$ecologia,$especialidades,$id,$ot,$clienteid,$representanteid,$especialidad,$atencion,$atenciontel,$atencioncorreo,$otant,$clientes,$representantes,$higienestudios,$ecologiaestudios,$pestanapag,$titulopagina,$accion,$boton;
  $pestanapag = 'Edita OT';
  $titulopagina = 'Edición de la información de la orden de trabajo';
  $accion = 'editaorden';
  $boton = 'Salva cambios';
  $id = $_POST['id'];
  $ot = $_POST['ot'];
  $clienteid = $_POST['cliente'];
  $representanteid = $_POST['representante'];
  $especialidad = $_POST['tipo'];
  $atencion = $_POST['atencion'];
  $atenciontel = $_POST['atenciontel'];
  $atencioncorreo = $_POST['atencioncorreo'];
  $otant = $_POST['otant'];
  $clientes = listaclientes();
  $representantes = listarepresentantes();

  if (!isset($_POST['higienestudios']))
  {
    $estudioSelec=array();
  }
  else
  {
    $estudioSelec=$_POST['higienestudios'];
  }

  for ($x=0; $x<count($higiene); $x++) 
  {
    $higienestudios[] = array('id'=>$x,
                              'nombre'=>$higiene[$x],
                              'seleccionada'=>in_array($higiene[$x],$estudioSelec));
  }

  if (!isset($_POST['ecologiaestudios'])){
    $estudioSelec=array();
  }
  else
  {
    $estudioSelec=$_POST['ecologiaestudios'];
  }

  for ($x=0; $x<count($ecologia); $x++)
  {
    $ecologiaestudios[] = array('id'=>$x,
                                'nombre'=>$ecologia[$x],
                                'seleccionada'=>in_array($ecologia[$x],$estudioSelec));
  }
}
?>
<?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<title><?php htmlout($pestanapag); ?></title>
  <meta charset="utf-8" />
  <!--[if lt IE 9]>
   <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]--> 
   <link rel="stylesheet" type="text/css" href="/reportes/estilo.css" />
<link rel="stylesheet" href="../includes/jquery-validation-1.13.1/demo/site-demos.css">
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/lib/jquery.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/lib/jquery-1.11.1.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/dist/jquery.validate.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/dist/additional-methods.js"></script>
</head>
<body>
<div id="contenedor">
  <header>
   <?php 
    $ruta='/reportes/img/logoblco2.gif';
	  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/encabezado.inc.php'; ?>
  </header>
  <div id="cuerpoprincipal">
   <h2><?php htmlout($titulopagina); ?></h2>
    <?php if($equiposel): ?>
      <table>
        <tr>
          <th></th>
          <th width="70">Inventario</th>
          <th width="100">Equipo</th>
          <th width="60">Parám 1</th>
          <th width="60">Cal Ref</th>
          <th width="60">Cal Ini</th>
          <th width="60">Cal Fin</th>
          <th width="60">Parám 2</th>
          <th width="60">Cal Ref</th>
          <th width="60">Cal Ini</th>
          <th width="60">Cal Fin</th>
          <th width="60">Parám 3</th>
          <th width="60">Cal Ref</th>
          <th width="60">Cal Ini</th>
          <th width="60">Cal Fin</th>
        </tr>
        <?php foreach ($equiposel as $equipo): ?>
          <tr>
            <td>
              <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <input type="hidden" name="equipoid" value="<?php echo $equipo['id']; ?>">
                <input type="submit"  name="accion" value="Ver">
                <input type="submit"  name="accion" value="Borrar">
              </form>
            </td>
            <td width="70"><?php echo $equipo['inventario']; ?></td>
            <td width="100"><?php echo $equipo['descripcion']; ?></td>

            <?php for ($i=0; $i <= 2; $i++): ?>

              <?php if(isset($equipo['parametros'][$i])){ ?>
                <td width="60">
                  <?php echo $equipo['parametros'][$i]['parametro']; ?>
                  <br>
                  <?php echo $equipo['parametros'][$i]['unidades']; ?>
                  <br>
                  <?php //echo $equipo['parametros'][$i]; ?>
                </td>
                <td width="60">
                  <?php echo $equipo['parametros'][$i]['refesperada1']; ?>
                  <br>
                  <?php echo $equipo['parametros'][$i]['refesperada2']; ?>
                  <br>
                  <?php echo $equipo['parametros'][$i]['refesperada3']; ?>
                </td>
                <td width="60">Cal Ini</td>
                <td width="60">Cal Fin</td>
              <?php }else{ ?>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
                <td width="60"></td>
              <?php } ?>

            <?php endfor; ?>
          </tr>
        <?php endforeach; ?>
      </table>
     <?php else : ?>
       <p>Lo sentimos no se encontro ningún equipo seleccionado</p> 
     <?php endif; ?>

    <form id="medsform" name="medsform" action="" method="post">
      <select name="equipos[]" multiple size=20>
        <?php foreach ($equipos as $key => $value){ ?>
          <option value=<?php echo $value['id'] ?>><?php echo $value['tipo'].' - '.$value['descripcion'].' - '.$value['inventario']; ?></option>
        <?php } ?>
      </select>
      <br>
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      <input type="submit" name="accion" value="Agregar">
    </form>

    <p><a href="..">Volver a bitacoras</a></p>
  </div>  <!-- cuerpoprincipal -->
  <div id="footer">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
  </div>  <!-- footer -->
  </div> <!-- contenedor -->
</body>
</html>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/acceso.inc.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<title>Captura</title>
  <meta charset="utf-8" />
  <!--[if lt IE 9]>
   <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]--> 
   <link rel="stylesheet" type="text/css" href="/reportes/estilo.css" />
</head>
<body>
<div id="contenedor">
  <header>
   <?php 
      $ruta='/reportes/img/logoblco2.gif';
    include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/encabezado.inc.php'; ?>
  </header>
  <div id="cuerpoprincipal">
   <h2>Bitacoras</h2>
   <p><a href="..">Regresa a administrador</a></p>
   <form action="" method="post">
    <input type="submit" name="accion" value="Nueva">
   </form>
   <?php if($bitacoras): ?>
    <table>
      <tr><th>Orden</th><th></th></tr>
      <?php foreach ($bitacoras as $bitacora): ?>
        <tr>
          <td><?php htmlout($bitacora['ot']); ?></td>
          <td>
            <form action="" method="post">
              <input type="hidden" name="id" value="<?php echo $bitacora['id']; ?>">
              <input type="submit" name="accion" value="Editar">
              <input type="submit" name="accion" value="Equipos">
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
   <?php else : ?>
     <p>Lo sentimos no se encontro ninguna bitacora</p> 
   <?php endif; ?>
  </div>  <!-- cuerpoprincipal -->
  <div id="footer">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
  </div>  <!-- footer -->
  </div> <!-- contenedor -->
</body>
</html>
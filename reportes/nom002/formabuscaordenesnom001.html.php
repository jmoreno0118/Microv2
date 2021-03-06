<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Captura</title>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
        <h2>Administrador de ordenes</h2>
        <form action="" method="get">
          <p>Buscar a una orden por:</p>
          <div>
            <label for="ot">Núm de OT:</label>
            <input type="text" name="ot" id="ot">
          </div>
          <?php  if(usuarioConPermiso('Supervisor')){ ?>
            <div>
            <input type="checkbox" name="otsproceso" id="otsproceso" checked>
            <label for="otsproceso">Ordenes en proceso</label>
            </div>
            <div>
          <?php }else{ ?>
            <input type="hidden" name="otsproceso" id="otsproceso">
          <?php } ?>
          <div>
            <input type="checkbox" name="supervisada" id="supervisada">
            <label for="supervisada">Ordenes revisadas</label>
          </div>
          <div>
            <input type="hidden" name="accion" value="buscar">
            <input type="submit" value="buscar">
          </div>
        </form>

        <?php if (isset($ordenes)): ?>
          <table>
            <caption><?php htmlout($tablatitulo); ?></caption>
            <tr><th>OT.</th><th>Cliente</th><th>Ciudad</th><th>Estado</th><th></th></tr>
            <?php foreach ($ordenes as $orden): ?>
            <tr>
              <td><?php htmlout($orden['ot']); ?></td>
              <td><?php htmlout(htmldecode($orden['razonsocial']))?></td>
              <td><?php htmlout(htmldecode($orden['ciudad']))?></td>
              <td><?php htmlout(htmldecode($orden['estado']))?></td>
              <td>
                <form action="?" method="post">
                  <div>
                    <input type="hidden" name="ot" value="<?php echo $orden['id']; ?>">
                    <input type="submit" name="accion" value="ver mediciones">
                  </div>
                </form>
                <!--form action="../plantas/index.php" method="post">
                  <div>
                    <input type="hidden" name="id" value="<?php echo $orden['idplanta']; ?>">
                    <input type="submit" name="accion" value="modificar planta">
                  </div>
                </form-->
              </td>
            </tr>
            <?php endforeach; ?>
          </table>
        <?php else : ?>
          <p>Lo sentimos no se encontro nunguna orden con las caracteristicas solicitadas</p>  
        <?php endif; ?>
        <p><a href="..">Regresa a administrador</a></p>
      </div>  <!-- cuerpoprincipal -->
      <div id="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
      </div>  <!-- footer -->
    </div> <!-- contenedor -->
  </body>
</html>
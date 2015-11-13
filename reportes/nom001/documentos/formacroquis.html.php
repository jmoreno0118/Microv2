<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php'; ?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <title>Documentos</title>
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
        <h2>OT <?php htmlout($nombreot['ot']) ?> croquis de la medición <?php htmlout($numedicion); ?></h2>
        <p><a href="../../nom001">Hacer otra búsqueda de ordenes</a> 
        <fieldset>
          <legend>Para subir documentos al sistema</legend>
            <form action="?" method="post" enctype="multipart/form-data">
              <div>
                <label for="tipo">Tipo de documento</label>
                <select name="tipo" id="tipo">
                  <?php
                    $tipo = array('Croquis','Expediente A', 'Expediente B', 'Expediente C','A1',
                      'A2','A3','A3.1','A4','A4.1','A5','A5.1','A2-B','A3-B','A3.1-B','A4-B','A4.1-B','A5-B',
                      'A5.1-B'
                      );
                    foreach ($tipo as $value) {
                      echo "<option>".$value."</option>";
                    }
                    ?>
                </select> 
              </div>
              <br>

              <div style="display:table;width:100%;">
                <div style="display:table-cell;width:50%;">
                  <table style="width:300px;">
                    <thead>
                      <tr>
                        <th>Formato</th>
                        <th>C</th>
                        <th>NC</th>
                        <th>NA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $array = array('ASC-F-1','ASC-F-2','ASC-F-4','LGM-AAM-001','APC-F-1A','OMW-F-17','OMW-F-1',
                            'OMW-F-2','AAS-F-24','OCC-F-58');

                      foreach ($array as $key => $value) {
                        ?>
                        <tr>
                          <td><?php echo $value; ?></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="C" <?php if( strcmp($docs[$value], "C") === 0 ) echo 'checked'; ?>></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="NC" <?php if( strcmp($docs[$value], "NC") === 0 ) echo 'checked'; ?>></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="NA" <?php if( strcmp($docs[$value], "NA") === 0 ) echo 'checked'; ?>></td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                   </table>

                </div>
                <div style="display:table-cell;width:50%;">

                  <table style="width:300px;">
                    <thead>
                      <tr>
                        <th>Formato</th>
                        <th>C</th>
                        <th>NC</th>
                        <th>NA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $array = array('AIR-F-11','AEI-F-15','OMW-F-15','OMW-F-4','OMW-F-5',
                            'OMW-F-6','OMW-F-16','OMW-F-9','OMW-F-20','OCC-F-25','Calibración termometro');

                      foreach ($array as $key => $value) {
                        ?>
                        <tr>
                          <td><?php echo $value; ?></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="C" <?php if( strcmp($docs[$value], "C") === 0 ) echo 'checked'; ?>></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="NC" <?php if( strcmp($docs[$value], "NC") === 0 ) echo 'checked'; ?>></td>
                          <td><input name="docs[<?php echo $value; ?>]" type="radio" value="NA" <?php if( strcmp($docs[$value], "NA") === 0 ) echo 'checked'; ?>></td>
                        </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                   </table>

                </div>
              </div>

              <div>
                <label for="archivo">Observaciones:</label><br>
                <textarea name="observaciones" style="resize:none;" rows=5 cols=60><?php if($observaciones) echo $observaciones; ?></textarea>
              </div>
              <br>
              <div>
                <input type="hidden" name="ot" value="<?php htmlout($ot);?>">
                <input type="hidden" name="id" value="<?php htmlout($id);?>">
                <input type="hidden" name="numedicion" value="<?php htmlout($numedicion);?>">
                <input type="hidden" name="hora" value="<?php htmlout(time());?>">
                <label for="archivo">Selecciona el archivo a subir</label>
                <input type="file" id="archivo" name="archivo">
                <input type="hidden" name="accion" value="subir"> 
                <input type="submit" value="Subir">  
              </div>
              <p>Nota: Los archivos que se permite subir al sistema deben tener un tamaño MAXIMO <strong>5Mb</strong></p> 
            </form> 
        </fieldset>
        
        <?php if(strcmp($documentos, "") !== 0){ ?>
          <table>
            <caption>Documentos en el sistema</caption>
            <tr><th>Nombre</th><th>Descripción</th><th>Enlace</th><th></th></tr>
            <?php foreach ($documentos as $documento): ?>
              <tr>
                <td><?php htmlout($documento['nombre']); ?></td>
                <td><?php htmlout($documento['tipo'])?></td>
                <td><a href="<?php htmlout($documento['liga'])?>" target="_blank"><?php htmlout($documento['nombre'])?></a></td>
                <td>
                <form action="?" method="post">
                  <div>
                    <input type="hidden" name="iddoc" value="<?php echo $documento['id']; ?>">
                    <input type="hidden" name="ot" value="<?php htmlout($ot);?>">
                    <input type="hidden" name="id" value="<?php htmlout($id);?>">
                    <input type="hidden" name="numedicion" value="<?php htmlout($numedicion);?>">
                    <input type="hidden" name="accion" value="borraplano">
                    <input type="submit" value="Borrar">
                  </div>
                </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </table>
        <?php } ?>
        
        <p><a href="../generales">Volver a mediciones</a></p>
      </div>  <!-- cuerpoprincipal -->
      <div id="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
      </div>  <!-- footer -->
    </div> <!-- contenedor -->
  </body>
</html>
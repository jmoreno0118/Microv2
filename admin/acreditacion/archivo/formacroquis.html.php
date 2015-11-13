<?php include_once direction.functions.'html.inc.php'; ?>
<?php open_html_head('Documentos'); ?>
<?php load_jquery(); ?>
<?php load_datatables(); ?>

<script type="text/javascript">
  $(document).ready(function(){
      $('#datatable').DataTable({
        "filter": false,
        "paging":   false,
        "ordering": false,
        "info":     false,
        "language": {
                    "sProcessing":     "Procesando...",
                    "sLengthMenu":     "Mostrar _MENU_ registros",
                    "sZeroRecords":    "No se encontraron resultados",
                    "sEmptyTable":     "Ningún dato disponible en esta tabla",
                    "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix":    "",
                    "sSearch":         "Buscar:",
                    "sUrl":            "",
                    "sInfoThousands":  ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst":    "Primero",
                        "sLast":     "Último",
                        "sNext":     "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                }
      });
  });
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3>Acreditación: <?php echo $nombreacred; ?></h3>
  <div>
    <h4>Para subir documentos al sistema</h4>
    <form action="?" method="post" class="form-inline" enctype="multipart/form-data">
      <div class="form-group">
        <label for="archivo">Selecciona el archivo a subir</label>
        <input type="file" id="archivo" name="archivo">
      </div>
      <br>
      <div>
        <input type="hidden" name="id" value="<?php htmlout($id);?>">
        <input type="hidden" name="hora" value="<?php htmlout(time());?>">
        <input type="hidden" name="accion" value="subir"> 
        <input type="submit" class="btn btn-success" value="Subir">  
      </div>
      <p class="help-block">Nota: Los archivos que se permite subir al sistema deben tener un tamaño MAXIMO <strong>2Mb</strong></p> 
    </form> 
  </div>
  
  <?php if( is_array($documentos) OR strcmp($documentos, "") !== 0){ ?>
    <h4>Documentos en el sistema</h4>
    <table id="datatable" class="table table-striped table-bordered compact">
      <thead>
        <tr><th>Nombre</th><th>Enlace</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($documentos as $documento): ?>
          <tr>
            <td><?php htmlout($documento['nombre']); ?></td>
            <td><a href="<?php htmlout($documento['nombrearchivado'])?>" target="_blank"><?php htmlout($documento['nombre'])?></a></td>
            <td>
            <form action="?" method="post">
              <div>
                <input type="hidden" name="iddoc" value="<?php echo $documento['id']; ?>">
                <input type="hidden" name="id" value="<?php htmlout($id);?>">
                <input type="hidden" name="accion" value="borraplano">
                <input type="submit" class="btn btn-danger" value="Borrar">
              </div>
            </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php } ?>
  <p class="help-block"><a href="..">Volver a acreditaciones</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
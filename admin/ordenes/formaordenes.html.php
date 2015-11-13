<?php include_once direction.functions.'html.inc.php'; ?>
<?php open_html_head('Ordenes'); ?>
<?php load_jquery(); ?>
<?php load_datatables(); ?>

<script type="text/javascript">
  $(document).ready(function(){
      $('#datatable').DataTable({
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
  <h3>Resultado de la búsqueda de las ordenes de trabajo</h3>
  <?php if (isset($ordenes)) : ?>
    <p><a href="?">Hacer otra búsqueda</a> 
    <p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
    <table id="datatable" class="table table-striped table-bordered compact">
      <colgroup>
        <col width="5%">
        <col width="35%">
        <col width="25%">
        <col width="5%">
        <col width="10%">
        <col width="20%">
      </colgroup>
      <thead>
        <tr>
          <th>OT.</th>
          <th>Cliente</th>
          <th>Representante</th>
          <th>Tipo</th>
          <th>Fecha</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($ordenes as $orden): ?>
          <tr>
            <td><?php htmlout($orden['ot']); ?></td>
            <td><?php htmlout(htmldecode(substr($orden['cliente'],0,50)))?></td>
            <td><?php htmlout($orden['representante'])?></td>
            <td><?php htmlout($orden['tipo'])?></td>
            <td><?php htmlout($orden['fechalta'])?></td>
            <td>
              <form action="" method="post">
                <div>
                  <input type="hidden" name="id" value="<?php echo $orden['id']; ?>">
                  <input class="btn btn-default" style="width:75px" type="submit" name="accion" value="Editar">
                  <input class="btn btn-danger" style="width:75px" type="submit" name="accion" value="Borrar" disabled>
                </div>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php else : ?>
    <p>Lo sentimos no se encontro nunguna orden con las caracteristicas solicitadas</p>	
  <?php endif; ?>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
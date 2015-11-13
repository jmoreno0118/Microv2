﻿<?php include_once direction.functions.'html.inc.php'; ?>
<?php open_html_head('Usuarios'); ?>
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
  <h3>Administra usuarios</h3>
  <p><a class="btn btn-default" href="?usuarionuevo">Agrega un nuevo usuario</a></p>
  <table id="datatable" class="table table-striped table-bordered compact">
      <colgroup>
        <col width="40%">
        <col width="40%">
        <col width="20%">
      </colgroup>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Apellidos</th>
          <th>Opciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $usuario): ?>
          <tr>
            <td><?php htmlout($usuario['nombre']); ?></td>
            <td><?php htmlout($usuario['apellido'])?></td>
            <td>
              <form action="" method="post">
                <div>
                  <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                  <input class="ntbn btn btn-default" type="submit" name="accion" value="Editar">
                  <input class="ntbn btn btn-danger" type="submit" name="accion" value="Borrar" disabled>
                </div>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
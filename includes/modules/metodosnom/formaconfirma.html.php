<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Confirmación'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <div id="confirma">
    <h3>Confirmación de borrado del metodo</h3>
    <form action="" method="post">
      <h4>Estás seguro de que deseas borrar el metodo <?php htmlout($valores['metodo']); ?> del 
      parametro <?php htmlout($valores['parametro']); ?>.</h4>
      <p>
        <input type="hidden" name="id" value="<?php htmlout($id); ?>">
        <input type="hidden" name="ot" value="<?php htmlout($_SESSION['OT']); ?>">
        <input type="submit" class="btn btn-danger" style="width:90px" name="accion" value="Cancelar">
        <input type="submit" class="btn btn-primary" style="width:90px" name="accion" value="Continuar">
      </p>
    </form> 
  </div> <!-- confirma -->
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
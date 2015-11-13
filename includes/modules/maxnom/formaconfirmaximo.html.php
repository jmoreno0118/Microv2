<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Confirmación'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <div id="confirma">
    <h3>Confirmación de borrado del valor máximo de la norma</h3>
    <form action="" method="post">.
      <h4>Estás seguro de que deseas borrar el valor máximo para descarge en: <?php htmlout($descargaen); ?> 
        y uso en <?php htmlout($uso); ?>.</h4>
      <p>
        <input type="hidden" name="id" value="<?php htmlout($id); ?>">
        <input type="submit" class="btn btn-danger" style="width:90px" name="accion" value="Cancelar">
        <input type="submit" class="btn btn-primary" style="width:90px" name="accion" value="Continuar">
      </p> 
    </form> 
  </div> <!-- confirma -->
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
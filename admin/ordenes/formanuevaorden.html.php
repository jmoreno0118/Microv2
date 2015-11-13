<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Orden nueva'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3>Alta de ordenes</h3>
  <h4>Para que dar de alta una orden en el sistema de captura es necesario que antes se de alta administrativamente</h4> 
  <?php if (isset($mensaje)): ?>
    <p><strong><?php htmlout($mensaje) ?></strong></p>
  <?php endif; ?>	
  <form action="?contordenueva" class="form-inline" method="post">
    <div class="form-group">
      <label for="ot">Num. de OT: </label>
      <input type="text" class="form-control" name="ot" id="ot" value="">
    </div>
    <br><br>
    <div>	
      <input type="submit" class="btn btn-success" value="Continuar">
    </div> 
  </form>
  <br>
  <p><a href="../ordenes">Regresa a busqueda de ordenes</a>	
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
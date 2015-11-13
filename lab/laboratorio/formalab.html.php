<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Laboratorio'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3>Laboratorio</h3>
  <form id="medsform" name="medsform" class="form-inline" action="index.php" method="post">
    <div class="form-group">
      <label>Norma: </label>
      <select name="norma" class="form-control">
        <?php foreach ($normas as $value) { ?>
          <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
        <?php } ?>
      </select>
    </div>
    <br><br>
    <div>
      <input type="submit" class="btn btn-success" name="accion" value="Crear">
    </div>
  </form>
  <br>
  <p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
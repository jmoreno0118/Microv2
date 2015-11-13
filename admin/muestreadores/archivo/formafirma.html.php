<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Firma'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h2>Muestreador: <?php echo $nombremuestreador; ?></h2>
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
    <?php if(strcmp($documentos['firma'], "") !== 0){ ?>
      Firma actual: <a href="<?php htmlout($documentos['firmaarchivar'])?>" target="_blank"><?php htmlout($documentos['firma'])?></a>
    <?php } ?>
  <?php } ?>
  
  <p class="help-block"><a href="..">Volver a muestreadores</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
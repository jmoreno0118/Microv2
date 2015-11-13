<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
i = 1;
  $(document).ready(function() {
    $("#plantaform").validate({
      rules: {
        nombre: {
         required: true
        }
      },
      success: "valid"
    });

    $('#agregar').click(function(e){
        e.preventDefault();
        agregarParametro(<?php echo json_encode($parametros); ?>, '');
    });
   
    $('#parametros').on("click",".borrar", function(e){
        e.preventDefault();
        $(this).parent('div').remove();
    });
  });

  function agregarParametro(parametros, id){
    var option = "";
    $.each(parametros, function (key, item) {
      var selected = "";
      if(key == id){
        selected = "selected";
      }
      option += '<option value="'+key+'" '+selected+'>'+item+'</option>';
    });
    $('#parametros').append('<div class="form-group">'
      +'<label>'+(i+1)+'. </label>'
      +'<select id="parametro['+i+']" name="parametro['+i+']" class="form-control">'
      +'<option value="">Seleccionar</option>'
      +option
      +'</select>'
      +'<a href="#" class="borrar">Remove</a>'
      +'</div>'
      +'<br><br>');
    
    i = i + 1;
  }
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3><?php htmlout($titulopagina); ?></h3>
  <form id="plantaform" name="plantaform" class="form-inline" action="" method="post">
    <label>Nombre</label>
    <?php if( !isset($valores['norma']) ){ ?>
      <input name="nombre" type="text">
    <?php }else{ echo $valores['norma']; } ?>
    <br>
    <h4>Parametros</h4>
    <input type="button" class="btn btn-default" id="agregar" value="Agregar nuevo parametro">
    <br><br>
    <div id="parametros">
      <div class="form-group">
        <label>1. </label>
        <select name="parametro[0]" class="form-control">
          <option value="">Seleccionar</option>
          <?php foreach ($parametros as $key => $value) {
            $selected = "";
            if($key == $nparams[0]){
              $selected = "selected";
            }
            ?>
            <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $value; ?></option>
          <?php } ?>
        </select>
      </div>
      <br><br>
      <?php if(isset($nparams) AND count($nparams) > 0):?>
        <?php for($i=1; $i <= count($nparams)-1; $i++): ?>
          <script>
            agregarParametro(<?php echo json_encode($parametros); ?>, <?php echo $nparams[$i]; ?>);
          </script>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
	  <div>
      <?php if(isset($id)){ ?><input type="hidden" name="id" value="<?php htmlout($id); ?>"><?php } ?>
	    <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
	  </div> 
	</form>
  <br>
  <p><a href="">Volver a normas</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>


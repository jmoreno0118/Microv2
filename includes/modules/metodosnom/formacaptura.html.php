<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
  $(document).ready(function() {
    $("#acredform").validate({
      rules: {
        nombre: {
         required: true
        },
        fecha: {
         required: true,
         date: true
        }
      },
      success: "valid"
    });
  });
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
   <h3><?php htmlout($titulopagina); ?></h3>
   <?php $formulario = array(
                  'parametro' => array(    
                                        'label' => 'Parametro',
                                        'tipo' => 'select2',
                                        'option' => $parametros
                                        ),
                  'metodo' => array(
                                        'label' => 'Metodo',
                                        'tipo' => 'text'
                                        )
      );
    ?>
    <form id="acredform" name="acredform" action="" class="form-inline" method="post">
      <?php foreach($formulario as $key => $value): ?>
        <?php
        $value['atts'] = array('style' => 'width:250px');
        crearForma(
            (isset($value['label'])) ? $value['label'] : '', //Texto del label
            $key, //Texto a colocar en los atributos id y name
            (isset($valores[$key])) ? $valores[$key] : '', //Valor extraido de la bd
            (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
            $value['tipo'], //Tipo de etiqueta
            (isset($value['option'])) ? $value['option'] : '' //Options para los select
        );
        ?>
        <br><br>
      <?php endforeach; ?>
	  <div>
      <?php if(isset($id)){ ?>
        <input type="hidden" name="id" value="<?php htmlout($id); ?>">
      <?php } ?>
	    <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
	  </div> 
	</form>
  <br>
  <p><a href="">Volver a metodos</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>

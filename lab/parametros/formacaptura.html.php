<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
  $(document).ready(function() {
    $("#plantaform").validate({
      rules: {
        clave: {
         required: true
        },
        parametro: {
         required: true
        },
        unidades: {
         required: true
        },
        metodo: {
         required: true
        },
        LD: {
         required: true
        },
        LC: {
         required: true
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
                  'clave' => array(    
                                        'label' => 'Clave',
                                        'tipo' => 'text'
                                        ),
                  'parametro' => array(
                                        'label' => 'Parametro',
                                        'tipo' => 'text'
                                        ),
                  'unidades' => array(
                                        'label' => 'Unidades',
                                        'tipo' => 'text'
                                        ),
                  'metodo' => array(
                                        'label' => 'Metodo', 
                                        'tipo' => 'text'
                                        ),
                  'LD' => array(
                                        'label' => 'LD',
                                        'tipo' => 'text'
                                        ),
                  'LC' => array(
                                        'label' => 'LC',
                                        'tipo' => 'text'
                                        )
      );
    ?>
    <form id="plantaform" name="plantaform" class="form-inline" action="" method="post">
      <?php foreach($formulario as $key => $value): ?>
        <?php 
                $value['atts'] = array('style' => 'width:250px');
                if($key == 'clave' and isset($id)){
                  //array_push($value['atts'], 'disabled');
                }
                $valor = "";
                if(isset($value['valor'])){
                  $valor = $value['valor'];
                }elseif(isset($valores[$key])){
                  $valor = $valores[$key];
                }
                crearForma(
                          (isset($value['label'])) ? $value['label'] : '', //Texto del label
                          $key, //Texto a colocar en los atributos id y name
                          $valor, //Valor extraido de la bd
                          (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
                          $value['tipo'], //Tipo de etiqueta
                          (isset($value['option'])) ? $value['option'] : '' //Options para los select
                ); ?>
        <br><br>
      <?php endforeach?>
	  <div>
      <?php if(isset($id)){ ?><input type="hidden" name="id" value="<?php htmlout($id); ?>"><?php } ?>
	    <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
	  </div> 
  </form>
  <br>
  <p><a href="">Volver a parametros</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>

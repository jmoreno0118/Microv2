<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
$(document).ready(function() {
    jQuery.validator.addMethod('permitido', function (value, element, param) {
    return /^(\d{1,2}\.\d{1,4})$/.test(value); 
    }, 'Ingresar de 1 a 4 decimales.');

    $("#limitesform").validate({
      rules: {
        GyA: {
          required: true,
          permitido: true
        },
        coliformes: {
          required: true,
          permitido: true
        },
        ssedimentables: {
          required: true,
          permitido: true
        },
        ssedimentables: {
          required: true,
          permitido: true
        },
        ssuspendidos: {
         required: true,
         permitido: true
        },
        dbo: {
         required: true,
         permitido: true
        },
        nkjedahl: {
         required: true,
         permitido: true
        },
        nitritos: {
         required: true,
         permitido: true
        },
        nitratos: {
         required: true,
         permitido: true
        },
        /*nitrogeno: {
         required: true,
         permitido: true
        },*/
        fosforo: {
         required: true,
         permitido: true
        },
        arsenico: {
         required: true,
         permitido: true
        },
        cadmio: {
         required: true,
         permitido: true
        },
        cianuros: {
         required: true,
         permitido: true
        },
        cobre: {
         required: true,
         permitido: true
        },
        cromo: {
         required: true,
         permitido: true
        },
        mercurio: {
         required: true,
         permitido: true
        },
        niquel: {
         required: true,
         permitido: true
        },
        plomo: {
         required: true,
         permitido: true
        },
        zinc: {
         required: true,
         permitido: true
        },
        hdehelminto: {
         required: true,
         permitido: true
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
  <?php
    $formulario = array();
    $formulario['GyA'] = array(
                                      'label' => 'Grasas y Aceites',
                                      'tipo' => 'text'
                                      );
    if( !isset($GLOBALS["nom002"])){
      $formulario['coliformes'] = array(
                                        'label' => 'Coliformes Fecales',
                                        'tipo' => 'text'
                                        );
    }
    if( !isset($GLOBALS["nom003"])){
      $formulario['ssedimentables'] = array(
                                        'label' => 'Solidos sedimentables',
                                        'tipo' => 'text'
                                        );
    }
    $formulario['ssuspendidos'] = array(
                                      'label' => 'Solidos suspendidos',
                                      'tipo' => 'text'
                                      );
    $formulario['dbo'] = array(
                                      'label' => 'DBO',
                                      'tipo' => 'text'
                                      );
    if( isset($GLOBALS["nom001"])){
      $formulario['nkjedahl'] = array(
                                        'label' => 'Nitrógeno Kjeldahl',
                                        'tipo' => 'text'
                                        );
      $formulario['nitritos'] = array(
                                        'label' => 'Nitrógeno de Nitritos',
                                        'tipo' => 'text'
                                        );
      $formulario['nitratos'] = array(
                                        'label' => 'Nitrógeno de Nitratos',
                                        'tipo' => 'text'
                                        );
      $formulario['fosforo'] = array(
                                        'label' => 'Fosforo',
                                        'tipo' => 'text'
                                        );
    }
    $formulario['arsenico'] = array(
                                      'label' => 'Arsenico',
                                      'tipo' => 'text'
                                      );
    $formulario['cadmio'] = array(
                                      'label' => 'Cadmio',
                                      'tipo' => 'text'
                                      );
    $formulario['cianuros'] = array(
                                      'label' => 'Cianuros',
                                      'tipo' => 'text'
                                      );
    $formulario['cobre'] = array(
                                      'label' => 'Cobre',
                                      'tipo' => 'text'
                                      );
    if( !isset($GLOBALS["nom002"])){
      $formulario['cromo'] = array(
                                        'label' => 'Cromo',
                                        'tipo' => 'text'
                                        );
    }else{
      $formulario['cromo'] = array(
                                        'label' => 'Cromo Hexavalente',
                                        'tipo' => 'text'
                                        );
    }
    $formulario['mercurio'] = array(
                                      'label' => 'Mercurio',
                                      'tipo' => 'text'
                                      );
    $formulario['niquel'] = array(
                                      'label' => 'Niquel',
                                      'tipo' => 'text'
                                      );
    $formulario['plomo'] = array(
                                      'label' => 'Plomo',
                                      'tipo' => 'text'
                                      );
    $formulario['zinc'] = array(
                                      'label' => 'Zinc',
                                      'tipo' => 'text'
                                      );
    if( !isset($GLOBALS["nom002"])){
      $formulario['hdehelminto'] = array(
                                        'label' => 'Huevos de Helminto',
                                        'tipo' => 'text'
                                        );
    }
  ?>
  <form id="limitesform" action="" class="form-inline" method="post">
  	<?php foreach($formulario as $key => $value): ?>
    		<?php
        crearForma(
            $value['label'], //Texto del label
            $key, //Texto a colocar en los atributos id y name
            (isset($valores[$key])) ? $valores[$key] : '', //Valor extraido de la bd
            (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
            $value['tipo'], //Tipo de etiqueta
            (isset($value['option'])) ? $value['option'] : '' //Options para los select
        );
        ?>
        <br><br>
  	<?php endforeach?>
    <div>
      <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
    </div>
  </form>
  <br>
  <p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
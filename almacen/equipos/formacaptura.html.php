<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
i = 1;
$(document).ready(function() {

 jQuery.validator.addMethod('entcimales', function (value, element, param) {
    return /^(\d*\.\d{1,3}|\d*)$/.test(value);
 }, 'Sólo valores enteros o de 1 a 3 decimales.');
 
 jQuery.validator.addMethod('negativeentcimales', function (value, element, param) {
    return /^(-?\d*\.\d{1,3}|\d*)$/.test(value);
 }, 'Sólo valores enteros o de 1 a 3 decimales.');

$("#medsform").validate({
    rules: {
      'rango[0]': {
       required: true,
       digits: true
      },
      'fcorreccion1[0]': {
       required: true,
       entcimales: true
      },
      'fcorreccion2[0]': {
       required: true,
       negativeentcimales: true
      }
    },
    ignore: [],
    success: "valid"
  });

  $('#agregar').click(function(e){
      e.preventDefault();
      agregarParametro("", "", "", "", "", "");
  });
 
  $('#parametros').on("click",".borrar", function(e){
      e.preventDefault();
      $(this).parent('div').remove();
  });

    $('#estado').on('change',function(){
      console.log($(this).find("option:selected").attr('value'));
      if($(this).find("option:selected").attr('value') === "Baja"){
        showBaja('', '');
      }else{
        $('#baja').children('div').remove();
        $('#baja').children('br').remove();
      }
    });
});

function showBaja(fechabaja, causabaja){
  $('#baja').append('<br>'
    +'<div style="margin-bottom:10px">'
    +'<label for="fechabaja">Fecha de baja: </label>'
    +'<input name="fechabaja" value="'+fechabaja+'" >'
    +'<br>'
    +'<label for="causabaja">Causa de la baja: </label>'
    +'<br>'
    +'<textarea style="resize: none;" name="causabaja" rows="5" cols="60">'+causabaja+'</textarea>'
    +'<br>'
    +'</div>');
}

function agregarParametro(nombre, unidades, ref1, ref2, ref3){
  console.log($('#parametros').children().size());
  if($('#parametros').children().size() < 3){
    $('#parametros').append('<div style="margin-bottom:10px">'
      +'<label for="parametros['+i+'][parametro]">Nombre del Parámetro: </label>'
      +'<input name="parametros['+i+'][parametro]" class="form-control" value="'+nombre+'" >'
      +'<br>'
      +'<label for="parametros['+i+'][unidades]">Unidades del Parámetro: </label>'
      +'<input name="parametros['+i+'][unidades]" class="form-control" value="'+unidades+'">'
      +'<br>'
      +'<label for="parametros['+i+'][refesperada1]">Magnitud de Referencia Esperada en Nivel bajo: </label>'
      +'<input name="parametros['+i+'][refesperada1]" class="form-control" value="'+ref1+'">'
      +'<br>'
      +'<label for="parametros['+i+'][refesperada2]">Magnitud de Referencia Esperada en Nivel medio: </label>'
      +'<input name="parametros['+i+'][refesperada2]" class="form-control" value="'+ref2+'">'
      +'<br>'
      +'<label for="parametros['+i+'][refesperada3]">Magnitud de Referencia Esperada en Nivel alto: </label>'
      +'<input name="parametros['+i+'][refesperada3]" class="form-control" value="'+ref3+'">'
      +'<br>'
      +'<a href="#" class="borrar">Remove</a>'
      +'</div>');

    /*$("#medsform").validate({
      rules: {
        'rango[0]': {
         required: true,
         digits: true
        },
        'fcorreccion1[0]': {
         required: true,
         entcimales: true
        },
        'fcorreccion2[0]': {
         required: true,
         entcimales: true
        }
      },
      ignore: [],
      success: "valid"
    });

    $('input[name="rango['+i+']"]').rules("add", {
      digits: true
    });

    $('input[name="fcorreccion1['+i+']"]').rules("add", {
      entcimales: true
    });

    $('input[name="fcorreccion2['+i+']"]').rules("add", {
      entcimales: true
    });*/

    i = i + 1;
  }
}
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3><?php htmlout($titulopagina); ?></h3>
   <?php
    //var_dump($valores);
      $formulario = array(
                  'inventario' => array(    
                                        'label' => 'No. de inventario',
                                        'tipo' => 'text'
                                        ),
                  'marca' => array(
                                        'label' => 'Marca',
                                        'tipo' => 'text'
                                        ),
                  'modelo' => array(
                                        'label' => 'Modelo',
                                        'tipo' => 'text'
                                        ),
                  'serie' => array(
                                        'label' => 'Número de Serie', 
                                        'tipo' => 'text'
                                        ),
                  'tipo' => array(
                                        'label' => 'Tipo', 
                                        'tipo' => 'text'
                                        ),
                  'descripcion' => array(
                                        'label' => 'Descripción',
                                        'tipo' => 'text'
                                        ),
                  'fechaalta' => array(
                                        'label' => 'Fecha de adquisición',
                                        'tipo' => 'text'
                                        ),
                  'estudio' => array(
                                        'label' => 'Estudio',
                                        'tipo' => 'text'
                                        ),
                  'estado' => array(
                                        'label' => 'Estado',
                                        'tipo' => 'select2',
                                        'option' => array('Almacen', 'Campo', 'Baja', 'Mantenimiento', 'Calibracion')
                                        ),
                  'responsable' => array(
                                        'label' => 'Responsable',
                                        'tipo' => 'text'
                                        ),
                  'notas' => array(
                                        'label' => 'Notas',
                                        'tipo' => 'textarea'
                                        ),
                  'periodo_cal_externo' => array(
                                        'label' => 'Vigencia en Meses de la Calibracion Externa',
                                        'tipo' => 'text'
                                        ),
                  'periodo_manto_interno' => array(
                                        'label' => 'Periodo en Meses entre Mantenimientos Preventivos',
                                        'tipo' => 'text'
                                        ),
      );

      $arquitectura = array("valores" => array("variables" => 'numero,fecha,nombre,laboratoriocalibro,laboratorioacreditacion,fechaLabAcreditacion,fechacalibracion,parametroevaluado,criterioaceptacion,especificaciones,cumple,correccion',
                                              "tipo" => 1),
                            "id" => array("variables" => "id",
                                          "tipo" => 0));
   ?>
    <form id="medsform" name="medsform" action="" class="form-inline" method="post">
      <input type="hidden" name="post" value='<?php htmlout(json_encode($_POST)); ?>'>
      <input type="hidden" name="url" value="<?php htmlout($_SESSION['url']); ?>">
      <input type="hidden" name="arquitectura" value='<?php htmlout(json_encode($arquitectura)); ?>'>

    	<?php foreach($formulario as $key => $value): ?>
        <?php 
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
        <?php if( strcmp($key, 'estado') === 0 ){ ?>
          <div id="baja"></div>
          <?php if($valor === "Baja"){ ?>
            showBaja(<?php echo $valores['fechabaja']; ?>, <?php echo $valores['causabaja']; ?>);
          <?php } ?>
        <?php } ?>
      <br><br>
    	<?php endforeach?>
      
      <input type="button" class="btn btn-default" id="agregar" value="Agregar parametro">
      
      <div id="parametros">
        <div style="margin-bottom:10px">
          <label for="parametros[0][parametro]">Nombre del Parámetro: </label>
          <input name="parametros[0][parametro]" class="form-control" value="<?php if(isset($valores['parametros'])) htmlout($valores['parametros'][0]['parametro']); ?>" >
          <br>
          <label for="parametros[0][unidades]">Unidades del Parámetro: </label>
          <input name="parametros[0][unidades]" class="form-control" value="<?php if(isset($valores['parametros'])) htmlout($valores['parametros'][0]['unidades']); ?>">
          <br>
          <label for="parametros[0][refesperada1]">Magnitud de Referencia Esperada en Nivel bajo: </label>
          <input name="parametros[0][refesperada1]" class="form-control" value="<?php if(isset($valores['parametros'])) htmlout($valores['parametros'][0]['refesperada1']); ?>">
          <br>
          <label for="parametros[0][refesperada2]">Magnitud de Referencia Esperada en Nivel medio: </label>
          <input name="parametros[0][refesperada2]" class="form-control" value="<?php if(isset($valores['parametros'])) htmlout($valores['parametros'][0]['refesperada2']); ?>">
          <br>
          <label for="parametros[0][refesperada3]">Magnitud de Referencia Esperada en Nivel alto: </label>
          <input name="parametros[0][refesperada3]" class="form-control" value="<?php if(isset($valores['parametros'])) htmlout($valores['parametros'][0]['refesperada3']); ?>">
        </div>
        <?php if(isset($valores['parametros'])): ?>
          <?php for ($i=1; $i <= count($valores['parametros'])-1; $i++): ?>
            <script>
              agregarParametro(<?php echo $valores['parametros'][$i]['parametro']; ?>,
                              <?php echo $valores['parametros'][$i]['unidades']; ?>,
                              <?php echo $valores['parametros'][$i]['refesperada1']; ?>,
                              <?php echo $valores['parametros'][$i]['refesperada2']; ?>,
                              <?php echo $valores['parametros'][$i]['refesperada3']; ?>
                            );
            </script>
          <?php endfor; ?>
        <?php endif; ?>
      </div>

	  <div>
	    <input type="hidden" name="id" value="<?php htmlout($valores['id']); ?>">
	    <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
	  </div> 
	</form>
  <br>
  <p><a href="">Volver a equipos</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>

<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $("#plantaform").validate({
      rules: {
        razonsocial: {
         required: true
        },
        planta: {
         required: true
        },
        calle: {
         required: true
        },
        colonia: {
         required: true
        },
        ciudad: {
         required: true
        },
        estado: {
         required: true
        },
        cp: {
         required: true
        },
        rfc: {
         required: true
        },
      idcliente: {
         required: true
        }
      },
      success: "valid"
    });
  });

$(document).ready(function(){
  $("#razonsocial").val($("#cliente option:selected").html());

  $("#cliente").change(function(){
    $("#razonsocial").val($("#cliente option:selected").html());
  });

});
</script>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
   <h2><?php htmlout($titulopagina); ?></h2>
   <?php $formulario = array(
                  'razonsocial' => array(    
                                        'label' => 'Razon Social',
                                        'tipo' => 'text'
                                        ),
                  'planta' => array(
                                        'label' => 'Planta',
                                        'tipo' => 'text'
                                        ),
                  'calle' => array(
                                        'label' => 'Calle',
                                        'tipo' => 'text'
                                        ),
                  'colonia' => array(
                                        'label' => 'Colonia', 
                                        'tipo' => 'text'
                                        ),
                  'ciudad' => array(
                                        'label' => 'Ciudad',
                                        'tipo' => 'text'
                                        ),
                  'estado' => array(
                                        'label' => 'Estado',
                                        'tipo' => 'text'
                                        ),
                  'cp' => array(
                                        'label' => 'Código Postal',
                                        'tipo' => 'text'
                                        ),
                  'rfc' => array(
                                        'label' => 'RFC',
                                        'tipo' => 'text'
                                        )
      );
    ?>
  <form id="plantaform" name="plantaform" class="form-inline" action="" method="post">
    <label for="cliente">Cliente: </label>
    <select name="cliente" id="cliente" class="form-control">
      <option value="">Seleciona cliente</option>
      <?php foreach($clientes as $cliente): ?>
        <option value="<?php echo $cliente['id']; ?>"
        <?php if ($cliente['id']==$valores['Numero_Clienteidfk']){echo ' selected';}?>>
          <?php echo $cliente['nombre']; ?>
        </option>
      <?php endforeach; ?>
    </select>
    <br>
    <br>
    <?php foreach($formulario as $key => $value): ?>
      <?php 
      if(!isset($value['atts']['style'])){
        $value['atts'] = array('style' => 'width:250px');
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
        trim($valor), //Valor extraido de la bd
        (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
        $value['tipo'], //Tipo de etiqueta
        (isset($value['option'])) ? $value['option'] : '' //Options para los select
      ); ?>
      <br><br>
    <?php endforeach?>
    <div>
      <?php if(isset($id)){ ?>
        <input type="hidden" name="id" value="<?php htmlout($id); ?>">
      <?php } ?>
      <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
    </div> 
  </form>
  <br>
  <p><a href="">Volver a plantas</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>

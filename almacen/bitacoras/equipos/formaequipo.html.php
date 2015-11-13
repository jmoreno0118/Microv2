<?php
 include_once $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/ayudas.inc.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<title><?php htmlout($pestanapag); ?></title>
  <meta charset="utf-8" />
  <!--[if lt IE 9]>
   <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]--> 
   <link rel="stylesheet" type="text/css" href="/reportes/estilo.css" />
</head>
<body>
<div id="contenedor">
  <header>
   <?php 
    $ruta='/reportes/img/logoblco2.gif';
	  include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/encabezado.inc.php'; ?>
  </header>
  <div id="cuerpoprincipal">
   <h2><?php htmlout($titulopagina); ?></h2>
   <?php
    //var_dump($valores);
      $formulario = array(
                  'inventario' => array(    
                                        'label' => 'No. de inventario',
                                        'tipo' => 'label'
                                        //'atts' => array('disabled' => 'disabled')
                                        ),
                  'marca' => array(
                                        'label' => 'Marca',
                                        'tipo' => 'label'
                                        ),
                  'modelo' => array(
                                        'label' => 'Modelo',
                                        'tipo' => 'label'
                                        ),
                  'serie' => array(
                                        'label' => 'Número de Serie', 
                                        'tipo' => 'label'
                                        ),
                  'tipo' => array(
                                        'label' => 'Tipo', 
                                        'tipo' => 'label'
                                        ),
                  'descripcion' => array(
                                        'label' => 'Descripción',
                                        'tipo' => 'label'
                                        )
      );

      $formulario2 = array(
                  'fechahoraentrega' => array(    
                                        'label' => 'Fecha y Hora de Salida del Equipo',
                                        'tipo' => 'text'
                                        //'atts' => array('disabled' => 'disabled')
                                        ),
                  'comprobacionsalida' => array(
                                        'label' => 'Comprobación de Funcionamiento de Salida',
                                        'tipo' => 'tfcheck',
                                        'option' => array('')
                                        ),
                  'fechahoradevolucion' => array(
                                        'label' => 'Fecha y Hora de Regreso del Equipo',
                                        'tipo' => 'text'
                                        ),
                  'comprobacionregreso' => array(
                                        'label' => 'Comprobación de Funcionamiento de Regreso', 
                                        'tipo' => 'tfcheck',
                                        'option' => array(''),
                                        ),
                  'observacionsalida' => array(
                                        'label' => 'Observaciones de Salida', 
                                        'tipo' => 'textarea'
                                        ),
                  'observacionregreso' => array(
                                        'label' => 'Observaciones de Regreso',
                                        'tipo' => 'textarea'
                                        ),
                  'reviso' => array(
                                        'label' => 'Revisó',
                                        'tipo' => 'text'
                                        )
      );

      $arquitectura = array("valores" => array("variables" => 'inventario,marca,modelo,serie,tipo',
                                              "tipo" => 1),
                            "id" => array("variables" => "id",
                                          "tipo" => 0));
   ?>
    <form id="medsform" name="medsform" action="?" method="post">
      <input type="hidden" name="post" value='<?php htmlout(json_encode($_POST)); ?>'>
      <input type="hidden" name="url" value="<?php htmlout($_SESSION['url']); ?>">
      <input type="hidden" name="arquitectura" value='<?php htmlout(json_encode($arquitectura)); ?>'>

    	<?php foreach($formulario as $key => $value): ?>
    	<div>
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
    	</div>
      <br>
    	<?php endforeach?>
          
      <?php $cuenta = count($valores['parametros'])-1; ?>
      <div id="parametros">
        <table>
          <tr>
            <td style="width:180px;">Párametro</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center" colspan=3><?php if(isset($valores['parametros'][$i])) htmlout($valores['parametros'][$i]['parametro']); ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td>Unidades</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center" colspan=3><?php if(isset($valores['parametros'][$i])) htmlout($valores['parametros'][$i]['unidades']); ?></td>
            <?php } ?>
          </tr>
          <tr>
            <td>Notas</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center" colspan=3><input type="text" size="6" name="lectura[<?php echo $i; ?>][notas]" value="<?php if(isset($valores['parametros'][$i]['notas'])) htmlout($valores['parametros'][$i]['notas']); ?>"></td>
            <?php } ?>
          </tr>
          <tr>
            <td>Niveles de Calibración</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center">1</td>
              <td align="center">2</td>
              <td align="center">3</td>
            <?php } ?>
          </tr>
          <tr>
            <td>Magnitud de Referencia</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calref1]" value="<?php if(isset($valores['parametros'][$i]['refesperada1'])) htmlout($valores['parametros'][$i]['refesperada1']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calref2]" value="<?php if(isset($valores['parametros'][$i]['refesperada2'])) htmlout($valores['parametros'][$i]['refesperada2']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calref3]" value="<?php if(isset($valores['parametros'][$i]['refesperada3'])) htmlout($valores['parametros'][$i]['refesperada3']); ?>"></td>
            <?php } ?>
          </tr>
          <tr>
            <td>Magnitud de Lectura Inicial</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calini1]" value="<?php if(isset($valores['parametros'][$i]['calini1'])) htmlout($valores['parametros'][$i]['calini1']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calini2]" value="<?php if(isset($valores['parametros'][$i]['calini2'])) htmlout($valores['parametros'][$i]['calini2']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calini3]" value="<?php if(isset($valores['parametros'][$i]['calini3'])) htmlout($valores['parametros'][$i]['calini3']); ?>"></td>
            <?php } ?>
          </tr>
          <tr>
            <td>Magnitud de Lectura Final</td>
            <?php for($i = 0; $i <= $cuenta; $i++){ ?>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calfin1]" value="<?php if(isset($valores['parametros'][$i]['calfin1'])) htmlout($valores['parametros'][$i]['calfin1']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calfin2]" value="<?php if(isset($valores['parametros'][$i]['calfin2'])) htmlout($valores['parametros'][$i]['calfin2']); ?>"></td>
              <td align="center"><input type="text" size="6" name="lectura[<?php echo $i; ?>][calfin3]" value="<?php if(isset($valores['parametros'][$i]['calfin3'])) htmlout($valores['parametros'][$i]['calfin3']); ?>"></td>
            <?php } ?>
          </tr>
        </table>
      </div>
      <br>

      <?php foreach($formulario2 as $key => $value): ?>
      <div>
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
      </div>
      <br>
      <?php endforeach?>

	  <div>
	    <input type="hidden" name="id" value="<?php htmlout($id); ?>">
      <input type="hidden" name="equipoid" value="<?php htmlout($equipoid); ?>">
      <input type="hidden" name="bitacoraeqid" value="<?php htmlout($valores['bitacoraeqid']); ?>">
	    <input type="submit" name="accion" value="<?php htmlout($boton); ?>">
      <p><a href="?">Volver a bitacora</a></p>
	  </div> 
	</form>
  </div>  <!-- cuerpoprincipal -->
  <div id="footer">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
  </div>  <!-- footer -->
  </div> <!-- contenedor -->
</body>
<link rel="stylesheet" href="../../includes/jquery-validation-1.13.1/demo/site-demos.css">
<script type="text/javascript" src="../../includes/jquery-validation-1.13.1/lib/jquery.js"></script>
<script type="text/javascript" src="../../includes/jquery-validation-1.13.1/lib/jquery-1.11.1.js"></script>
<script type="text/javascript" src="../../includes/jquery-validation-1.13.1/dist/jquery.validate.js"></script>
<script type="text/javascript" src="../../includes/jquery-validation-1.13.1/dist/additional-methods.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  
   jQuery.validator.addMethod('datetime', function (value, element, param) {
    return /^(19|20)?\d{2}[-](0?[1-9]|1[0-2])[-](0?[1-9]|[12][0-9]|3[0-1]) ([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/.test(value); 
   }, 'Favor de introducir una hora valida.');

   jQuery.validator.addMethod('datetime2', function (value, element, param) {
    return /^ *|(19|20)?\d{2}[-](0?[1-9]|1[0-2])[-](0?[1-9]|[12][0-9]|3[0-1]) ([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/.test(value); 
   }, 'Favor de introducir una hora valida.');

  /*$("#medsform").validate({
      rules: {
        'fechahoraentrega': {
         datetime: true,
         remote:
          {
           url: 'checkfechas.php',
           type: "post",
           data:
           {
             fechahoraentrega: function()
             {
              return $('#medsform :input[name="fechahoraentrega"]').val();
             },
             equipoid: function()
             {
              return $('#medsform :input[name="equipoid"]').val();
             },
             bitacoraid: function()
             {
              return $('#medsform :input[name="id"]').val();
             },
            }
          }
        },
        'fechahoradevolucion': {
         datetime2: true,
         remote:
          {
           url: 'compfechas.php',
           type: "post",
           data:
           {
             fechahoraentrega: function()
             {
              return $('#medsform :input[name="fechahoraentrega"]').val();
             },
             fechahoradevolucion: function()
             {
              return $('#medsform :input[name="fechahoradevolucion"]').val();
             },
            }
          }
        }
      },
      messages: {
        fechahoraentrega:{
          remote: jQuery.validator.format("Esta fecha de entrega no puede ser seleccionada. Modifiquela o contacte al administrador")
        },
        fechahoradevolucion:{
          remote: jQuery.validator.format("Fecha de devolucion debe ser mayor a la fecha de entrega")
        }
      },
      ignore: [],
      success: "valid",
      submitHandler: function(form) {
                    if ($(form).valid())
                     form.submit();
                    return false; // prevent normal form posting
                  }
    });*/
  });
</script>
</html>

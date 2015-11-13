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
<link rel="stylesheet" href="../includes/jquery-validation-1.13.1/demo/site-demos.css">
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/lib/jquery.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/lib/jquery-1.11.1.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/dist/jquery.validate.js"></script>
<script type="text/javascript" src="../includes/jquery-validation-1.13.1/dist/additional-methods.js"></script>
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

  });
</script>
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
                  'fechainicio' => array(    
                                        'label' => 'Fecha y Hora de Inicio de la Actividad',
                                        'tipo' => 'text'
                                        //'atts' => array('disabled' => 'disabled')
                                        ),
                  /*'muestreadoridfk' => array(
                                        'label' => 'Muestreador',
                                        'tipo' => 'text'
                                        ),*/
                  'ot' => array(
                                        'label' => 'Número de Orden de Muestreo',
                                        'tipo' => 'text'
                                        ),
                  'entrego' => array(
                                        'label' => 'Entrego',
                                        'tipo' => 'text'
                                        ),
                  'observaciones' => array(
                                        'label' => 'Observaciones', 
                                        'tipo' => 'textarea'
                                        ),
                  'agentesevaluados' => array(
                                        'label' => 'Agentes Evaluados', 
                                        'tipo' => 'textarea'
                                        ),
                  'metodosreferencia' => array(
                                        'label' => 'Métodos de Referencia', 
                                        'tipo' => 'textarea'
                                        ),
                  'procedimiento' => array(
                                        'label' => 'Procedimiento Estándar',
                                        'tipo' => 'textarea'
                                        ),
                  'actividadesrealizadas' => array(
                                        'label' => 'Actividades Realizadas',
                                        'tipo' => 'textarea'
                                        ),
                  'problemas' => array(
                                        'label' => 'Problemas Encontrados y Correcciones Realizadas',
                                        'tipo' => 'textarea'
                                        ),
                  'fechafin' => array(
                                        'label' => 'Fecha y Hora de Término de la Actividad',
                                        'tipo' => 'text'
                                        )
      );

      $arquitectura = array("valores" => array("variables" => 'numero,fecha,nombre,laboratoriocalibro,laboratorioacreditacion,fechaLabAcreditacion,fechacalibracion,parametroevaluado,criterioaceptacion,especificaciones,cumple,correccion',
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

	  <div>
	    <input type="hidden" name="id" value="<?php htmlout($valores['id']); ?>">
	    <input type="submit" name="accion" value="<?php htmlout($boton); ?>">
      <p><a href="?">Volver a bitacoras</a></p>
	  </div> 
	</form>
  </div>  <!-- cuerpoprincipal -->
  <div id="footer">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/reportes/includes/pie_pag.inc.php'; ?>
  </div>  <!-- footer -->
  </div> <!-- contenedor -->
</body>
</html>

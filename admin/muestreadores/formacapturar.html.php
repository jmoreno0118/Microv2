<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h2><?php htmlout($titulopagina); ?></h2>
  <?php
  //var_dump($valores);
    $formulario = array(
      'estudiosmuestreador' => array(
                                  'label' => 'Estudios de Muestreador',
                                  'tipo' => 'checks',
                                  'options' => $estudios,
                                  'extra' => array('multi' => 1, 'comp' => 'texto', 'value' => 'texto')
                                )
    );

    if(in_array("Signatario", $valores['permisos'])){
      $formulario['estudiossignatarios'] = array(
                                                'label' => 'Estudios para Signatario',
                                                'tipo' => 'checks',
                                                'options' => $estudiossig,
                                                'atts' => array('class' => 'estudiossignatarios'),
                                                'extra' => array('multi' => 1, 'comp' => 'texto', 'value' => 'texto')
      );
    }

    $arquitectura = array("valores" => array("variables" => 'estudios,estudios, permisos',
                                            "tipo" => 2),
                          "id" => array("variables" => "id",
                                        "tipo" => 0),
                          "regreso" => array("variables" => "id",
                                              "tipo" => 0,
                          "valor" => 2)
    );
  ?>
  <form action="" method="post">
    <input type="hidden" name="permisos" value="<?php echo json_encode($valores['permisos']); ?>">
    <?php foreach($formulario as $key => $value): ?>
      <?php if($key === 'estudiossignatarios'){
        if(isset($valores['signatario']) AND $valores['signatario'] === 1){
          $value['atts'] = array('class' => 'estudiossignatarios');
        }
      }?>
      <?php crearForma(
      $value['label'], //Texto del label
      $key, //Texto a colocar en los atributos id y name
      (isset($valores[$key])) ? $valores[$key] : '', //Valor extraido de la bd
      (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
      $value['tipo'], //Tipo de etiqueta
      (isset($value['options'])) ? $value['options'] : '', //Options para los select
      (isset($value['extra'])) ? $value['extra'] : '' //Extra para seleccionar multi check
      ); ?>
    <?php endforeach?>

    <div>	
      <input type="hidden" name="id" value="<?php htmlout($id); ?>">
      <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
    </div> 
  </form>
  <br>
  <p><a href="">Volver a muestreadores</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
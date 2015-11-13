<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3><?php htmlout($titulopagina); ?></h3>
  <?php
    $formulario = array(
                        'descargaen' => array(
                                            'label' => 'Descarga en',
                                            'tipo' => 'text'
                                            ),
                        'uso' => array(
                                          'label' => 'Uso',
                                          'tipo' => 'text'
                                          )
    );
    $formulario['GyA'] = array(
                                      'label' => 'Grasas y Aceites',
                                      'tipo' => 'text'
                                      );
    $formulario['coliformes'] = array(
                                      'label' => 'Coliformes Fecales',
                                      'tipo' => (!isset($GLOBALS["nom002"]))? 'text' : 'hidden'
                                      );
    $formulario['ssedimentables'] = array(
                                      'label' => 'Solidos sedimentables',
                                      'tipo' => (!isset($GLOBALS["nom003"]))? 'text' : 'hidden'
                                      );
    $formulario['ssuspendidos'] = array(
                                      'label' => 'Solidos suspendidos',
                                      'tipo' => 'text'
                                      );
    $formulario['dbo'] = array(
                                      'label' => 'DBO',
                                      'tipo' => 'text'
                                      );
    $formulario['nitrogeno'] = array(
                                      'label' => 'Nitrógeno',
                                      'tipo' => (!isset($GLOBALS["nom001"]))? 'text' : 'hidden'
                                      );
    $formulario['fosforo'] = array(
                                      'label' => 'Fosforo',
                                      'tipo' => (!isset($GLOBALS["nom001"]))? 'text' : 'hidden'
                                      );
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
    $formulario['hdehelminto'] = array(
                                      'label' => 'Huevos de Helminto',
                                      'tipo' => (!isset($GLOBALS["nom002"]))? 'text' : 'hidden'
                                      );
    $formulario['temperatura'] = array(
                                      'label' => 'Temperatura',
                                      'tipo' => 'select2',
                                      'option' => array('N.A.','40')
                                      );
    $formulario['mflotante'] = array(
                                      'label' => 'Materia Flotante',
                                      'tipo' => 'select2',
                                      'option' => array('Ausente', 'Presente')
                                      );
  ?>
  <form action="" method="post" class="form-inline">
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
        <?php if( strcmp($value['tipo'], 'hidden') !== 0 ){ ?><br><br><?php } ?>
    <?php endforeach?>
    <div>
      <?php if(isset($id)): ?><input type="hidden" name="id" value="<?php echo $id; ?>"><?php endif; ?>
      <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
    </div>
  </form>
  <br>
  <p><a href="">Volver a maximos</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
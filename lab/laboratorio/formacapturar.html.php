<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Laboratorio'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3>Laboratorio</h3>
  <?php
    $arquitectura = array(
                          "valores" => array("variables" => 'requerido,para,ot,idep,fecharecepcion,fechamuestreo,fechamuestreofin,fechainforme,fechaanalisis,fechaanalisisfin,muestreador,signatario,acreditacion,pgya,gyac,gya,pcoliformes,coliformes,parametro,parametros,adicionales',
                                            "tipo" => 1),
                          "norma" => array("variables" => "norma",
                                      "tipo" => 0));
  ?>
  <form id="medsform" name="medsform" class="form-inline" action="index.php" method="post">
      <input type="hidden" name="post" value='<?php echo json_encode($_POST); ?>'>
      <input type="hidden" name="prevact" value='<?php htmlout($_SESSION['accion']); ?>'>
      <input type="hidden" name="url" value="<?php htmlout($_SESSION['url']); ?>">
      <input type="hidden" name="arquitectura" value='<?php echo json_encode($arquitectura); ?>'>
      <input type="hidden" name="norma" value='<?php echo $norma; ?>'>
      <?php 
      $form = array(
              'requerido' => array(
                                'label' => 'Requerido por',
                                'tipo' => 'select',
                                'option' => $clientes,
                                'valor' => (isset($valores['requerido'])) ? $valores['requerido'] : ''
                                ),
              'para' => array(
                                'label' => 'Para',
                                'tipo' => 'select',
                                'option' => $clientes,
                                'valor' => (isset($valores['para'])) ? $valores['para'] : ''
                                ),
              );
      ?>
      <?php foreach($form as $key => $value){ ?>
        <?php crearForma(
          $value['label'], //Texto del label
          $key, //Texto a colocar en los atributos id y name
          $value['valor'], //Valor extraido de la bd
          (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
          $value['tipo'], //Tipo de etiqueta
          (isset($value['option'])) ? $value['option'] : '' //Options para los select
        ); ?>
        <br><br>
      <?php } ?>

      <div class="form-group">
        <label>OT</label>
        <input name="ot" class="form-control" type="text" value="<?php echo (isset($valores['ot'])) ? $valores['ot'] : ''; ?>">
      </div>
      <br><br>

      <div class="form-group">
        <label>No. I. De P.</label>
        <input name="idep" class="form-control" type="text" value="<?php echo (isset($valores['idep'])) ? $valores['idep'] : ''; ?>">
      </div>
      <br><br>

      <div class="form-group">
        <label>Fecha de recepción</label>
        <input name="fecharecepcion" class="form-control" type="text" value="<?php echo (isset($valores['fecharecepcion'])) ? $valores['fecharecepcion'] : ''; ?>">
      </div>
      <br><br>

      <div style="display:table;width:60%;">
        <div style="display:table-cell;width:45%;">
          <label>Fecha de muestreo</label>
          <input name="fechamuestreo" class="form-control" type="text" value="<?php echo (isset($valores['fechamuestreo'])) ? $valores['fechamuestreo'] : ''; ?>">
        </div>
        <div style="display:table-cell;width:55%;">
          <label>Fecha de muestreo fin</label>
          <input name="fechamuestreofin" class="form-control" type="text" value="<?php echo (isset($valores['fechamuestreofin'])) ? $valores['fechamuestreofin'] : ''; ?>">
        </div>
      </div>
      <br>

      <div class="form-group">
        <label>Fecha de informe</label>
        <input name="fechainforme" class="form-control" type="text" value="<?php echo (isset($valores['fechainforme'])) ? $valores['fechainforme'] : ''; ?>">
      </div>
      <br><br>

      <div style="display:table;width:50%;">
        <div style="display:table-cell;width:50%;">
          <label>Fecha de análisis</label>
          <input name="fechaanalisis" class="form-control" type="text" value="<?php echo (isset($valores['fechaanalisis'])) ? $valores['fechaanalisis'] : ''; ?>">
        </div>
        <div style="display:table-cell;width:50%;">
          <label>Fecha de análisis fin</label>
         <input name="fechaanalisisfin" class="form-control" type="text" value="<?php echo (isset($valores['fechaanalisisfin'])) ? $valores['fechaanalisisfin'] : ''; ?>">
        </div>
      </div>
      <br>
      <?php 
        if( !isset($valores['muestreador']) )
        {
          $valores['muestreador'][0] = '';
          $valores['muestreador'][1] = '';
        }
      ?>
      <?php 
      $form = array(
              'muestreador[0]' => array(
                                'label' => 'Muestreador 1',
                                'tipo' => 'select',
                                'option' => $muestreadores,
                                'valor' => $valores['muestreador'][0]
                                ),
              'muestreador[1]' => array(
                                'label' => 'Muestreador 2',
                                'tipo' => 'select',
                                'option' => $muestreadores,
                                'valor' => $valores['muestreador'][1]
                                )
              );
      ?>
      <?php foreach($form as $key => $value){ ?>
        <?php crearForma(
          $value['label'], //Texto del label
          $key, //Texto a colocar en los atributos id y name
          $value['valor'], //Valor extraido de la bd
          (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
          $value['tipo'], //Tipo de etiqueta
          (isset($value['option'])) ? $value['option'] : '' //Options para los select
        ); ?>
        <br>
      <?php } ?>
      <br>
      <?php 
        if( !isset($valores['signatario']) )
        {
          $valores['signatario'][0] = '';
          $valores['signatario'][1] = '';
          $valores['signatario'][2] = '';
        }
      ?>
      <?php 
      $form = array(
              'signatario[0]' => array(
                                'label' => 'Signatario 1',
                                'tipo' => 'select',
                                'option' => $signatarios,
                                'valor' => $valores['signatario'][0]
                                ),
              'signatario[1]' => array(
                                'label' => 'Signatario 2',
                                'tipo' => 'select',
                                'option' => $signatarios,
                                'valor' => $valores['signatario'][1]
                                ),
              'signatario[2]' => array(
                                'label' => 'Signatario 3',
                                'tipo' => 'select',
                                'option' => $signatarios,
                                'valor' => $valores['signatario'][2]
                                )
              );
      ?>
      <?php foreach($form as $key => $value){ ?>
        <?php crearForma(
          $value['label'], //Texto del label
          $key, //Texto a colocar en los atributos id y name
          $value['valor'], //Valor extraido de la bd
          (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
          $value['tipo'], //Tipo de etiqueta
          (isset($value['option'])) ? $value['option'] : '' //Options para los select
        ); ?>
        <br>
      <?php } ?>
      <br>
      <?php 
        if( !isset($valores['acreditacion']) )
        {
          $valores['acreditacion'] = key($acreditaciones);
        }
      ?>
      <?php 
      $form = array(
              'acreditacion' => array(
                                'label' => 'Acreditacion',
                                'tipo' => 'select',
                                'option' => $acreditaciones,
                                'valor' => $valores['acreditacion']
                                )
              );
      ?>
      <?php foreach($form as $key => $value){ ?>
        <?php crearForma(
          $value['label'], //Texto del label
          $key, //Texto a colocar en los atributos id y name
          $value['valor'], //Valor extraido de la bd
          (isset($value['atts'])) ? $value['atts'] : '', //Atributos extra de la etiqueta
          $value['tipo'], //Tipo de etiqueta
          (isset($value['option'])) ? $value['option'] : '' //Options para los select
        ); ?>
        <br>
      <?php } ?>

      <div>
        <div>
          <h3>Grasas y Aceites, Y Coliformes</h3>
          <?php
            if( !isset($valores['pgya']) )
            {
              $valores['pgya'] = key($parametros['GYA']);
            }
          ?>
          <?php crearForma(
            'GyA', //Texto del label
            'pgya', //Texto a colocar en los atributos id y name
            $valores['pgya'], //Valor extraido de la bd
            '', //Atributos extra de la etiqueta
            'select', //Tipo de etiqueta
            $parametros['GYA'] //Options para los select
          ); ?>
          <?php if($coliformes){ ?>
          <br><br>
            <?php
              if( !isset($valores['pcoliformes']) )
              {
                $valores['pcoliformes'] = key($parametros['NMPCF-AR1']);
              }
            ?>
            <?php crearForma(
              'Coliformes', //Texto del label
              'pcoliformes', //Texto a colocar en los atributos id y name
              $valores['pcoliformes'], //Valor extraido de la bd
              '', //Atributos extra de la etiqueta
              'select', //Tipo de etiqueta
              $parametros['NMPCF-AR1'] //Options para los select
            ); ?>

          <?php } ?>
          <br><br>
          <table style="width:270px" class="table">
            <thead>
              <tr>
                <th>No. Muestra</th>
                <th>Identificacion</th>
                <th>Resultado GyA</th>
                <?php if($coliformes){ ?>
                  <th>Resultado Coliformes</th>
                <?php } ?>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i < 6; $i++) { ?>
                <tr>
                  <td><input name="gyac[<?php echo $i; ?>][muestranum]" class="form-control" type="text" style="width:80px;" value="<?php echo (isset($valores['gyac'][$i]['muestranum'])) ? $valores['gyac'][$i]['muestranum'] : ''; ?>"></td>
                  <td><input name="gyac[<?php echo $i; ?>][identificacion]" class="form-control" type="text" style="width:300px;" value="<?php echo (isset($valores['gyac'][$i]['identificacion'])) ? $valores['gyac'][$i]['identificacion'] : ''; ?>"></td>
                  <td><input name="gya[<?php echo $i; ?>][resultado]" class="form-control" type="text" style="width:70px;" value="<?php echo (isset($valores['gya'][$i]['resultado'])) ? $valores['gya'][$i]['resultado'] : ''; ?>"></td>
                  <?php if($coliformes){ ?>
                    <td><input name="coliformes[<?php echo $i; ?>][resultado]" class="form-control" type="text" style="width:70px;" value="<?php echo (isset($valores['coliformes'][$i]['resultado'])) ? $valores['coliformes'][$i]['resultado'] : ''; ?>"></td>
                  <?php } ?>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <h3>Parametros</h3>
      <div class="form-group">
        <label>No. Muestra</label>
        <input name="parametro[muestranum]" class="form-control" type="text" value="<?php echo (isset($valores['parametro']['muestranum'])) ? $valores['parametro']['muestranum'] : ''; ?>">
      </div>
      <br><br>

      <div class="form-group">
        <label>Identificacion</label>
        <input name="parametro[identificacion]" class="form-control" type="text" value="<?php echo (isset($valores['parametro']['identificacion'])) ? $valores['parametro']['identificacion'] : ''; ?>">
      </div>
      <br><br>

      <table  style="width:800px" class="table">
        <colgroup>
          <col width="180px">
          <col width="75px">
          <col width="545px">
        </colgroup>
        <thead>
          <tr>
            <th>Parametro</th>
            <th>Resultado</th>
            <th>Unidad/Metodo/LD/LC</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($params as $clave => $parametro) { $i=0;?>
            <tr>
              <td><?php echo $parametro; ?></td>
              <td><input name="parametros[<?php echo $clave; ?>][resultado]" class="form-control" type="text" style="width:70px;" value="<?php echo (isset($valores['parametros'][$clave]['resultado'])) ? $valores['parametros'][$clave]['resultado'] : ''; ?>"></td>
              <td>
              <?php
                if( !isset($valores['parametros'][$clave]['parametro']) )
                {
                  $valores['parametros'][$clave]['parametro'] = key($parametros[$clave]);
                }
              ?>
              <?php crearForma(
                  '', //Texto del label
                  'parametros['.$clave.'][parametro]', //Texto a colocar en los atributos id y name
                  $valores['parametros'][$clave]['parametro'], //Valor extraido de la bd
                  array('style="width:540px;"'), //Atributos extra de la etiqueta
                  'select', //Tipo de etiqueta
                  $parametros[$clave] //Options para los select
                ); ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>

      <h3>Adicionales</h3>

      <table style="width:400px" class="table table-bordered table-condensed">
        <thead>
          <tr>
            <th>Resultado</th>
            <th>Parametro/Unidad/Metodo/LD/LC</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i=0; $i < 10; $i++) { ?>
            <tr>
              <td><input name="adicionales[<?php echo $i; ?>][resultado]" class="form-control" type="text" style="width:70px;" value="<?php echo (isset($valores['adicionales'][$i]['resultado'])) ? $valores['adicionales'][$i]['resultado'] : ''; ?>"></td>
              <td>
                <?php
                  if( !isset($valores['adicionales'][$i]['parametro']) )
                  {
                    $valores['adicionales'][$i]['parametro'] = '';
                  }
                ?>
                <?php crearForma(
                  '', //Texto del label
                  'adicionales['.$i.'][parametro]', //Texto a colocar en los atributos id y name
                  $valores['adicionales'][$i]['parametro'], //Valor extraido de la bd
                  '', //Atributos extra de la etiqueta
                  'select', //Tipo de etiqueta
                  $adicionales //Options para los select
                ); ?>

              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <br>

      <div>
        <input type="submit" class="btn btn-success" name="accion" value="Guardar">
      </div>
  </form>
  <br>
  <p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
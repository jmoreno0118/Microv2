<?php

function crearForma($label, $nombre, $valor, $atts, $tipo, $options, $extra = ''){
  if($tipo !== 'hidden'){
    echo '<div class="form-group">';
  }

  if($tipo !== 'hidden' AND strcmp($label, '') !== 0 ){
    echo '<label for="'.$nombre.'">'.$label.':</label>';
  }
    
  if(!isset($atts['class']) AND ( strcmp($tipo, 'tfcheck') !== 0 AND strcmp($tipo, 'checks') !== 0 ))
    $atts['class'] = 'form-control';

  
  switch ($tipo) {

    case 'label':
      echo $valor;
      echo '</div>';
      break;
    
    case 'text':
      if(isset($extra['enter']))
        echo '<br>';
      if(!isset($atts['name']))
        $atts['name'] = $nombre;
      if(!isset($atts['id']))
        $atts['id'] = $nombre;
      echo '<input type="text" value="'.$valor.'"';
      imprimeAtts($atts);
      echo '>';
      echo '</div>';
      break;

    case 'html5':
      if(!isset($atts['name']))
      $atts['name'] = $nombre;
      if(!isset($atts['id']))
      $atts['id'] = $nombre;
      echo '<input value="'.$valor.'"';
      imprimeAtts($atts);
      echo '>';
      break;

    case 'hidden':
      echo '<input type="hidden" name="'.$nombre.'" id="'.$nombre.'" value="'.$valor.'"';
      imprimeAtts($atts);
      echo '>';
      break;

    case 'textarea':
      echo '<br><textarea style="resize: none;" name="'.$nombre.'" id="'.$nombre.'"';
      if(!isset($atts['rows']))
        $atts['rows'] = 5;
      if(!isset($atts['cols']))
        $atts['cols'] = 60;
      imprimeAtts($atts);
      echo '>'.$valor.'</textarea>';
      echo '</div>';
      break;

    case 'select':
      echo '<select name="'.$nombre.'" id="'.$nombre.'"';
      imprimeAtts($atts);
      echo '>';
      $selected = strval($valor) === ''? 'selected' : '';
      $disabled = '';
      if(isset($extra['disabled']) AND $extra['disabled'] === 'false'){
        $disabled = 'disabled';
      }
      echo '<option value="" '.$disabled.' '.$selected.'>Seleccionar</option>';
      foreach ($options as $value => $texto){
        $selected = (strval($valor) === strval($value)) ? 'selected' : '';
        echo '<option value="'.$value.'" '.$selected.'>'.$texto.'</option>';
      }
      echo '</select>';
      echo '</div>';
      break;

    case 'select2':
      echo '<select name="'.$nombre.'" id="'.$nombre.'"';
      imprimeAtts($atts);
      echo '>';
      $selected = strval($valor) === ''? 'selected' : '';
      $disabled = '';
      if(isset($extra['disabled']) AND $extra['disabled'] === 'false'){
        $disabled = 'disabled';
      }
      echo '<option value="" '.$disabled.' '.$selected.'>Seleccionar</option>';
      foreach ($options as $value => $texto){
        $selected = (strval($valor) === strval($texto)) ? 'selected' : '';
        echo '<option value="'.$texto.'" '.$selected.'>'.$texto.'</option>';
      }
      echo '</select>';
      echo '</div>';
      break;

    case 'tfcheck':
      echo '<div class="checkbox">';
      echo '<label>';
      $selected = ( $valor === 1 ) ? 'checked' : '';
      echo '<input type="checkbox" name="'.$nombre.'" id="'.$nombre.'" value="1"';
      imprimeAtts($atts);
      echo $selected.'>';
      echo '</label>';
      echo '</div>';
      echo '</div>';
      break;

    //Varios check seleccionados, value = valor
    case 'checks':
      if($valor === '')
        $valor = array();
      foreach ($options as $value => $texto){
        $comp = $value;
        if(isset($extra['comp']) AND $extra['comp'] === 'texto'){
          $comp = $texto;
        }

        $val = $value;
        if(isset($extra['value']) AND $extra['value'] === 'texto'){
          $val = $texto;
        }

        $multi = '';
        if(isset($extra['multi']) AND $extra['multi'] === 1){
          $multi = '[]';
          $selected = in_array($comp, $valor) ? 'checked' : '';
        }else{
          $selected = (strval($valor) === strval($comp)) ? 'checked' : '';
        }
        echo '<div class="checkbox">';
        echo '<label>';
        echo '<input type="checkbox" name="'.$nombre.$multi.'" id="'.$nombre.'" value="'.$val.'"';
        imprimeAtts($atts);
        echo $selected.'>'.$texto;
        echo '</label>';
        echo '</div>';
      }
      echo '</div>';
      break;
  }
}


function imprimeAtts($atts){
  if($atts !== ''):
    foreach ($atts as $key => $value):
      if(!is_numeric($key)):
        echo ' '.$key.'="'.$value.'"" ';
      else:
        echo ' '.$value.' ';
      endif;
    endforeach;
  endif;
}
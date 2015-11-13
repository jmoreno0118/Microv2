<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Error'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div id="pantallaerror">
  <div id="mensajerror">
    <h3> Error!!! </h3>
    <div>
      <h4> <?php htmlout($mensaje); ?> </h4>
    </div>
    <?php if (isset($_POST['arquitectura'])): ?>
      <form action="<?php htmlout($_POST['url']); ?>" method="post">
        <?php 
        foreach (json_decode($_POST['arquitectura'], TRUE) as $nombrevariable => $estructura) {
          if($estructura['tipo'] === 0)
          {
            $valor = isset($estructura['valor']) ? $estructura['valor'] : $_POST[$estructura['variables']];
            echo '<input type="hidden" name="'.$nombrevariable.'" value="'.$valor.'">';
          }
          elseif($estructura['tipo'] === 1)
          {
            $inputs = explode(',', $estructura['variables']);
            $valores = array();
            foreach ($inputs as $variable) {
              $valores[$variable] =  isset($_POST[$variable])? $_POST[$variable] : "0";
            }
            echo '<input type="hidden" name="'.$nombrevariable.'" value=\''.json_encode($valores).'\'>';
          }
          elseif($estructura['tipo'] === 2)
          {
            $valores = array();
            foreach ($_POST[$nombrevariable] as $key => $valor) {
                $valores[$key] = $valor;
            }
            echo '<input type="hidden" name="'.$nombrevariable.'" value=\''.json_encode($valores).'\'>';
          }
        }
        $post = json_decode($_POST['post'], TRUE);
        if(isset($_POST['prevact'])){
          $accion = $_POST['prevact'];
        }elseif(isset($post['accion'])){
          $accion = $post['accion'];
        }else{
          $accion = $_SESSION['accion'];
        }
        echo '<input type="hidden" name="accion" value=\''.$accion.'\'>';
        $accionparam = isset($_POST['boton']) ? $_POST['boton'] : $_POST['accion'];
        echo '<input type="hidden" name="accionparam" value=\''.$accionparam.'\'>';
        ?>
        <input type="submit" value="Regresar">
      </form>
    <?php else: ?>
      <?php if( strcmp($errorlink,'') !== 0 ): ?>
        <a href="<?php echo $errorlink; ?>"><?php echo $errornav; ?></a>
      <?php else:?>
        <a href="<?php echo url ?>">Volver al menú principal</a>
      <?php endif; ?>
    <?php endif; ?>
  </div>  
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
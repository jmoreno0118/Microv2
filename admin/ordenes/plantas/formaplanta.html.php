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
      success: "valid",
      submitHandler: function(form) {
        $.ajax({
          data: $("#plantaform").serialize(),
          type: 'post', 
          url: 'guardaplanta.php',
          success: function(response) {
              if (response === "true") {
                  alert("La planta ha sido guardada. Favor de refrescar el listado en la pantalla anterior. Esta pantalla se cerrará");
                  closeWindow();
              } else {
                console.log(response);
                alert("La planta no pudo ser guardada.");
              }
          },
          error: function(response) {
              alert("ERROR: La planta no pudo ser guardada.");
          }
      });
      }
    });
  });
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
  <h3><?php htmlout($titulopagina.$razonsocial); ?></h3>
  <?php $formulario = array("Planta" => "planta",
                            "Calle" => "calle",
                            "Colonia" => "colonia",
                            "Ciudad" => "ciudad",
                            "Estado" => "estado",
                            "Código Postal" => "cp",
                            "RFC" => "rfc"); ?>
  <form id="plantaform" name="plantaform" class="form-inline" action="" method="post" onsubmit="return false;">
    <?php foreach($formulario as $label => $name): ?>
      <div class="form-group">
        <label for="<?php htmlout($name); ?>"><?php htmlout($label); ?>:</label>
        <input type="text" class="form-control" name="<?php htmlout($name); ?>" id="<?php htmlout($name); ?>" value="<?php if(isset($valores)){htmlout($valores[$name]);} ?>">
      </div>
      <br><br>
    <?php endforeach?>
    <div>
      <input type="hidden" name="razonsocial" value="<?php htmlout($razonsocial); ?>">
      <?php if(isset($_POST['idcliente'])){ ?>
        <input type="hidden" name="idcliente" value="<?php htmlout($_POST['idcliente']); ?>">
      <?php } ?>
      <input type="submit" class="btn btn-success" name="accion" value="<?php htmlout($boton); ?>">
    </div> 
  </form>
  <script language="javascript" type="text/javascript">
    function closeWindow() {
      window.open('','_parent','');
      window.close();
    }
  </script> 
  <br>
  <button type="button" class="btn btn-danger" onclick="closeWindow();">No guardar planta</button>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>

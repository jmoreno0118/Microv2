<?php open_html_head('Registro'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
  <div>
    <div id="registro">
      <center>
        <h2>Login</h2>
        <p>Debes estar registrado para poder entrar al sistema<p>
      </center>
      <?php if(isset($loginError)){ ?>
        <p><?php echo($loginError); ?></p>
      <?php } ?>
      <form action="" method="post">
        <div>
          <label for="usuario">Usuario</label>
          <input class="form-control" type="text" name="loginusuario" id="usuario"/>
        </div>
        <br>
        <div>    
          <label for="clave">Contraseña</label>
          <input class="form-control" type="password" name="loginclave" id="clave">
        </div>
        <br>
        <div>
          <center><input class="btn btn-primary btn-lg btn-block" type="submit" name="accion" value="Entrar"></center>
        </div> 
      </form>
    </div>
  </div>
<?php close_html_body_footer(); ?>
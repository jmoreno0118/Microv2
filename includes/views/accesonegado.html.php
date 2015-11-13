<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Acceso Denegado'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div id="pantallaerror">
  <div id="mensajerror">
    <h3> Acceso denegado </h3>
    <div>
      <h4> <?php htmlout($mensaje); ?> </h4>
    </div>
  <div>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
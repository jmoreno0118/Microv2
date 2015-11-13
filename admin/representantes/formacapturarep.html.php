<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
	<h2><?php htmlout($titulopagina); ?></h2>
	<form action="?<?php htmlout($accion); ?>" class="form-inline" method="post">
		<div class="form-group">
			<label for="nombre">Nombre de representante: </label>
			<input type="text" class="form-control" name="nombre" id="nombre" value="<?php htmlout($nombre); ?>">
		</div>
		<br><br>
		<div class="form-group">
			<label for="estado">Por estado:</label>
			<select name="estado" id="estado" class="form-control">
				<option value="">cualquier estado</option>
				<?php $num=count($estados);
				for($x = 0; $x < $num; $x++): ?>
					<option value="<?php echo $estados[$x]; ?>"
						<?php if ($estados[$x]==$estado)
						{echo ' selected';}?>><?php echo $estados[$x]; ?>
					</option>
				<?php endfor; ?>
			</select>
		</div>
		<br><br>
		<div class="form-group">
			<label for="tel">Teléfono: </label>
			<input type="text" class="form-control" name="tel" id="tel" value="<?php htmlout($tel); ?>">
		</div>
		<br><br>
		<div>	
			<input type="hidden" name="id" value="<?php htmlout($id); ?>">
			<input type="submit"  class="btn btn-success" value="<?php htmlout($boton); ?>">
		</div> 
	</form>
	<br>
  	<p><a href="">Volver a representantes</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
	<h2><?php htmlout($titulopagina); ?></h2>
	<form action="" class="form-inline" method="post">
		<div>
			<label for="usuario">Nombre de usuario: </label>
			<input type="text" class="form-control ntext" name="usuario" id="usuario" value="<?php htmlout($usuario); ?>">
		</div>
		<div>
			<label for="nombre">Nombre: </label>
			<input type="text" class="form-control ntext" name="nombre" id="nombre" value="<?php htmlout($nombre); ?>">
		</div>
		<div>
			<label for="nombre">Apellidos: </label>
			<input type="text" class="form-control ntext" name="apellido" id="apellido" value="<?php htmlout($apellido); ?>">
		</div>
		<div>
			<label for="correo">Correo electónico: </label>
			<input type="text" class="form-control ntext" name="correo" id="correo" value="<?php htmlout($correo); ?>">
		</div>
		<div>
			<label for="clave">Clave de acceso</label>
			<input type="password" class="form-control ntext" name="clave" id="clave" value="">
		</div>

		<fieldset>
			<legend>Permisos:</legend>
			<?php for ($i=0; $i<count($actividades); $i++) :?>
			<div>
				<label for="actividad<?php echo $i; ?>">
				<input type="checkbox" name="actividades[]" id="actividad<?php echo $i; ?>" 
				value="<?php htmlout($actividades[$i]['id']); ?>"
				<?php if ($actividades[$i]['seleccionada'])
				{echo ' checked';}?>>		
				<?php htmlout($actividades[$i]['id']); ?>
				</label>:<?php htmlout($actividades[$i]['descripcion']); ?>
			</div>
			<?php endfor; ?>
		</fieldset>

		<fieldset>
			<legend>Representantes:</legend>
			<?php for ($i=0; $i<count($representantes); $i++) :?>
				<div>
					<label for="representante<?php echo $i; ?>">
					<input type="checkbox" name="representantes[]" id="representante<?php echo $i; ?>" 
					value="<?php htmlout($representantes[$i]['id']); ?>"
					<?php if ($representantes[$i]['seleccionada'])
					{echo ' checked';}?>>		
					<?php htmlout($representantes[$i]['nombre']); ?>
					</label>
				</div>
			<?php endfor; ?>
		</fieldset>

		<div>	
			<input type="hidden" name="id" value="<?php htmlout($id); ?>">
			<input type="submit" class="btn btn-success" value="<?php htmlout($boton); ?>">
		</div> 
	</form>
	<br>
	<p><a href="">Volver a usuarios</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
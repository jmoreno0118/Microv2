<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head('Ordenes'); ?>
<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
	<h3>Administrador de ordenes</h3>
	<p><a class="btn btn-default" href="?ordenueva">Agrega una orden nueva</a></p>
	<form action="?" class="form-inline" method="get">
		<p>Buscar a una orden de acuerdo con los siguientes criterios</p>
		<div class="form-group">
			<label for="ot">Num de OT:</label>
			<input type="text" class="form-control" name="ot" id="ot">
		</div>
		<br><br>
		<div class="form-group">
			<label for="fechaini">Fecha de inicio:</label>
			<input type="date" class="form-control" name="fechaini" id="fechaini">
			<label for="fechafin">Fecha fin:
			<input type="date" class="form-control" name="fechafin" id="fechafin"> (aaaa-mm-dd)</label>
		</div>
		<br><br>
		<div class="form-group">
			<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo" class="form-control">
				<option value="">Cualquier especialidad</option>
				<?php $num=count($especialidades);
				for($x = 0; $x < $num; $x++): ?>
					<option value="<?php echo $especialidades[$x]; ?>"><?php echo $especialidades[$x]; ?></option>
				<?php endfor; ?>
			</select>
		</div>
		<br><br>
		<div class="form-group">
			<label for="representante">Representante:</label>
			<select name="representante" id="representante" class="form-control">
				<option value="">Cualquier representante</option>
				<?php foreach($representantes as $representante): ?>
					<option value="<?php htmlout($representante['id']); ?>"><?php htmlout($representante['nombre']); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<br>
		<div>
			<input type="submit" class="btn btn-success" name="accion" value="Buscar">
		</div>
	</form>
	<br>
	<p><a href="<?php echo url; ?>">Regresa al menú principal</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
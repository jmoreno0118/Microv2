<?php include_once direction.functions.'html.inc.php'; ?>
<?php include_once direction.functions.'form.inc.php'; ?>
<?php open_html_head($pestanapag); ?>
<?php load_jquery(); ?>
<?php load_jqueryvalidation(); ?>

<script type="text/javascript">
$(document).ready(function(){

	function listaPlantas(planta, cliente){
		$("#idcliente").val($("#cliente").val());
		$.ajax({
			type: "POST",
			url: "plantas/plantas.php",
			data: {id: $("#cliente").val(), planta: planta, cliente: cliente},
			cache: false,
			success: function(html){
				$("#planta").html(html);
			}
		});
	}

	listaPlantas(<?php echo $planta.",".$clienteid; ?>);

	$("#cliente").change(function(){
		listaPlantas(<?php echo $planta.",".$clienteid; ?>);
	});

	$("#refreshPlantas").click(function(e){
		e.preventDefault();
		listaPlantas(<?php echo $planta.",".$clienteid; ?>);
	});

	function mostrar(valor){
		if(valor == 'MH')
		{
			$('#meestudios').hide();
			$('#mhestudios').show();
		}
		if(valor == 'ME')
		{
			$('#mhestudios').hide();
			$('#meestudios').show();
		}
		if(valor == 'Medicas'){
			$('#mhestudios').hide();
			$('#meestudios').hide();
		}
	}

	<?php if(isset($especialidad)){ ?>
		mostrar('<?php echo $especialidad ?>');
	<?php } ?>

	$('#tipo').change(function(){
	  mostrar($(this).val());
	});


    $("#ordenform").validate({
      rules: {
        ot: {
         required: true
        },
        representante: {
         required: true
        },
        cliente: {
         required: true
        },
        atencion: {
         required: true
        },
        atenciontel: {
         required: true
        },
        atencioncorreo: {
         required: true
        },
        tipo: {
         required: true
        }
      },
      success: "valid",
    });

});
</script>

<?php close_head_open_body(); ?>
<?php get_header(); ?>
<div>
	<h2><?php htmlout($titulopagina); ?></h2>
	<div>
		<form id="fplanta" action="plantas/index.php" class="form-inline" method="post" target="_blank">
			<input id="idcliente" name="idcliente" type="hidden" value="<?php htmlout($clienteid); ?>">
			<input name="nplanta" class="btn btn-default" type="submit" value="Nueva Planta">
		</form>
	</div>
	<br>
	<form id="ordenform" action="?<?php htmlout($accion); ?>" class="form-inline" method="post">
		<?php if (isset($mensaje)): ?>
			<div><strong><?php htmlout($mensaje); ?></strong></div>
		<?php endif; ?> 
		<div class="form-group">
			<label for="ot">Num. de OT: </label>
			<input type="text" class="form-control" name="ot" id="ot" value="<?php htmlout($ot); ?>">
		</div>
		<br><br>
		<div class="form-group">
			<label for="representante">Representante: </label>
			<select name="representante" id="representante" class="form-control">
			<option value="">Seleciona representante</option>
			<?php foreach($representantes as $rep): ?>
				<option value="<?php echo $rep['id']; ?>"
					<?php if ($rep['id']==$representanteid){echo ' selected';}?>>
					<?php echo $rep['nombre']; ?>
				</option>
			<?php endforeach; ?>
			</select>
		</div>
		<br><br>
		<div class="form-group">
			<label for="cliente">Cliente: </label>
			<input type="hidden" name="cliente" id="cliente" value="<?php echo $clienteid; ?>">
			<input type="text" class="form-control" style="width:500px" value="<?php echo $razonsocial; ?>" disabled>
			<!--select name="cliente" id="cliente" >
			<option value="">Seleciona cliente</option>
			<?php foreach($clientes as $cliente): ?>
			<option value="<?php echo $cliente['id']; ?>"
			<?php if ($cliente['id']==$clienteid)
			{echo ' selected';}?>><?php echo $cliente['nombre']; ?></option>
			<?php endforeach; ?>
			</select-->
		</div>
		<br><br>
		<div class="form-group">
			<label for="planta">Planta: </label>
			<select name="planta" id="planta" class="form-control">
				<option selected="selected" disabled value="0">--Selecciona planta--</option>
			</select>
			<button type="button" class="btn btn-default" id="refreshPlantas">Refrescar plantas</button>
		</div>
		<br><br>
		<div class="form-group">
			<label for="atencion">La orden irá dirigida a:</label>
			<input name="atencion" class="form-control" style="width: 350px;" id="atencion" value="<?php htmlout(htmldecode($atencion)); ?>">
		</div>
		<br><br>
		<div class="form-group">
			<label for="atenciontel">Teléfono: </label>
			<input name="atenciontel" class="form-control" id="atenciontel" value="<?php htmlout($atenciontel); ?>">
		</div>
		<br><br>
		<div class="form-group">
			<label for="atencioncorreo">Correo electrónico: </label>
			<input name="atencioncorreo" class="form-control" style="width: 350px;" id="atencioncorreo" value="<?php htmlout($atencioncorreo); ?>">
		</div>
		<br><br>
		<div class="form-group">
			<label for="tipo">Tipo:</label>
			<select name="tipo" id="tipo" class="form-control">
				<option value="">Selecciona especialidad</option>
				<?php $num=count($especialidades);
				for($x = 0; $x < $num; $x++): ?>
					<option value="<?php echo $especialidades[$x]; ?>"
						<?php if ($especialidades[$x]==$especialidad){echo ' selected';}?>>
						<?php echo $especialidades[$x]; ?>
					</option>		   
				<?php endfor; ?>
			</select>
		</div>
		<br>
		<div id="mhestudios" class="form-group" style="display:none;">
			<h3>Higiene</h3>
			<?php for ($i=0; $i<count($higienestudios); $i++) :?>
				<div>
					<label for="higienestudio<?php echo $i; ?>">
					<input type="checkbox" name="higienestudios[]" id="higienestudio<?php echo $i; ?>" 
					value="<?php htmlout($higienestudios[$i]['nombre']); ?>"
					<?php if ($higienestudios[$i]['seleccionada']){echo ' checked';}?>>		
					<?php htmlout($higienestudios[$i]['nombre']); ?>
					</label>
				</div>
			<?php endfor; ?>
		</div>
		<div id="meestudios" class="form-group" style="display:none;">
			<h3>Ecologia</h3>
			<?php for ($i=0; $i<count($ecologiaestudios); $i++) :?>
				<div>
					<label for="ecologiaestudio<?php echo $i; ?>">
					<input type="checkbox" name="ecologiaestudios[]" id="ecologiaestudio<?php echo $i; ?>" 
					value="<?php htmlout($ecologiaestudios[$i]['nombre']); ?>"
					<?php if ($ecologiaestudios[$i]['seleccionada']){echo ' checked';}?>>		
					<?php htmlout($ecologiaestudios[$i]['nombre']); ?>
					</label>
				</div>
			<?php endfor; ?>
		</div>
		<br><br>
		<div>
			<input type="hidden" name="fechalta" value="<?php htmlout($fechalta); ?>">
			<input type="hidden" name="otant" value="<?php htmlout($otant); ?>">
			<input type="hidden" name="id" value="<?php htmlout($id); ?>">
			<input type="submit" class="btn btn-success" value="<?php htmlout($boton); ?>">
		</div>
	</form>
	<br>
	<p><a href="../ordenes">Volver a ordenes</a></p>
</div>  <!-- cuerpoprincipal -->
<?php close_html_body_footer(); ?>
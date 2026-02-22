 <link href="css/estiloinputlabel.css" rel="stylesheet">
<script src="js/jquery.form.js"></script>
<script src="js/jquery.mask.js"></script>
<script src="js/croppie.js"></script>
<script type='text/javascript' src="cadastros/perfil/acoes.js"></script>
<?php
require_once("../includes/padrao.inc.php");
 // print_r($_SESSION["usuariosaw"]);
?>
<script>
$( document ).ready(function() {	
	$('#Listar').load("empresas/listar.php");
	
	$('.form_campos').on('focus blur',
function (e) {
  $(this).parents('.form-group').toggleClass('focused', (e.type==='focus' || this.value.length > 0));
}
).trigger('blur');
 $('.select').on('change blur',
function (e) {
  $(this).parents('.form-group-select').toggleClass('focused', (e.type==='focus' || this.value !==''));
}
).trigger('blur');
	
	});
</script>

<form method="post" id="grava" name="grava" action="cadastros/perfil/gravarPerfil.php" enctype="multipart/form-data">
<h1>Perfil</h1>
	<input type="hidden" id="id_usuarios" name="id_usuarios" value="0" />
    <input type="hidden" value="0" name="acaoUsuario" id="acaoUsuario" />
	 <div class="row">
		  <div class="col-md-8">
			<div class='form-group'>
			   <label class='control-label' for='inputNormal'>Nome</label>
			   <input type="text" id="nome_usuario" name="nome_usuario" class="form_campos" value="<?php echo $_SESSION["usuariosaw"]["nome"]; ?>"> 
			   <div id="valida_nome" style="display: none" class="msgValida">
					Por favor, informe o Nome .
				</div> 
			 </div>         
		  </div>  
		  <div class="col-md-4">
			<div class='form-group'>
			  <label class='control-label' for='inputNormal'>Login</label>
			  <input type="text" id="login" name="login" class="form_campos" value="<?php echo $_SESSION["usuariosaw"]["login"]; ?>"> 
			    <div id="valida_login" style="display: none" class="msgValida">
					Por favor, informe o Login .
				</div> 
			 </div>         
		  </div>  

	 </div>	
	 <div class="row">
	 <div class="col-md-12">
			<div class='form-group'>
			  <label class='control-label' for='inputNormal'>Email</label>
			   <input type="text" id="email" name="email" class="form_campos" value="<?php echo $_SESSION["usuariosaw"]["email"]; ?>">
			   
			 </div>         
		  </div>  
	 </div>		
	
	 <div class="row">			
			    <input type="hidden" id="acao" name="acao" value="0">
			    <input type="hidden" id="id" name="id" value="0">
				<input type="reset" value="Cancelar" id="btnCancelar" class="btn btn-danger ml-auto" style="visibility:hidden;">&nbsp;&nbsp;
				<input type="submit" value="Gravar" id="btnGravar" class="btn btn-primary">
		</div>
	</form>
 
 
 


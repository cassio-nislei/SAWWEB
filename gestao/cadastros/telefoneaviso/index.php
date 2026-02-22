<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/telefoneaviso/listar.php");


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

<form method="post" action="cadastros/telefoneaviso/salvar.php" name="grava" id="grava">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormTelefone">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Adicionar Telefone para receber notificações de funcionamento</b></div>
  <div class="panel-body">


    <div class="form-group">
       <label class='control-label' for='inputNormal'>Telefone*</label>
      <input type="text" id="txttelefone" name="txttelefone" class="form_campos">   
      <div id="valida_telefone" style="display: none" class="msgValida">
        Por favor, informe o telefone para receber notificações de funcionamento.
      </div>  
    </div>   
  
  
   <br />
    <div class="form-group">   
        <input type="reset" value="Cancelar" id="btnCancelar" class="btn btn-danger ml-auto" style="visibility:hidden;">&nbsp;&nbsp;
				<input type="submit" value="Gravar" id="btnGravar" class="btn btn-primary">
   
  	 </div>
	 </div>
	 </div>
	  </div>
</form> 
   
   
   <div class="container">
			<div class="panel panel-default">
				<div class="panel-heading"><b>Telefones Cadastrados</b></div>
				<div class="panel-body" id="Listar">
				
				<!-- Aqui Lista os departamentos Cadastrados -->
				
				
				</div>
	  </div>
	</div>
  <script type='text/javascript' src="js/funcoes.js"></script>
  <script>
$( document ).ready(function() {	
   //Botão Cancelar Ao CLicar deve ser oculto
		$("#btnCancelar").click(function(){
			$("#acao").val(0);
			$("#btnCancelar").css({"visibility" : "hidden"});
		})


  // Salvando um Registro //
	 // Salvando um Registro //
	 $('#btnGravar').click(function(e){
		e.preventDefault();

		var mensagem  = "<strong>Telefone Cadastrado com sucesso!</strong>";
		var mensagem2 = 'Falha ao Efetuar Cadastro!';
		var mensagem3 = 'Telefone Já Cadastrado!';
		var mensagem4 = 'Telefone Atualizado com Sucesso!';

		$("input:text").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#txttelefone").val()) == ''){
			$("#valida_telefone").css({"display" : "inline", "color" : "red"});
			$("#txttelefone").css({"border-color" : "red"});
			$("#txttelefone").focus();
			return false;
		}

	    $('#grava').ajaxForm({
			resetForm: true, 			  
			beforeSend:function() { 
				$("#btnGravar").attr('value', 'Salvando ...');
				$('#btnGravar').attr('disabled', true);
				$('#FormTelefone').find('input').prop('disabled', true);
			},
			success: function( retorno ){
           		if (retorno == 1) { mostraDialogo(mensagem, "success", 2500); }
				else if (retorno == 2){ mostraDialogo(mensagem4, "success", 2500); }
				else if (retorno == 3){ mostraDialogo(mensagem3, "danger", 2500); }
				else if (retorno == 4){ mostraDialogo(mensagem4, "success", 2500); }
				else if (retorno == 5){ mostraDialogo(mensagem5, "danger", 2500); }
				else{ mostraDialogo(mensagem2, "danger", 2500); }

                $("#acao").val(0); //Reseto o formulário para modo de Inserção
				$("#btnCancelar").css({"visibility" : "hidden"}); //Removo o botão cancelar da Tela
				$.ajax("cadastros/telefoneaviso/listar.php").done(function(data) {
					$('#Listar').html(data);
				});
			},		 
			complete:function(retorno) {              
				$("#btnGravar").attr('value', 'Salvar');
				$('#btnGravar').attr('disabled', false);
				$('#FormTelefone').find('input, button').prop('disabled', false);             
			},
			error: function (retorno) { mostraDialogo(mensagem5, "danger", 2500); }
		}).submit();
	});
	// FIM Salvando um Registro //  

});
  </script>
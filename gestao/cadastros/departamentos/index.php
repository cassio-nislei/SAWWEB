<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/departamentos/listar.php");


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

<form method="post" action="cadastros/departamentos/salvar.php" name="gravaDepartamento" id="gravaDepartamento">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormDepartamentos">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Departamentos</b></div>
  <div class="panel-body">

    <div class="form-group">
      <div class='form-group-select' style="width:100%">
           <label class='control-label'>Escolha um item de Menu</label>
          <select class="select form_campos" id="menu" name="menu" style="width:100%">
		  <?php
		 	 $menu = mysqli_query($conexao , "SELECT * FROM tbmenu ORDER BY id");
			echo '<option selected></option>';
			while ($ListaMenus = mysqli_fetch_array($menu)){
				echo '<option value="'.$ListaMenus["id"].'">'.$ListaMenus["descricao"].'</option>';
			}
			?>
         </select> 
           <div id="valida_categoria" style="display: none" class="msgValida">
              Por favor, informe o tipo do Produto .
          </div> 
       </div>  
    </div>   

    <div class="form-group">
       <label class='control-label' for='inputNormal'>Descrição do Departamento*</label>
      <input type="text" id="departamento" name="departamento" class="form_campos">   
      <div id="valida_menu" style="display: none" class="msgValida">
        Por favor, informe a descrição do Departamento.
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
				<div class="panel-heading"><b>Departamentos Cadastrados</b></div>
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
	$('#btnGravar').click(function(e){
		e.preventDefault();	

		var mensagem  = "<strong>Departamento Cadastrado com sucesso!</strong>";
		var mensagem2 = 'Falha ao Efetuar Cadastro!';
		var mensagem3 = 'Departamento Já Cadastrado!';
		var mensagem4 = 'Departamento Atualizado com Sucesso!';
		var mensagem5 = 'Já existe um departamento vinculado ao Item de Menu Selecionado!';
		var mensagem6 = 'Existe uma resposta Automática vinculada ao Item de Menu Selecionado!';
		

		$("input:text").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#departamento").val()) == ''){
			$("#valida_menu").css({"display" : "inline", "color" : "red"});
			$("#departamento").css({"border-color" : "red"});
			$("#departamento").focus();
			return false;
		}
 
	    $('#gravaDepartamento').ajaxForm({
			resetForm: true, 			  
			beforeSend:function() { 
				$("#btnGravar").attr('value', 'Salvando ...');
				$('#btnGravar').attr('disabled', true);
				$('#gravaDepartamento').find('input').prop('disabled', true);
			},
			success: function( retorno ){
               // alert(retorno);
				// Mensagem de Cadastro efetuado
				if (retorno == 1) { mostraDialogo(mensagem, "success", 2500); }
				// Mensagem de Atualização Efetuada
				else if (retorno == 2){ mostraDialogo(mensagem4, "success", 2500); }
				// Departamento já existe
				else if (retorno == 3){ mostraDialogo(mensagem3, "danger", 2500); }
				// Já existe um departamento cadastrado para este menu
				else if (retorno == 4){ mostraDialogo(mensagem5, "danger", 2500); }
				// Já existe uma resposta automática Cadastrada para o Item de Menu selecionado
				else if (retorno == 5){ mostraDialogo(mensagem6, "danger", 2500); }
				// Mensagem de Falha no Cadastro
				else{ mostraDialogo(mensagem2, "danger", 2500); }

				
            $("#btnCancelar").css({"visibility" : "hidden"});
			$.ajax("cadastros/departamentos/listar.php").done(function(data) {
					$('#Listar').html(data);
				});

			},		 
			complete:function(retorno) {
		//		alert("Completo"+retorno);
		        $("#acao").val(0);
				$("#btnGravar").attr('value', 'Salvar');
				$('#btnGravar').attr('disabled', false);
				$('#gravaDepartamento').find('input, button').prop('disabled', false);	
				
			},
			error: function (retorno) {
			//	alert("Erro"+retorno);
				 mostraDialogo(mensagem5, "danger", 2500);
			 
		}
		}).submit();
	});
	// FIM Salvando um Registro //  

});
  </script>
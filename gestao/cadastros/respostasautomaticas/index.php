<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/respostasautomaticas/listar.php");


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

<form method="post" action="cadastros/respostasautomaticas/salvar.php" name="gravaRespostaAutomatica" id="gravaRespostaAutomatica">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormRespostaAutomatica">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Adicionar Nova Resposta Automática</b></div>
  <div class="panel-body">

   <div class="row">
	  <div class="col-8">
		  <div class="form-group">
			<div class='form-group-select' style="width:100%">
				<label class='control-label'>Escolha um item de Menu</label>
				<select class="select form_campos" id="menu_resposta" name="menu_resposta" style="width:100%">
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
	    </div>
		<div class="col-4">
		  <div class="form-group">
			<div class='form-group-select' style="width:100%">
				<label class='control-label'>Ação</label>
				<select class="select form_campos" id="menu_acao" name="menu_acao" style="width:100%">
				    <option value="0">Nenhuma</option>
					<option value="1">Devolve Menu</option>
					<option value="2">Devolve Menu Sem titulo</option>
					<option value="9">Encerra Atendimento</option>
				</select> 
				<div id="valida_categoria" style="display: none" class="msgValida">
					Por favor, informe o tipo do Produto .
				</div> 
			</div>  
		  </div> 
	   </div>
	</div>


    <div class="form-group">
       <label class='control-label' for='inputNormal'>Descrição do Departamento*</label>
      <input type="text" id="respostaautomatica" name="respostaautomatica" class="form_campos">   
      <div id="valida_menu" style="display: none" class="msgValida">
        Por favor, informe a descrição do Departamento.
      </div>  
    </div>  
	
	<div class="form-group">
            <h2 class="title" id="arquivo_carregado"></h2>						
	</div>
	<div class="form-group">
		<label for="exampleFormControlFile1">Arquivo de envio Automático</label>
		<input type="file" class="form-control-file" id="foto" name="foto">
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
				<div class="panel-heading"><b>Respostas Cadastradas</b></div>
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
			$("#arquivo_carregado").html("");
			$("#btnCancelar").css({"visibility" : "hidden"});
		})


  // Salvando um Registro //
	$('#btnGravar').click(function(e){
		e.preventDefault();	

		var mensagem  = "<strong>Resposta Automatica Cadastrada com sucesso!</strong>";
        var mensagem2 = 'Falha ao Efetuar Cadastro!';
        var mensagem3 = 'Resposta Automática Já Cadastrado!';
        var mensagem4 = 'Resposta Automática Atualizada com Sucesso!';
        var mensagem5 = 'Já existe um departamento vinculado ao Item de Menu Selecionado!';
        var mensagem6 = 'Existe uma resposta Automática vinculada ao Item de Menu Selecionado!';
  
        $("input:text").css({"border-color" : "#999"});
        $(".msgValida").css({"display" : "none"});
	    
        if ($.trim($("#menu_resposta").val()) == ''){	
            $("#valida_menu_resposta").css({"display" : "inline", "color" : "red"});			
            $("#menu_resposta").css({"border-color" : "red"});
            $("#menu_resposta").focus();
            return false;
        }	

        if ($.trim($("#respostaautomatica").val()) == ''){	
            $("#valida_respostaautomatica").css({"display" : "inline", "color" : "red"});
            $("#respostaautomatica").css({"border-color" : "red"});
            $("#respostaautomatica").focus();
            return false;
        }
 
	    $('#gravaRespostaAutomatica').ajaxForm({
		    resetForm: true, 			  
            beforeSend:function() { 
                $("#btnGravarRespostaAutomatica").attr('value', 'Salvando ...');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', true);
            },
            success: function( retorno ){
			//	alert(retorno);
                // Mensagem de Cadastro efetuado //
                if (retorno == 1) { 
					mostraDialogo(mensagem, "success", 2500);
					$("#btnCancelar").click(); }
                // Mensagem de Atualização Efetuada //
                else if (retorno == 2){ 
					mostraDialogo(mensagem4, "success", 2500); 
					$("#btnCancelar").click();
				}
                // Departamento já existe //
                else if (retorno == 3){ 
					mostraDialogo(mensagem3, "danger", 2500);			
				}
                // Já existe um departamento cadastrado para este menu //
                else if (retorno == 4){ 
					mostraDialogo(mensagem5, "danger", 2500);					
				 }
                // Já existe uma resposta automática Cadastrada para o Item de Menu selecionado //
                else if (retorno == 5){ 
					mostraDialogo(mensagem6, "danger", 2500); 				
				}
                // Mensagem de Falha no Cadastro //
                else{ mostraDialogo(mensagem2, "danger", 2500); }
				
                $.ajax("cadastros/respostasautomaticas/listar.php").done(function(data) {
                    $('#Listar').html(data);
                });
            },		 
		    complete:function() {
                $("#btnGravarRespostaAutomatica").attr('value', 'Salvar');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', false);
            },
            error: function (retorno) { mostraDialogo(mensagem5, "danger", 2500); }
	    }).submit();
	});
    // FIM Novo Registro //

});
  </script>
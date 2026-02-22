<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("cadastros/lista_transmissao/listar.php");


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

<form method="post" action="cadastros/lista_transmissao/importar.php" name="importaMensagens" id="importaMensagens">
<div class="panel panel-default">
	<div class="panel-heading"><b>Importar Arquivo de mensagens</b></div>
    <div class="panel-body">
	<div class="row">
		<div class="col-8">
			<div class="form-group">
				<label for="exampleFormControlFile1">Importar arquivo XMLs <a href="cadastros/lista_transmissao/file.csv" target="_blank">(arquivo CSV)</a></label>
				<input type="file" accept=".csv" class="form-control-file" id="arquivo" name="arquivo">
			</div>
		</div>
		<div class="col-4">
		  <input type="submit" value="Importar" id="btnImportar" class="btn btn-primary">
		  </div>
	</div>
</div>
</form>


<form method="post" action="cadastros/lista_transmissao/salvar.php" name="gravaRespostaAutomatica" id="gravaRespostaAutomatica">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormRespostaAutomatica">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Agendar envio de mensagens</b></div>
  <div class="panel-body">

   <div class="row">
      <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-12">
						<div class="form-group focused">
						   <label class="control-label" for="inputNormal">Data programada</label>
						   <input type="text" name="dt_envio" id="dt_envio" class="form_campos hasDatepicker" value="<?php echo date("d/m/Y"); ?>" maxlength="10"><img class="ui-datepicker-trigger" src="imgs/calendario.png" alt="..." title="..."> 
						 </div> 
		</div>
		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-12">
		<div class="form-group focused">
			<label class="control-label" for="inputNormal">Hora programada</label>
					<input type="text" value="<?php echo date("H:i:s"); ?>" id="hr_envio" name="hr_envio" class="form_campos text-uppercase"> 
					<div id="valida_inicio_segunda" style="display: none" class="msgValida">
						Preencha o Inicio de Expediente na Segunda Feira.
					</div> 
			</div>
		</div>

		<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 col-12">
			<div class='form-group'>
			  <label class='control-label' for='inputNormal' id="celular">Celular</label>
			  <input type="text" name="celular" id="celular" size="46" class="form_campos"/>				   	
			</div>         
		</div>

	</div>

    <div class="form-group">
       <label class='control-label' for='inputNormal'>Mensagem para enviar*</label>
      <input type="text" id="mensagem" name="mensagem" class="form_campos">   
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
				<div class="panel-heading"><b>Envios cadastrados Cadastradas</b></div>
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

		$('#btnImportar').click(function(e){
			e.preventDefault();	

			$('#importaMensagens').ajaxForm({
		    resetForm: true, 			  
            beforeSend:function() { 
                $("#btnImportar").attr('value', 'Salvando ...');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', true);
            },
            success: function( retorno ){				
                // Mensagem de Cadastro efetuado //
                if (retorno == 1){ 
					mostraDialogo("Dados Importados", "success", 2500); 	
					$.ajax("cadastros/lista_transmissao/listar.php").done(function(data) {
                    $('#Listar').html(data);
                });			
				}
                // Mensagem de Falha no Cadastro //
                else{ mostraDialogo("Erro ao importar arquivo", "danger", 2500); }
				
               
            },		 
		    complete:function() {
                $("#btnImportar").attr('value', 'Salvar');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', false);
            },
            error: function (retorno) { mostraDialogo(mensagem5, "danger", 2500); }
	    }).submit();
	});

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
	    
        if ($.trim($("#dt_envio").val()) == ''){	
            $("#valida_menu_resposta").css({"display" : "inline", "color" : "red"});			
            $("#menu_resposta").css({"border-color" : "red"});
            $("#menu_resposta").focus();
            return false;
        }	

        if ($.trim($("#hr_envio").val()) == ''){	
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
				
                $.ajax("cadastros/lista_transmissao/listar.php").done(function(data) {
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

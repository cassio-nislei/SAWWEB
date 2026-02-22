<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/jquery.form.js"></script>
<link href="css/estiloinputlabel.css" rel="stylesheet">
<script>
$( document ).ready(function() {	
	$('#Listar').load("marketing/envios_massa/listar.php");


	$('.form_campos, textarea').on('focus blur',
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


 

<form method="post" action="marketing/envios_massa/salvar.php" name="gravaRespostaAutomatica" id="gravaRespostaAutomatica">	
<input type="hidden" value="0" name="acao" id="acao" />
<input type="hidden" name="id" id="id" value="0">
<div class="container" id="FormRespostaAutomatica">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Enviar mensagem em massa</b></div>
  <div class="panel-body">

   <div class="row">
      <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 col-12">
						<div class="form-group focused">
						   <label class="control-label" for="inputNormal">Data para enviar</label>
						   <input type="text" name="dt_envio" id="dt_envio" class="form_campos hasDatepicker" value="<?php echo date("d/m/Y"); ?>" maxlength="10"><img class="ui-datepicker-trigger" src="imgs/calendario.png" alt="..." title="..."> 
						 </div> 
		</div>


		<div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 col-12">
			<div class='form-group'>
			  <label class='control-label' for='inputNormal' id="celular">Nome Mensagem</label>
			  <input type="text" name="nome" id="nome" size="46" class="form_campos"/>				   	
			</div>         
		</div>

		<div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 col-12">
			<div class='form-group'>
			  <label class='control-label' for='inputNormal' id="celular">Ativo</label>
			  <input type="checkbox" name="ativo" id="ativo" size="46" class="form_campos" checked/>				   	
			</div>         
		</div>

	</div>

    <div class="form-group">
	    <label class="control-label" for="inputNormal">Mensagem</label>
			 <textarea class="form_textArea" name="mensagem" id="mensagem"></textarea>							
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



<div class="modal" tabindex="-1" role="dialog" id="mdlImporta">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Fazer upload da lista de envio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <form method="post" action="marketing/envios_massa/importar.php" name="importaMensagens" id="importaMensagens" enctype="multipart/form-data">
<div class="panel panel-default">
	<div class="panel-heading"><b>Importar Arquivo de mensagens</b></div>
    <div class="panel-body">
	<div class="row">
		<div class="col-8">
			<div class="form-group">
				<label for="exampleFormControlFile1">Importar arquivo XMLs <a href="marketing/envios_massa/file.csv" target="_blank">(arquivo CSV)</a></label>
				<input type="hidden" name="id_msg" id="id_msg" value="" />
				<input type="file" accept=".csv" class="form-control-file" id="arquivo" name="arquivo">
			</div>
		</div>
		<div class="col-4">
		  
		  </div>
	</div>
</div>

      </div>
      <div class="modal-footer">
	    <input type="submit" value="Importar" id="btnImportar" class="btn btn-primary">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
  </form>
</div>
</div>


  
   
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
                $("#btnImportar").attr('value', 'Importando ...');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', true);
            },
            success: function( retorno ){				
                // Mensagem de Cadastro efetuado //
                if (retorno == 1){ 
					const mdlImporta = bootstrap.Modal.getInstance(document.getElementById('mdlImporta'));
					if (mdlImporta) mdlImporta.hide();
					mostraDialogo("Dados Importados", "success", 2500); 	
					$.ajax("marketing/envios_massa/listar.php").done(function(data) {
                    $('#Listar').html(data);
					
                });			
				}
                // Mensagem de Falha no Cadastro //
                else{ mostraDialogo("Erro ao importar arquivo", "danger", 2500); }
				
               
            },		 
		    complete:function() {
                $("#btnImportar").attr('value', 'Importar');
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

 
	    $('#gravaRespostaAutomatica').ajaxForm({
		    resetForm: true, 			  
            beforeSend:function() { 
                $("#btnGravarRespostaAutomatica").attr('value', 'Salvando ...');
				$('#FormRespostaAutomatica').find('input, button').prop('disabled', true);
            },
            success: function( retorno ){
				alert(retorno);
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
				
                $.ajax("marketing/envios_massa/listar.php").done(function(data) {
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
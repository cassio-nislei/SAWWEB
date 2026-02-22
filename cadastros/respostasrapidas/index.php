<?php
// Arquivo index para Respostas Rápidas
// Este arquivo é incluído de gestao/index.php que já carrega padrao.inc.php
?>

<div class="box-modal" id="FormRespostaRapida" style="display: none;">    
    <h2 class="title" id="titleCadastroRespostaRapida">Adicionar Resposta Rápida</h2>
    <div class="">
        <form method="post" id="gravaRespostaRapida" name="gravaRespostaRapida" action="/cadastros/respostasrapidas/salvar.php" enctype="multipart/form-data">
            <input type="hidden" value="0" name="acaoRespostaRapida" id="acaoRespostaRapida" />
			<input type="hidden" value="0" name="IdRespostaRapida" id="IdRespostaRapida" />
			
  
            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Título </div>
                <input type="text" id="titulo" name="titulo" class="uk-input" placeholder="Título da Resposta Rápida" />
                <div id="valida_titulo" style="display: none" class="msgValida">
                    Por favor, informe o título.
                </div>
            </div>

            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Resposta </div>
                <textarea id="resposta" name="resposta" class="uk-textarea" placeholder="Conteúdo da Resposta Rápida" rows="6"></textarea>
                <div id="valida_resposta" style="display: none" class="msgValida">
                    Por favor, informe a resposta.
                </div>
            </div>

            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Ação </div>
                <select id="acao" name="acao" class="uk-select">
                    <option value="0">Nenhuma</option>
                    <option value="1">Devolve Menu</option>
                    <option value="2">Devolve Menu Sem titulo</option>
                    <option value="9">Encerra Atendimento</option>
                </select>
            </div>

            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Anexar Arquivo </div>
                <input type="file" id="foto" name="foto" class="form-control" placeholder="Selecione um arquivo">
            </div>         

         
        </form>
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button id="btnFecharCadastroRespostaRapida" class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
        <input id="btnGravarRespostaRapida" class="uk-button uk-button-primary " type="submit" value="Salvar">
    </p>
</div>

<div class="box-modal" id="ListaRespostasRapidas">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="title" style="margin: 0;">Respostas Rápidas Cadastradas</h2>
        <button id="btnNovaRespostaRapida" class="btn btn-success" type="button" style="cursor: pointer;">
            <i class="fas fa-plus"></i> Inserir
        </button>
    </div>

    <div class="panel-body" id="ListarRespostaRapida">				
        <!-- Respostas Rápidas Cadastradas -->
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button class="uk-button uk-button-default uk-modal-close fechar" type="button">Cancelar</button>
    </p>
</div>

<script>
    $( document ).ready(function(){
        $.ajax("/cadastros/respostasrapidas/listar.php").done(function(data) {
            $('#ListarRespostaRapida').html(data);
        });

        // Adicionar Novo Registro //
		$('#btnNovaRespostaRapida').click(function(e){
			e.preventDefault();
			$("#gravaRespostaRapida")[0].reset();
			$("#FormRespostaRapida").css("display","block");
			$("#ListaRespostasRapidas").css("display","none");
			$("#acaoRespostaRapida").val("0");
			$("#IdRespostaRapida").val("0");
		});
	// Adicionar Novo Registro //

    $('#btnFecharCadastroRespostaRapida').click(function(e){
			e.preventDefault();
			$("#gravaRespostaRapida")[0].reset();
			$("#FormRespostaRapida").fadeOut();
			$("#ListaRespostasRapidas").fadeIn();
			$("#acaoRespostaRapida").val("0");
		});

        // Fechar a Janela //
		$('.fechar').on("click", function(){ fecharModal(); });


          // Cadastro/Alteração de Resposta Rápida //
	 $('#btnGravarRespostaRapida').click(function(e){
	   	e.preventDefault();
	  
		$("input:text, textarea").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#titulo").val()) == ''){
			$("#valida_titulo").css({"display" : "inline", "color" : "red"});
			$("#titulo").css({"border-color" : "red"});
			$("#titulo").focus();
			return false;
		}

		if ($.trim($("#resposta").val()) == ''){
			$("#valida_resposta").css({"display" : "inline", "color" : "red"});
			$("#resposta").css({"border-color" : "red"});
			$("#resposta").focus();
			return false;
		}

		// Gravando os dados da Resposta Rápida //
	    $('#gravaRespostaRapida').ajaxForm({
			resetForm: false,
        	beforeSend:function() {
				$("#btnGravarRespostaRapida").attr('value', 'Salvando ...');
				$('#btnGravarRespostaRapida').attr('disabled', true);
				$('#btnFecharCadastroRespostaRapida').attr('disabled', true);
				$('#FormRespostaRapida').find('input, textarea').prop('disabled', true);
        	},
			success: function( retorno ){
				if (retorno == 1) { mostraDialogo("Resposta Rápida inserida com sucesso", "success", 2500); }
				else if (retorno == 2){ mostraDialogo("Resposta Rápida atualizada", "success", 2500); }
				else{ mostraDialogo(retorno, "danger", 5500); }

				$.ajax("/cadastros/respostasrapidas/listar.php").done(function(data) {
					$('#ListarRespostaRapida').html(data);
				});
			},		 
			complete:function() {
				$("#btnGravarRespostaRapida").attr('value', 'Salvar');
				$('#btnGravarRespostaRapida').attr('disabled', false);
				$('#FormRespostaRapida').find('input, textarea, button').prop('disabled', false);
				$("#ListaRespostasRapidas").css("display","block");
				$("#FormRespostaRapida").css("display","none");
		 	},
		 	error: function (retorno) {
				mostraDialogo("Erro ao salvar a resposta rápida", "danger", 2500);
            }
		}).submit();
		// FIM Gravando //
	});
	// FIM Cadastro/Alteração //

        
	});
</script>

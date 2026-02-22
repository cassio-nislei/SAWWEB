


<div class="box-modal" id="FormEtiquetas" style="display: none;">    
    <h2 class="title" id="titleCadastroUser">Adicionar Etiquetas de Marcação</h2>
    <div class="">
        <form method="post" id="gravaEtiqueta" name="gravaEtiqueta" action="/cadastros/etiquetas/salvar.php">
            <input type="hidden" value="0" name="acaoEtiqueta" id="acaoEtiqueta" />
			<input type="hidden" value="0" name="id_Etiqueta" id="id_Etiqueta" />
			
  
            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Escolha a cor </div>
                 <input type="color" id="cor" name="cor" value="#ff0000">
                <div id="valida_nome" style="display: none" class="msgValida">
                    Por favor, informe o nome que será exibido nas conversas.
                </div>
            </div>

            <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Descrição </div>
                <input type="text" id="descricao" name="descricao" class="uk-input" placeholder="Descrição" />
                <div id="valida_descricao" style="display: none" class="msgValida">
                    Informe a Descrição
                </div>
            </div>         

         
        </form>
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button id="btnFecharCadastroEtiqueta" class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
        <input id="btnGravarEtiqueta" class="uk-button uk-button-primary " type="submit" value="Salvar">
    </p>
</div>

<div class="box-modal" id="ListaEtiquetas">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="title" style="margin: 0;">Etiquetas Cadastrados</h2>
        <button id="btnNovaEtiqueta" class="btn btn-success" type="button" style="cursor: pointer;">
            <i class="fas fa-plus"></i> Inserir
        </button>
    </div>

    <div class="panel-body" id="ListarEtiqueta">				
        <!-- Etiquetas Cadastrados -->
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button class="uk-button uk-button-default uk-modal-close fechar" type="button">Cancelar</button>
    </p>
</div>

<script>
    $( document ).ready(function(){
        $.ajax("/cadastros/etiquetas/listar.php").done(function(data) {
            $('#ListarEtiqueta').html(data);
        });

        // Adicionar Novo Registro //
		$('#btnNovaEtiqueta').click(function(e){
			e.preventDefault();
			$("#gravaEtiqueta")[0].reset();
			$("#FormEtiquetas").css("display","block");
			$("#ListarEtiqueta").css("display","none");
			$("#acaoEtiqueta").val("0");
		});
	// Adicionar Novo Registro //

    $('#btnFecharCadastroEtiqueta').click(function(e){
			e.preventDefault();
			$("#gravaEtiqueta")[0].reset();
			$("#FormEtiquetas").fadeIn();
			$("#ListarEtiqueta").fadeIn();
            $("#FormEtiquetas").fadeOut();
            
			$("#acaoEtiqueta").val("0");
		});

        // Fechar a Janela //
		$('.fechar').on("click", function(){ fecharModal(); });


          // Cadastro/Alteração de Usuário //
	 $('#btnGravarEtiqueta').click(function(e){
	   	e.preventDefault();
	  
		$("input:text").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#descricao").val()) == ''){
			$("#valida_descricao").css({"display" : "inline", "color" : "red"});
			$("#descricao").css({"border-color" : "red"});
			$("#descricao").focus();
			return false;
		}

		// Gravando os dados do Usuário //
	    $('#gravaEtiqueta').ajaxForm({
			resetForm: false,
        	beforeSend:function() {
				$("#btnGravarEtiqueta").attr('value', 'Salvando ...');
				$('#btnGravarEtiqueta').attr('disabled', true);
				$('#btnFecharCadastroEtiqueta').attr('disabled', true);
				$('#FormEtiquetas').find('input').prop('disabled', true);
        	},
			success: function( retorno ){
				if (retorno == 1) { mostraDialogo("Etiqueta inserida com sucesso", "success", 2500); }
				else if (retorno == 2){ mostraDialogo("Etiqueta atualizada", "success", 2500); }
				else if (retorno == 3){ mostraDialogo("Já existe uma etiqueta com essa cor", "danger", 2500); }
				else{ mostraDialogo(retorno, "danger", 5500); }

				$.ajax("/cadastros/etiquetas/listar.php").done(function(data) {
					$('#ListarEtiqueta').html(data);
				});
			},		 
			complete:function() {
				$("#btnGravarEtiqueta").attr('value', 'Salvar');
				$('#btnGravarEtiqueta').attr('disabled', false);
				$('#FormEtiquetas').find('input, button').prop('disabled', false);
				$("#ListarEtiqueta").css("display","block");
				$("#FormEtiquetas").css("display","none");
		 	},
		 	error: function (retorno) {
				mostraDialogo(mensagem5, "danger", 2500);
            }
		}).submit();
		// FIM Gravando os dados do Usuário //
	});
	// FIM Cadastro/Alteração de Usuário //


        
	});
</script>
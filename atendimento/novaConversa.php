<div class="box-modal">
    <h2 class="title" id="titleCadastroUser">Iniciar nova Conversa</h2>
    <div class="">
        <form method="post" id="iniciarConversa" name="iniciarConversa" action="atendimento/gerarAtendimento.php">
        <div class="uk-width-1-1@m">
                <div class="uk-form-label"> Celular com Whatsapp <b>(DDI Brasil 55)</b></div>
                <input type="text" id="numero_inicio" name="numero_inicio" class="uk-input" placeholder="Informe o Celular com DDD e DDI (com Whatsapp)" maxlength="19" autocomplete="off">
                <div id="valida_numero_inicio" style="display: none" class="msgValida">
                    Por favor, informe o Número do Telefone com DDD e DDI.
                </div>
            </div>
        </form>
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button class="uk-button uk-button-default uk-modal-close fechar" type="button">Cancelar</button>
        <button id="btnIniciarConversa" class="uk-button uk-button-primary " type="button">Iniciar Conversa</button>
    </p>
</div>
<script>
    $('#btnIniciarConversa').attr('disabled', false);
				// Exibe mensagem de carregamento
	$("#btnIniciarConversa").html("Iniciar Conversa");
    $("#numero_inicio").focus();
    
    // Formatando o 'Número do Telefone' //
    var behavior = function (val) {
        return val.replace(/\D/g, '').length === 13 ? '+00 (00) 00000-0000' : '+00 (00) 0000-00009';
    },
    options = {
        onKeyPress: function (val, e, field, options) {
            field.mask(behavior.apply({}, arguments), options);
        }
    };
    $('#numero_inicio').mask(behavior, options);
// Criar um Novo Atendimento //
		$('#btnIniciarConversa').click(function(e){
            e.preventDefault();
			var numero = $("#numero_inicio").val();
			var nome = '';
        
            $('#btnIniciarConversa').attr('disabled', true);
				// Exibe mensagem de carregamento
			$("#btnIniciarConversa").html("<img src='images/loader.gif' alt='Gravando...' />").fadeIn("slow");

            if( numero.replace(/\D/g, '').length < 12 
                || numero.replace(/\D/g, '').length > 13 ){
                $("#valida_numero_inicio").html("Número do Telefone fora do padrão. Por favor informe o número corretamente!");
                $("#valida_numero_inicio").css({"display" : "inline", "color" : "red"});
                $("#numero_inicio").css({"border-color" : "red"});
                $("#numero_inicio").focus();
                 
                $('#btnIniciarConversa').attr('disabled', false);
				// Exibe mensagem de carregamento
			   $("#btnIniciarConversa").html("Iniciar Conversa").fadeIn("slow");

                return false;
            }

            function extrairNumeros(str) {
                return str.replace(/\D/g, '');
            }

            numero = extrairNumeros(numero);

			// Salva os dados nos seus devidos 'input:hidden' //
			$("#s_numero").val(numero);
        	$("#s_nome").val(nome);
     

			$.post("atendimento/gerarAtendimento.php",{numero:numero,nome:nome}, function(idAtendimento){
						// Atualiza a notificação      
                       // alert(idAtendimento)    ;              
						if( parseInt(idAtendimento) > 0 ){
							$('#not'+idAtendimento).text(""); //limpa a qtd quando de notificações abre a conversa
							$('#AtendimentoAberto').html("<div class='spinner-border text-primary' role='status'><span class='sr-only'>Carregando ...</span></div>");
							
							$.ajax("atendimento/conversa.php?id="+idAtendimento+"&id_canal=1&numero&numero="+encodeURIComponent(numero)+"&nome="+encodeURIComponent(nome)).done(function(data) {
								$('#AtendimentoAberto').html(data);

								$.ajax("atendimento/atendendo.php").done(function(data) {
									$('#ListaEmAtendimento').html(data);
                                    $('#modalNovaConversa').hide();
                                    $("#fundo_preto").hide();
                                    $(".window").hide();
                                    $("#msg").focus();
								});
							});
						}
						else if( idAtendimento == "erro" ){ 
                            mostraDialogo("Erro ao tentar Iniciar o Atendimento!", "danger", 2500);
                        }else{ 
                            mostraDialogo(idAtendimento, "danger", 10000); 
                        }
					});
					
			
		});
		// FIM Criar um Novo Atendimento //

        // Fechar a Janela //
  $('.fechar').on("click", function() {
    fecharModal();
  });
        </script>
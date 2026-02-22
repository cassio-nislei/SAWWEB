// JavaScript Document
$( document ).ready(function() {
    // Cadastro/Alteração de Usuário //
	$('#btnRedefinirSenha').click(function(e){
	   	e.preventDefault();
	   
		var msgSuccess  = "<strong>Senha atualizada com Sucesso!</strong>";
		var msgError = 'Falha ao atualizar a sua Nova Senha!';

		if ($.trim($("#senha").val()) == ''){	
			$("#valida_senha").css({"display" : "inline", "color" : "red"});
			$("#senha").css({"border-color" : "red"});
			$("#senha").focus();
			return false;
		}

		// Salvando a Nova Senha //
	    $('#redefinirSenha').ajaxForm({
			resetForm: false,
        	beforeSend:function() {
				$("#btnRedefinirSenha").attr('value', 'Gravando ...');
				$('#btnRedefinirSenha').attr('disabled', true);
				$('#btnFecharRedefinirSenha').attr('disabled', true);
				$('#redefinirSenha').find('input').prop('disabled', true);
        	},
			success: function( retorno ){
				if (retorno == 1) { mostraDialogo(msgSuccess, "success", 2500); }
				else{ mostraDialogo(msgError, "danger", 2500); }

                // Fecha a Modal //
				fecharModal()
				
				//$('#modalRedefinirSenha').attr('style', 'display: none');
			},		 
			complete:function() {
				$("#btnRedefinirSenha").attr('value', 'Gravar Nova Senha');
				$('#btnRedefinirSenha').attr('disabled', false);
				$('#redefinirSenha').find('input, button').prop('disabled', false);
				$("#redefinirSenha").css("display","none");
		 	},
		 	error: function (retorno) {
				mostraDialogo(mensagem5, "danger", 2500);
            }
		}).submit();
		// FIM Gravando os dados do Usuário //
	});
	// FIM Cadastro/Alteração de Usuário //

	$('.fechar').click(function(){
		fecharModal()
	});
});
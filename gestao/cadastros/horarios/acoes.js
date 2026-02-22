// JavaScript Document
$( document ).ready(function() {
			
$("#hora_inicio_expediente_domingo").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_domingo").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_segunda").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_segunda").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_terca").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_terca").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_quarta").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_quarta").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_quinta").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_quinta").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_sexta").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_sexta").mask("99:99:99", { placeholder: "0", autoclear: false });
	$("#hora_inicio_expediente_sabado").mask("99:99:99", { placeholder: "0", autoclear: false });
$("#hora_fim_expediente_sabado").mask("99:99:99", { placeholder: "0", autoclear: false });
	
	
  function mostraDialogo(mensagem, tipo, tempo){
    
    // se houver outro alert desse sendo exibido, cancela essa requisição
    if($("#message").is(":visible")){
        return false;
    }

    // se não setar o tempo, o padrão é 3 segundos
    if(!tempo){
        var tempo = 3000;
    }

    // se não setar o tipo, o padrão é alert-info
    if(!tipo){
        var tipo = "info";
    }

    // monta o css da mensagem para que fique flutuando na frente de todos elementos da página
    var cssMessage = "display: block; position: fixed; top: 0; left: 20%; right: 20%; width: 60%; padding-top: 10px; z-index: 9999";
    var cssInner = "margin: 0 auto; box-shadow: 1px 1px 5px black;";

    // monta o html da mensagem com Bootstrap
    var dialogo = "";
    dialogo += '<div id="message" style="'+cssMessage+'">';
    dialogo += '    <div class="alert alert-'+tipo+' alert-dismissable" style="'+cssInner+'">';
    dialogo += '    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>';
    dialogo +=          mensagem;
    dialogo += '    </div>';
    dialogo += '</div>';

    // adiciona ao body a mensagem com o efeito de fade
    $("body").append(dialogo);
    $("#message").hide();
    $("#message").fadeIn(200);

    // contador de tempo para a mensagem sumir
    setTimeout(function() {
        $('#message').fadeOut(300, function(){
            $(this).remove();
        });
    }, tempo); // milliseconds

}	
	 

     //Novo Registro
	 $('#btnGravarFormaPagto').click(function(e){
	   e.preventDefault();
	 
	  var mensagem  = "<strong>Horarios Cadastrados com sucesso!</strong>";
	  var mensagem2 = 'Falha ao Efetuar Cadastro!';
	  var mensagem3 = 'Horarios Já Cadastrado!';
	  var mensagem4 = 'Horarios Atualizada com Sucesso!';
	  var mensagem5 = 'Horarios, Tente novamente mais tarde!';
	  var conteudo = $("#FormFormasPagamento").html();
	  
	  $("input:text").css({"border-color" : "#999"});
	  $(".msgValida").css({"display" : "none"});
	    
		 //Validações de Campos Obrigatórios
		 if ($.trim($("#hora_inicio_expediente_domingo").val()) == ''){	
			$("#valida_inicio_domingo").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_domingo").css({"border-color" : "red"});
			$("#hora_inicio_expediente_domingo").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_domingo").val()) == ''){	
			$("#valida_fim_domingo").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_domingo").css({"border-color" : "red"});
			$("#hora_fim_expediente_domingo").focus();
			return false;  
		  }	
		  if ($.trim($("#hora_inicio_expediente_segunda").val()) == ''){	
			$("#valida_inicio_segunda").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_segunda").css({"border-color" : "red"});
			$("#hora_inicio_expediente_segunda").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_segunda").val()) == ''){	
			$("#valida_fim_segunda").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_segunda").css({"border-color" : "red"});
			$("#hora_fim_expediente_segunda").focus();
			return false;  
		  }	
	      if ($.trim($("#hora_inicio_expediente_terca").val()) == ''){	
			$("#valida_inicio_terca").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_terca").css({"border-color" : "red"});
			$("#hora_inicio_expediente_terca").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_terca").val()) == ''){	
			$("#valida_fim_terca").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_terca").css({"border-color" : "red"});
			$("#hora_fim_expediente_terca").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_inicio_expediente_quarta").val()) == ''){	
			$("#valida_inicio_quarta").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_quarta").css({"border-color" : "red"});
			$("#hora_inicio_expediente_quarta").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_quarta").val()) == ''){	
			$("#valida_fim_quarta").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_quarta").css({"border-color" : "red"});
			$("#hora_fim_expediente_quarta").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_inicio_expediente_quinta").val()) == ''){	
			$("#valida_inicio_quinta").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_quinta").css({"border-color" : "red"});
			$("#hora_inicio_expediente_quinta").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_quinta").val()) == ''){	
			$("#valida_fim_quinta").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_quinta").css({"border-color" : "red"});
			$("#hora_fim_expediente_quinta").focus();
			return false;  
		  }
		   if ($.trim($("#hora_inicio_expediente_sexta").val()) == ''){	
			$("#valida_inicio_sexta").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_sexta").css({"border-color" : "red"});
			$("#hora_inicio_expediente_sexta").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_sexta").val()) == ''){	
			$("#valida_fim_sexta").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_sexta").css({"border-color" : "red"});
			$("#hora_fim_expediente_sexta").focus();
			return false;  
		  }
		   if ($.trim($("#hora_inicio_expediente_sabado").val()) == ''){	
			$("#valida_inicio_sabado").css({"display" : "inline", "color" : "red"});
			$("#hora_inicio_expediente_sabado").css({"border-color" : "red"});
			$("#hora_inicio_expediente_sabado").focus();
			return false;  
		  }	
		 if ($.trim($("#hora_fim_expediente_sabado").val()) == ''){	
			$("#valida_fim_sabado").css({"display" : "inline", "color" : "red"});
			$("#hora_fim_expediente_sabado").css({"border-color" : "red"});
			$("#hora_fim_expediente_sabado").focus();
			return false;  
		  }
		 		 
	  
	      $('#grava').ajaxForm({
		resetForm: false, 			  
        beforeSend:function() { 
           $('#btnGravarFormaPagto').html('<i class="fa fa-spinner fa-spin"></i> Salvando...');
           $('#btnGravarFormaPagto').attr('disabled', true);	
		   $('#FormFormasPagamento').find('input').prop('disabled', true);
        },
		success: function( retorno )
				{
				//	alert(retorno);
					if (retorno == 1) {
					  mostraDialogo(mensagem, "success", 2500);	

					}else{
					  mostraDialogo(mensagem2, "danger", 2500);	
					}
				},		 
		complete:function() {
			$('#btnGravarFormaPagto').html('<i class="fa fa-save"></i> Salvar Alterações');
			$('#btnGravarFormaPagto').attr('disabled', false);
			$('#FormFormasPagamento').find('input, button').prop('disabled', false); 
			
		 },
		 error: function (retorno) {
			   mostraDialogo(mensagem5, "danger", 2500);	
                //  alert(retorno);
                }
			  

	}).submit(); 		 
	
	

	 });	
	
	
	
	
	
  
});
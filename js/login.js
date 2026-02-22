// JavaScript Document
$( document ).ready(function() {
	
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
	
	 
	
    //Faz o Login
	$( "#btnLogin" ).on( "click", function() {
		$(this).html("<img src='images/aguarde.gif' />");
		$('#FormLogin').find('input, button').prop('disabled', true);
		
		  var usuario = $("#usuario").val();	
		  var senha   = $("#senha").val();
		  

          $.post("login.php", {usuario:usuario,senha:senha}, function(data){
		   var mensagem = data;
           // alert(data);		 
			if(data==2){ //Usuario ainda não está ativo
			  mostraDialogo(mensagemReenvio, "success", 4000);
			}else if(data == 1){ //Logou com Sucesso como Operador
              window.location.replace("conversas.php");           
		   }else if(data == 0){ //Logou com Sucesso como Administrador
			window.location.replace("gestao/index.php");
		 }  else { mostraDialogo(mensagem, "danger", 2500); }
		   
		  $("#btnLogin").html("Entrar");
		  $('#FormLogin').find('input, button').prop('disabled', false);
        });		
  });
	
	
	 //Solicita Recuperação de Senha
	$( "#btnSolicitaRecuperaSenha" ).on( "click", function() {
		//$(this).prop("disabled",true);
		$(this).html("<img src='imgs/aguarde.gif' />");
		$('#FormSolicitaSenha').find('input, button').prop('disabled', true);
		try{
		  var login_usuario = $("#recupera_email").val();	
          var app = 2;

          $.post("api/recuperasenha.php", {usuario:login_usuario,idApp:app}, function(data){
		   var mensagemReenvio = "Foi Enviado um email para: "+login_usuario+ ' Com as informações para alterar sua senha!';
		   var mensagemsenha   = "Email Não Cadastrado no ipediu, Confira os dados e Tente novamente";
			if(data==1){ //Email Enviado
			  mostraDialogo(mensagemReenvio, "success", 4000);	
				window.location="https://ipediu.com";
			}else{ //Email Não encontrado
       	      mostraDialogo(mensagemsenha, "danger", 4500);			  
		   }
			 $("#btnSolicitaRecuperaSenha").html("Solicitar Recuperação de Senha");
			 $('#FormSolicitaSenha').find('input, button').prop('disabled', false);
        });
		}finally{
			setTimeout(function () {
         //   $("#btnSolicitaRecuperaSenha").html("Solicitar Recuperação de Senha");
		//	$('#FormSolicitaSenha').find('input, button').prop('disabled', false);
        }, 1000);
			
		}
		
  });
	
	 //Faz uma nova Senha
	$( "#btnRecriarSenha" ).on( "click", function() {
		$(this).html("<img src='imgs/aguarde.gif' />");
		$('#FormAtualizaSenha').find('input, button').prop('disabled', true);
		try{
		  var senha  = md5($("#altera_senha").val());	
		  var senha2 = md5($("#altera_senha2").val());
		  var codigo = $("#codigo").val();
		  var id = $("#id").val();
          var app = 2;
		  var senhainvalida   = "A Senha e a Confirmação não coincidem! verifique";	
		  if (senha != senha2){
			mostraDialogo(senhainvalida, "danger", 4500); 
			  $("#btnRecriarSenha").html("Salvar Nova Senha");
			 $('#FormAtualizaSenha').find('input, button').prop('disabled', false);
			  return false;
		  }

          $.post("api/salvarAlteracaoSenha.php", {senha:senha, codigo:codigo, id:id}, function(data){
		   var mensagemSucesso = "Senha Alterada com Sucesso!";
		   var mensagemExpirada   = "O Código de Alteração de Senha expirou, faça a solicitação novamente!";
		   var mensagemUtilizada  = "Este Código de Recuperação já foi utilizado!";
			if(data==1){ //Senha Alterada
			  mostraDialogo(mensagemSucesso, "success", 4000);
			  window.location="https://ipediu.com";
			}else if (data == 2){ //Prazo Expirado
       	      mostraDialogo(mensagemExpirada, "danger", 4500);			  
		   }else if (data == 3){ //Código de Recuperação já Utilizado
			  mostraDialogo(mensagemUtilizada, "danger", 4500);	 
		   }
			 $("#btnRecriarSenha").html("Salvar Nova Senha");
			 $('#FormAtualizaSenha').find('input, button').prop('disabled', false);
        });
		}finally{
			setTimeout(function () {
         //   $("#btnSolicitaRecuperaSenha").html("Solicitar Recuperação de Senha");
		//	$('#FormSolicitaSenha').find('input, button').prop('disabled', false);
        }, 1000);
			
		}
		
  });

  	//Se pressionar ENTER dispara o botão para fazer Login  
	  $(function(){
		$('.login-form').keypress(function(e){
		  if(e.which == 13) {
			//dosomething
			$( "#btnLogin" ).click();
		  }
		})
	  })
  
  
  $('#FormLogin').on('shown.bs.modal', function () {
    $('#login_usuario').focus();
})
  
  
  
});
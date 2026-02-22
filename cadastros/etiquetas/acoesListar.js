// JavaScript Document
$( document ).ready(function() {		
	// Exclusão de Usuário //
	$('.ConfirmaExclusaoUsuario').on('click', function (){
	    var id = $(this).parent().parent().parent("li").find('#IdUsuario').val();
		abrirModal("#modalUsuarioExclusao");
		$("#IdUsuario2").val(id);
	});

	// Remoção do Cadastro //
	$('#btnConfirmaRemoveUsuario').on('click', function (){
		$("#btnConfirmaRemoveUsuario").attr('value', 'Removendo ...');
        $('#btnConfirmaRemoveUsuario').attr('disabled', true);

		var idUsuario = $("#IdUsuario2").val();

		$.post("cadastros/usuarios/excluir.php",{IdUsuario:idUsuario},function(resultado){             
			var mensagem  = "<strong>Usuário Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Usuário!';

			if (resultado = 2) {
				mostraDialogo(mensagem, "success", 2500);	

				$.ajax("cadastros/usuarios/listar.php").done(function(data) {
					$('#ListarUsuario').html(data);
				});
			}
			else{ mostraDialogo(mensagem2, "danger", 2500); }

			// Fechando a Modal de Confirmação //
			$('#modalUsuarioExclusao').attr('style', 'display: none');
		});
	});
	// FIM Remoção do Cadastro //
	
	// Alteração de Usuário //
	$('.botaoAlterarUsuario').on('click', function (){
		// Busco os dados do Produto Selecionado  
		var codigo = $(this).parent().parent().parent("li").find('input:hidden').val();

		// Alterando Displays //
		$("#FormUsuarios").css("display","block");
		$("#ListaUsuarios").css("display","none");

		// Alterando o Título do Cadastro //
		$("#titleCadastroUser").html("Alteração de Usuário");

		$.getJSON('cadastros/usuarios/carregardados.php?codigo='+codigo, function(registro){			
			// Carregando os Dados //
			$("#id_usuarios").val(registro.id);
			$("#nome_usuario").val(registro.nome);
			$("#login").val(registro.login);
			$("#senha").val(registro.senha);
			$("#perfil").val(registro.perfil);
		});
			  
		// Mudo a Ação para Alterar    
		$("#acaoUsuario").val("2");
		$("#nome_usuario").focus();
	});
	// FIM Alteração de Usuário //

	// Fechar Cadastro do Usuário //
	$('#btnFecharCadastroUsuario').on('click', function (){
		$("#ListaUsuarios").css("display","block");
		$("#FormUsuarios").css("display","none");
	});
	$('#btnCancelaRemoveUsuario').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalUsuarioExclusao').attr('style', 'display: none');
		
		$("#ListaUsuarios").css("display","block");
		$("#FormUsuarios").css("display","none");
	});
	// FIM Fechar Cadastro do Usuário //
	
	// Remover Vínculo do Usuário //
	$('.btnVinculaUsuario').on('click', function (){
		if( confirm('Deseja remover o departamento selecionado a este usuário?') ){					
			var idUsuario = $(this).closest("li").find('#IdUsuario3').val()
				, idDepartamento = $(this).closest("li").find('#IdDepartamento').val();

			$.post("cadastros/usuarios/excluirVinculo.php"
				, {IdUsuario:idUsuario,idDepartamento:idDepartamento}
				, function(resultado){
				var mensagem  = "<strong>Vinculo com departamento Removido com sucesso!</strong>";
				var mensagem2 = 'Falha ao Remover Vinculo do Departamento!';
				
				if( resultado == 2 ){
					mostraDialogo(mensagem, "success", 2500);	

					$.ajax("cadastros/usuarios/listar.php").done(function(data) {
						$('#ListarUsuario').html(data);
					});
				}
				else{ mostraDialogo(mensagem, "danger", 2500); }
			});
		}
  	});
	// FIM Remover Vínculo do Usuário //
	
	// Incluir Vínculo do Usuário //
	$('.btnIncluirDepartamento').on('click', function (){
	 	var idUsuario      = $(this).closest("li").find('#IdUsuario').val()
	 		, idDepartamento = $(this).closest("li").find('#sltDepartamentos').val();

		// Validando a escolha do Departamento //
		if( idDepartamento === "0" ){
			mostraDialogo("Por favor, escolha corretamente um Departamento!", "danger", 2500);
		}
		else{
			$.post("cadastros/usuarios/salvarVinculo.php"
				, {IdUsuario:idUsuario,idDepartamento:idDepartamento}
				, function(resultado){
					var mensagem  = "<strong>Vinculo com departamento Cadastrado com sucesso!</strong>";
					var mensagem2 = 'Falha ao Remover Vinculo do Departamento!';
					var mensagem3 = 'Usuário já vinculado a este Departamento!';

				if( resultado == 1 ){
					mostraDialogo(mensagem, "success", 2500);

					$.ajax("cadastros/usuarios/listar.php").done(function(data) {
                        $('#ListarUsuario').html(data);
                    });
				}
				else if( resultado == 2 ){ mostraDialogo(mensagem3, "danger", 2500); }
				else{ mostraDialogo(mensagem, "danger", 2500); }
			});
		}
  	});
  	// Incluir Vínculo do Usuário //
});
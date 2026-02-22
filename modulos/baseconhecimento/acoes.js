// JavaScript Document
$( document ).ready(function() {
	// Initialize Quill editor
	var quillProblema = new Quill('#editorProblema', {
		theme: 'snow'
	});
	var quillSolucao = new Quill('#editorSolucao', {
		theme: 'snow'
	});

	// Adicionar Novo Registro //
		$('#btnNovoBC').click(function(e){
			e.preventDefault();
			$("#gravaBC")[0].reset();
			$("#FormBC").css("display","block");
			$("#ListaBC").css("display","none");
			$("#acaoBC").val("0");
			$("#IdBC").val("");
		});
	// Adicionar Novo Registro //

    // Cadastro/Alteração  //
		$('#btnGravarBC').click(function(e){
			// Montando o FormData //
				idBC = $("#idBC").val();
				acaoBC = $("#acaoBC").val();
				var problema = quillProblema.root.innerHTML;
				var solucao = quillSolucao.root.innerHTML;
				tags = $("#categorias").val();
			// FIM Montando o FormData //

			// Validação de Dados //
				// $("input:text").css({"border-color" : "#999"});
				// $(".msgValida").css({"display" : "none"});

				if (problema == '' || problema == '<p><br></p>'){
					$("#valida_problema").css({"display" : "inline", "color" : "red"});
					return false;
				}

				if (solucao == '' || solucao == '<p><br></p>'){
					$("#valida_solucao").css({"display" : "inline", "color" : "red"});
					return false; 	
				}
			// FIM Validação de Dados //

			// Tratamento dos Botões //
        	$('#btnGravarBC').attr('disabled', true);
			$('#btnFecharCadastroBC').attr('disabled', true);

			// Exibe mensagem de carregamento
			$("#aguarde").html("<img src='../../images/loader.gif' alt='Gravando...' />").fadeIn("slow");

			// Submitando o Cadastro //
			$.post('modulos/baseconhecimento/salvar.php'
				, { idBC: idBC, acaoBC: acaoBC, problema: problema, solucao: solucao, tags: tags}
				, function(retorno) {
				$("#aguarde").fadeOut(100);
				var mensagem1  = "<strong>Base de Conhecimento Cadastrada com sucesso!</strong>";
				var mensagem2  = "<strong>Base de Conhecimento Atualizada com sucesso!</strong>";
				var mensagem9 = 'Falha ao Cadastrar/Atualizar o Registro!';

				if (retorno == 1) { mostraDialogo(mensagem1, "success", 2500); }
				else if (retorno == 2){ mostraDialogo(mensagem2, "success", 2500); }
				else{ mostraDialogo(mensagem9, "danger", 2500); }

				$.ajax("modulos/baseconhecimento/index.php").done(function(data) {
					// $('#ListarBC').html(data);
					$('#modalBaseConhecimento').html(data);
				});

				// Tratamento dos Botões //
				$('#btnGravarBC').attr('disabled', false);
				$('#btnFecharCadastroBC').attr('disabled', false);
				$("#FormBC").css("display","none");
				$("#ListaBC").css("display","block");
			});
			// FIM Submitando o Cadastro //
		});
	// FIM Cadastro/Alteração  //

	// Ações do Arquivo Listar //
	// Exclusão //
	$('.ConfirmaExclusaoBC').on('click', function (){
	    var id = $(this).parent().parent().parent("li").find('#IdBC').val();
		abrirModal("#modalBaseConhecimentoExclusao");
		$("#IdBC2").val(id);
	});

	// Remoção do Cadastro //
	$('#btnConfirmaRemoveBC').on('click', function (){
		$("#btnConfirmaRemoveBC").attr('value', 'Removendo ...');
        $('#btnConfirmaRemoveBC').attr('disabled', true);

		var idBC = $("#IdBC2").val();

		$.post("modulos/baseconhecimento/excluir.php",{IdBC:idBC},function(resultado){             
			var mensagem1  = "<strong>Base de Conhecimento Removido com sucesso!</strong>";
			var mensagem9 = 'Falha ao Remover Base de Conhecimento!';

			if( resultado = 1 ){
				mostraDialogo(mensagem1, "success", 2500);	

				$.ajax("modulos/baseconhecimento/index.php").done(function(data) {
					// $('#ListarBC').html(data);
					$('#modalBaseConhecimento').html(data);
				});
			}
			else{ mostraDialogo(mensagem9, "danger", 2500); }

			// Fechando a Modal de Confirmação //
			$('#modalBaseConhecimentoExclusao').attr('style', 'display: none');
			$("#btnConfirmaRemoveBC").attr('value', 'Confirmar Exclusão!');
        	$('#btnConfirmaRemoveBC').attr('disabled', false);
		});
	});
	// FIM Remoção do Cadastro //
	
	// Alteração //
		$('.botaoAlterarBC').on('click', function (){
			// Busco os dados do Produto Selecionado  
			var codigo = $(this).parent().parent().parent("li").find('input:hidden').val();

			// Alterando Displays //
			$("#FormBC").css("display","block");
			$("#ListaBC").css("display","none");

			// Alterando o Título do Cadastro //
			$("#titleCadastroBC").html("Alteração de Base de Conhecimento");

			$.getJSON('modulos/baseconhecimento/carregardados.php?codigo='+codigo, function(registro){			
				// Carregando os Dados //
				$("#idBC").val(registro.id);
				quillProblema.root.innerHTML = registro.problema;
				quillSolucao.root.innerHTML = registro.solucao;
				$("#categorias").val(registro.tags);
				$("#qtdeFiles").val(registro.qtdeFiles);
				var files = JSON.parse(registro.files);

				// Tratamento - Quantidade Máxima de Arquivos //
				if( $("#qtdeFiles").val() >= 5 ){
					mostraDialogo("Você pode anexar apenas 5 arquivo por Solução", "danger", 2500);
					// Desabilitando o Campo Upload //
					$('input[name="uploadfile"]').prop('disabled', true);
				}
				else{ $('input[name="uploadfile"]').prop('disabled', false); }

				(files).forEach(element => {
					$('<li></li>').appendTo('#files').html(element.name +'<a href="#" class="'+element.name+'" id="ApagaAnexo" name="ApagaAnexo">X</a><br />').addClass('success');
				});
			});
				
			// Mudo a Ação para Alterar    
			$("#acaoBC").val("2");
			$("#problema").focus();
		});
	// FIM Alteração //

	// Fechar Cadastro //
	$('#btnFecharCadastroBC').on('click', function (){
		$("#ListaBC").css("display","block");
		$("#FormBC").css("display","none");
	});
	$('#btnCancelaRemoveBC').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalBaseConhecimentoExclusao').attr('style', 'display: none');
		
		$("#ListaBC").css("display","block");
		$("#FormBC").css("display","none");
	});
	// FIM Fechar Cadastro //

	// Apaga Item Anexado da Lista
    $(document).on('click', "#ApagaAnexo", function (evt){
		var file = $("#ApagaAnexo").attr('class');
		$("li").remove(":contains('"+file+"')");
		
		$.post('modulos/baseconhecimento/apaga-file.php', {file:file}, function(resposta) {
			// Habilitando o Campo Upload //
			$('input[name="uploadfile"]').prop('disabled', false);

			// Incrementa //
			$("#qtdeFiles").val( parseInt($("#qtdeFiles").val()) - 1 );
		});
	});

	// Enviar Multiplos arquivos por upload antes de submeter o formulario
	var btnUpload=$('#uploadd');
	var status=$('#statuss');

	new AjaxUpload(btnUpload, {
		// Arquivo que fará o upload
		action: 'modulos/baseconhecimento/upload-file.php',
		//Nome da caixa de entrada do arquivo
		name: 'uploadfile',
		onSubmit: function(file, ext){
				if (! (ext && /^(pdf|txt|doc|docx|xls|xlsx|zip|rar|xml|dll|jpg|png|jpeg|gif)$/.test(ext))){ 
				// verificar a extensão de arquivo válido
				status.text('Somente PDF, DOC, XLS, TXT, DLL, ZIP, RAR, XML, JPG, PNG ou GIF são permitidas');
				return false;
			}
		},
		onComplete: function(file, response){
			//Limpamos o status
			status.text('');

			//Adicionar arquivo carregado na lista
			if (response==="limite"){
				mostraDialogo("Você pode anexar apenas 5 arquivo por Solução", "danger", 2500);

				// Desabilitando o Campo Upload //
				$('input[name="uploadfile"]').prop('disabled', true);

				return false;	
			}
			else if( response === "success" ){
				// Tratamento - Quantidade Máxima de Arquivos //
				if( $("#qtdeFiles").val() >= 5 ){
					mostraDialogo("Você pode anexar apenas 5 arquivo por Solução", "danger", 2500);
					// Desabilitando o Campo Upload //
					$('input[name="uploadfile"]').prop('disabled', true);
				}
				else{ 	
					// Habilitando o Campo Upload //
					$('input[name="uploadfile"]').prop('disabled', false);

					// Imprime a Saída do Arquivo //
					$('<li></li>').appendTo('#files').html(file +'<a href="#" class="'+ file +'" id="ApagaAnexo" name="ApagaAnexo">X</a><br />').addClass('success');
				}

				// Incrementa //
				$("#qtdeFiles").val( parseInt($("#qtdeFiles").val()) + 1 );
			}
			else{ $('<li></li>').appendTo('#files').text('ERRO - Não foi possivel anexar'+file).addClass('error').fadeOut(2000); }
		}
	});

	// Ações de Pesquisa //
		// Botão da Lupa - Pesquisar por Termo //
		$("#btnPesquisar").on("click", function() {
			var termo = $("#termo").val();

			if( $.trim(termo) != "" && termo.length > 2 ){ pesquisaBC(termo); }
			else{
				alert("Informe ao menos três letras para efetuar uma pesquisa!");
				$("#termo").focus();
			}
		});

		// Botão Ver Todos //
		$("#btnVerTodos").on("click", function() {
			pesquisaBC();
		});

		// Função de Pesquisa //
		function pesquisaBC(termo){
			$.post('modulos/baseconhecimento/index.php'
				, { termo: termo}
				, function(retorno) {
				$('#modalBaseConhecimento').html(retorno);
			});
		}
	// FIM Ações de Pesquisa //

	// Fechar a Janela //
	$('.fechar').on("click", function(){ fecharModal(); });
});
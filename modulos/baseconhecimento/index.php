<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php"); ?>
<link rel="stylesheet" href="/css/quill.snow.css">
<link rel="stylesheet" href="/css/tagsinput.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
	.table address, dl, fieldset, figure, ol, p, pre, ul{
		margin:0;
		vertical-align: middle; /* Centraliza verticalmente o conteúdo */
		padding-bottom:0 !important;
		line-height: 1.3rem !important;
	}
 .table td, 
.table th {
    padding: 4px; /* Reduz o espaçamento interno das células */
    line-height: 1; /* Reduz a altura da linha */
    vertical-align: middle; /* Centraliza verticalmente o conteúdo */
}
</style>

<div class="container-fluid" style="padding: 20px 0;">

<div class="card" id="FormBC" style="display: none; margin-bottom: 20px;">
    <div class="card-header">
        <h4 class="mb-0" id="titleCadastroBC">Adicionar Nova Solução</h4>
    </div>
    <div class="card-body">
        <form method="post" id="gravaBC" name="gravaBC">
            <input type="hidden" id="idBC" name="idBC" value="0" />
            <input type="hidden" value="0" name="acaoBC" id="acaoBC" />

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Descrição do problema</b></label>
                        <div class="editor" id="editorProblema"></div>
                        <small id="valida_problema" style="display: none; color: red;">
                            Por favor, informe o [problema].
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Descrição da solução</b></label>
                        <div class="editor" id="editorSolucao"></div>
                        <small id="valida_solucao" style="display: none; color: red;">
                            Por favor, informe a [solução].
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Tags EX: (NFe, SAT, Fiscal)</b></label>
                        <input type="text" data-role="tagsinput" id="categorias" name="categorias" value="" class="form-control" />
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label><b>Anexos</b></label>
                        <div id="uploadd" class="btn btn-sm btn-secondary" style="cursor: pointer; display: block; padding: 10px; text-align: center;">Anexar Arquivos</div>
                        <small id="statuss" style='display:block;color:#F90;text-align:center'></small>		
                        <ul id="files" style="margin-top: 10px;"></ul>
                    </div>
                </div>
            </div>

            <div id="aguarde" style="margin:auto;text-align:center;z-index:9999;"></div>
        </form>
    </div>

    <div class="card-footer text-right">
        <button id="btnFecharCadastroBC" class="btn btn-secondary" type="button">Voltar</button>
        <button id="btnGravarBC" class="btn btn-primary" type="button">Salvar</button>
    </div>
</div>

<div class="card" id="ListaBC">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Base de Conhecimento</h4>
        <button id="btnNovoBC" class="btn btn-primary btn-sm" title="+ Incluir Solução">
            <i class="fas fa-plus-circle"></i> Inserir
        </button>
    </div>

    <div class="card-body">
        <!-- Search Bar -->
        <div class="row mb-3">
            <div class="col-md-10">
                <input type="hidden" name="ordenacao" id="hstOrdenacao" value="ASC" />
                <input name="termo" id="termo" class="form-control" type="text" placeholder="Informe o seu termo de busca">
            </div>
            <div class="col-md-2">
                <button type="button" id="btnPesquisar" class="btn btn-secondary btn-block">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <button type="button" id="btnVerTodos" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Ver Todos
                </button>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="table table-striped table-sm">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="width: 30%;">Problema</th>
                        <th scope="col" style="width: 30%;">Solução</th>
                        <th scope="col" style="width: 40%;">Anexos / Ações</th>
                    </tr>
                </thead>
                <tbody>


     

        <?php	 
            // Busncando os Usuários cadastrados //
            $l = 1;
            $termo = isset($_REQUEST["termo"]) ? $_REQUEST["termo"] : null;
            $sqlBC = "SELECT * FROM base_conhecimento where ORDER BY problema";

            function limitarTexto($texto, $limite = 60) {
                if (mb_strlen($texto) > $limite) {
                    // Retorna os primeiros 60 caracteres e acrescenta "..."
                    return mb_substr($texto, 0, $limite) . '...';
                } else {
                    // Retorna o texto sem modificações se for menor ou igual ao limite
                    return $texto;
                }
            }

            // Tratamento de Dados //
                if( $termo !== null ){
                    $sqlBC = str_replace("where","WHERE UPPER(problema) LIKE UPPER('%".$termo."%') OR UPPER(solucao) LIKE UPPER('%".$termo."%') ",$sqlBC);
                }
                else{ $sqlBC = str_replace("where","",$sqlBC); }
            // INI Tratamento de Dados //

            $registros = mysqli_query(
                $conexao
                , $sqlBC
            );
            
            while ($objResult = mysqli_fetch_object($registros)){
                // Declaração de Variáveis //
                    $tamanho = 50;
                    $downloads = '';
                // FIM Declaração de Variáveis //

                // Tratamento de Dados //
               //     $objResult->problema = (strlen($objResult->problema) > $tamanho) ? substr($objResult->problema, 0, $tamanho) . "..." : $objResult->problema;
               //     $objResult->solucao = (strlen($objResult->solucao) > $tamanho) ? substr($objResult->solucao, 0, $tamanho) . "..." : $objResult->solucao;
                // FIM Tratamento de Dados //
                
                // Buscando os Anexos //
                    $registrosAnxs = mysqli_query(
                        $conexao
                        , "SELECT id, nome_arquivo FROM base_conhecimento_anexos WHERE id_base_conhecimento = '".$objResult->id."'"
                    );
                    
                    while( $objResultAnexos = mysqli_fetch_object($registrosAnxs) ){
                        $downloads .= '<a href="../modulos/baseconhecimento/anexo.php?id='.$objResultAnexos->id.'" target="_blank" title="'.$objResultAnexos->nome_arquivo.'" style="text-decoration:none">
                            <i class="fas fa-cloud-download"></i>
                        </a> ';
                    }
                // FIM Buscando os Anexos //

                echo '<tr id="linha'.$l.'" style="padding: 4px;line-height: 1;">
                        <input type="hidden" name="IdBC" id="IdBC" value="'.$objResult->id.'" />
                        <td>'.limitarTexto($objResult->problema,40).'</td>
                        <td>'.limitarTexto($objResult->solucao,40).'</td>
                        <td>
                            <button class="btn btn-sm btn-danger ConfirmaExclusaoBC" style="padding: 2px 6px;" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button class="btn btn-sm btn-primary botaoAlterarBC" style="padding: 2px 6px;" title="Editar">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            '.$downloads.'
                        </td>                     
                    </tr>';
                $l = $l+1;
            }
            // FIM Busncando os Usuários cadastrados //		
        ?>
        </tbody>
            </table>
        </div>
    </div>

    <div class="card-footer text-right">
        <button class="btn btn-secondary fechar" type="button">Cancelar</button>
    </div>
</div>

</div>
<input type="hidden" name="qtdeFiles" id="qtdeFiles" value="0" />

<!-- Load required third-party libraries -->
<script src="/js/quill.js"></script>
<script src="/js/tagsinput.js"></script>
<script src="/js/ajaxupload.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
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
			$.post('../modulos/baseconhecimento/salvar.php'
				, { idBC: idBC, acaoBC: acaoBC, problema: problema, solucao: solucao, tags: tags}
				, function(retorno) {
                  //  alert(retorno);
				$("#aguarde").fadeOut(100);
				var mensagem1  = "<strong>Base de Conhecimento Cadastrada com sucesso!</strong>";
				var mensagem2  = "<strong>Base de Conhecimento Atualizada com sucesso!</strong>";
				var mensagem9 = 'Falha ao Cadastrar/Atualizar o Registro!';

				if (retorno == 1) { mostraDialogo(mensagem1, "success", 2500); }
				else if (retorno == 2){ mostraDialogo(mensagem2, "success", 2500); }
				else{ mostraDialogo(mensagem9, "danger", 2500); }

				$.ajax("../modulos/baseconhecimento/index.php").done(function(data) {
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
	    var id = $(this).closest("tr").find('#IdBC').val();
		new bootstrap.Modal(document.getElementById('modalBaseConhecimentoExclusao')).show();
		$("#IdBC2").val(id);
	});

	// Remoção do Cadastro //
	$('#btnConfirmaRemoveBC').on('click', function (){
		$("#btnConfirmaRemoveBC").attr('value', 'Removendo ...');
        $('#btnConfirmaRemoveBC').attr('disabled', true);

		var idBC = $("#IdBC2").val();

		$.post("../modulos/baseconhecimento/excluir.php",{IdBC:idBC},function(resultado){             
			var mensagem1  = "<strong>Base de Conhecimento Removido com sucesso!</strong>";
			var mensagem9 = 'Falha ao Remover Base de Conhecimento!';

			if( resultado == 1 ){
				mostraDialogo(mensagem1, "success", 2500);	

				$.ajax("../modulos/baseconhecimento/index.php").done(function(data) {
					// $('#ListarBC').html(data);
					$('#modalBaseConhecimento').html(data);
				});
			}
			else{ mostraDialogo(mensagem9, "danger", 2500); }

			// Fechando a Modal de Confirmação //
			const mdlExclusao = bootstrap.Modal.getInstance(document.getElementById('modalBaseConhecimentoExclusao'));
			if (mdlExclusao) mdlExclusao.hide();
			$("#btnConfirmaRemoveBC").attr('value', 'Confirmar Exclusão!');
        	$('#btnConfirmaRemoveBC').attr('disabled', false);
		});
	});
	// FIM Remoção do Cadastro //
	
	// Alteração //
		$('.botaoAlterarBC').on('click', function (){
			// Busco os dados do Produto Selecionado  
			var codigo = $(this).closest("tr").find('input:hidden').val();

			// Alterando Displays //
			$("#FormBC").css("display","block");
			$("#ListaBC").css("display","none");

			// Alterando o Título do Cadastro //
			$("#titleCadastroBC").html("Alteração de Base de Conhecimento");
            $('#files').html('');

           // alert("codigo"+ codigo)

			$.getJSON('../modulos/baseconhecimento/carregardados.php?codigo='+codigo, function(registro){	
                console.log(registro);		
				// Carregando os Dados //
				$("#idBC").val(registro.id);
				quillProblema.root.innerHTML = registro.problema;
				quillSolucao.root.innerHTML = registro.solucao;
				//$("#categorias").val(registro.tags);
                $('#categorias').tagsinput('add',registro.tags);
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
					$('<li></li>').appendTo('#files').html(element.name +'<a href="#" data-valor="'+element.id+'" class="'+element.name+'" id="ApagaAnexo" name="ApagaAnexo">X</a><br />').addClass('success');
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
	// FIM Fechar Cadastro //

	// Apaga Item Anexado da Lista
    $(document).on('click', "#ApagaAnexo", function (evt){
		//var file = $("#ApagaAnexo").attr('class');
		var file = $(this).attr('class');
		var idfile =  $(this).data('valor'); //$(this).attr('href');
		$("li").remove(":contains('"+file+"')");		
		
		$.post('../modulos/baseconhecimento/apaga-file.php', {id:idfile,file:file}, function(resposta) {
		//	alert(resposta);
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
		action: '../modulos/baseconhecimento/upload-file.php',
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
           // alert(response.charAt(0));          
			if (response.charAt(0)==3){
				mostraDialogo("Você pode anexar apenas 5 arquivo por Solução", "danger", 2500);

				// Desabilitando o Campo Upload //
				$('input[name="uploadfile"]').prop('disabled', true);

				return false;	
			}
			else if( response.charAt(0) == 0 ){ //0 se retornar Zero é porque o upload foi realizado com sucess
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
			else{				
				 $('<li></li>').appendTo('#files').text('ERRO - Não foi possivel anexar'+file).addClass('error').fadeOut(2000); 
				}
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
			$.post('../modulos/baseconhecimento/index.php'
				, { termo: termo}
				, function(retorno) {
				$('#modalBaseConhecimento').html(retorno);
			});
		}
	// FIM Ações de Pesquisa //

	// Fechar a Janela //
	$('.fechar').on("click", function(){ fecharModal(); });
});
</script>
// JavaScript Document
$( document ).ready(function() {
    $('.btnExcluirRespostaRapida').on('click', function (){
        var id = $(this).parent().parent("div").find('#IdRespostaRapida').val();
		abrirModal("#modalRespostaRapidaExclusao");
		$("#IdRespostaRapida2").val(id);
    });	  

	// Remoção do Cadastro //
	$('#btnConfirmaExclusaoRespostaRapida').on('click', function (){
        $("#btnConfirmaExclusaoRespostaRapida").attr('value', 'Removendo ...');
        $('#btnConfirmaExclusaoRespostaRapida').attr('disabled', true);
        $('#btnCancelaExclusaoRespostaRapida').attr('disabled', true);

	    var id = $("#IdRespostaRapida2").val();
            
        $.post("cadastros/respostasrapidas/excluir.php",{id:id},function(resultado){
            var mensagem1  = "<strong>Resposta Rápida removido com sucesso!</strong>";
            var mensagem9 = 'Falha ao remover Resposta Rápidas!';
                
            if (resultado = 1) { mostraDialogo(mensagem1, "success", 2500); }
            else{ mostraDialogo(mensagem9, "danger", 2500); }

            // Recarrega a Modal de Respostas Rápidas //
            $.ajax("cadastros/respostasrapidas/listar.php").done(function(data) {
                var lista = data.split("#@#");
                $("#todaLista").html(lista[0]);
                $("#minhaLista").html(lista[1]);
                $("#countTodas").html(lista[2]);
                $("#countMinhas").html(lista[3]);
            });

            // Fechando a Modal de Confirmação //
            $('#modalRespostaRapidaExclusao').attr('style', 'display: none');
            $('#btnConfirmaExclusaoRespostaRapida').attr('disabled', false);
            $('#btnCancelaExclusaoRespostaRapida').attr('disabled', false);
        });
    });
    // FIM Remoção do Cadastro //
	
    // Alteração de Cadastro //
	$('.btnAlterarRespostaRapida').on('click', function (){
        // Busco os dados do Produto Selecionado  
        var codigo = $(this).parent().parent("div").find('input:hidden').val();

        // Alterando Displays //
        $("#FormRespostaRapida").css("display","block");
        $("#ListaRespostasRapidas").css("display","none");

        // Alterando o Título do Cadastro //
        $("#titleCadastroRespostaRapida").html("Alteração de Resposta Rápida");
        
	    $.getJSON('cadastros/respostasrapidas/carregardados.php?codigo='+codigo, function(registro){
            if( registro.id_usuario > 0 ){ registro.id_usuario = "2"; }
            else{ registro.id_usuario = "1"; }

            $("#IdRespostaRapida").val(registro.id);
            $("#titulo").val(registro.titulo);
            $("#id_usuario").val(registro.id_usuario);
            $("#resposta").val(registro.resposta);
            if (registro.arquivo!=null && registro.arquivo!=""){
                $("#arquivo_carregado").html("Existe um arquivo carregado");
                $("#arquivo_carregado").css({ 'color': 'red', 'font-size': '150%' });
            } else {             
                $("#arquivo_carregado").html("Não Existe um arquivo carregado");
                $("#arquivo_carregado").css({ 'color': 'black', 'font-size': '150%' });
            }
            $("#foto").val('');

        });
              
        // Mudo a Ação para Alterar    
		$("#acaoRespostaRapida").val("2");
		$("#titulo").focus();
	});
    // FIM Alteração de Cadastro //

	// Fechar Cadastro //
	$('#btnFecharCadastroRespostaRapida').on('click', function (){
		$("#ListaRespostasRapidas").css("display","block");
		$("#FormRespostaRapida").css("display","none");
	});
	$('#btnCancelaExclusaoRespostaRapida').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalRespostaRapidaExclusao').attr('style', 'display: none');
		
		$("#ListaRespostasRapidas").css("display","block");
		$("#FormRespostaRapida").css("display","none");
	});
	// FIM Fechar Cadastro //

    // Incluindo no TextArea a Resposta Rápida //
    $('.addRespostaRapida').on('click', function (){
        var respostaRapida = $(this).parent().find('span').text();
        var arquivo = 'cadastros/respostasrapidas/' + $(this).parent().find('#AnexoRespostaRapida').val();
        var nome_arquivo = $(this).parent().find('#NomeAnexoRespostaRapida').val();
        var fileExtension = nome_arquivo.substring(nome_arquivo.lastIndexOf(".") + 1);
        $('#msg').val(respostaRapida);

        if (nome_arquivo != ''){
            
            // Tratamento dos Paineis //
            $(".panel-upImage").addClass("open");
            $("#btnEnviar").attr("style", "display: block");
            $("#divAudio").attr("style", "display: none");
            
            // Criando o Elemento <img> //

            var img = document.createElement("img");
            img.setAttribute("id", "imgView");
            
            fetch(arquivo)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Não foi possível obter o arquivo.');
                    }
                    return response.blob();
                })
                .then(blob => {
                    console.log("Blob:", blob);
                    var reader = new FileReader();
                    reader.onload = function () {
                        if (fileExtension === "jpeg" || fileExtension === "jpg" || fileExtension === "png" || fileExtension === "gif") {
                            img.src = reader.result;
                        } else if (fileExtension === "pdf") {
                            img.src = "images/abrir_pdf.png";
                        } else if (fileExtension === "doc" || fileExtension === "docx") {
                            img.src = "images/abrir_doc.png";
                        } else if (fileExtension === "xls" || fileExtension === "xlsx" || fileExtension === "csv") {
                            img.src = "images/abrir_xls.png";
                        } else {
                            img.src = "images/abrir_outros.png";
                        }
                    };
                    reader.readAsDataURL(blob);
                    $("#anexomsgRapida").val(arquivo);
                    $("#nomeanexomsgRapida").val(nome_arquivo);
                    

                    $("#dragDropImage").attr("style", "display:none");
                    document.getElementById("panel-upload-image").appendChild(img);
                    $("#msg").focus();
                })
                .catch(error => {
                    console.error('Erro:', error);
                });
            }//fim da verificação se possui anexo nas respostas rápidas

        // Envio automático da Resposta Rápida //
        if( $('#parametrosRespRapidaAut').val() === "1" ){
            $('#btnEnviar').click();
        }
        else{
            // Habilitando o Envio da Imagem e Bloqueando as demais Opções //
                $("#btnEnviar").attr("style", "display: block");
                $("#divAudio").attr("style", "display: none");
            // FIM Habilitando o Envio da Imagem e Bloqueando as demais Opções //
        }

        // Fechando a Modal de Confirmação //
		$('#btnCancelarRespostasRapidas').click();
	});
    // FIM Incluindo no TextArea a Resposta Rápida //
});
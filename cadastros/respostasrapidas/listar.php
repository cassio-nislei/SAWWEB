<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php"); ?>

<table class="table table-striped">
    <thead>
      <tr>
        <th>Título</th>
        <th>Resposta</th>
        <th>Ação do Menu</th>
        <th>Anexo</th>
        <th>Ações</th>
      </tr>
    </thead>
	<tbody>

<?php
  $l = 1;
  $qryRespostasRapidas = mysqli_query($conexao, "SELECT * FROM tbrespostasrapidas ORDER BY titulo");
  
  while( $qrrRespostasRapidas = mysqli_fetch_array($qryRespostasRapidas) ){
    $strResposta = (strlen($qrrRespostasRapidas["resposta"]) > 50) ? substr($qrrRespostasRapidas["resposta"], 0, 50) . " ..." : $qrrRespostasRapidas["resposta"];
    
    $anexo = '';
    if ($qrrRespostasRapidas["arquivo"]!=''){
      $anexo = '<i class="fas fa-paperclip"></i>';
    }

    // Traduzindo o valor de ação
    $acaoTexto = 'Nenhuma';
    switch($qrrRespostasRapidas["acao"]){
      case 1: $acaoTexto = 'Devolve Menu'; break;
      case 2: $acaoTexto = 'Devolve Menu Sem titulo'; break;
      case 9: $acaoTexto = 'Encerra Atendimento'; break;
      default: $acaoTexto = 'Nenhuma';
    }

    echo '<tr id="linha'.$l.'" class="resposta-row">
            <td><input type="hidden" name="IdRespostaRapida" class="IdRespostaRapida" value="'.$qrrRespostasRapidas["id"].'" />
            <input type="hidden" name="AnexoRespostaRapida" class="AnexoRespostaRapida" value="'.$qrrRespostasRapidas["arquivo"].'" />
            <input type="hidden" name="NomeAnexoRespostaRapida" class="NomeAnexoRespostaRapida" value="'.$qrrRespostasRapidas["nome_arquivo"].'" />
            '. $qrrRespostasRapidas["titulo"].'</td>
            <td>'. $strResposta.'
            <span class="resposta-completa" style="display: none;">'.$qrrRespostasRapidas["resposta"].'</span></td>
            <td>'. $acaoTexto .'</td>
            <td>'. $anexo .'</td>
            <td> 
                <button class="btn btn-sm btn-primary btnAlterarRespostaRapida" title="Editar"><i class="fas fa-pencil"></i></button>
                <button class="btn btn-sm btn-danger btnExcluirRespostaRapida" title="Excluir"><i class="fas fa-trash"></i></button>
            </td>
          </tr>';
    $l++;
  }
?>

	</tbody>
</table>

<script>
$( document ).ready(function() {
    // Exclusão de Resposta Rápida //
    $('.btnExcluirRespostaRapida').on('click', function (){
        var id = $(this).closest('tr').find('.IdRespostaRapida').val();
		abrirModal("#modalRespostaRapidaExclusao");
		$("#IdRespostaRapida2").val(id);
    });	  

	// Confirmação de Exclusão //
	$('#btnConfirmaExclusaoRespostaRapida').on('click', function (){
        $("#btnConfirmaExclusaoRespostaRapida").attr('value', 'Removendo ...');
        $('#btnConfirmaExclusaoRespostaRapida').attr('disabled', true);
        $('#btnCancelaExclusaoRespostaRapida').attr('disabled', true);

	    var id = $("#IdRespostaRapida2").val();
            
        $.post("/cadastros/respostasrapidas/excluir.php",{id:id},function(resultado){
            var mensagem1  = "<strong>Resposta Rápida removida com sucesso!</strong>";
            var mensagem9 = 'Falha ao remover Resposta Rápida!';
                
            if (resultado = 1) { 
                mostraDialogo(mensagem1, "success", 2500); 
                $.ajax("/cadastros/respostasrapidas/listar.php").done(function(data) {
                    $('#ListaRespostasRapidas').html(data);
                });
            }
            else{ mostraDialogo(mensagem9, "danger", 2500); }

            // Fechando a Modal de Confirmação //
            $('#modalRespostaRapidaExclusao').attr('style', 'display: none');
            $('#btnConfirmaExclusaoRespostaRapida').attr('disabled', false);
            $('#btnCancelaExclusaoRespostaRapida').attr('disabled', false);
        });
    });
    // FIM Remoção //
	
    // Alteração de Cadastro //
	$('.btnAlterarRespostaRapida').on('click', function (){
        // Busco os dados da Resposta Selecionada  
        var codigo = $(this).closest('tr').find('.IdRespostaRapida').val();

        // Alterando Displays //
        $("#FormRespostaRapida").css("display","block");
        $("#ListaRespostasRapidas").css("display","none");

        // Alterando o Título do Cadastro //
        $("#titleCadastroRespostaRapida").html("Alteração de Resposta Rápida");

        $.getJSON('/cadastros/respostasrapidas/carregardados.php?codigo='+codigo, function(registro){			
            // Carregando os Dados //
            $("#IdRespostaRapida").val(registro.id);
            $("#titulo").val(registro.titulo);
            $("#resposta").val(registro.resposta);
        });
              
        // Mudo a Ação para Alterar    
        $("#acaoRespostaRapida").val("2");
        $("#titulo").focus();
	});
    // FIM Alteração //

	// Fechar Cadastro //
	$('#btnFecharCadastroRespostaRapida').on('click', function (){
		$("#ListaRespostasRapidas").css("display","block");
		$("#FormRespostaRapida").css("display","none");
	});
	
	$('#btnCancelaExclusaoRespostaRapida').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalRespostaRapidaExclusao').attr('style', 'display: none');
	});
	// FIM Fechar //
});
</script>
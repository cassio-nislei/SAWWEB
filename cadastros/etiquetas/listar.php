<?php require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php"); ?>

<table class="table table-striped">
    <thead>
      <tr>
        <th>Cor</th>
        <th>Descrição</th>
		<th>Ações</th>
      </tr>
    </thead>
	<tbody>

<?php
    // Buscando as Etiquetas cadastradas //
    $l = 1;
    $query = mysqli_query($conexao, "SELECT * FROM tbetiquetas ORDER BY descricao");
    
    while ($registros = mysqli_fetch_object($query)){
        echo '<tr id="linha'.$l.'" class="etiqueta-row">
				<td><input type="hidden" name="IdEtiqueta" class="IdEtiqueta" value="'.$registros->id.'" />
                <i style="color:'.$registros->cor.'; font-size: 1.5rem;" class="fas fa-square"></i></td>
				<td>'. $registros->descricao.'</td>
				<td> 
				    <button class="btn btn-sm btn-danger ConfirmaExclusaoEtiqueta" title="Excluir"><i class="fas fa-trash"></i></button>
			        <button class="btn btn-sm btn-primary botaoAlterarEtiqueta" title="Editar"><i class="fas fa-pencil"></i></button>
				</td>
			</tr>';
          $l = $l+1;
      }
    // FIM Buscando as Etiquetas cadastradas //		
?>

	</tbody>
</table>
<script>

// JavaScript Document
$( document ).ready(function() {		
	// Exclusão de Etiquetas //
	$('.ConfirmaExclusaoEtiqueta').on('click', function (){
	    var id = $(this).closest('tr').find('.IdEtiqueta').val();
		abrirModal("#modalEtiquetaExclusao");
		$("#IdEtiqueta2").val(id);
	});

	// Remoção do Cadastro //
	$('#btnConfirmaRemoveEtiqueta').on('click', function (){
		$("#btnConfirmaRemoveEtiqueta").attr('value', 'Removendo ...');
        $('#btnConfirmaRemoveEtiqueta').attr('disabled', true);

		var idEtiqueta = $("#IdEtiqueta2").val();

		$.post("/cadastros/etiquetas/excluir.php",{IdEtiqueta:idEtiqueta},function(resultado){             
			var mensagem  = "<strong>Etiqueta Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Usuário!';

			if (resultado = 2) {
				mostraDialogo(mensagem, "success", 2500);	
                $("#btnConfirmaRemoveEtiqueta").attr('value', 'Confirmar Exclusão!');
                $('#btnConfirmaRemoveEtiqueta').attr('disabled', false);
				$.ajax("/cadastros/etiquetas/listar.php").done(function(data) {
					$('#ListarEtiqueta').html(data);
				});
			}
			else{ mostraDialogo(mensagem2, "danger", 2500); }

			// Fechando a Modal de Confirmação //
			$('#modalEtiquetaExclusao').attr('style', 'display: none');
		});
	});
	// FIM Remoção do Cadastro //

    $('#btnCancelaRemoveEtiqueta').on('click', function (){
        $('#modalEtiquetaExclusao').attr('style', 'display: none');
	});
    
	
	// Alteração de Etiqueta //
	$('.botaoAlterarEtiqueta').on('click', function (){
		// Busco os dados da Etiqueta Selecionada  
		var codigo = $(this).closest('tr').find('.IdEtiqueta').val();

		// Alterando Displays //
		$("#FormEtiquetas").css("display","block");
		$("#ListaEtiquetas").css("display","none");

		// Alterando o Título do Cadastro //
		$("#titleCadastroUser").html("Alteração de Etiquetas");

		$.getJSON('/cadastros/etiquetas/carregardados.php?codigo='+codigo, function(registro){			
			// Carregando os Dados //
			$("#id_Etiqueta").val(registro.id);
			$("#cor").val(registro.cor);
			$("#descricao").val(registro.descricao);
		});
			  
		// Mudo a Ação para Alterar    
		$("#acaoEtiqueta").val("2");
		$("#cor").focus();
	});
	// FIM Alteração de Etiqueta //

	// Fechar Cadastro da Etiqueta //
	$('#btnFecharCadastroEtiqueta').on('click', function (){
		$("#ListaEtiquetas").css("display","block");
		$("#FormEtiquetas").css("display","none");
	});
	$('#btnCancelaRemoveEtiqueta').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalEtiquetaExclusao').attr('style', 'display: none');
		
		$("#ListaEtiquetas").css("display","block");
		$("#FormEtiquetas").css("display","none");
	});
	// FIM Fechar Cadastro da Etiqueta //	
	

});

</script>
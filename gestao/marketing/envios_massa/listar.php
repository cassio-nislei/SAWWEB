<?php require_once("../../../includes/padrao.inc.php"); ?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Nome</th>
          <th>Ativo</th>
          <th>Ações</th>
		  <th>Importados</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$respostasautomaticas = mysqli_query(
				$conexao
				, "SELECT * from tbenviomgsmassa order by id desc limit 20"
			  );
			  while ($ListaRespostasAutomaticas = mysqli_fetch_array($respostasautomaticas)){	

			//Verifico o Item Padrão da Categoria	
			if ($ListaRespostasAutomaticas["ativo"]){
				$estilo = 'style="background-color: #cbf7c7;"';
				$ativo = '<input type="checkbox" class="btnAtivo" checked>';
			}else{
				$estilo = 'style="background-color: #f7e3f3"';
				$ativo = '<input type="checkbox" class="btnAtivo">';
			}
			echo '<tr id="linha'.$l.'" '.$estilo.'>';
			echo '<td><input type="hidden" name="IdRespostaAutomatica" id="IdRespostaAutomatica" value="'.$ListaRespostasAutomaticas["id"].'" />
			'.$ListaRespostasAutomaticas["nome"].'</td>';  
			echo '<td>'.$ativo.'</td>';
			echo '<td>
			 <button class="btn btn-primary btnimportar" title="Importar"><i class="fa fa-download" aria-hidden="true"></i></button>
			 <button class="btn btn-success botaoAlterar" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button>
            <button class="btn btn-danger ConfirmaExclusao" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>  
      </td>';
	  echo '<td>'.$ListaRespostasAutomaticas["importados"].'</td></tr>';
	  
			  $l = $l+1;
		  }
		  ?>
        <tr>
    
        </tr>
      </tbody>
    </table>
  </div>




			

	<script>
    $( document ).ready(function() {	
    $('.ConfirmaExclusao').on('click', function (e){
		e.preventDefault();	
	    var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();

		//alert(id);
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover esta resposta automática?', function (data) {
		  if (data) {      
			  $.post("marketing/envios_massa/excluir.php",{IdRespostaAutomatica:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Resposta Automática Removido com sucesso!</strong>";
            var mensagem2 = 'Falha ao Remover Resposta Automática!';		
      
          //   alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$.ajax("marketing/envios_massa/listar.php").done(function(data) {
                    $('#Listar').html(data);
                });		
        
			}
			else{ 
				mostraDialogo(mensagem2, "danger", 2500); 
			}
		 });//Fim do POst que faz a exclusão
		  } 
		});
		
	});
	// FIM Remoção do Cadastro //

	$('.btnimportar').on('click', function (e){
		e.preventDefault();	
		var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();
		$("#id_msg").val(id);
	new bootstrap.Modal(document.getElementById('mdlImporta')).show();
});


  $(".btnAtivo").on('click', function (e){
	e.preventDefault();	
	var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();
	var marcado = $(this).prop("checked");

	$.post("marketing/envios_massa/ativardesativar.php",{IdRespostaAutomatica:id, ativo:marcado},function(resultado){   
		$.ajax("marketing/envios_massa/listar.php").done(function(data) {
                    $('#Listar').html(data);
                });	  
		mostraDialogo("Ativação/Desativação efetuada com sucesso", "success", 2500);
	})

});


$('.botaoAlterar').on('click', function (){
		// Busco os dados do Produto Selecionado  
		var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();

    $("#btnCancelar").css({"visibility" : "visible"});


		$.getJSON('marketing/envios_massa/carregardados.php?codigo='+id, function(registro){      
			$("#id").val(registro.id);			
			$("#dt_envio").val(registro.dt_envio);
			$("#dt_envio").trigger('blur');
			$("#nome").val(registro.nome);
			$("#nome").trigger('blur');
			$("#mensagem").val(registro.msg);
			$("#mensagem").trigger('blur');
			$("#ativo").prop("checked", registro.ativo === "checked");
		});

		// Mudo a Ação para Alterar    
		
		$("#acao").val("2");
		$("#departamento").focus();
	});  


});
  </script>

 


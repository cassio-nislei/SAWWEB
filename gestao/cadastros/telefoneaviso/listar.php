<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Telefone</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$telefones = mysqli_query(
				$conexao
				, "SELECT * FROM tbtelefonesavisos"
			  );
			  while ($ListaTelefones = mysqli_fetch_array($telefones)){	
			//Verifico o Item Padrão da Categoria		
			echo '<tr id="linha'.$l.'">';
			echo '<td><input type="hidden" name="IdTelefone" id="IdTelefone" value="'.$ListaTelefones["numero"].'" />
			'. $ListaTelefones["numero"].'</td>';  
			echo '<td>
      <button class="btn btn-danger ConfirmaExclusao" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>
      <button class="btn btn-success botaoAlterar" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button>

      </td></tr>';
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
    $('.ConfirmaExclusao').on('click', function (){
	    var id = $(this).parent().parent("tr").find('#IdTelefone').val();
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover este Telefone?', function (data) {
		  if (data) {      
			  $.post("cadastros/telefoneaviso/excluir.php",{IdTelefone:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Telefone Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Telefone!';		
      
           //  alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$.ajax("cadastros/telefoneaviso/listar.php").done(function(data) {
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


  $('.botaoAlterar').on('click', function (){
		// Busco os dados do Produto Selecionado  
		var id = $(this).parent().parent("tr").find('#IdTelefone').val();

    $("#btnCancelar").css({"visibility" : "visible"});

		$.getJSON('cadastros/telefoneaviso/carregardados.php?id='+id, function(registro){      
			$("#id").val(registro.numero);
			$("#txttelefone").val(registro.numero);
			$("#txttelefone").trigger('blur');

		});

		// Mudo a Ação para Alterar    
		
		$("#acao").val("2");
		$("#txttelefone").focus();
	});  


});
  </script>

 


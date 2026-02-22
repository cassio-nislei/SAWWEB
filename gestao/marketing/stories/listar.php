<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr> 
          <th>Imagem</th>
		  <th>Repostar</th>
		  <th>Postado</th>
		  <th>Último Post</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$stories = mysqli_query(
				$conexao
				, "SELECT id, data, enviado, arquivo, nome_arquivo, renovar
					FROM tbstorie					  
						ORDER BY data desc"
			  );
			  while ($ListaStories = mysqli_fetch_array($stories)){	
				$UsaArquivo="Sem arquivo";
				if ($ListaStories["arquivo"]!=""){
				  $UsaArquivo=$ListaStories["nome_arquivo"];
				}	
				if ($ListaStories["renovar"]){
                   $renovar = 'SIM';
				}else{
					$renovar = 'NÃO';
				}
				if ($ListaStories["enviado"]){
					$postado = 'SIM';
				 }else{
					$postado = 'NÃO';
				 }
			//Verifico o Item Padrão da Categoria		
			echo '<tr id="linha'.$l.'">';
            echo '<td><input type="hidden" name="IdStorie" id="IdStorie" value="'.$ListaStories["id"].'" />'.$UsaArquivo.'</td>'; 
			echo '<td>'.$renovar.'</td>';
			echo '<td>'.$postado.'</td>';
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
	    var id = $(this).parent().parent("tr").find('#IdStorie').val();

		//alert(id);
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover este Storie?', function (data) {
		  if (data) {      
			  $.post("marketing/stories/excluir.php",{IdStorie:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Storie Removido com sucesso!</strong>";
            var mensagem2 = 'Falha ao Remover Storie!';		
      
           //  alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$("#btnCancelar").click();
				$.ajax("marketing/stories/listar.php").done(function(data) {
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
		var IdStorie = $(this).parent().parent("tr").find('#IdStorie').val();

    $("#btnCancelar").css({"visibility" : "visible"});

		// Alterando Displays //
		$("#gravaStorie").css("display","block");


		$.getJSON('marketing/stories/carregardados.php?codigo='+IdStorie, function(registro){
	
            $("#id").val(registro.id);

			$("#respostaautomatica").trigger('blur');
            
            
            if (registro.arquivo!=null && registro.arquivo!=""){
                $("#arquivo_carregado").html("Arquivo:"+registro.nome_arquivo);
                $("#arquivo_carregado").css({ 'color': 'red', 'font-size': '150%' });
            } else {             
                $("#arquivo_carregado").html("Não Existe um arquivo carregado");
                $("#arquivo_carregado").css({ 'color': 'black', 'font-size': '150%' });
            }
            $("#foto").val('');
            
        });
              
        // Mudo a Ação para Alterar    
		$("#acao").val("2");
		$("#menu_resposta").focus();
	});  


});
  </script>

 


<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Menu</th>
          <th>Arquivo</th>
		  <th>Respostas Automáticas</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$respostasautomaticas = mysqli_query(
				$conexao
				, "SELECT tr.id_menu, tm.descricao AS menu, tr.descricao AS resposta, tr.arquivo, tr.nome_arquivo 
					FROM tbrespostasautomaticas tr 
					  INNER JOIN tbmenu tm ON(tm.id = tr.id_menu)
						ORDER BY id"
			  );
			  while ($ListaRespostasAutomaticas = mysqli_fetch_array($respostasautomaticas)){	
				$ListaRespostasAutomaticas["menu"] = (strlen($ListaRespostasAutomaticas["menu"]) > 30) ? substr($ListaRespostasAutomaticas["menu"],0,30)." ..." : $ListaRespostasAutomaticas["menu"];
				$ListaRespostasAutomaticas["resposta"] = (strlen($ListaRespostasAutomaticas["resposta"]) > 75) ? substr($ListaRespostasAutomaticas["resposta"],0,75)." ..." : $ListaRespostasAutomaticas["resposta"];
		  
				$UsaArquivo="Sem arquivo";
				if ($ListaRespostasAutomaticas["arquivo"]!=""){
				  $UsaArquivo=$ListaRespostasAutomaticas["nome_arquivo"];
				}	
			//Verifico o Item Padrão da Categoria		
			echo '<tr id="linha'.$l.'">';
			echo '<td><input type="hidden" name="IdRespostaAutomatica" id="IdRespostaAutomatica" value="'.$ListaRespostasAutomaticas["id_menu"].'" />
			'.$ListaRespostasAutomaticas["menu"].'</td>';  
            echo '<td>'.$UsaArquivo.'</td>'; 
			echo '<td>'.$ListaRespostasAutomaticas["resposta"].'</td>';
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
	    var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();

		//alert(id);
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover esta resposta automática?', function (data) {
		  if (data) {      
			  $.post("cadastros/respostasautomaticas/excluir.php",{IdRespostaAutomatica:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Resposta Automática Removido com sucesso!</strong>";
            var mensagem2 = 'Falha ao Remover Resposta Automática!';		
      
           //  alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$("#btnCancelar").click();
				$.ajax("cadastros/respostasautomaticas/listar.php").done(function(data) {
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
		var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();

    $("#btnCancelar").css({"visibility" : "visible"});

		// Alterando Displays //
		$("#gravaDepartamento").css("display","block");


		$.getJSON('cadastros/respostasautomaticas/carregardados.php?codigo='+id, function(registro){
            $("#menu_resposta").val(registro.id_menu);
            $("#menu_acao").val(registro.acao);            
            $("#respostaautomatica").val(registro.descricao);
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

 


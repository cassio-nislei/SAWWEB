<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Celular</th>
          <th>data - hora De envio</th>
		  <th>Mensagem</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$respostasautomaticas = mysqli_query(
				$conexao
				, "SELECT * from tbmsgsenviadaspelosaw order by id desc limit 20"
			  );
			  while ($ListaRespostasAutomaticas = mysqli_fetch_array($respostasautomaticas)){	

			//Verifico o Item Padrão da Categoria	
			if ($ListaRespostasAutomaticas["enviada"]){
				$estilo = 'style="background-color: #cbf7c7;"';
			}else{
				$estilo = 'style="background-color: #f7e3f3"';
			}
			echo '<tr id="linha'.$l.'" '.$estilo.'>';
			echo '<td><input type="hidden" name="IdRespostaAutomatica" id="IdRespostaAutomatica" value="'.$ListaRespostasAutomaticas["id"].'" />
			'.$ListaRespostasAutomaticas["numero"].'</td>';  
            echo '<td>'.$ListaRespostasAutomaticas["dt_programada"]. ' '. $ListaRespostasAutomaticas["hora_programada"].'</td>'; 
			echo '<td>'.$ListaRespostasAutomaticas["msg"].'</td>';
			echo '<td>
      <button class="btn btn-danger ConfirmaExclusao" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>
    

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
    $('.ConfirmaExclusao').on('click', function (e){
		e.preventDefault();	
	    var id = $(this).parent().parent("tr").find('#IdRespostaAutomatica').val();
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover esta resposta automática?', function (data) {
		  if (data) {      
			  $.post("cadastros/lista_transmissao/excluir.php",{IdRespostaAutomatica:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Resposta Automática Removido com sucesso!</strong>";
            var mensagem2 = 'Falha ao Remover Resposta Automática!';		
      
           //  alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				location.reload();
        
			}
			else{ 
				mostraDialogo(mensagem2, "danger", 2500); 
			}
		 });//Fim do POst que faz a exclusão
		  } 
		});
		
	});
	// FIM Remoção do Cadastro //


});
  </script>

 


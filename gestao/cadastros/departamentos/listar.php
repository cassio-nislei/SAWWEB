<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Menu</th>
          <th>Departamento</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

			$departamentos = mysqli_query(
          $conexao
          , "SELECT td.id, tm.descricao as menu, td.departamento FROM tbdepartamentos td left join tbmenu tm on tm.id = td.id_menu order by id;"
        );
		while ($ListaDepartamentos = mysqli_fetch_array($departamentos)){	
			//Verifico o Item Padrão da Categoria		
			echo '<tr id="linha'.$l.'">';
			echo '<td><input type="hidden" name="IdDepartamento" id="IdDepartamento" value="'.$ListaDepartamentos["id"].'" />
			'. $ListaDepartamentos["menu"].'</td>';  
      echo '<td>'. $ListaDepartamentos["departamento"].'</td>'; 
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
	    var id = $(this).parent().parent("tr").find('#IdDepartamento').val();
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover este Departamento?<br> se você apagar este departamento todos os vinculos de usuário a ele serão apagados também!', function (data) {
		  if (data) {      
			  $.post("cadastros/departamentos/excluir.php",{IdDepartamento:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Departamento Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Departamento!';		
      
           //  alert(resultado);
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$.ajax("cadastros/departamentos/listar.php").done(function(data) {
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
		var id = $(this).parent().parent("tr").find('#IdDepartamento').val();

    $("#btnCancelar").css({"visibility" : "visible"});

		// Alterando Displays //
		$("#gravaDepartamento").css("display","block");


		$.getJSON('cadastros/departamentos/carregardados.php?id='+id, function(registro){      
			$("#id").val(registro.id);
			$("#menu").val(registro.id_menu);
			$("#menu").trigger('blur');
			$("#departamento").val(registro.departamento);
		});

		// Mudo a Ação para Alterar    
		
		$("#acao").val("2");
		$("#departamento").focus();
	});  


});
  </script>

 


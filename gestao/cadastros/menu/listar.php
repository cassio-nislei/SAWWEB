<?php require_once("../../../includes/padrao.inc.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover" id="grid1">
      <thead>
        <tr>         
          <th>Menu Superior</th>
          <th>Menu</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
       <?php
		    $l = 1;

        $menus = mysqli_query(
          $conexao
          , "SELECT menu.id, menu.descricao, coalesce(submenu.descricao,'Nenhum') as pai FROM tbmenu menu left join tbmenu submenu on submenu.id = menu.pai ORDER BY id;"
        );
        while ($ListaMenus = mysqli_fetch_array($menus)){
			//Verifico o Item Padrão da Categoria		
			echo '<tr id="linha'.$l.'">';
			echo '<td><input type="hidden" name="IdMenu" id="IdMenu" value="'.$ListaMenus["id"].'" />
      '.$ListaMenus["pai"].'</td>';  
      echo '<td>'.$ListaMenus["descricao"].'</td>'; 
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
	    var id = $(this).parent().parent("tr").find('#IdMenu').val();
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover este item do Menu?', function (data) {
		  if (data) {      
			  $.post("cadastros/menu/excluir.php",{IdMenu:id},function(resultado){     
				//  alert(resultado);   
			var mensagem  = "<strong>Menu Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Menu!';		
      
      
			if (resultado == 1) {
				mostraDialogo(mensagem2, "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$.ajax("cadastros/menu/listar.php").done(function(data) {
            $('#Listar').html(data);					
        });
        //Atualizo a lista
				$.ajax("cadastros/menu/combomenu.php").done(function(data) {
					$('#id_menu').html(data);
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
		var id = $(this).parent().parent("tr").find('#IdMenu').val();

    $("#btnCancelar").css({"visibility" : "visible"});

		// Alterando Displays //
		$("#gravaMenu").css("display","block");

     //atualizo o combo com as opções d emenu superio
     $.ajax("cadastros/menu/combomenu.php").done(function(data) {
					$('#id_menu').html(data);
				});


		$.getJSON('cadastros/menu/carregardados.php?id='+id, function(registro){

			$("#id").val(registro.id);
			$("#id_menu").val(registro.pai);
			$("#txtmenu").val(registro.descricao);
		});

		// Mudo a Ação para Alterar    
		$("#acao").val("2");
		$("#txtmenu").focus();
	});  


});
  </script>

 


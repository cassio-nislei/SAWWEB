

<style>
	table.table {
      border-spacing: 0px;
    }
	table.table tbody tr:last-child td {
      margin-bottom: 0px;
    }
	table.table td, table.table th {
      padding: 2px;
      margin: 0;
    }
	 /* Remover espaçamento inferior na tabela detalhe */
	 .collapse.show td {
      padding-bottom: 0;
    }

	.collapse.show + .table tbody tr:last-child td {
    margin-bottom: 0 !important;
}
</style>
<table class="table table-striped">
    <thead>
      <tr>
        <th>Nome do Usuário</th>
        <th>Login</th>
        <th>Ativo</th>
		<th>Perfil</th>
		<th>Departamentos</th>
		<th>Ações</th>
      </tr>
    </thead>
	<tbody>

<?php	 
 include_once("../../../includes/conexao.php");
    // Monto os options dos Departamentos //
    $options = '<select name="sltDepartamentos" id="sltDepartamentos" class="uk-select" style="width:75%;height:25px;margin-top:-15px;clear: both;font-size:12px;padding:0">
                    <option value="0">Departamento</option>';

    $departamentos = mysqli_query(
        $conexao
        , "SELECT * FROM tbdepartamentos"
    );

    while ($optDepartamentos = mysqli_fetch_array($departamentos)){
        $options .= '<option value="'.$optDepartamentos["id"].'"> '.$optDepartamentos["departamento"].'</option>';
    }

	$options .= '</select>';
    // FIM Monto os options dos Departamentos //

    // Busncando os Usuários cadastrados //
    $l = 1;

    $usuarios = mysqli_query(
        $conexao
        , "SELECT * FROM tbusuario ORDER BY id"
    );
    
    while ($ListaUsuarios = mysqli_fetch_array($usuarios)){
        if( $ListaUsuarios["situacao"] == 'A' ){
            $ativo = '<i class="fa fa-check" aria-hidden="true"></i>'; 
        }
        else{ $ativo = 'Inativo'; }

        if($ListaUsuarios["perfil"] == '0'){ $perfil= 'ADM.'; }
        else if($ListaUsuarios["perfil"] == '2'){ $perfil = 'COOR.'; }
        else{ $perfil = 'OP.'; }

		echo '<tr class="accordion-toggle" id="linha'.$l.'">
				<td><input type="hidden" name="IdUsuario" id="IdUsuario" value="'.$ListaUsuarios["id"].'" />
				<input type="hidden" name="idLinha" id="idLinha" value="'.$l.'" />
                <a id="UsuarioExpandir'.$l.'" data-toggle="collapse" data-target="#detail'.$l.'" style="cursor:pointer"><label style="margin-top:-7px;cursor:pointer">'. $ListaUsuarios["nome"].'</label></a></td>
				<td>'.$ListaUsuarios["login"].'</td>
				<td>'.$ativo.'</td>
				<td>'.$perfil.'</td>
				<td>'.$options.'
				<button class="btn btn-primary btnIncluirDepartamento" title="Adicionar"><i class="fas fa-plus"></i></button>
				</td>
				<td> 
				    <button class="btn btn-danger ConfirmaExclusaoUsuario" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>
			        <button class="btn btn-success botaoAlterarUsuario" title="Editar"><i class="fa fa-pencil" aria-hidden="true"></i></button>
				</td>
			</tr>';
			//Após Mostrar os Usuários, mostra os Departamentos Vinculados
       				$departamentos = mysqli_query($conexao, "select d.id, d.departamento from tbusuariodepartamento ud
                    INNER JOIN tbdepartamentos d on d.id = ud.id_departamento
                    where ud.id_usuario = '".$ListaUsuarios["id"]."'");
		
				if (mysqli_num_rows($departamentos)==0){
					echo'
					<tr>
					<td colspan="3" class="hiddenRow">
					<div class="collapse" id="detail'.$l.'">
						<table class="table">
						<tbody>
							<tr>
							<td colspan="2">
							  <font color="red"><b>Usuário não vinculado a nenhum departamento</b></font>
							 </td>							
							</tr>
						</tbody>
						</table>
					</div>
					</td>
				</tr>
					';
	            }else{
                   //Se possuir Departamentos Vinculados
				   echo ' <tr>
				   <td colspan="3" class="hiddenRow">
				   <div class="collapse" id="detail'.$l.'">
					   <table class="table">
					   <tbody>';
					while ($listaDepartamentos = mysqli_fetch_array($departamentos)){
						//Listo os Departamentos Vinculados ao Usuário
							echo '
							 
										<tr>
										<td>
										  <input type="hidden" name="IdLinha" id="IdLinha" value="'.$l.'" />
										  <input type="hidden" name="IdDepartamento" id="IdDepartamento" value="'.$listaDepartamentos["id"].'" />
										  <input type="hidden" name="IdUsuario" id="IdUsuario3" value="'.$ListaUsuarios["id"].'" />
										  &nbsp;&nbsp;<font color="darkblue"><b>'.$listaDepartamentos["departamento"].'</b></font> 
										  </td>
										<td>
										  <button class="btn btn-danger btnVinculaUsuario" title="Excluir"><i class="fa fa-trash" aria-hidden="true"></i></button>
										</td>
										</tr>
									
							';
					}
					echo '</tbody>
					</table>
				</div>
				</td>
			</tr>';

				}

      
            
       
          $l = $l+1;
      }
    // FIM Busncando os Usuários cadastrados //		
?>

<p style="display: none;">* Clique em cima do nome do usuário para verificar quais departamentos ele está vinculado.</p>

</tbody>
  </table>




  <script type='text/javascript' src="js/funcoes.js"></script>
<script>
    // JavaScript Document
$( document ).ready(function() {		
	// Exclusão de Usuário //
	$('.ConfirmaExclusaoUsuario').on('click', function (){
	    var id = $(this).parent().parent("tr").find('#IdUsuario').val();
	
	 
		ConfirmarDados('Confirmação', 'Deseja realmente Remover este usuário?', function (data) {
		  if (data) {      
			  $.post("cadastros/usuarios/excluir.php",{IdUsuario:id},function(resultado){     
				     
			var mensagem  = "<strong>Usuário Removido com sucesso!</strong>";
			var mensagem2 = 'Falha ao Remover Usuário!';					
			if (resultado == 4) {
				mostraDialogo("Você não pode Remover o Administrador Principal", "warning", 2500);	
			}else if (resultado == 3) {
				mostraDialogo("Você não pode Remover seu Próprio usuário", "warning", 2500);	
			}else if (resultado == 2) {
				mostraDialogo(mensagem, "success", 2500);	
				$.ajax("cadastros/usuarios/listar.php").done(function(data) {
                        $('#ListaUsuarios').html(data);					
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
	
	// Alteração de Usuário //
	$('.botaoAlterarUsuario').on('click', function (){
		// Busco os dados do Produto Selecionado  
		var id = $(this).parent().parent("tr").find('#IdUsuario').val();

		$("#btnCancelar").css({"visibility" : "visible"});
		//$(".msgValida").css({"display" : "none"});

		$.getJSON('cadastros/usuarios/carregardados.php?id='+id, function(registro){			
			// Carregando os Dados //
			$("#id_usuarios").val(registro.id);
			$("#nome_usuario").val(registro.nome);
			$("#nome_usuario").trigger('blur');
			$("#login").val(registro.login);
			$("#login").trigger('blur');
			$("#email").val(registro.email);
			$("#email").trigger('blur');
			$("#senha").val(registro.senha);
			$("#senha").trigger('blur');
			$("#senha2").val(registro.senha);
			$("#senha2").trigger('blur');
			$("#perfil").val(registro.perfil);
			if (registro.situacao=='A'){
				$("#usuario_ativo").prop("checked", true);
			}else{
				$("#usuario_ativo").prop("checked",false);
			}
		});
			  
		// Mudo a Ação para Alterar    
		$("#acaoUsuario").val("2");
		$("#nome_usuario").focus();
	});
	// FIM Alteração de Usuário //

	// Fechar Cadastro do Usuário //
	$('#btnFecharCadastroUsuario').on('click', function (){
		$("#ListaUsuarios").css("display","block");
		$("#FormUsuarios").css("display","none");
	});
	$('#btnCancelaRemoveUsuario').on('click', function (){
		// Fechando a Modal de Confirmação //
		$('#modalUsuarioExclusao').attr('style', 'display: none');
		
		$("#ListaUsuarios").css("display","block");
		$("#FormUsuarios").css("display","none");
	});
	// FIM Fechar Cadastro do Usuário //
	
	// Remover Vínculo do Usuário //
	$('.btnVinculaUsuario').on('click', function (){
		if( confirm('Deseja remover o departamento selecionado a este usuário?') ){					
			var idUsuario = $(this).closest("tr").find('#IdUsuario3').val(),
			    idDepartamento = $(this).closest("tr").find('#IdDepartamento').val(),
				idLinha        = $(this).closest("tr").find('#IdLinha').val();;


			$.post("cadastros/usuarios/excluirVinculo.php"
				, {IdUsuario:idUsuario,idDepartamento:idDepartamento}
				, function(resultado){
				var mensagem  = "<strong>Vinculo com departamento Removido com sucesso!</strong>";
				var mensagem2 = 'Falha ao Remover Vinculo do Departamento!';
				//alert(resultado);
				if( resultado == 2 ){
					mostraDialogo(mensagem, "success", 2500);	

					$.ajax("cadastros/usuarios/listar.php").done(function(data) {
						$('#ListaUsuarios').html(data);
						$("#detail"+idLinha).toggleClass("show");
					});
				}
				else{ mostraDialogo(mensagem2, "danger", 2500); }
			});
		}
  	});
	// FIM Remover Vínculo do Usuário //
	
	// Incluir Vínculo do Usuário //
	$('.btnIncluirDepartamento').on('click', function (){
	 	var idUsuario      = $(this).closest("tr").find('#IdUsuario').val(),
	 		idDepartamento = $(this).closest("tr").find('#sltDepartamentos').val(),
			idLinha        = $(this).closest("tr").find('#idLinha').val();

		// Validando a escolha do Departamento //
		if( idDepartamento === "0" ){
			mostraDialogo("Por favor, escolha corretamente um Departamento!", "danger", 2500);
		}
		else{
			$.post("cadastros/usuarios/salvarVinculo.php"
				, {IdUsuario:idUsuario,idDepartamento:idDepartamento}
				, function(resultado){
					var mensagem  = "<strong>Vinculo com departamento Cadastrado com sucesso!</strong>";
					var mensagem2 = 'Falha ao Remover Vinculo do Departamento!';
					var mensagem3 = 'Usuário já vinculado a este Departamento!';              
				if( resultado == 1 ){
					mostraDialogo(mensagem, "success", 2500);
					$.ajax("cadastros/usuarios/listar.php").done(function(data) {
                        $('#ListaUsuarios').html(data);
						$("#detail"+idLinha).toggleClass("show");
                    });

				}
				else if( resultado == 2 ){ mostraDialogo(mensagem3, "danger", 2500); }
				else{ mostraDialogo(mensagem2, "danger", 2500); }
			});
		}
  	});
  	// Incluir Vínculo do Usuário //
});
</script>




    
    
  

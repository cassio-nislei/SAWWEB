<span id="msgContatos"></span>

<?php
	// Quando vier pela Pesquisa, passa por aqui //
	if( !isset($conexao) ){
		// Requires //
		require_once("../includes/padrao.inc.php");
	}

	// Definições de Variáveis //
		$pesquisa = isset($_POST["pesquisaContato"]) ? $_POST["pesquisaContato"] : "";			
		$qtdeContatos = 0;
		$fotoPerfil = fotoPerfil;

		// FIM Definições de Variáveis //
		if (isset($_POST["etiqueta"])){
			$etiqueta = $_POST["etiqueta"];
		}else{
			$etiqueta =  0;
		}

		if (isset($_POST["tipo_pesquisa"])){
			$tipo_pesquisa = $_POST["tipo_pesquisa"];
		}else{
			$tipo_pesquisa = 0;
		}

		if ($pesquisa==""){
			$pesquisa=="%";
		}


        $Consulta = "SELECT tbc.*, tbe.cor, tbe.descricao as etiqueta  FROM tbcontatos tbc 
			   left join tbetiquetascontatos tec on tec.numero = tbc.numero
               left join tbetiquetas tbe on tbe.id = tec.id_etiqueta ";
			   if ($tipo_pesquisa==1){	//Pesquisa por Telefone		
		      	   $Consulta = $Consulta ." WHERE (tbc.numero LIKE '%".$pesquisa."%')";
				   if ($etiqueta != '0'){
					$Consulta = $Consulta ." AND  tbe.cor = '$etiqueta'";  
				   }
				   $Consulta = $Consulta . "group by tbc.numero ";
				}else if ($tipo_pesquisa==2){ //CPF ou CNPJ
				  $Consulta = $Consulta ." WHERE (cpf_cnpj LIKE '%".$pesquisa."%')";
				  if ($etiqueta != '0'){
					$Consulta = $Consulta ." AND  tbe.cor = '$etiqueta'";  
				   }
				   $Consulta = $Consulta . "group by tbc.numero ";
				}else if ($tipo_pesquisa==2){ //CPF ou CNPJ
				}else{ //Aqui a Consulta por nome
					$Consulta = $Consulta ." WHERE (upper(nome) LIKE upper('%".$pesquisa."%')) ";
					if ($etiqueta != '0'){
						$Consulta = $Consulta ." AND  tbe.cor = '$etiqueta'";  
					   }
					$Consulta = $Consulta . "group by tbc.numero ";
					$Consulta = $Consulta . "  ORDER BY POSITION(upper('$pesquisa') IN upper(nome) ), upper(nome)";
				}
				
				
				$Consulta = $Consulta . "LIMIT 30";
		
		//	echo $Consulta;

		$qryContatos = mysqli_query($conexao, $Consulta	);


	

	$qtdeContatos = mysqli_num_rows($qryContatos);
							
	if( $qtdeContatos == 0 ){
		echo "<font size=\"2\" color=\"#CCC\"><b>&nbsp;&nbsp;&nbsp;&nbsp;Nenhum contato encontrado</b></font>";
	}

	// Aqui faz a listagem dos Atendimentos Pendentes
	while( $registros = mysqli_fetch_object($qryContatos) ){
		// Pego a foto de perfil //
		$cordefundo = rand ( 100000 , 999999 );
		$estiloPerfil = 'style="font-size: 1.3em;display: -webkit-flex;
			display: -ms-flexbox;
				   display: flex;
	   
		   -webkit-align-items: center;
			 -webkit-box-align: center;
			-ms-flex-align: center;
			   align-items: center;
		   
		 justify-content: center;color:white; background-color:'.$cordefundo.'"';
		if( $_SESSION["parametros"]["exibe_foto_perfil"] ){
			$fotoPerfil = getFotoPerfil($conexao, $registros->numero);
			if (strlen($fotoPerfil)<40){
                $perfil = RetornaNomeAbreviado($registros->nome); 
			}else{
				$perfil = '<img src="'.$fotoPerfil.'" class="rounded-circle user_img">';
				$estiloPerfil = 'style="color:white; background-color:'.$cordefundo.'"';
			}			
			
		}
		else{ 
			$perfil = RetornaNomeAbreviado($registros->nome); 			
		}

		$etiqueta = '';
		//BUco as etiquetas vinculadas ao numero
		$qryEtiquetas = mysqli_query($conexao,"select te.cor, te.descricao as etiqueta from tbetiquetascontatos tec
		inner join tbetiquetas te on te.id = tec.id_etiqueta
		where tec.numero = '$registros->numero'");

		while( $registrosEtiqueta = mysqli_fetch_object($qryEtiquetas) ){

		if ($registrosEtiqueta->cor != ''){
			$etiqueta .= '<i class="fas fa-tag" style="color:'.$registrosEtiqueta->cor.'" alt="'.$registrosEtiqueta->etiqueta.'" title="'.$registrosEtiqueta->etiqueta.'"></i>';
		}

	}
		
		
		echo '<div class="contact-item" data-numero="'.$registros->numero.'" data-nome="'.$registros->nome.'">
				<div class="dIyEr lnkDivContato">
					<div class="_1WliW" style="height: 49px; width: 49px;">
						<img src="#" class="Qgzj8 gqwaM photo" style="display:none;">
						<div class="_3ZW2E" class="" '.$estiloPerfil.'>
							<span data-icon="default-user">
							  '.$perfil.'
							</span>
						</div>
					</div>
				</div>
				<div class="_3j7s9 lnkDivContato" style="border:0">
					<div class="_2FBdJ">
						<div class="_25Ooe">
							<span dir="auto" title="'.$registros->nome.'" class="_1wjpf">'.$registros->nome.'</span>
						</div>
					</div>
					<div class="_1AwDx">
						<div class="_itDl">
							<span class="_2_LEW last-message">
								<span dir="ltr" class="_1wjpf _3NFp9">'.Mask($registros->numero).'</span>
							</span>
						</div>
					</div>
				</div>
				<div style="margin-top: 25px;">
				  '.$etiqueta.'
					<button class="add" style="padding: 0 10px;" title="Enviar">
						<span uk-icon="forward" class="btnEnviarContato _blue"></span>
					</button>
					<button class="add" style="padding: 0 10px;" title="Editar">
						<span uk-icon="pencil" class="btnAlterarContato"></span>
					</button>
					<button class="add" style="padding: 0 10px;" title="Excluir">
						<span uk-icon="trash" class="btnExclusaoContato"></span>
					</button>
				</div>
			</div>';
	}
?>

<script type='text/javascript' src="cadastros/contatos/contatosList.js"></script>
<script>
	$(document).ready(function(){
		$("#qtdeContatos").html(<?php echo $qtdeContatos; ?>);

		// Criar um Novo Atendimento //
		$('.lnkDivContato').click(function(){
			var numero = $(this).parent().data("numero");
			var nome = $(this).parent().data("nome");

			// Salva os dados nos seus devidos 'input:hidden' //
			$("#s_numero").val(numero);
        	$("#s_nome").val(nome);
			
			$('.lnkDivContato').removeClass( "active" );
			$(this).addClass( "active" );

			// Verifica se um 'Operador' pode 'Iniciar uma Conversa' //
			if( $("#perfilUsuario").val() == 1
				&& $("#parametrosIniciarConversa").val() == 0 ){
				mostraHistorico();
			}
			else{
				if( confirm('Clique em [OK] para iniciar um <<< Novo Atendimento >>>. Ou clique em [Cancelar] para ter acesso ao <<< Histórico das Conversas >>> com este Contato!') ){
					//Faz a Inicialização do atendimento
					
					$('.voltar').click();
					$.post("atendimento/gerarAtendimento.php",{numero:numero,nome:nome}, function(idAtendimento){
						// Atualiza a notificação
						if( parseInt(idAtendimento) > 0 ){
							$('#not'+idAtendimento).text(""); //limpa a qtd quando de notificações abre a conversa
							$('#AtendimentoAberto').html("<div class='spinner-border text-primary' role='status'><span class='sr-only'>Carregando ...</span></div>");
							
							$.ajax("atendimento/conversa.php?id="+idAtendimento+"&id_canal=1&numero&numero="+encodeURIComponent(numero)+"&nome="+encodeURIComponent(nome)).done(function(data) {
								$('#AtendimentoAberto').html(data);

								$.ajax("atendimento/atendendo.php").done(function(data) {
									$('#ListaEmAtendimento').html(data);
								});
							});
						}
						else if( idAtendimento == "erro" ){ mostraDialogo("Erro ao tentar Iniciar o Atendimento!", "danger", 2500); }
						else{ mostraDialogo(idAtendimento, "danger", 10000); }
					});
				}
				else{ mostraHistorico(); }
			}
			// FIM Verifica se um 'Operador' pode 'Iniciar uma Conversa' //

			function mostraHistorico(){
				// Abre a Modal do Histórico //
				abrirModal('#modalHistorico');

				// Carrega o Histórico das Conversas //
				$.ajax("atendimento/historico.php?numero="+numero+"&nome="+nome).done(function(data) {
					$('#HistoricoAberto').html(data);
				});
			}
		});
		// FIM Criar um Novo Atendimento //

		// Enviando o Contato //
		$(".btnEnviarContato").click(function(){
			// Registrando o Envio do Contato //
			var numero = $("#s_numero").val();
			var nome = $("#s_nome").val();
			var idAtend = $("#s_id_atendimento").val();
			var contatoNumero = $(this).parent().parent().parent().data("numero");
			var contatoNome = $(this).parent().parent().parent().data("nome");
			var sobreNomeVC = "";
			var nomeVC = contatoNome;
			var id_canal = $("#s_id_canal").val();

			// Tratamento do Nome //
				var arrNome = contatoNome.split(" ");

				if( arrNome.length > 1 ){
					sobreNomeVC = arrNome[(arrNome.length)-1];
					nomeVC = $.trim(contatoNome.replace(sobreNomeVC, ""));
				}
			// FIM Tratamento do Nome //

			// Tratamento do Número //
				numeroVC = contatoNumero;
			// FIM Tratamento do Número //

			// Fecha a Aba de Contatos //
			$('.voltar').click();

			// Registra o Contato para Envio //
			$.post("includes/enviarContato.php",{numero:numero,nome:nome,idAtend:idAtend,contatoNome:contatoNome,contatoNumero:contatoNumero,id_canal:id_canal}, 
				function(retorno){
				if(retorno != "9"){
					mostraDialogo("Contato enviado com Sucesso!", "success", 2500);	

					// Envia a Mensagem //
					$('#msg').val("BEGIN:VCARDVERSION:3.0N:"+sobreNomeVC+";"+nomeVC+";;;FN:"+contatoNome+"TEL;type=CELL;waid="+contatoNumero+":"+numeroVC+"END:VCARD");
					$('#btnEnviar').click();
				}
				else{ mostraDialogo("Erro ao tentar enviar o Contato!", "danger", 2500); }
			});
		});
		// FIM Enviando o Contato //

		function ajustarCoresSelect2() {
    
	var selectedColors = {};
  
	  $('.pesqEtiquetas').on('select2:select', function(e) {
	  var selectedOption = e.params.data.element;
	  var selectedColor = $(selectedOption).attr('data-color');
	  var selectedId = $(selectedOption).val();
	  var selectedTag = $(this).next().find('.select2-selection__choice[title="' + e.params.data.text + '"]');
	  selectedColors[selectedId] = selectedColor;
	  for (var id in selectedColors) {
		var selectedOption = $(this).find('option[value="' + id + '"]');
		selectedOption.css('background-color', selectedColors[id]);
		var selectedTag = $(this).next().find('.select2-selection__choice[title="' + selectedOption.text() + '"]');
		selectedTag.css('background-color', selectedColors[id]);
	  }
	  selectedTag.css('background-color', selectedColor);
	});
  }
  


		// JavaScript Document
// Abrindo a tela para Alteração de Contato //
$('.btnAlterarContato').on('click', function (){
        // Abrindo a Modal //
        abrirModal('#modalContato');


		  // Recupera o 'Número' do Contato Selecionado //
	     var id = $(this).parent().parent().parent().data("numero");
        if (id.length ==0){
			alert("Erro ao selecionar contato, tente novamente");
			return false;
		}

		$('#id_etiqueta2').val(null);

        // Carregando a Tela para Edição //
        $.ajax("cadastros/contatos/index.php").done(function(data) {
            $('#modalContato').html(data);
      
          
      
         
        // Alterando o Título do Cadastro //
        $("#titleCadastroContato").html("Alteração de Contato");

        $.getJSON('cadastros/contatos/ContatoController.php?id='+id
            , function(registro){
            // Carregando os Dados no Form //
            //CArrego as TAGS selecionadas
			$.post('cadastros/contatos/listaEtiquetas.php',{numero_contato:registro.numero}, function(retorno){
				if (retorno!=0){        
					$('#id_etiqueta2').val(retorno.split(',')).trigger("change");			
  		
				}		          
               
            });

            $("#idContato").val(registro.numero);
            $("#numero_contato").val(registro.numero);
            $("#nome_contato").val(registro.nome);
			$("#cpf_cnpj").val(registro.cpf_cnpj);
			$("#razao_social").val(registro.razao_social);
			  
            // Mudo a Ação para Alterar    
            $("#acaoContato").val("2");
            $("#numero_contato").focus();
        });
	  }); //Fim do Carregamento da tela
    });
// FIM Abrindo a tela para Alteração de Contato //

// Modal Exclusão //
    $('.btnExclusaoContato').on('click', function (){
        abrirModal("#modalContatoExclusao");

        // Recupera o 'Número' do Contato Selecionado //
        var id = $(this).parent().parent().parent().data("numero");
        $("#idContatoExcluir").val(id);
    });
// FIM Modal Exclusão //



								



	});
</script>
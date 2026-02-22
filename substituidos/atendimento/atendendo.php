<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Definições de Variáveis //
		$id_usuario = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : "";
		$htmlConversas = "";
		//Perfil 0 = Administrador e Perfil 2 = Corrdenador
		$permissaoAdmin = ($_SESSION["usuariosaw"]["perfil"] == 0 || $_SESSION["usuariosaw"]["perfil"] == 2) && $_SESSION["parametros"]["mostra_todos_chats"] == 1 ? '' : "AND ta.id_atend = '".$id_usuario."'";
		$ultHora = null;
		$ultMsg = null;
	// FIM Definições de Variáveis //
	$filtroDepartamento = '';
	
	if ($_SESSION["parametros"]["nao_usar_menu"]==0 || $_SESSION["usuariosaw"]["perfil"] > 0){
       $filtroDepartamento = ' AND ta.setor IN(SELECT id_departamento FROM tbusuariodepartamento WHERE id_usuario = '.$id_usuario.')';
	}


	$strAtendimento = "SELECT taa.id, taa.numero, ta.nome, CASE WHEN  tc.nome IS NULL then ta.nome when tc.nome = '' then ta.nome else tc.nome END  AS nomeContato, ta.canal, ta.id_atend, ta.nome_atend, td.departamento, tfp.foto AS foto_perfil, 
						(SELECT nome FROM tbusuario WHERE id = ta.id_atend) AS operador,
						(SELECT MAX(hr_msg) FROM tbmsgatendimento WHERE id = taa.id) AS ordem
						, tbe.cor, tbe.descricao as etiqueta
						FROM tbatendimentoaberto taa
							INNER JOIN tbatendimento ta ON(taa.id = ta.id) AND taa.numero = ta.numero
							LEFT JOIN tbdepartamentos td ON(td.id = ta.setor)
							LEFT JOIN tbcontatos tc ON taa.numero = tc.numero
							LEFT JOIN tbfotoperfil tfp ON(tfp.numero = taa.numero)
							LEFT JOIN tbetiquetas tbe on tbe.id = tc.idetiqueta
								WHERE ta.situacao = 'A' ${permissaoAdmin}
								$filtroDepartamento
									ORDER BY ordem DESC";
	$qryAtendimento = mysqli_query(
		$conexao
		, $strAtendimento
	);
							
	if( mysqli_num_rows($qryAtendimento) == 0 ){
		echo "<font size=\"2\" color=\"#CCC\"><b>&nbsp;&nbsp;&nbsp;&nbsp;Nenhum atendimento iniciado</b></font>";
	}

	// Aqui faz a listagem dos Atendimentos Pendentes //
	while( $registros = mysqli_fetch_object($qryAtendimento) ){
		
			
		// Busco a QTD de mensagens novas //
		$qtdNovas = mysqli_query(
			$conexao
			, "SELECT count(id) AS qtd_novas 
				FROM tbmsgatendimento 
					WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' AND id_atend = 0 AND visualizada = false"
		);
		
		$not = mysqli_fetch_array($qtdNovas);

		if( $not["qtd_novas"] > 0 ){
			$notificacoes = '<span class="OUeyt messages-count-new">'.$not["qtd_novas"].'</span>';

			// Dispara o Alerta Sonoro - Se definido no Painel de Configurações //
			if( $_SESSION["parametros"]["alerta_sonoro"] ){
				echo '<iframe src="https://player.vimeo.com/video/402630730?autoplay=1&loop=0&autopause=1" style="display: none" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
			}
		}
		else{ $notificacoes = ""; }

		// Verificando a última Mensagem //
			$qryUltMsg = mysqli_query(
				$conexao
				, "SELECT msg, DATE_FORMAT(hr_msg, '%H:%i') AS hora,
						 TIMESTAMPDIFF(MINUTE, dt_msg,
							NOW()) AS MINUTOS_MSG
				
				  FROM tbmsgatendimento 
					WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' and id_atend = '".$registros->id_atend."'
						ORDER BY seq DESC
							LIMIT 1"
			);
			
			// Verifica se Existe Resultado //
			$ultMsg	= '';
			if( mysqli_num_rows($qryUltMsg) > 0 ){
				$arrUltMsg = mysqli_fetch_array($qryUltMsg);
				$ultHora = $arrUltMsg['hora'];
				$ultMsg	= $arrUltMsg['msg'];
				
				// Encurta a MSG caso ela possua mais que 40 caracteres //
				if( strlen($ultMsg) > 40 ){ $ultMsg = substr($ultMsg, 0, 40) . "..."; }
			}
		// FIM Verificando a última Mensagem //

		// Tratamento do Nome //
			if( $registros->nomeContato !== "" ){ $registros->nome = $registros->nomeContato; }
		// FIM Tratamento do Nome //

        //Mostro a etiqueta de acordo com a selecionada no
		$etiqueta = '';
		if ($registros->cor != ''){
			$etiqueta = '<i class="fas fa-tag" style="margin-left:6px;color:'.$registros->cor.'" alt="'.$registros->etiqueta.'" title="'.$registros->etiqueta.'"></i>';
		}

		//MOstro o relógio indicando a qtd de minutos sem atendimento
		@$msgtempoEspera = trataTempoOciosodoAtendente($arrUltMsg['MINUTOS_MSG']);
		@$tempoOcioso = '<i class="fas fa-solid fa-clock  fa-1x" alt="'.$msgtempoEspera[0].'"  title="'.$msgtempoEspera[0].'" style="margin-left:1px;'.$msgtempoEspera[1].'"></i>';


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
		
		// Saída HTML //
			echo '<div class="contact-item linkDivAtendendo">
					<input type="hidden" id="numero" value="'.$registros->numero.'">
					<input type="hidden" id="id_atendimento" value="'.$registros->id.'">
					<input type="hidden" id="nome" value="'.limpaNome($registros->nome).'">
					<input type="hidden" id="id_canal" value="'.$registros->canal.'">

					<div class="dIyEr">
						<div class="_1WliW" style="height: 49px; width: 49px;">
							<img src="#" class="Qgzj8 gqwaM photo" style="display:none;">
							<div class="_3ZW2E" '.$estiloPerfil.'>
								'.$perfil.'
							</div>
						</div>
					</div>
					<div class="_3j7s9">
						<div class="_2FBdJ">
							<div class="_25Ooe">
								<span dir="auto" title="'.limpaNome($registros->nome).' '.Mask($registros->numero).'" class="_1wjpf">
								    '.$tempoOcioso.'
								    '.getCanal($conexao, $registros->canal).limpaNome($registros->nome).'
									'.$etiqueta.'									
								</span>
								<span style="font-size:.8rem; color: #808080;">'.limpaNome($registros->operador) .'</span>
							</div>
							<div class="_3Bxar">
								<span class="_3T2VG" id="hor'.$registros->numero.'">'.$ultHora.'</span>
							</div>
						</div>
						<div class="_1AwDx">
							<div class="_itDl">
								<span class="_2_LEW last-message">
									<div class="_1VfKB"></div>
									<span dir="ltr" class="_1wjpf _3NFp9" id="msg'.$registros->numero.'" style="width: 1px; padding: 0;">'.$ultMsg.'</span>
									<div class="_3Bxar">
										<span>
											<div class="_15G96" id="not'.$registros->numero.'">'.$notificacoes.'</div>
										</span>
									</div>
								</span>
							</div>
						</div>
					</div>
				</div>';
	}
?>
<script>
	$(document).ready(function(){	
		$('.linkDivAtendendo').click(function(){
			// Para inibir múltiplos clicks no Atendimento //
			var find = /carregando/g;

			if( !find.test($(this).attr('class')) ){
				var numero = $(this).find("#numero").val();
				var id_atendimento = $(this).find("#id_atendimento").val();
				var nome = $(this).find("#nome").val();
				var id_canal = $(this).find("#id_canal").val();
				var compareA = numero + id_canal;
				var compareB = $("#s_numero").val() + $("#s_id_canal").val();
			
				// Só permite carregar a conversa se a mesma ainda não foi carregada //
				if( compareA !== compareB ){
					$('#AtendimentoAberto').html("Carregando conversa ... Aguarde um momento, por favor!");
					$('.linkDivAtendendo').removeClass( "active" );
					$(this).addClass( "active carregando" );
					$('#not'+id_atendimento).text("");
					
					//Faz a Inicialização do atendimento
					$.ajax("atendimento/conversa.php?id="+id_atendimento+"&id_canal="+id_canal+"&numero="+numero+"&nome="+encodeURIComponent(nome)).done(
						function(data) {
						$('#AtendimentoAberto').html(data);
						$('.linkDivAtendendo').removeClass( "carregando" );
					});
				}
				// FIM Só permite carregar a conversa se a mensma ainda não foi carregada //
			}
			// FIM Para inibir múltiplos clicks no Atendimento //
		});
	});
</script>

<div id="contacts-messages-list" class="contact-list" style="z-index: 326; height: 72px; transform: translate3d(0px, 0px, 0px);">
    <?php echo $htmlConversas; ?>
</div>
<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Definições de Variáveis //
		$ultHora = null;
		$ultMsg = null;
	// FIM Definições de Variáveis //

	$qryTriagem = mysqli_query(
		$conexao
		, "SELECT 	taa.id, 
		taa.numero, 
        ta.nome,
        CASE 	WHEN tc.nome IS NULL AND ta.nome <> '' THEN ta.nome
				WHEN tc.nome = '' AND ta.nome <> '' THEN ta.nome 
                WHEN tc.nome IS NULL AND ta.nome IS NULL THEN taa.numero
                WHEN tc.nome = '' AND ta.nome = '' THEN taa.numero
				ELSE tc.nome END AS nomeContato, 
        ta.canal, 
        'Triagem' AS departamento, 
        /*tfp.foto AS foto_perfil ,*/ 
		tbe.cor, tbe.descricao as etiqueta
		FROM tbatendimentoaberto taa
			INNER JOIN tbatendimento ta 
				ON taa.id = ta.id AND taa.numero = ta.numero
			LEFT JOIN tbcontatos tc 
				ON taa.numero = tc.numero
			/*LEFT JOIN tbfotoperfil tfp 
				ON tfp.numero = taa.numero*/
			LEFT JOIN tbetiquetas tbe
				ON tbe.id = tc.idetiqueta
		WHERE (ta.situacao = 'T') AND (ta.setor = 0)
		ORDER BY dt_atend, hr_atend;"
	);

  /* Removi a exibição da mensagem porque na tela agora tem o contador
	if (!$qryTriagem || mysqli_num_rows($qryTriagem)==0){
		// echo "<font size=\"2\" color=\"#CCC\"><b>&nbsp;&nbsp;&nbsp;&nbsp;Nenhum atendimento para Triagem</b></font>";
	}
	*/


	// Aqui faz a listagem dos Atendimentos Pendentes //
	while ($registros = mysqli_fetch_object($qryTriagem)){

		
		// Busco a QTD de mensagens novas para notificação Sonora //
		$qtdNovas = mysqli_query(
			$conexao
			, "SELECT count(id) AS qtd_novas 
				FROM tbmsgatendimento 
					WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' AND id_atend = 0 AND visualizada = false"
		);
		
		$not = mysqli_fetch_array($qtdNovas);

		if ($not["qtd_novas"] > 0){									 
			//$notificacoes = $not["qtd_novas"];
			$notificacoes = '<span class="OUeyt messages-count-new">'.$not["qtd_novas"].'</span>';

			// Dispara o Alerta Sonoro - Se definido no Painel de Configurações //
			if( $_SESSION["parametros"]["alerta_sonoro"] ){
				echo '<iframe src="https://player.vimeo.com/video/402630730?autoplay=1&loop=0&autopause=1" style="display: none" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
			}
		}
		else{ $notificacoes = ""; }
		// Fim da Notificação Sonora //

		// Verificando a última Mensagem //
			$qryUltMsg = mysqli_query(
				$conexao
				, "SELECT msg, DATE_FORMAT(hr_msg, '%H:%i') AS hora,
				    TIMESTAMPDIFF(MINUTE, dt_msg,
							NOW()) AS MINUTOS_MSG
				  FROM tbmsgatendimento 
					WHERE numero = '".$registros->numero."' AND id = '".$registros->id."'
						ORDER BY seq DESC
							LIMIT 1"
			);

			// Verifica se Existe Resultado //
			if( mysqli_num_rows($qryUltMsg) > 0 ){
				$arrUltMsg = mysqli_fetch_array($qryUltMsg);
				$ultHora = $arrUltMsg['hora'];
				$ultMsg	= $arrUltMsg['msg'];

				 //Trato a hora da Última mensagem de acordo com o Parametro
				//Verifico se é para Exibir o tempo da Última mensagem apenas quando for enviada pelos Clientes
				if( $_SESSION["parametros"]["contar_tempo_espera_so_dos_clientes"] ){
					$qryHoraUltMsg = mysqli_query(
						$conexao
						, "SELECT DATE_FORMAT(hr_msg, '%H:%i') AS hora,
								TIMESTAMPDIFF(MINUTE, dt_msg,
									NOW()) AS MINUTOS_MSG
						
						FROM tbmsgatendimento 
							WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' and id_atend = '0'
								ORDER BY seq DESC
									LIMIT 1"
					);	//Passo o ID_ATEND Zero para pegar a hora da mensagem enviada pelo Cliente
					
					$arrUltHoraCliente = mysqli_fetch_array($qryHoraUltMsg);
					$ultHora = $arrUltHoraCliente['hora'];
				}
                $regrex = '/\*(.*?)\*/';
				
				// Encurta a MSG caso ela possua mais que 40 caracteres //
				if( strlen($ultMsg) > 40 ){ 
					$ultMsg = substr($ultMsg, 0, 40) . "..."; 
					
					// Usa o REGEX Negrito:
					$ultMsg = preg_replace($regrex, '<b>$1</b>', $ultMsg); //Substituindo todos utilizando a expressão regular. By Marcelo 24/04/2023					
				}
			}
		// FIM Verificando a última Mensagem //

		// Tratamento do Nome //
			if( $registros->nomeContato !== "" ){
				$nomeExibicao = $registros->nomeContato; 				
			}
		// FIM Tratamento do Nome //

		        //Mostro a etiqueta de acordo com a selecionada no
				$etiqueta = '';
				//BUsco as etiquetas vinculadas ao numero
				$qryEtiquetas = mysqli_query($conexao,"select te.cor, te.descricao as etiqueta from tbetiquetascontatos tec
				inner join tbetiquetas te on te.id = tec.id_etiqueta
				where tec.numero = '$registros->numero'");
		
				while( $registrosEtiqueta = mysqli_fetch_object($qryEtiquetas) ){
		
				if ($registrosEtiqueta->cor != ''){
					$etiqueta .= '<i class="fas fa-tag" style="color:'.$registrosEtiqueta->cor.'" alt="'.$registrosEtiqueta->etiqueta.'" title="'.$registrosEtiqueta->etiqueta.'"></i>';
				}
		
			}
		//MOstro o relógio indicando a qtd de minutos sem atendimento
		@$msgtempoEspera = trataTempoOciosodoAtendente($arrUltMsg['MINUTOS_MSG']);
		@$tempoOcioso = '<i class="fas fa-solid fa-clock  fa-1x" alt="'.$msgtempoEspera[0].'"  title="'.$msgtempoEspera[0].'" style="margin-left:0px;'.$msgtempoEspera[1].'"></i>';


		$cordefundo = rand ( 100000 , 999999 );
		$estiloPerfil = 'style="font-size: 1.3em;display: -webkit-flex;
			display: -ms-flexbox;
				   display: flex;
	   
		   -webkit-align-items: center;
			 -webkit-box-align: center;
			-ms-flex-align: center;
			   align-items: center;
		   
		 justify-content: center;color:white; background-color:'.@$cordefundo.'"';
		if( $_SESSION["parametros"]["exibe_foto_perfil"] ){
			$fotoPerfil = getFotoPerfil($conexao, $registros->numero);
			if (strlen($fotoPerfil)<40){
                $perfil = RetornaNomeAbreviado($nomeExibicao); 
			}else{
				$perfil = '<img src="'.$fotoPerfil.'" class="rounded-circle user_img">';
				$estiloPerfil = 'style="color:white; background-color:'.@$cordefundo.'"';
			}		
			
		}
		else{ 
			$perfil = RetornaNomeAbreviado($nomeExibicao); 			
			
		}

		if ($registros->canal==0){
			//Se o Canal for igual a Zero, significa que o atendimento veio através do WEBCHAT
			$corDoNome = '<font style="color:#8B0000">'.getCanal($conexao, $registros->canal).limpaNome($nomeExibicao).'</font>';
		}else if ($registros->canal==1){
			$corDoNome = '<font style="color:#000000">'.getCanal($conexao, $registros->canal).limpaNome($nomeExibicao).'</font>';
		}else if ($registros->canal==2){
			$corDoNome = '<font style="color:#0000FF">'.getCanal($conexao, $registros->canal).limpaNome($nomeExibicao).'</font>';
		}else{
			$corDoNome = '<font style="color:#006400">'.getCanal($conexao, $registros->canal).limpaNome($nomeExibicao).'</font>';
		}
								  
		echo '<div class="contact-item linkDivTriagem">
				<input type="hidden" id="numero" value="'.$registros->numero.'">
				<input type="hidden" id="id_atendimento" value="'.$registros->id.'">
				<input type="hidden" id="nome" value="'.limpaNome($nomeExibicao).'">
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
					        <span dir="auto" title="'.limpaNome($nomeExibicao).' '.Mask($registros->numero).'" class="_1wjpf">
						  	    '.$tempoOcioso.'
								'.$corDoNome.'										
							</span>
							
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
									   <div>'.$etiqueta .'</div>
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
		$('.linkDivTriagem').click(function(){
			// Para inibir múltiplos clicks no Atendimento //
			var find = /carregando/g;
			var larguradatela = $(window).width();

			if( !find.test($(this).attr('class')) ){
				// Verifica se um Operador pode visualizar um Atendimento ainda em Triagem //
				if( $("#perfilUsuario").val() == 1
					&& $("#atendTriagem").val() == 0 ){
					mostraDialogo("Atenção: Apenas Administradores pode visualizar um Atendimento ainda sem Setor!", "danger", 3000);
				}
				else{
					var numero = $(this).find("#numero").val();
					var id_atendimento = $(this).find("#id_atendimento").val();
					var nome = $(this).find("#nome").val();
					var id_canal = $(this).find("#id_canal").val();
					var compareA = numero + id_canal;
					var compareB = $("#s_numero").val() + $("#s_id_canal").val();

				

					// Só permite carregar a conversa se a mesma ainda não foi carregada //
					if( compareA !== compareB ){
						$('#AtendimentoAberto').html("Carregando conversa ... Aguarde um momento, por favor!");
						$('.linkDivTriagem').removeClass( "active" );
						$(this).addClass( "active carregando" );
						
						$.ajax("atendimento/conversaTriagem.php?id="+id_atendimento+"&id_canal="+id_canal+"&numero="+numero+"&nome="+encodeURIComponent(nome)).done(
							function(data) {
								if (larguradatela < 801){ //Se a tela for menor qu 800pixels minimizo os atendimentos para ficar mais responsivo							
							       $('#btnMinimuiConversas').click();							
						        }
							$('#AtendimentoAberto').html(data);
							$('.linkDivTriagem').removeClass( "carregando" );
						});
					}
					// FIM Só permite carregar a conversa se a mensma ainda não foi carregada //
				}
			}
			// FIM Para inibir múltiplos clicks no Atendimento //
		});
	});
</script>
<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Definições de Variáveis //
		$id_usuario = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : "";
		$htmlConversas = "";
		//Perfil 0 = Administrador e Perfil 2 = Corrdenador
		$mostra_todos_chats = isset($_SESSION["parametros"]["mostra_todos_chats"]) ? $_SESSION["parametros"]["mostra_todos_chats"] : 0;
		$permissaoAdmin = (isset($_SESSION["usuariosaw"]["perfil"]) && ($_SESSION["usuariosaw"]["perfil"] == 0 || $_SESSION["usuariosaw"]["perfil"] == 2) && $mostra_todos_chats == 1) ? '' : "AND ta.id_atend = '".$id_usuario."'";
		$ultHora = null;
		$ultMsg = null;
	// FIM Definições de Variáveis //
	$filtroDepartamento = '';
	
	$nao_usar_menu = isset($_SESSION["parametros"]["nao_usar_menu"]) ? $_SESSION["parametros"]["nao_usar_menu"] : 0;
	$perfil = isset($_SESSION["usuariosaw"]["perfil"]) ? $_SESSION["usuariosaw"]["perfil"] : 1;
	
	if ($nao_usar_menu==0 || $perfil > 0){
       $filtroDepartamento = ' AND ta.setor IN(SELECT id_departamento FROM tbusuariodepartamento WHERE id_usuario = '.$id_usuario.')';
	}

                                                                                                                                                                                                                                                    //Add Marcelo NOME_EMPRESA 
	$strAtendimento = "SELECT taa.id, taa.numero, ta.nome, CASE WHEN  tc.nome IS NULL then ta.nome when tc.nome = '' then ta.nome else tc.nome END  AS nomeContato, ta.canal, ta.id_atend, ta.nome_atend, td.departamento, /*tfp.foto AS foto_perfil,*/ ta.nome_empresa,  
						(SELECT nome FROM tbusuario WHERE id = ta.id_atend) AS operador,
						(SELECT MAX(hr_msg) FROM tbmsgatendimento WHERE id = taa.id) AS ordem
						, tbe.cor, tbe.descricao as etiqueta
						FROM tbatendimentoaberto taa
							INNER JOIN tbatendimento ta ON(taa.id = ta.id) AND taa.numero = ta.numero
							LEFT JOIN tbdepartamentos td ON(td.id = ta.setor)
							LEFT JOIN tbcontatos tc ON taa.numero = tc.numero
							/*LEFT JOIN tbfotoperfil tfp ON(tfp.numero = taa.numero)*/
							LEFT JOIN tbetiquetas tbe on tbe.id = tc.idetiqueta
								WHERE ta.situacao = 'A' {$permissaoAdmin}
								$filtroDepartamento
									ORDER BY ordem DESC";
	$qryAtendimento = mysqli_query(
		$conexao
		, $strAtendimento
	);
	
	/* Removi a exibição da mensagem porque na tela agora tem o contador
	if( @mysqli_num_rows($qryAtendimento) == 0 ){
		echo "<font size=\"2\" color=\"#CCC\"><b>&nbsp;&nbsp;&nbsp;&nbsp;Nenhum atendimento iniciado</b></font>";
	}
	*/

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
			$alerta_sonoro = isset($_SESSION["parametros"]["alerta_sonoro"]) ? $_SESSION["parametros"]["alerta_sonoro"] : 0;
			if( $alerta_sonoro ){
				echo '<iframe src="https://player.vimeo.com/video/402630730?autoplay=1&loop=0&autopause=1" style="display: none" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
			}


			//Se recebeu uma nova mensagem em horario de almoço, respondo com a mensagem de almoço
			//echo "Variavel".$_SESSION["usuariosaw"]["em_almoco"];
			$em_almoco = isset($_SESSION["usuariosaw"]["em_almoco"]) ? $_SESSION["usuariosaw"]["em_almoco"] : "false";
			$msg_almoco = isset($_SESSION["usuariosaw"]["msg_almoco"]) ? $_SESSION["usuariosaw"]["msg_almoco"] : "";
			if (($em_almoco=="true") && ($id_usuario==$registros->id_atend)){
				$newSequence = newSequence($conexao, $registros->id,$registros->numero, $registros->canal); // Gera a sequencia da mensagem
				$msgAlmoco = $msg_almoco;
				$gravaMsgAlmoco = mysqli_query(
					$conexao, 
					"INSERT INTO tbmsgatendimento(id,seq,numero,msg,  nome_chat,situacao, dt_msg,hr_msg,id_atend,canal)
						VALUES('".$registros->id."','".$newSequence."' ,'".$registros->numero."', '".$msgAlmoco."', 
								'".$registros->nomeContato."' ,'E',NOW(),CURTIME(),'".$id_usuario."','".$registros->canal."')"
				)or die(mysqli_error($conexao));
				//Fim da Gravação de Mensagem de Horario de almoço
			}
			


		}
		else{ $notificacoes = ""; }

		// Verificando a última Mensagem //
		//Consulta alterada pelo Marcelo para deixar fixo o id_atend = 0
			$qryUltMsg = mysqli_query(
				$conexao
				, "SELECT msg, DATE_FORMAT(hr_msg, '%H:%i') AS hora,
						 TIMESTAMPDIFF(MINUTE, dt_msg,
							NOW()) AS MINUTOS_MSG, id_atend
				
				  FROM tbmsgatendimento 
					-- WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' and id_atend = '".$registros->id_atend."'
					WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' 
						ORDER BY seq DESC
							LIMIT 1"
			);
            
	
			
			
			// Verifica se Existe Resultado //
			$ultMsg	= '';
			if( mysqli_num_rows($qryUltMsg) > 0 ){
				$arrUltMsg = mysqli_fetch_array($qryUltMsg);
				$ultHora = $arrUltMsg['hora'];
				$ultMsg	= $arrUltMsg['msg'];
				$id_atendente = $arrUltMsg['id_atend'];  //Novo pegar o id do Atendente


                 //Trato a hora da Última mensagem de acordo com o Parametro
				//Verifico se é para Exibir o tempo da Última mensagem apenas quando for enviada pelos Clientes
				if( $_SESSION["parametros"]["contar_tempo_espera_so_dos_clientes"] ){
					$qryHoraUltMsg = mysqli_query(
						$conexao
						, "SELECT DATE_FORMAT(hr_msg, '%H:%i') AS hora,
								TIMESTAMPDIFF(MINUTE, dt_msg,
									NOW()) AS MINUTOS_MSG, id_atend
						
						FROM tbmsgatendimento 
							WHERE numero = '".$registros->numero."' AND id = '".$registros->id."' and id_atend = '0'
								ORDER BY seq DESC
									LIMIT 1"
					);	//Passo o ID_ATEND Zero para pegar a hora da mensagem enviada pelo Cliente
					
					$arrUltHoraCliente = mysqli_fetch_array($qryHoraUltMsg);
					$ultHora = $arrUltHoraCliente['hora'] ?? 0;
				}
                $regrex = '/\*(.*?)\*/';


				
				// Encurta a MSG caso ela possua mais que 40 caracteres //
				if( strlen($ultMsg) > 40 ){ 
					$ultMsg = str_replace("\\n"," ",$ultMsg);
					$ultMsg = substr($ultMsg, 0, 40) . "..."; 
					// Usa o REGEX Negrito:
					$ultMsg = preg_replace($regrex, '<b>$1</b>', $ultMsg); //Substituindo todos utilizando a expressão regular. By Marcelo 24/04/2023					

				}
			}
		// FIM Verificando a última Mensagem //

		// Tratamento do Nome //
			if( $registros->nomeContato !== "" ){ 
				$registros->nome = $registros->nomeContato; 
			} 
			else{
				$registros->nome = $registros->numero;	
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

		//Mostro o relógio indicando a qtd de minutos sem atendimento
        //Ajuste Marcelo não exibir o Relogio quando a ultima mensagem e do Atendente 23/04/2023
	   if (@$id_atendente == 0) {
		  @$msgtempoEspera = trataTempoOciosodoAtendente($arrUltMsg['MINUTOS_MSG'] ?? 0);
	  
	   } else{   
		  @$msgtempoEspera = trataTempoOciosodoAtendente(0); //Não Exibir o Relogio quando a última mensagem e do Atendente
	   }

		//@$msgtempoEspera = trataTempoOciosodoAtendente($arrUltMsg['MINUTOS_MSG']);
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
			//$fotoPerfil = getFotoPerfilNew($registros->numero);
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

		if ($registros->canal==0){
			//Se o Canal for igual a Zero, significa que o atendimento veio através do WEBCHAT
			$corDoNome = '<font style="color:#8B0000">'.getCanal($conexao, $registros->canal).limpaNome($registros->nome).'</font>';
		}else if ($registros->canal==1){
			$corDoNome = '<font style="color:#000000">'.getCanal($conexao, $registros->canal).limpaNome($registros->nome).'</font>';
		}else if ($registros->canal==2){
			$corDoNome = '<font style="color:#0000FF">'.getCanal($conexao, $registros->canal).limpaNome($registros->nome).'</font>';
		}else{
			$corDoNome = '<font style="color:#006400">'.getCanal($conexao, $registros->canal).limpaNome($registros->nome).'</font>';
		}
		
		// Saída HTML //
			echo '<div class="contact-item linkDivAtendendo">
					<input type="hidden" id="numero" value="'.$registros->numero.'">
					<input type="hidden" id="id_atendimento" value="'.$registros->id.'">      <!-- Add Marcelo NOME_EMPRESA -->
					<input type="hidden" id="nome" value="'.limpaNome($registros->nome).' - '.$registros->nome_empresa.'">
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
							<div class="_25Ooe">                                                                        <!-- Add Marcelo NOME_EMPRESA -->
								<span dir="auto" title="'.limpaNome($registros->nome).' '.Mask($registros->numero).' - '.$registros->nome_empresa.'" class="_1wjpf">
								    '.$tempoOcioso.'
								    '.$corDoNome.'																	
								</span>
								<span dir="auto" title="'.limpaNome($registros->operador).'" style="font-size:.8rem; color: #808080;">'.limpaNome($registros->operador).'</span>
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
		$('.linkDivAtendendo').click(function(){
			// Para inibir múltiplos clicks no Atendimento //
			var find = /carregando/g;
			var larguradatela = $(window).width();
		

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
						//	alert(larguradatela);
						if (larguradatela < 801){ //Se a tela for menor qu 800pixels minimizo os atendimentos para ficar mais responsivo
							
							$('#btnMinimuiConversas').click();							
						}
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
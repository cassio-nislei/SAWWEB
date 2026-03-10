<?php
	// Requires //
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	// Definições de Variáveis //
		$id_usuario = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : "";
		$htmlConversas = "";
		//Perfil 0 = Administrador e Perfil 2 = Corrdenador
		$mostra_todos_chats = isset($_SESSION["parametros"]["mostra_todos_chats"]) ? $_SESSION["parametros"]["mostra_todos_chats"] : 0;
	$id_usuario_safe = intval($id_usuario);
	$permissaoAdmin = (isset($_SESSION["usuariosaw"]["perfil"]) && ($_SESSION["usuariosaw"]["perfil"] == 0 || $_SESSION["usuariosaw"]["perfil"] == 2) && $mostra_todos_chats == 1) ? '' : "AND ta.id_atend = '".$id_usuario_safe."'";
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
	$strAtendimento = "SELECT taa.id, taa.numero, ta.nome, CASE WHEN tc.nome IS NULL then ta.nome when tc.nome = '' then ta.nome else tc.nome END AS nomeContato, ta.canal, ta.id_atend, ta.nome_atend, td.departamento, ta.nome_empresa,  
						(SELECT nome FROM tbusuario WHERE id = ta.id_atend) AS operador,
						(SELECT MAX(hr_msg) FROM tbmsgatendimento WHERE id = taa.id) AS ordem,
						/* Subqueries para evitar N+1 */
						(SELECT count(m2.id) FROM tbmsgatendimento m2 WHERE m2.numero = taa.numero AND m2.id = taa.id AND m2.id_atend = 0 AND m2.visualizada = false) AS qtd_novas,
						(SELECT m3.msg FROM tbmsgatendimento m3 WHERE m3.numero = taa.numero AND m3.id = taa.id ORDER BY m3.seq DESC LIMIT 1) AS ult_msg,
						(SELECT DATE_FORMAT(m4.hr_msg, '%H:%i') FROM tbmsgatendimento m4 WHERE m4.numero = taa.numero AND m4.id = taa.id ORDER BY m4.seq DESC LIMIT 1) AS ult_hora,
						(SELECT TIMESTAMPDIFF(MINUTE, m5.dt_msg, NOW()) FROM tbmsgatendimento m5 WHERE m5.numero = taa.numero AND m5.id = taa.id ORDER BY m5.seq DESC LIMIT 1) AS minutos_msg,
						(SELECT m6.id_atend FROM tbmsgatendimento m6 WHERE m6.numero = taa.numero AND m6.id = taa.id ORDER BY m6.seq DESC LIMIT 1) AS ult_id_atend,
						(SELECT DATE_FORMAT(m7.hr_msg, '%H:%i') FROM tbmsgatendimento m7 WHERE m7.numero = taa.numero AND m7.id = taa.id AND m7.id_atend = 0 ORDER BY m7.seq DESC LIMIT 1) AS ult_hora_cliente
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
		
			
		// Usando dados das subqueries (evita N+1) //
		$qtdNovasVal = intval($registros->qtd_novas);

		if( $qtdNovasVal > 0 ){
			$notificacoes = '<span class="OUeyt messages-count-new">'.$qtdNovasVal.'</span>';

			// Dispara o Alerta Sonoro - Se definido no Painel de Configurações //
			$alerta_sonoro = isset($_SESSION["parametros"]["alerta_sonoro"]) ? $_SESSION["parametros"]["alerta_sonoro"] : 0;
			if( $alerta_sonoro ){
				echo '<iframe src="https://player.vimeo.com/video/402630730?autoplay=1&loop=0&autopause=1" style="display: none" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
			}


			//Se recebeu uma nova mensagem em horario de almoço, respondo com a mensagem de almoço
			$em_almoco = isset($_SESSION["usuariosaw"]["em_almoco"]) ? $_SESSION["usuariosaw"]["em_almoco"] : "false";
			$msg_almoco = isset($_SESSION["usuariosaw"]["msg_almoco"]) ? $_SESSION["usuariosaw"]["msg_almoco"] : "";
			if (($em_almoco=="true") && ($id_usuario==$registros->id_atend)){
				$newSequence = newSequence($conexao, $registros->id,$registros->numero, $registros->canal);
				$msgAlmoco = mysqli_real_escape_string($conexao, $msg_almoco);
				$nomeContEsc = mysqli_real_escape_string($conexao, $registros->nomeContato);
				$gravaMsgAlmoco = mysqli_query(
					$conexao, 
					"INSERT INTO tbmsgatendimento(id,seq,numero,msg,  nome_chat,situacao, dt_msg,hr_msg,id_atend,canal)
						VALUES('".intval($registros->id)."','".$newSequence."' ,'".mysqli_real_escape_string($conexao, $registros->numero)."', '".$msgAlmoco."', 
								'".$nomeContEsc."' ,'E',NOW(),CURTIME(),'".intval($id_usuario)."','".intval($registros->canal)."')"
				);
			}

		}
		else{ $notificacoes = ""; }

		// Usando dados das subqueries para última mensagem //
			$ultMsg	= $registros->ult_msg ?? '';
			$ultHora = $registros->ult_hora;
			$minutosMsg = $registros->minutos_msg;
			$id_atendente = $registros->ult_id_atend;

			if ($ultMsg !== '') {
				if( $_SESSION["parametros"]["contar_tempo_espera_so_dos_clientes"] ){
					if ($registros->ult_hora_cliente !== null) {
						$ultHora = $registros->ult_hora_cliente;
					}
				}
                $regrex = '/\*(.*?)\*/';
				
				// Encurta a MSG caso ela possua mais que 40 caracteres //
				if( strlen($ultMsg) > 40 ){ 
					$ultMsg = str_replace("\\n"," ",$ultMsg);
					$ultMsg = substr($ultMsg, 0, 40) . "..."; 
					$ultMsg = preg_replace($regrex, '<b>$1</b>', $ultMsg);
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
		$stmtEtiq = mysqli_prepare($conexao, "SELECT te.cor, te.descricao AS etiqueta FROM tbetiquetascontatos tec INNER JOIN tbetiquetas te ON te.id = tec.id_etiqueta WHERE tec.numero = ?");
		mysqli_stmt_bind_param($stmtEtiq, 's', $registros->numero);
		mysqli_stmt_execute($stmtEtiq);
		$qryEtiquetas = mysqli_stmt_get_result($stmtEtiq);

		while( $registrosEtiqueta = mysqli_fetch_object($qryEtiquetas) ){

		if ($registrosEtiqueta->cor != ''){
			$etiqueta .= '<i class="fas fa-tag" style="--tag-color:'.e($registrosEtiqueta->cor).'" alt="'.e($registrosEtiqueta->etiqueta).'" title="'.e($registrosEtiqueta->etiqueta).'"></i>';
		}

	}
		mysqli_stmt_close($stmtEtiq);

		//Mostro o relógio indicando a qtd de minutos sem atendimento
        //Ajuste Marcelo não exibir o Relogio quando a ultima mensagem e do Atendente 23/04/2023
	   if (@$id_atendente == 0) {
		  @$msgtempoEspera = trataTempoOciosodoAtendente($minutosMsg ?? 0);
	  
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

		$nomeEscapado = e(limpaNome($registros->nome));
		$canalIcon = getCanal($conexao, $registros->canal);
		if ($registros->canal==0){
			$corDoNome = '<font style="color:#8B0000">'.$canalIcon.$nomeEscapado.'</font>';
		}else if ($registros->canal==1){
			$corDoNome = '<font style="color:#000000">'.$canalIcon.$nomeEscapado.'</font>';
		}else if ($registros->canal==2){
			$corDoNome = '<font style="color:#0000FF">'.$canalIcon.$nomeEscapado.'</font>';
		}else{
			$corDoNome = '<font style="color:#006400">'.$canalIcon.$nomeEscapado.'</font>';
		}
		
		// Saída HTML //
			echo '<div class="contact-item linkDivAtendendo">
					<input type="hidden" id="numero" value="'.e($registros->numero).'">
					<input type="hidden" id="id_atendimento" value="'.intval($registros->id).'">			<!-- Add Marcelo NOME_EMPRESA -->
					<input type="hidden" id="nome" value="'.e(limpaNome($registros->nome)).' - '.e($registros->nome_empresa).'">
					<input type="hidden" id="id_canal" value="'.intval($registros->canal).'">

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
								<span dir="auto" title="'.e(limpaNome($registros->nome)).' '.e(Mask($registros->numero)).' - '.e($registros->nome_empresa).'" class="_1wjpf">
								    '.$tempoOcioso.'
								    '.$corDoNome.'																	
								</span>
								<span dir="auto" title="'.e(limpaNome($registros->operador)).'" style="font-size:.8rem; color: #808080;">'.e(limpaNome($registros->operador)).'</span>
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
<?php
	define('AJAX_CALL', true);
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Definições de Variáveis //
		$id_usuario = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : "";		
		$ultHora = null;
		$ultMsg = null;
	// FIM Definições de Variáveis //
							
	$id_usuario_safe = intval($id_usuario);
	$qryAtendPend = mysqli_query(
		$conexao
		, "SELECT taa.id, taa.numero, ta.nome, CASE WHEN tc.nome IS NULL then ta.nome when tc.nome = '' then ta.nome else tc.nome END AS nomeContato, ta.canal, td.id AS idDepartamento, td.departamento, coalesce(ta.nome_empresa, '') as nome_empresa  
		   , tbe.cor, tbe.descricao as etiqueta,
		   /* Subqueries para evitar N+1 */
		   (SELECT count(m2.id) FROM tbmsgatendimento m2 WHERE m2.numero = taa.numero AND m2.id = taa.id AND m2.id_atend = 0 AND m2.visualizada = false) AS qtd_novas,
		   (SELECT m3.msg FROM tbmsgatendimento m3 WHERE m3.numero = taa.numero AND m3.id = taa.id ORDER BY m3.seq DESC LIMIT 1) AS ult_msg,
		   (SELECT DATE_FORMAT(m4.hr_msg, '%H:%i') FROM tbmsgatendimento m4 WHERE m4.numero = taa.numero AND m4.id = taa.id ORDER BY m4.seq DESC LIMIT 1) AS ult_hora,
		   (SELECT TIMESTAMPDIFF(MINUTE, m5.dt_msg, NOW()) FROM tbmsgatendimento m5 WHERE m5.numero = taa.numero AND m5.id = taa.id ORDER BY m5.seq DESC LIMIT 1) AS minutos_msg,
		   (SELECT DATE_FORMAT(m6.hr_msg, '%H:%i') FROM tbmsgatendimento m6 WHERE m6.numero = taa.numero AND m6.id = taa.id AND m6.id_atend = 0 ORDER BY m6.seq DESC LIMIT 1) AS ult_hora_cliente
			FROM tbatendimentoaberto taa
				INNER JOIN tbatendimento ta ON taa.id = ta.id and taa.numero = ta.numero
				INNER JOIN tbdepartamentos td ON td.id = ta.setor
				LEFT JOIN tbcontatos tc ON taa.numero = tc.numero
				LEFT JOIN tbetiquetas tbe on tbe.id = tc.idetiqueta
					WHERE situacao = 'P' AND ta.setor IN(
						SELECT id_departamento 
							FROM tbusuariodepartamento 
								WHERE id_usuario = '".$id_usuario_safe."'
					) 
						ORDER BY ta.dt_atend, ta.hr_atend"
	);

	/* Removi a exibição da mensagem porque na tela agora tem o contador
	if( mysqli_num_rows($qryAtendPend) == 0 ){
		echo "<font size=\"2\" color=\"#CCC\"><b>&nbsp;&nbsp;&nbsp;&nbsp;Nenhum atendimento pendente</b></font>";
	}
	*/

	// Aqui faz a listagem dos Atendimentos Pendentes //
	while( $registros = mysqli_fetch_object($qryAtendPend) ){
		
		
		// Usando dados das subqueries (evita N+1) //
		$qtdNovasVal = intval($registros->qtd_novas);

		if( $qtdNovasVal > 0){
			$notificacoes = '<span class="OUeyt messages-count-new">'.$qtdNovasVal.'</span>';

			// Dispara o Alerta Sonoro - Se definido no Painel de Configurações //
			if( $_SESSION["parametros"]["alerta_sonoro"] ){
				echo '<iframe src="https://player.vimeo.com/video/402630730?autoplay=1&loop=0&autopause=1" style="display: none" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
			}
		}
		else{ $notificacoes = ""; }
		// Fim da NOtificação Sonora

		// Verificando a última Mensagem (das subqueries) //
			$ultHora = $registros->ult_hora;
			$ultMsg = $registros->ult_msg;
			$minutosMsg = $registros->minutos_msg;

			if ($ultMsg !== null) {
				//Trato a hora da Última mensagem de acordo com o Parametro
				if( $_SESSION["parametros"]["contar_tempo_espera_so_dos_clientes"] ){
					if ($registros->ult_hora_cliente !== null) {
						$ultHora = $registros->ult_hora_cliente;
					}
				}

                $regrex = '/\*(.*?)\*/';
				
				// Encurta a MSG caso ela possua mais que 40 caracteres //
				if( strlen($ultMsg) > 40 ){ 
					$ultMsg = substr($ultMsg, 0, 40) . "..."; 
					// Usa o REGEX Negrito:
					$ultMsg = preg_replace($regrex, '<b>$1</b>', $ultMsg);
				}
			}
		// FIM Verificando a última Mensagem //

		// Tratamento do Nome //
			if( $registros->nomeContato !== "" ){
				 $registros->nome = $registros->nomeContato; 
			}
		// FIM Tratamento do Nome //

        //Mostro a etiqueta de acordo com a selecionada
		$etiqueta = '';
		//Busco as etiquetas vinculadas ao numero com prepared statement
		$stmtEtiq = mysqli_prepare($conexao, "SELECT te.cor, te.descricao as etiqueta FROM tbetiquetascontatos tec
		INNER JOIN tbetiquetas te ON te.id = tec.id_etiqueta
		WHERE tec.numero = ?");
		mysqli_stmt_bind_param($stmtEtiq, "s", $registros->numero);
		mysqli_stmt_execute($stmtEtiq);
		$qryEtiquetas = mysqli_stmt_get_result($stmtEtiq);

		while( $registrosEtiqueta = mysqli_fetch_object($qryEtiquetas) ){

		if ($registrosEtiqueta->cor != ''){
			$etiqueta .= '<i class="fas fa-tag" style="--tag-color:'.e($registrosEtiqueta->cor).'" alt="'.e($registrosEtiqueta->etiqueta).'" title="'.e($registrosEtiqueta->etiqueta).'"></i>';
		}

	}
		mysqli_stmt_close($stmtEtiq);

		//Mostro o relógio indicando a qtd de minutos sem atendimento
		$msgtempoEspera = trataTempoOciosodoAtendente($minutosMsg ?? 0);
		@$tempoOcioso = '<i class="fas fa-solid fa-clock  fa-1x" alt="'.$msgtempoEspera[0].'"  title="'.$msgtempoEspera[0].'" style="margin-left:1px;'.$msgtempoEspera[1].'"></i>';


		// Pego a foto de perfil //
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
                $perfil = RetornaNomeAbreviado($registros->nome); 
			}else{
				$perfil = '<img src="'.$fotoPerfil.'" class="rounded-circle user_img">';
				$estiloPerfil = 'style="color:white; background-color:'.@$cordefundo.'"';
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
			echo '<div class="contact-item linkDivPendente">
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
								<span dir="auto" title="'.limpaNome($registros->departamento).'" style="font-size:.8rem; color: #808080;">'.limpaNome($registros->departamento).'</span>
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
		// Gravo em uma Sessão o Setor do Atendimento //
		$_SESSION["usuariosaw"]["idDepartamento"] = $registros->idDepartamento;
        $_SESSION["usuariosaw"]["nomeDepartamento"] = $registros->departamento;
	}
?>

<script>
	$(document).ready(function(){		
		$('.linkDivPendente').click(function(){
			// Para inibir múltiplos clicks no Atendimento //
			var find = /carregando/g;
			var larguradatela = $(window).width();

			// Limpa o campo Número e Nome //
				if( $("#transferirParaMim") !== undefined ){
					// Limpando os Hiddens de Controle //
						$("#s_numero").val("");
						$("#s_id_atendimento").val("");
						$("#s_id_canal").val("");
						$("#s_nome").val("");
					// FIM Limpando os Hiddens de Controle //
				}
			// FIM Limpa o campo Número e Nome //

			if( !find.test($(this).attr('class')) ){
				var numero = $(this).find("#numero").val();
				var id_atendimento = $(this).find("#id_atendimento").val();
				var nome = $(this).find("#nome").val();
				var id_canal = $(this).find("#id_canal").val();
				var compareA = numero + id_canal;
				var compareB = $("#s_numero").val() + $("#s_id_canal").val();

				// Só permite carregar a conversa se a mensma ainda não foi carregada //
				if( compareA !== compareB ){
					$('#AtendimentoAberto').html("Carregando conversa ... Aguarde um momento, por favor!");
					$('.linkDivPendente').removeClass( "active" );
					$(this).addClass( "active carregando" );
					
					// Faz a Inicialização do atendimento //
					$.post("atendimento/iniciarAtendimento.php",{id_atendimento:id_atendimento,id_canal:id_canal,numero:numero,nome:nome}, function(retorno){
						// Atualizando a Lista de Atendimentos Pendentes //
						$.ajax("atendimento/pendentes.php").done(function(data) {
							$("#ListaPendentes").html(data);
						});

						// Atualizando a Lista de Atendimentos em Andamento //
						$.ajax("atendimento/atendendo.php").done(function(data) {
							$("#ListaEmAtendimento").html(data);
						});

						// Tratamento de retorno com Conversa já em Atendimento (outro Operador) //
						if( retorno == 3 ){
							mostraDialogo("Este Atendimento já está sendo atendido", "danger", 2500);
							return false;
						}

						// Mostro a Conversa //
						$.ajax("atendimento/conversa.php?id="+id_atendimento+"&id_canal="+id_canal+"&numero="+numero+"&nome="+encodeURIComponent(nome)).done(
							function(data) {
							if (larguradatela < 801){ //Se a tela for menor qu 800pixels minimizo os atendimentos para ficar mais responsivo							
							       $('#btnMinimuiConversas').click();							
						     }
							$('#AtendimentoAberto').html(data);
							$('.linkDivPendente').removeClass( "carregando" );
						});
					});
					// FIM Faz a Inicialização do atendimento //
				}
				// FIM Só permite carregar a conversa se a mensma ainda não foi carregada //
			}
			// FIM Para inibir múltiplos clicks no Atendimento //
		});
	});
</script>
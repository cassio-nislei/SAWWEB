<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Definições de Variáveis //
		$idAtendimento = isset($_GET["id"]) ? $_GET["id"] : "";
		$numero = isset($_GET["numero"]) ? $_GET["numero"] : "";
		$Nome = isset($_GET["nome"]) ? $_GET["nome"] : "";
		$idCanal = isset($_GET["id_canal"]) ? $_GET["id_canal"] : "";
	// FIM Definições de Variáveis //

	// Definição do SQL //
	// Alteração necessária para mostrar o 'Histórico de Atendimentos' aqui vai mostrar apenas o histórico do atendimento  //
	if( $idAtendimento === "att" ){
		$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg, tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, ta.tipo_arquivo, ta.nome_original, tma.situacao, tma.reagir,tma.reacao,
		tma.reacaorec
					FROM tbmsgatendimento tma
						LEFT JOIN tbanexos ta ON tma.id = ta.id AND tma.seq = ta.seq AND tma.numero = ta.numero
							WHERE tma.numero = '".$numero."' and tma.id = '$idAtendimento'
								ORDER BY tma.id, seq";
	}else
		// Alteração necessária para mostrar o 'Histórico de Atendimentos' do Cliente completo pelo número//
		if( $idAtendimento === "all" ){
			$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg, tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, ta.tipo_arquivo, ta.nome_original, tma.situacao, tma.reagir,tma.reacao,
			tma.reacaorec
						FROM tbmsgatendimento tma
							LEFT JOIN tbanexos ta ON tma.id = ta.id AND tma.seq = ta.seq AND tma.numero = ta.numero
								WHERE tma.numero = '".$numero."'
									ORDER BY tma.id, seq";
		}
		else{
			// Atualizo as visualizações das mensagens para zerar o contador conforme atualiza a conversa //
			if( isset($_SESSION["usuariosaw"]["id"]) ){
				$intUserId = $_SESSION["usuariosaw"]["id"]; //Atualizo a mensagem como visualizada apenas se o Dono do chamado visualiza-la
				$mesmoUsuario = mysqli_query($conexao, "select id_atend from tbatendimento WHERE id = '".$idAtendimento."' AND  numero = '".$numero."'") ;
				$mesmousuariologado = mysqli_fetch_assoc($mesmoUsuario);
				if ($mesmousuariologado["id_atend"]==$intUserId){
					$sqlUpdateTbMsgAtendimento = "UPDATE tbmsgatendimento 
													SET visualizada = true
														WHERE id = '".$idAtendimento."' AND  numero = '".$numero."'";
					$qryConversa = mysqli_query($conexao, $sqlUpdateTbMsgAtendimento) 
						or die("Erro ao atualizar as visualizações das mensagens: " . $sqlUpdateTbMsgAtendimento . "<br/>" . mysqli_error($conexao));
				}
			}
				
			// FIM Atualizo as visualizações das mensagens para zerar o contador conforme atualiza a conversa //

			$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg,  tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, ta.tipo_arquivo, ta.nome_original, tma.situacao, tma.reagir,tma.reacao,
			tma.reacaorec
						FROM tbmsgatendimento tma
							LEFT JOIN tbanexos ta ON tma.id = ta.id AND tma.seq = ta.seq AND tma.numero = ta.numero
								WHERE tma.numero = '".$numero."' AND  tma.id = '".$idAtendimento."'
									ORDER BY seq";
		}
	// FIM Definição do SQL //

	// Lista as conversas //
	$qryConversa = mysqli_query($conexao, $strSQL) 
		or die("Erro ao listar as Conversas: " . $strSQL . "<br/>" . mysqli_error($conexao));

	// Foto Perfil //
	$fotoPerfil = getFotoPerfil($conexao, $numero);

	while( $objConversa = mysqli_fetch_object($qryConversa) ){
		$chatID  = $objConversa->chatid;
		$seq_msg = $objConversa->seq;
		$mensagem = "";
		  $reactreceiver = $objConversa->reacaorec;
		$mensagemResposta = "";
		$dt_msg = strtotime($objConversa->dt_msg);
		$datamensagem = date("d/m/Y", $dt_msg);
		$hr_msg = strtotime($objConversa->hr_msg);
		$horamensagem = date("H:i", $hr_msg);
		$cod_reacao = intval($objConversa->reacao);
		$reagiuMSG = intval($objConversa->reagir);

		if ($reagiuMSG >= 1){
				
			switch ($cod_reacao) {
			   case 0:	$reagiuP = "👍";
				   break;
			   case 1:	$reagiuP = "❤️";
				   break;
			   case 2:	$reagiuP = "😂";
				   break;
			   case 3:	$reagiuP = "😮";
				   break;
			   case 4:	$reagiuP = "👏🏻";
				   break;
			   case 5:	$reagiuP = "😁";
				   break;
			   case 6:	$reagiuP = "🙏";
				   break;
			   case 7:	$reagiuP = "😍";
				   break;
			   case 8:	$reagiuP = "😪";
				   break;
			   case 9:	$reagiuP = "✔️";
				   break;
			   case 10:	$reagiuP = "🤝";
				   break;
			   case 11:	$reagiuP = "😱";
				   break;
			   case 12:	$reagiuP = "😞";
				   break;
			   case 13:	$reagiuP = "👎";
				   break;
			   case 14:	$reagiuP = "🙌";
				   break;
			   case 15:	$reagiuP = "😘";
				   break;		
			   case 16:	$reagiuP = "☝️";
				   break;
			   case 17:	$reagiuP = "😉";
				   break;
			   case 18:	$reagiuP = "👊";
				   break;
			   case 19:	$reagiuP = "😅";
				   break;
			   case 20:	$reagiuP = "👋";
				   break;				
			   default:
			   $reagiuP = "";
			   }
		   
		   }
		   else
		   {
			   $reagiuP = "";
		   }

		//Trato a exibição do Status da Mensagem :) //André Luiz
		if ($objConversa->situacao == 'N'){ 
          $statusMensagemEnviada = '<i class="fas fa-solid fa-check-double  fa-1x" style="color:dodgerblue;"></i>';
		}else{
		  $statusMensagemEnviada = '<i class="fas fa-solid fa-clock  fa-1x" style="color:#CCC;"></i>';
		}

		
		//Trato o Anexo para exibir
		//Quando for gravação de Audio
		if ($objConversa->tipo_arquivo=='PTT'){									
			$mensagem = '<audio controls="" style="width:240px"><source src="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"></audio>';	
		//Quando for envio de Audio
		}
		elseif ($objConversa->tipo_arquivo=='AUDIO'){
			$mensagem = '<a class="youtube cboxElement" href="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"><img src="images/abrir_audio.png" width="100" height="100"></a><br>'.$objConversa->nome_original;	
		//Quando for envio de Video
		}
		elseif ($objConversa->tipo_arquivo=='VIDEO'){
			$mensagem = '<a class="youtube cboxElement" href="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"><img src="images/abrir_video.png" width="100" height="100"></a><br>'.$objConversa->nome_original;									 
			//Quando for Imagem
		}
		elseif ($objConversa->tipo_arquivo=='STICKER'){
			$mensagem = '<a class="youtube cboxElement" href="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"><img src="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'" width="100" height="100"></a>';
		} 
		elseif ($objConversa->tipo_arquivo=='IMAGE'){
			$strAnexos = "SELECT arquivo, nome_arquivo, tipo_arquivo, nome_contato FROM tbanexos WHERE id = '".$objConversa->id."' AND numero = '".$objConversa->numero."' AND seq = '".$objConversa->seq."'";
			$qryAnexos = mysqli_query($conexao, $strAnexos);
			$objAnexos = mysqli_fetch_object($qryAnexos);
			$extensao = explode(".", $objAnexos->nome_arquivo)[1];
			$fileName = "images/conversas/" . $objConversa->id.'_'.$objConversa->numero.'_'.$objConversa->seq.'.'.$extensao;
			$fileRootImage = "../" . $fileName;

			// Cria o arquivo se ele ainda não existir //
				if( !file_exists($fileRootImage) ){
				// Tenta criar a imagem - primeiro tenta dados brutos, depois base64 //
				$img = @imagecreatefromstring( $objAnexos->arquivo );
				
				// Se falhar, tenta decodificar base64 //
				if( $img === false ){
					$img = @imagecreatefromstring( base64_decode($objAnexos->arquivo, true) );
				}
				
				// Só salva se conseguiu criar a imagem //
				if( $img !== false ){
					imagejpeg( $img, $fileRootImage );
					imagedestroy( $img );
				}

			// Montando a Mensagem //
				$mensagem = '<a href="'.$fileName.'" data-lightbox-title="">
								<img style="border: 1px solid #ccc; border-radius: 5px;" width="100px" src="'.$fileName.'" />
							</a>';
				
				if (strlen($objConversa->msg)>0){
					$mensagem = $mensagem .'<br>'.  $objConversa->msg;
				}
			// FIM Montando a Mensagem //
		}  // FIM if (!file_exists)
		}  // FIM elseif IMAGE
		else if ( $objConversa->tipo_arquivo == 'DOCUMENT'
			|| $objConversa->tipo_arquivo == 'APPLI'
			|| $objConversa->tipo_arquivo == 'TEXT/' ) {
			$ext = strtoupper(pathinfo($objConversa->nome_original, PATHINFO_EXTENSION));
			
			if ($ext=='PDF'){
				$imgIcone = 'abrir_pdf.png';
			}
			else if ($ext=='DOC' or $ext=='DOCX'){
				$imgIcone = 'abrir_doc.png';
			}
			else if ($ext=='XLS' or $ext=='XLSX' or $ext=='CSV'){
				$imgIcone = 'abrir_xls.png';
			}
           else if ($ext=='PPT' or $ext=='PPTX' or $ext=='PPSX'){
				$imgIcone = 'abrir_ppt.png'; //Add Marcelo POWERPOINT
			}
			else{
				$imgIcone = 'abrir_outros.png'; // Icone Generico
			}

			$mensagem = '<a href="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"><img src="images/'.$imgIcone.'" width="100" height="100"></a><br>'.$objConversa->nome_original.'<br>'. $objConversa->msg;
		}
		else if (!empty($objConversa->msg) && strlen($objConversa->msg)>0) {
			$mensagem = $objConversa->msg;	
			$mensagemResposta = $objConversa->resp_msg ?? "";	
		}

		$mensagem = nl2br($mensagem);
		$string = $mensagem;

		// Regex (leia o final para entender!):
		$regrex = '/\*(.*?)\*/';

		// Usa o REGEX Negrito:
		$mensagem = preg_replace($regrex, '<b>$1</b>', $string); //Substituindo todos utilizando a expressão regular. By Marcelo 23/04/2023

		//o código abaixo só subistituia o primeiro resultado
		/*preg_match_all($regrex, $string, $resultado);
		
		if(count($resultado)>1)
		{
			if(!empty($resultado[0][0])&&!empty($resultado[1][0]))
			{
				$mensagem = str_replace($resultado[0][0],"<b>".$resultado[1][0]."</b>",$mensagem);
			}
		}*/
		$reasctemot = '<span dir="ltr" class="ReacaoManifestada" style="float:left;position:absolute;margin-top:8px;margin-left:10px;padding:2px;border-radius:50%;background-color:white;">' . $reagiuP . $reactreceiver .'</span>';

		//$reasctemot = '<span class="ReacaoManifestada" style="float:left;position:absolute;margin-top:8px;margin-left:10px;padding:2px;border-radius:50%;background-color:white;">'.$reagiuP.'</span>'; 
		
		$reascteret = '<span class="ReacaoManifestada" style="float:left;position:absolute;margin-top:8px;margin-left:10px;padding:2px;border-radius:50%;background-color:white;">'  . $reactreceiver . '</span>';

				
		
		// Pego a imagem do Perfil
		if( $objConversa->id_atend == 0 ){
			// Verifico se é um contato que foi enviado
			if( strpos($mensagem, 'BEGIN:VCARD') !== false ){
				$contato = extrairContatoWhats($mensagem);
				$arrContato = explode("<br>", $contato);

				echo '<div class="message">
						<div class="_3_7SH kNKwo message-in tail">
							<span class="tail-container"></span>
							<span class="tail-container highlight"></span>
							<div class="_1YNgi copyable-text">
								<div class="_3DZ69" role="button">
									<div class="_20hTB">
										<div class="_1WliW" style="height: 49px; width: 49px;">
											<img src="#" class="Qgzj8 gqwaM photo-contact-sended" style="display:none">
											<div class="_3ZW2E">
												<span data-icon="default-user">
													<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 212 212" width="212" height="212">
														<path fill="#DFE5E7" d="M106.251.5C164.653.5 212 47.846 212 106.25S164.653 212 106.25 212C47.846 212 .5 164.654.5 106.25S47.846.5 106.251.5z"></path>
														<g fill="#FFF">
															<path d="M173.561 171.615a62.767 62.767 0 0 0-2.065-2.955 67.7 67.7 0 0 0-2.608-3.299 70.112 70.112 0 0 0-3.184-3.527 71.097 71.097 0 0 0-5.924-5.47 72.458 72.458 0 0 0-10.204-7.026 75.2 75.2 0 0 0-5.98-3.055c-.062-.028-.118-.059-.18-.087-9.792-4.44-22.106-7.529-37.416-7.529s-27.624 3.089-37.416 7.529c-.338.153-.653.318-.985.474a75.37 75.37 0 0 0-6.229 3.298 72.589 72.589 0 0 0-9.15 6.395 71.243 71.243 0 0 0-5.924 5.47 70.064 70.064 0 0 0-3.184 3.527 67.142 67.142 0 0 0-2.609 3.299 63.292 63.292 0 0 0-2.065 2.955 56.33 56.33 0 0 0-1.447 2.324c-.033.056-.073.119-.104.174a47.92 47.92 0 0 0-1.07 1.926c-.559 1.068-.818 1.678-.818 1.678v.398c18.285 17.927 43.322 28.985 70.945 28.985 27.678 0 52.761-11.103 71.055-29.095v-.289s-.619-1.45-1.992-3.778a58.346 58.346 0 0 0-1.446-2.322zM106.002 125.5c2.645 0 5.212-.253 7.68-.737a38.272 38.272 0 0 0 3.624-.896 37.124 37.124 0 0 0 5.12-1.958 36.307 36.307 0 0 0 6.15-3.67 35.923 35.923 0 0 0 9.489-10.48 36.558 36.558 0 0 0 2.422-4.84 37.051 37.051 0 0 0 1.716-5.25c.299-1.208.542-2.443.725-3.701.275-1.887.417-3.827.417-5.811s-.142-3.925-.417-5.811a38.734 38.734 0 0 0-1.215-5.494 36.68 36.68 0 0 0-3.648-8.298 35.923 35.923 0 0 0-9.489-10.48 36.347 36.347 0 0 0-6.15-3.67 37.124 37.124 0 0 0-5.12-1.958 37.67 37.67 0 0 0-3.624-.896 39.875 39.875 0 0 0-7.68-.737c-21.162 0-37.345 16.183-37.345 37.345 0 21.159 16.183 37.342 37.345 37.342z"></path>
														</g>
													</svg>
												</span>
											</div>
										</div>
									</div>
									<div class="_1lC8v">
										<div dir="ltr" class="_3gkvk selectable-text invisible-space copyable-text">'.$contato.'</div>
									</div>
									<div class="_3a5-b">									
										<div class="_1DZAH" role="button">
											<span class="msg-time">Enviado '.$datamensagem. ' às '. $horamensagem.'</span>
											<div class="message-status"></div>
										</div>
									</div>
								</div>
								<div class="_6qEXM">
									<div class="btn-message-send" role="button" data-numero="'.SomenteNumero($arrContato[1]).'" data-nome="'.$arrContato[0].'">Enviar mensagem</div>
								</div>
							</div>
						</div>
					</div>';
			}
			// se não for um contato mostro a mensagem normal
			else{
				echo '<div class="message">					
						<div class="font-style _3DFk6 message-in tail">
							<span class="tail-container"></span>
							<span class="tail-container highlight"></span>														
							<div class="Tkt2p">';
				//Trato a existencia de mensagem de resposta
				if (strlen($mensagemResposta)>0){
					if (@ValidarImagemBase64('data:image/png;base64,'.$mensagemResposta) ){
						$mensagemResposta = '<img style="border: 1px solid #ccc; border-radius: 5px;" width="100px" src="data:image/png;base64,'.$mensagemResposta.'" />';
					}
					echo ' 
					<div style="border-left: solid green;border-radius:3px;background-color:#CCC;opacity: 0.2;color:#000">							
							<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagemResposta) .'</span>
						</div>	
					';
				}	
		      
                
				echo'<div class="_3zb-j ZhF0n">
									<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagem) .'</span>
								</div>
								<div class="_2f-RV" style="width:100%;">
							     						
								'.$reascteret.'
								    				
									<div class="_1DZAH" style="float:right;">																								    
										  <span class="msg-time">Enviado '.$datamensagem. ' as '. $horamensagem.'</span>																			
									</div>
								</div>
							</div>
							<span class="tail-container" style="margin-top:10px;margin-left:96%;width:25px;height:25px;							
							justify-content: center;color:#000;">
							<div class="dropup">
							<i class="fas fa-angle-down" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor:pointer;width:15px;"></i>		
							<div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -165px, 0px);">
								<input type="hidden" id="chatID" value="'.$chatID.'">
								<input type="hidden" id="seq_msg" value="'.$seq_msg.'">
								<input type="hidden" id="msg_original" value="'.htmlspecialchars($mensagem).'">
								<a class="dropdown-item btnResponderMSG">Responder</a>
								<a class="dropdown-item btnReagirMSG" href="#">Reagir à Mensagem</a>
								<a class="dropdown-item btnApagarMSG" href="#">Apagar Mensagem</a>
							</div>
						</div>

								  </span>

						</div>	
					</div>					
					';
			}
			// Fim da verificação se é contato ou mensagem
		}
		// $S_TIPO:= 'Atendimento';  Estilo da Exibição da Mensagem do Usuario do Chat //
		else{
			if( strpos($mensagem, 'BEGIN:VCARD') !== false ){
				$contato = extrairContatoWhats($mensagem);
				$arrContato = explode("<br>", $contato);

				// Busca a foto de perfil dos Contatos Enviados 
				$fotoPerfilContato = getFotoPerfil($conexao, SomenteNumero($arrContato[1]));
                
				echo '<div class="message">
						<div class="_3_7SH kNKwo message-out tail">
							<span class="tail-container"></span>
							<span class="tail-container highlight"></span>
							<div class="_1YNgi copyable-text">
								<div class="_3DZ69" role="button">
									<div class="_20hTB">
										<div class="_1WliW" style="height: 49px; width: 49px;">
											<img src="#" class="Qgzj8 gqwaM photo-contact-sended" style="display:none">
											<div class="_3ZW2E">
											<span data-icon="default-user">
												<img src="'.$fotoPerfilContato.'" class="rounded-circle user_img">
											</span>
											</div>
										</div>
									</div>
									<div class="_1lC8v">
										<div dir="ltr" class="_3gkvk selectable-text invisible-space copyable-text">'.$contato.'</div>
									</div>
									'.$reascteret.'
									<div class="_3a5-b">
										<div class="_1DZAH" role="button">
											<span class="msg-time">Enviado '.$datamensagem. ' às '. $horamensagem.'</span>
											<div class="message-status">.$statusMensagemEnviada.</div>
											
										</div>
									</div>
								</div>
								<div class="_6qEXM">
									<div class="btn-message-send" role="button">Segue o contato solicitado!</div>
								</div>
							</div>
						</div>
					</div>';
			}
			else {
				
				echo '
				<div class="message" style="z-index:0;">
					<div class="font-style _3DFk6 message-out tail">
						<span class="tail-container"></span>
						<span class="tail-container highlight"></span>
						<div class="Tkt2p">
						  ';	
					//Trato a existencia de mensagem de resposta
				if (!empty($mensagemResposta) && strlen($mensagemResposta)>0){						
						echo '
						<div style="border-left: solid green;border-radius:3px;background-color:#CCC;opacity: 0.2;color:#000">							
								<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagemResposta) .'</span>
							</div>	
						';
					}	
						   
				echo '	<div class="_3zb-j ZhF0n">
								<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagem) .'</span>
							</div>							
							'.$reascteret.'							
							<div class="_2f-RV">
								<div class="_1DZAH" role="button">
									<span class="msg-time">Enviado '.$datamensagem. ' às '. $horamensagem.'</span>
									<div class="message-status">
									    '.$statusMensagemEnviada.'	
                                       								
									  							 
									</div>
								
								</div>
							</div>
						</div>

						<span class="tail-container" style="z-index:200;position:absolute;margin-top:10px;margin-right:10px;width:35px;height:25px;							
				  justify-content: center;color:white;">
				  <div class="dropup">
							<i class="fas fa-angle-down" aria-hidden="true" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor:pointer;width:15px;"></i>		
							<div class="dropdown-menu" x-placement="top-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, -165px, 0px);">
							    <input type="hidden" id="chatID" value="'.$chatID.'">
								<input type="hidden" id="seq_msg" value="'.$seq_msg.'">
								<input type="hidden" id="msg_original" value="'.htmlspecialchars($mensagem).'">
								<a class="dropdown-item btnResponderMSG">Responder</a>
								<a class="dropdown-item btnReagirMSG" href="#">Reagir à Mensagem</a>
								<a class="dropdown-item btnApagarMSG" href="#">Apagar Mensagem</a>
							</div>
						</div>
				        </span>

						

					</div>					
				</div>    
			   

				';
			}
		}
	}
?>

<!-- Modal -->
<div class="modal fade" id="ModalReacoes" tabindex="-1" data-backdrop="false" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="border-radius:30px !important;;">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
   <span class=" modal-content" style="background:none"> 
      <div class="modal-body">
	   
	     <button type="button" class="emojreacao" style="padding:5px " value="0">👍</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="1">❤️</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="2">😂</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="3">😮</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="4">👏🏻</button>
         <button type="button" class="emojreacao" style="padding:5px " value="5">😁</button>         
		 <button type="button" class="emojreacao" style="padding:5px " value="6">🙏</button>		 
		 <button type="button" class="emojreacao" style="padding:5px " value="7">😍</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="8">😪</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="9">✔️</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="10">🤝</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="11">😱</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="12">😞</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="13">👎</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="14">🙌</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="15">😘</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="16">☝️</button>
		 <button type="button" class="emojreacao" style="padding:5px " value="17">😉</button>
         <button type="button" class="emojreacao" style="padding:5px " value="18">👊</button>
         <button type="button" class="emojreacao" style="padding:5px " value="19">😅</button>
         <button type="button" class="emojreacao" style="padding:5px " value="20">👋</button>

		
reacao</div>
		</span>

    </div>
  </div>

<script src="js/responsive.min.js"></script>
<script>
	$(document).ready(function() {
		// Scroll Automático //
			if( $("#mensagens").length ){
				var rolagem = document.getElementById('mensagens');
				var target = $('#mensagens');
		
				target.animate({ scrollTop: rolagem.scrollHeight }, 200);
			}
		// FIM Scroll Automático //

		// Cartão Contato //
        $(".btn-message-send").on("click", function() {
			// Declaração de Variáveis //
			var numero = $(this).data('numero');
			var nome = $(this).data('nome');

			// Cadastrando o Contato //
				$.post("cadastros/contatos/ContatoController.php",{
					id: 0,
					acao: 1,
					numero_contato: numero,
					nome_contato: nome
				},function(resultado){});
			// FIM Cadastrando o Contato //

            // Faz a Inicialização do Atendimento //
				$.post("atendimento/gerarAtendimento.php",{numero:numero,nome:nome}, function(idAtendimento){
					// Atualiza a notificação
					if (idAtendimento != "erro"){
						$('#not'+idAtendimento).text(""); //limpa a qtd quando de notificações abre a conversa
						$('#AtendimentoAberto').html("<div class='spinner-border text-primary' role='status'><span class='sr-only'>Carregando ...</span></div>");
						$.ajax("atendimento/conversa.php?id="+idAtendimento+"&id_canal=1&numero="+encodeURIComponent(numero)+"&nome="+encodeURIComponent(nome)).done(function(data) {
							$('#AtendimentoAberto').html(data);

							$.ajax("atendimento/atendendo.php").done(function(data) {
								$('#ListaEmAtendimento').html(data);
							});
						});
					}
					else{ mostraDialogo("Erro ao tentar Iniciar o Atendimento!", "danger", 2500); }
				});
			// FIM Faz a Inicialização do Atendimento //
		});


		$(".btnResponderMSG").click(function(){ 
			var msgRecuperada = $(this).parent().find("#msg_original").val();
			var idResposta    = $(this).parent().find("#chatID").val();

			$(".panel-Respostas").fadeIn(500);  
			$("#chatid_resposta").val(idResposta);         
			$("#RespostaSelecionada").html(msgRecuperada);
			$('#msg').focus();

	    });	

		$("#fecharResposta").click(function(){ 
			$(".panel-Respostas").fadeOut(500);           
			$("#RespostaSelecionada").html('');
	    });	

		//Gravo o elemento que Vai armazenar a Reação pra Exibir
        var elementoreacao = "" ;
        var idResposta = "" ;
		$(".btnReagirMSG").click(function(){ 
			var msgRecuperada = $(this).parent().find("#msg_original").val();
			idResposta    = $(this).parent().find("#chatID").val();
			elementoreacao = $(this).parents(".message").find(".ReacaoManifestada") ;
            
			 $('#ModalReacoes').modal('show');	

			
			
	    });	

		$(".emojreacao").click(function(){ 
			$(elementoreacao).fadeIn();
			$(elementoreacao).html($(this).html());
			var iconereact = $(this).val(); 
			//alert(iconereact);
			$.post("atendimento/reacaoMensagem.php",{id:idResposta,reacao:iconereact},function(resultado){
				//elementoMensagem.html(elementoreacao+"Mensagem Apagada");
				//alert(idResposta);
			}); 
		    
			$('#ModalReacoes').modal('hide');				
		});	

		$(document).on("click", "#ModalReacoes", function(){
			$('#ModalReacoes').modal('hide');	
        });
		

		$(".btnApagarMSG").click(function(){ 
			var idUnico    = $(this).parent().find("#chatID").val(),
			elementoMensagem = $(this).parent().parent().parent().parent().find(".Tkt2p");
			var numero = $("#s_numero").val();
            var id_atendimento = $("#s_id_atendimento").val();
            var id_canal = $("#s_id_canal").val();
			var sequencia =  $(this).parent().find("#seq_msg").val();

		//	alert("NUmero"+numero+" ID:"+ id_atendimento+ ' Sequencia: '+sequencia)
		//alert(elementoMensagem.html());
            // reagindo a mensagem //
			 

			// Apagando a mensagem //
			$.post("atendimento/apagarMensagem.php",{id:idUnico,numero:numero,id_atendimento:id_atendimento, seq:sequencia},function(resultado){
				elementoMensagem.html("🚫Mensagem Apagada!!!");
				//alert(resultado);
			});
			// FIM da Exclusão da Mensagem //
		
	    });	
		

	});
</script>
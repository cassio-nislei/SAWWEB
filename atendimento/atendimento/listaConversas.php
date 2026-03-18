<?php
	define('AJAX_CALL', true);
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
		$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg, tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, 
	       ta.id as anexo_id, ta.numero as anexo_numero, ta.seq as anexo_seq, ta.tipo_arquivo, ta.nome_original, ta.base64, tma.situacao, tma.reagir, tma.reacao,
		       tma.reacaorec, tma.apagada
					FROM tbmsgatendimento tma
						LEFT JOIN tbanexos ta ON tma.id_anexo = ta.id
							WHERE tma.numero = '".$numero."' and tma.id = '$idAtendimento'
								ORDER BY tma.id, seq";
	}else
		// Alteração necessária para mostrar o 'Histórico de Atendimentos' do Cliente completo pelo número//
		if( $idAtendimento === "all" ){
			$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg, tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, 
		       ta.id as anexo_id, ta.numero as anexo_numero, ta.seq as anexo_seq, ta.tipo_arquivo, ta.nome_original, ta.base64, tma.situacao, tma.reagir, tma.reacao,
			       tma.reacaorec, tma.apagada
						FROM tbmsgatendimento tma
						LEFT JOIN tbanexos ta ON tma.id_anexo = ta.id
								WHERE tma.numero = '".$numero."'
									ORDER BY tma.id, seq";
		}
		else{
			// Atualizo as visualizações das mensagens para zerar o contador conforme atualiza a conversa //
			  $intUserId = $_SESSION["usuariosaw"]["id"]; //Atualizo a mensagem como visualizada apenas se o Dono do chamado visualiza-la
			  $mesmoUsuario = mysqli_query($conexao, "select id_atend from tbatendimento WHERE id = '".$idAtendimento."' AND  numero = '".$numero."'") ;
			  $mesmousuariologado = mysqli_fetch_assoc($mesmoUsuario);
			  if ($mesmousuariologado && isset($mesmousuariologado["id_atend"]) && $mesmousuariologado["id_atend"]==$intUserId){
                $sqlUpdateTbMsgAtendimento = "UPDATE tbmsgatendimento 
												SET visualizada = true
													WHERE id = '".$idAtendimento."' AND  numero = '".$numero."'";
				$qryConversa = mysqli_query($conexao, $sqlUpdateTbMsgAtendimento) 
					or die("Erro ao atualizar as visualizações das mensagens: " . $sqlUpdateTbMsgAtendimento . "<br/>" . mysqli_error($conexao));
			  }
				
			// FIM Atualizo as visualizações das mensagens para zerar o contador conforme atualiza a conversa //

			$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg,  tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, 
		       ta.id as anexo_id, ta.numero as anexo_numero, ta.seq as anexo_seq, ta.tipo_arquivo, ta.nome_original, ta.base64, tma.situacao, tma.reagir, tma.reacao,
			       tma.reacaorec, tma.apagada
						FROM tbmsgatendimento tma
						LEFT JOIN tbanexos ta ON tma.id_anexo = ta.id
								WHERE tma.numero = '".$numero."' AND  tma.id = '".$idAtendimento."'
									ORDER BY seq";
		}
	// FIM Definição do SQL //

	// Lista as conversas //
	$qryConversa = mysqli_query($conexao, $strSQL) 
		or die("Erro ao listar as Conversas: " . $strSQL . "<br/>" . mysqli_error($conexao));

	// Foto Perfil //
	$fotoPerfil = getFotoPerfil($conexao, $numero);

	// Verifica se há mensagens //
	$numLinhas = mysqli_num_rows($qryConversa);
	if($numLinhas == 0) {
		echo '<div style="padding: 20px; text-align: center; color: #999;">
				<p>📭 Nenhuma mensagem neste atendimento</p>
			</div>';
	}

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
		
		// Verificar se mensagem foi apagada pelo outro lado (apagada = 2)
		$foiApadaDoOutroLado = isset($objConversa->apagada) && intval($objConversa->apagada) === 2;
		$mensagemOriginal = $objConversa->msg; // Guardar mensagem original

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
		//Quando para gravação de Audio
		if ($objConversa->tipo_arquivo=='PTT'){
			// Tentar recuperar o base64 diretamente do banco
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				// Usar base64 diretamente se disponível
				$audioData = $objAnexo->base64;
				// Se o base64 não tem o prefixo data URI, adicionar
				if (strpos($audioData, 'data:audio') !== 0) {
					$audioData = 'data:audio/mpeg;base64,' . $audioData;
				}
				$mensagem = '<audio controls="" style="width:240px"><source src="'.$audioData.'" type="audio/mpeg" /></audio>';
			} else if ($objAnexo && !empty($objAnexo->arquivo)) {
				// Fallback para coluna arquivo se base64 não existir
				if (strpos($objAnexo->arquivo, 'data:audio') === 0) {
					$mensagem = '<audio controls="" style="width:240px"><source src="'.$objAnexo->arquivo.'" type="audio/mpeg" /></audio>';
				} else {
					// Se for base64 puro, converter para data URI
					$audioData = 'data:audio/mpeg;base64,' . $objAnexo->arquivo;
					$mensagem = '<audio controls="" style="width:240px"><source src="'.$audioData.'" type="audio/mpeg" /></audio>';
				}
			} else {
				// Fallback para anexo.php caso nenhum dos anteriores funcione
				$url_anexo = htmlspecialchars("atendimento/anexo.php?id=".$objConversa->anexo_id."&numero=".$objConversa->anexo_numero."&seq=".$objConversa->anexo_seq, ENT_QUOTES, 'UTF-8');
				$mensagem = '<audio controls="" style="width:240px"><source src="'.$url_anexo.'" type="audio/mpeg" /></audio>';
			}
		//Quando for envio de Audio
		}
		elseif ($objConversa->tipo_arquivo=='AUDIO'){
			// Recuperar base64 direto do banco (igual ao PTT)
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				// Usar base64 diretamente se disponível
				$audioData = $objAnexo->base64;
				// Se o base64 não tem o prefixo data URI, adicionar
				if (strpos($audioData, 'data:audio') !== 0) {
					$audioData = 'data:audio/mpeg;base64,' . $audioData;
				}
				$mensagem = '<audio controls="" style="width:240px"><source src="'.$audioData.'" type="audio/mpeg" /></audio>';
			} else if ($objAnexo && !empty($objAnexo->arquivo)) {
				// Fallback para coluna arquivo se base64 não existir
				if (strpos($objAnexo->arquivo, 'data:audio') === 0) {
					$mensagem = '<audio controls="" style="width:240px"><source src="'.$objAnexo->arquivo.'" type="audio/mpeg" /></audio>';
				} else {
					// Se for base64 puro, converter para data URI
					$audioData = 'data:audio/mpeg;base64,' . $objAnexo->arquivo;
					$mensagem = '<audio controls="" style="width:240px"><source src="'.$audioData.'" type="audio/mpeg" /></audio>';
				}
			} else {
				// Fallback para anexo.php caso nenhum dos anteriores funcione
				$url_anexo = htmlspecialchars("atendimento/anexo.php?id=".$objConversa->anexo_id."&numero=".$objConversa->anexo_numero."&seq=".$objConversa->anexo_seq, ENT_QUOTES, 'UTF-8');
				$mensagem = '<audio controls="" style="width:240px"><source src="'.$url_anexo.'" type="audio/mpeg" /></audio>';
			}
		//Quando for envio de Video
		}
		elseif ($objConversa->tipo_arquivo=='VIDEO'){
			// Recuperar base64 do banco (video)
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				$videoData = $objAnexo->base64;
				
				// Detectar tipo de video pela extensão do arquivo
				$ext = strtolower(pathinfo($objConversa->nome_original, PATHINFO_EXTENSION));
				if ($ext === 'mp4') {
					$videoType = 'video/mp4';
				} elseif ($ext === 'webm') {
					$videoType = 'video/webm';
				} elseif ($ext === 'ogg' || $ext === 'ogv') {
					$videoType = 'video/ogg';
				} elseif ($ext === 'mov') {
					$videoType = 'video/quicktime';
				} else {
					$videoType = 'video/mp4'; // Default para MP4
				}
				
				// Se não tiver o prefixo data URI, adicionar
				if (strpos($videoData, 'data:video') !== 0) {
					$videoData = 'data:' . $videoType . ';base64,' . $videoData;
				}
				
				$videoName = htmlspecialchars(basename($objConversa->nome_original), ENT_QUOTES, 'UTF-8');
				$mensagem = '<video controls="" style="max-width:300px; border-radius: 5px;"><source src="'.$videoData.'" type="'.$videoType.'" />Seu navegador não suporta vídeo HTML5</video><br><span style="font-size: 12px; color: #666;">'.$videoName.'</span>';
			} else {
				// Se não houver video armazenado, mostrar mensagem
				$mensagem = '<span style="color: #999;">❌ Vídeo não disponível</span>';
			}
			//Quando for Imagem - Sticker
		}
		elseif ($objConversa->tipo_arquivo=='STICKER'){
			// Recuperar base64 do banco (sticker)
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				$stickerData = $objAnexo->base64;
				// Se não tiver o prefixo data URI, adicionar
				if (strpos($stickerData, 'data:image') !== 0) {
					$stickerData = 'data:image/webp;base64,' . $stickerData;
				}
				$mensagem = '<a class="youtube cboxElement" href="'.$stickerData.'"><img src="'.$stickerData.'" width="100" height="100"></a>';
			} else {
				$mensagem = '<span style="color: #999;">❌ Sticker não disponível</span>';
			}
		}
		// Imagem da Câmera (base64 comprimida)
		elseif ($objConversa->tipo_arquivo=='IMG'){
			// Exibe a imagem direto do base64 sem salvar em arquivo
			if (!empty($objConversa->base64)) {
				// Se já tiver o cabeçalho data:image, usa direto
				if (strpos($objConversa->base64, 'data:image') === 0) {
					$base64_img = $objConversa->base64;
				} else {
					// Caso contrário, adiciona o cabeçalho
					$base64_img = 'data:image/jpeg;base64,' . $objConversa->base64;
				}
				
				$mensagem = '<a href="'.$base64_img.'" data-lightbox-title="">
								<img style="border: 1px solid #ccc; border-radius: 5px; max-width: 300px; max-height: 300px;" src="'.$base64_img.'" />
							</a>';
				
				if (strlen($objConversa->msg) > 0) {
					$mensagem = $mensagem . '<br>' . $objConversa->msg;
				}
			}
		}
		elseif ($objConversa->tipo_arquivo=='IMAGE'){
			// Recuperar base64 do banco (imagem)
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				$base64_img = $objAnexo->base64;
				// Se não tiver o cabeçalho data:image, adiciona
				if (strpos($base64_img, 'data:image') !== 0) {
					$base64_img = 'data:image/jpeg;base64,' . $base64_img;
				}
				
				// Montando a Mensagem //
				$mensagem = '<a href="'.$base64_img.'" data-lightbox-title="">
								<img style="border: 1px solid #ccc; border-radius: 5px; max-width: 300px; max-height: 300px;" src="'.$base64_img.'" />
							</a>';
				
				if (strlen($objConversa->msg)>0){
					$mensagem = $mensagem .'<br>'.  $objConversa->msg;
				}
			} else {
				$mensagem = '<span style="color: #999;">❌ Imagem não disponível</span>';
			}
		}
		else if ($objConversa->tipo_arquivo == 'PDF') {
			// Tratamento especial para PDFs via base64
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				$base64_pdf = $objAnexo->base64;
				// Se não tiver o prefixo data URI, adicionar
				if (strpos($base64_pdf, 'data:') !== 0) {
					$base64_pdf = 'data:application/pdf;base64,' . $base64_pdf;
				}
				
				$msgEscaped = htmlspecialchars($objConversa->msg, ENT_QUOTES, 'UTF-8');
				$nomeEscaped = htmlspecialchars(basename($objConversa->nome_original), ENT_QUOTES, 'UTF-8');
				$mensagem = '<a href="'.$base64_pdf.'" target="_blank"><img src="images/abrir_pdf.png" width="100" height="100"></a><br>'.$nomeEscaped.'<br>'.$msgEscaped;
			} else {
				$msgEscaped = htmlspecialchars($objConversa->msg, ENT_QUOTES, 'UTF-8');
				$nomeEscaped = htmlspecialchars(basename($objConversa->nome_original), ENT_QUOTES, 'UTF-8');
				$mensagem = '<img src="images/abrir_pdf.png" width="100" height="100"><br>'.$nomeEscaped.'<br>' . $msgEscaped . '<br><span style="color: #999;">❌ PDF não disponível</span>';
			}
		}
		else if ( $objConversa->tipo_arquivo == 'DOCUMENT'
			|| $objConversa->tipo_arquivo == 'APPLI'
			|| $objConversa->tipo_arquivo == 'TEXT/' ) {
			// Recuperar base64 do banco (documento)
			$sqlAnexo = "SELECT base64, arquivo FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."' LIMIT 1";
			$qryAnexo = mysqli_query($conexao, $sqlAnexo);
			$objAnexo = mysqli_fetch_object($qryAnexo);
			
			$ext = strtoupper(pathinfo($objConversa->nome_original, PATHINFO_EXTENSION));
			
			// Mapear MIME types comuns
			$mimeTypes = array(
				'PDF' => 'application/pdf',
				'DOC' => 'application/msword',
				'DOCX' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'XLS' => 'application/vnd.ms-excel',
				'XLSX' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'PPT' => 'application/vnd.ms-powerpoint',
				'PPTX' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
				'PPSX' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow'
			);
			$mimeType = isset($mimeTypes[$ext]) ? $mimeTypes[$ext] : 'application/octet-stream';
			
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

			$msgEscaped = htmlspecialchars($objConversa->msg, ENT_QUOTES, 'UTF-8');
			$nomeEscaped = htmlspecialchars(basename($objConversa->nome_original), ENT_QUOTES, 'UTF-8');
			
			if ($objAnexo && !empty($objAnexo->base64)) {
				$documentData = $objAnexo->base64;
				// Se não tiver o prefixo data URI, adicionar
				if (strpos($documentData, 'data:') !== 0) {
					$documentData = 'data:' . $mimeType . ';base64,' . $documentData;
				}
				$mensagem = '<a href="'.$documentData.'" target="_blank"><img src="images/'.$imgIcone.'" width="100" height="100"></a><br>'.$nomeEscaped.'<br>'.$msgEscaped;
			} else {
				$mensagem = '<img src="images/'.$imgIcone.'" width="100" height="100"><br>'.$nomeEscaped.'<br>' . $msgEscaped . '<br><span style="color: #999;">❌ Documento não disponível</span>';
			}
		}
				// PDF detectado por extensão - usar base64
				$strAnexos = "SELECT arquivo, base64, nome_arquivo, nome_original FROM tbanexos WHERE id = '".$objConversa->anexo_id."' AND numero = '".$objConversa->anexo_numero."' AND seq = '".$objConversa->anexo_seq."'";
				$qryAnexos = mysqli_query($conexao, $strAnexos);
				$objAnexos = mysqli_fetch_object($qryAnexos);
				
				$base64_pdf = '';
				if (!empty($objAnexos->base64)) {
					$base64_pdf = $objAnexos->base64;
				} elseif (!empty($objAnexos->arquivo)) {
					if (strpos($objAnexos->arquivo, 'data:application/pdf') === 0) {
						$base64_pdf = $objAnexos->arquivo;
					} else {
						$base64_pdf = 'data:application/pdf;base64,' . base64_encode($objAnexos->arquivo);
					}
				}
				
				$msgEscaped = htmlspecialchars($objConversa->msg, ENT_QUOTES, 'UTF-8');
				$nomeEscaped = htmlspecialchars($objConversa->nome_original, ENT_QUOTES, 'UTF-8');
				$base64Escaped = htmlspecialchars($base64_pdf, ENT_QUOTES, 'UTF-8');
				$mensagem = '<a href="'.$base64Escaped.'" target="_blank" download="'.$nomeEscaped.'">
							<img src="images/abrir_pdf.png" width="100" height="100" alt="PDF">
						</a>
						<br><span title="'.$nomeEscaped.'">'.$nomeEscaped.'</span><br>'.$msgEscaped;
			}
		}
		else if (strlen($objConversa->msg)>0) {
			$mensagem = $objConversa->msg;	
			$mensagemResposta = $objConversa->resp_msg;	
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
		      
                
				// Exibir aviso se mensagem foi apagada do outro lado (apagada = 2)
				if ($foiApadaDoOutroLado) {
					echo '<div style="background-color: #f0f0f0; border-left: 4px solid #ff9800; padding: 8px 12px; margin-bottom: 8px; border-radius: 3px;">
							<span style="color: #ff9800; font-weight: bold; font-size: 14px;">🚫 Mensagem Apagada pelo outro lado</span>
						</div>';
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
					if (strlen($mensagemResposta)>0){						
						echo '
						<div style="border-left: solid green;border-radius:3px;background-color:#CCC;opacity: 0.2;color:#000">							
								<span dir="ltr" class="selectable-text invisible-space message-text">'. str_replace("\\n","<br/>",$mensagemResposta) .'</span>
							</div>	
						';
					}	
						   
				// Exibir aviso se mensagem foi apagada do outro lado (apagada = 2)
				if ($foiApadaDoOutroLado) {
					echo '<div style="background-color: #f0f0f0; border-left: 4px solid #ff9800; padding: 8px 12px; margin-bottom: 8px; border-radius: 3px;">
							<span style="color: #ff9800; font-weight: bold; font-size: 14px;">🚫 Mensagem Apagada pelo outro lado</span>
						</div>';
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
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document" style="pointer-events: auto;">
    <div class="modal-content" style="background:none; pointer-events: auto;"> 
      <div class="modal-body">
	   
	     <button type="button" class="emojreacao" style="padding:5px; pointer-events: auto;" value="0">👍</button>
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

      </div>
    </div>

    </div>
  </div>
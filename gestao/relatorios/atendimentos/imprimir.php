<?php
	// IMPORTANTE: Ativar output buffering ANTES de qualquer coisa
	ob_start();
	error_reporting(E_ERROR | E_PARSE); // Suprime warnings e notices (incluindo deprecated)
	
	// Requires
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
	
	// Limpar todo o output anterior (headers, whitespace, etc)
	ob_end_clean();
	
	// Reiniciar buffer para capturar output do HTML
	ob_start();
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/dompdf/autoload.inc.php");
	use Dompdf\Dompdf;
	use Dompdf\Options;
	
	// Configuração do DomPDF
	$options = new Options();
	$options->set('isRemoteEnabled', true);
	$options->set('isHtml5ParserEnabled', true);
	$options->set('defaultFont', 'Arial');
	$options->set('fontSize', 12);
	
	$dompdf = new Dompdf($options);

$html = '
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Conversa</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table td { padding: 5px; border-bottom: 1px solid #eee; }
        #mensagem-recebida { margin-bottom: 15px; }
        #mensagem-enviada { margin-bottom: 15px; }
        .msg-time { font-size: 10px; color: #999; }
        h2 { margin-top: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; font-size: 14px; }
        .msg-box { padding: 8px; margin-bottom: 10px; }
    </style>
  </head>
  <body>
    <div>';



	// Definições de Variáveis //
	$idAtendimento = isset($_GET["id_atendimento"]) ? $_GET["id_atendimento"] : "";
	$numero = isset($_GET["numero"]) ? $_GET["numero"] : "";
	//	$Nome = isset($_GET["nome"]) ? $_GET["nome"] : "";
	// FIM Definições de Variáveis //

	// Definição do SQL //

  

		$strSQL = "SELECT tma.chatid, tma.id, tma.seq, tma.numero, tma.msg, tma.resp_msg, tma.dt_msg, tma.hr_msg, tma.id_atend, ta.tipo_arquivo, ta.nome_original, tma.situacao, tat.canal, tat.protocolo, tat.situacao as status, de.departamento,
		tat.nome, u.nome as atendente, tat.dt_atend, tat.hr_atend, tat.dt_fim, tat.finalizado_por
					FROM tbmsgatendimento tma					    
					    INNER JOIN tbatendimento tat ON tat.numero = tma.numero and tat.id = tma.id
						INNER JOIN tbusuario u on u.id = tat.id_atend 
						LEFT JOIN tbanexos ta ON tma.id = ta.id AND tma.seq = ta.seq AND tma.numero = ta.numero
						LEFT JOIN tbdepartamentos de on de.id = tat.setor
							WHERE tma.numero = '".$numero."' and tma.id = '$idAtendimento'
             --  WHERE tma.numero = '5517991900295' and tma.id = '5'          
								ORDER BY tma.id, seq";
	
	// FIM Definição do SQL //

	// Lista as conversas //
	$qryConversa = mysqli_query($conexao, $strSQL) 
		or die("Erro ao listar as Conversas: " . $strSQL . "<br/>" . mysqli_error($conexao));

	// Foto Perfil //
	$fotoPerfil = getFotoPerfil($conexao, $numero);
    
	$cabecalho = true;
	while( $objConversa = mysqli_fetch_object($qryConversa) ){
		
		$chatID  = $objConversa->chatid;
		$seq_msg = $objConversa->seq;
		$mensagem = "";
		$mensagemResposta = "";
		$dt_msg = strtotime($objConversa->dt_msg);
		$datamensagem = date("d/m/Y", $dt_msg);
		$hr_msg = strtotime($objConversa->hr_msg);
		$horamensagem = date("H:i", $hr_msg);

		//Monto o Cabeçalho que será exibido uma única vez no inicio do atendimento
		if ($cabecalho){
			$cabecalho = false;
			$protocolo = $objConversa->protocolo;
			switch ($objConversa->canal){
				case 0 : $origem = ' Site'; break;
				case 1 : $origem = ' WhatsApp'; break;
			}
			if( $objConversa->id_atend == 0 ){
				$natureza = 'Iniciado pelo Contato';
			}else{
                $natureza = 'Iniciado pelo Atendente';
			}		
			$setor = $objConversa->departamento;
			$numero =Mask($objConversa->numero);
			$contato = $objConversa->nome;
			$atendente = $objConversa->atendente;	

			$dataIni = $objConversa->dt_atend;
			$horaIni = $objConversa->hr_atend;
			$dataHoraFim = $objConversa->dt_fim;

			if ( $objConversa->status == 'A'){
				$situacao = 'Em Atendimento';
				$dataHoraFim = 'Em andamento';
				$duracao = 'Em andamento';
				$finalizado_por = 'Não finalizado';
			}else{
				$situacao = 'Finalizado'; //Se já estiver Finalizado exibo a data de fim e duração do atendimento
				// Crie objetos DateTime para as datas e horários de início e fim
				$inicio = new DateTime($dataIni . ' ' . $horaIni);
				$fim = new DateTime($dataHoraFim);

				// Calcule a diferença entre o início e o fim do atendimento
				$diferenca = $fim->diff($inicio);

				// Exiba a diferença em formato de horas, minutos e segundos
				$duracao = $diferenca->format('%H:%I:%S');
				$dataHoraFim = date("d/m/Y H:i:s",strtotime($dataHoraFim));
				$finalizado_por = $objConversa->finalizado_por;
			}
						
			$html .='<table border="0" width="500">';
            $html .= "<tr><td><b>Protocolo:</b></td><td>#$protocolo </td></tr>";
			$html .= "<tr><td><b>Origem:</b></td><td> $origem  </td></tr>";
			$html .= "<tr><td><b>Natureza:</b></td><td> $natureza  </td></tr>";
			$html .= "<tr><td><b>Setor:</b></td><td> $setor  </td></tr>";
			$html .= "<tr><td><b>Celular:</b></td><td> $numero  </td></tr>";
			$html .= "<tr><td><b>Contato:</b></td><td> $contato  </td></tr>";
			$html .= "<tr><td><b>Atendente:</b></td><td> $atendente  </td></tr>";
			$html .= "<tr><td><b>Situação:</b></td><td> $situacao </td></tr>";
			$html .= "<tr><td><b>Início:</b></td><td> ".date("d/m/Y H:i:s",strtotime($dataIni .' '.$horaIni)). " </td></tr>";
			$html .= "<tr><td><b>Conclusão:</b></td><td> $dataHoraFim  </td></tr>";
			$html .= "<tr><td><b>Duração:</b></td><td> $duracao  </td></tr>";
			$html .= "<tr><td><b>Encerrado por:</b></td><td> $finalizado_por </td></tr>";
			$html .='</table>';


			$html .= "<h2>Diálogo:</h2>";
		}

		
		//Trato o Anexo para exibir
		//Quando for gravação de Audio
		if ($objConversa->tipo_arquivo=='PTT'){									
			$mensagem = 'Enviou um Audio';	
		//Quando for envio de Audio
		}
		elseif ($objConversa->tipo_arquivo=='AUDIO'){
			$mensagem = 'Enviou um Audio';	
		//Quando for envio de Video
		}
		elseif ($objConversa->tipo_arquivo=='VIDEO'){
			$mensagem = 'Enviou um Video';										 
			//Quando for Imagem
		}
		elseif ($objConversa->tipo_arquivo=='STICKER'){
			$mensagem = 'Enviou uma Figurinha';	
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
					// GAMBI, POG PLUS+ //
					// if( strlen(($objAnexos->nome_contato)) === 0 ){ $img = imagecreatefromstring( $objAnexos->arquivo ); }
					// else{ $img = imagecreatefromstring( base64_decode($objAnexos->arquivo) ); }
					
					$img = imagecreatefromstring( $objAnexos->arquivo );
					imagejpeg( $img, $fileRootImage );
				}
			// FIM Cria o arquivo se ele ainda não existir //

			// Montando a Mensagem //
				$mensagem = '<a href="'.$fileName.'" data-lightbox-title="">
								<img style="border: 1px solid #ccc; border-radius: 5px;" width="100px" src="'.$fileName.'" />
							</a>';
				
				if (strlen($objConversa->msg)>0){
					$mensagem = $mensagem .'<br>'.  $objConversa->msg;
				}
			// FIM Montando a Mensagem //
		}
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

			$mensagem = '<a href="atendimento/anexo.php?id='.$objConversa->id.'&numero='.$objConversa->numero.'&seq='.$objConversa->seq.'"><img src="images/'.$imgIcone.'" width="100" height="100"></a><br>'.$objConversa->nome_original;
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


		
		// Pego a imagem do Perfil
		if( $objConversa->id_atend == 0 ){
			// Verifico se é um contato que foi enviado
			if( strpos($mensagem, 'BEGIN:VCARD') !== false ){
				$contato = extrairContatoWhats($mensagem);
				$arrContato = explode("<br>", $contato);

				$html .= ' <div id="mensagem-recebida">
         
           <div style="border-left: 5px solid blue;">																						    
            <span class="msg-time">Enviado '.$datamensagem. ' as '. $horamensagem.'</span><br>													
      
          '.$contato.'
          </div>
        </div><br>';


			}
			// se não for um contato mostro a mensagem normal
			else{

		   $html .= '<div id="mensagem-recebida">         
				      <div style="border-left: 5px solid blue;">																						    
					<span class="msg-time">																							    
						<span class="msg-time">Enviado '.$datamensagem. ' as '. $horamensagem.'</span><br>																			
					</span>
				'. str_replace("\\n","<br/>",$mensagem) .'
					';
					//Trato a existencia de mensagem de resposta
					if (strlen($mensagemResposta)>0){
						$html .= '
					<div style="border-left: solid green;>							
						<span dir="ltr">'. str_replace("\\n","<br/>",$mensagemResposta) .'</span>
						</div>	
					';
					}	
			$html .='  </div>
        </div><br>';


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


				 $html .= ' <div id="mensagem-enviada" >
         
            <div style="border-left: 5px solid red;">
            <span class="msg-time">Enviado '.$datamensagem. ' às '. $horamensagem.'</span><br>
            '.$contato.'
            </div>
          </div><br>';
                    

			}
			else {
			

				$html .='<div id="mensagem-enviada">
       
        <div style="border-left: 5px solid red;">
        <span class="msg-time">Enviado '.$datamensagem. ' às '. $horamensagem.'</span><br>
          '. str_replace("\\n","<br/>",$mensagem) ;
           	//Trato a existencia de mensagem de resposta
					if (!empty($mensagemResposta) && strlen($mensagemResposta)>0){
						$html .= '
						<div style="border-left: solid green;border-radius:3px;">							
								<span dir="ltr" >'. str_replace("\\n","<br/>",$mensagemResposta) .'</span>
							</div>	
						';
					}	

					$html .='
        </div>
      </div><br>';

						   

			}
		}
	}

   $html .='</div> <!-- Fim da Conversa -->
   </body>
 </html>';

	// Gera o PDF com DomPDF
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();
	$dompdf->stream('Conversa_' . date('dmY_His') . '.pdf', array('Attachment' => false));

?>
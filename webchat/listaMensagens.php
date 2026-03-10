<?php
	// Flag para avisar padrao.inc.php que é uma chamada AJAX
	define('AJAX_CALL', true);
	
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	// Inicializar usuário se não autenticado
	if (!isset($_SESSION["usuariosaw"]["id"])) {
		$_SESSION["usuariosaw"]["id"] = 0;
	}

	$htmlMensagens = "";
	$idDepto = intval($_GET['idDepto'] ?? 0);
	$usuarioAtualId = $_SESSION["usuariosaw"]["id"] ?? 0;

	// SQL para Busca das Mensagens
	$sqlMensagens = "SELECT 
						tbc.id,
						tbu.id AS idUsuario, 
						tbd.id AS idDepartamento, 
						tbc.mensagem, 
						tbu.nome, 
						tbu.foto AS fotoUsuario,
						tbd.departamento, 
						DATE_FORMAT(tbc.data_hora, '%d/%m %H:%i') AS data_hora,
						TIME_FORMAT(tbc.data_hora, '%H:%i') AS hora,
						COALESCE(tbc.eh_privada, 0) AS eh_privada,
						COALESCE(tbc.id_destinatario, 0) AS id_destinatario,
						COALESCE(tbc.visualizado, 0) AS visualizado,
						COALESCE(tuu.nome, '') AS nome_destinatario,
						COALESCE(tbc.id_anexo, 0) AS id_anexo,
						COALESCE(ta.base64, '') AS anexo_base64,
						COALESCE(ta.tipo_arquivo, '') AS tipo_arquivo,
						COALESCE(ta.nome_arquivo, '') AS nome_arquivo
					FROM tbchatoperadores tbc
					INNER JOIN tbusuario tbu ON(tbu.id=tbc.id_usuario)
					LEFT JOIN tbdepartamentos tbd ON(tbd.id=tbc.id_departamento)
					LEFT JOIN tbusuario tuu ON(tuu.id=tbc.id_destinatario)
					LEFT JOIN tbanexos ta ON(ta.id=tbc.id_anexo)
					WHERE DATE(tbc.data_hora) = CURDATE()";

	// Filtro por Departamento
	if( intval($idDepto) > 0 ){
		$sqlMensagens .= " AND tbc.id_departamento = '".intval($idDepto)."'";
	}
	// Quando idDepto = 0 (Todos os setores), não adiciona filtro de departamento - traz todas as conversas
	
	// Filtro de Privacidade - mostrar apenas mensagens que o usuário pode ver
	// 1. Mensagens públicas (eh_privada = 0)
	// 2. Mensagens privadas onde o usuário é o remetente ou destinatário
	if ($usuarioAtualId > 0) {
		$sqlMensagens .= " AND (
						tbc.eh_privada = 0 OR 
						(tbc.eh_privada = 1 AND (tbc.id_usuario = '".intval($usuarioAtualId)."' OR tbc.id_destinatario = '".intval($usuarioAtualId)."'))
					)";
	}
	
	$sqlMensagens .= " ORDER BY tbc.id ASC";

	$qryMensagens = mysqli_query($conexao, $sqlMensagens) 
		or die("Erro ao listar as Conversas: " . mysqli_error($conexao));

	$totalMensagens = mysqli_num_rows($qryMensagens);

	if ($totalMensagens === 0) {
		echo '<div class="empty-state" style="margin: 40px 20px;">
				<i class="bi bi-chat-left"></i>
				<p>Nenhuma mensagem ainda</p>
				<small>Comece a conversa digitando sua primeira mensagem</small>
			</div>';
		exit;
	}

	$ultimoUsuarioId = null;
	$ultimaDataGrupo = null;

	while( $arrMensagens = mysqli_fetch_assoc($qryMensagens) ){
		$isOwnMessage = ($arrMensagens["idUsuario"] == $usuarioAtualId);
		$isPrivateMessage = intval($arrMensagens["eh_privada"]) === 1;
		$inicial = substr($arrMensagens["nome"], 0, 1);
		$temDestino = intval($arrMensagens["idDepartamento"]) > 0;
		
		// Criar grupo de mensagens se é novo usuário
		if ($ultimoUsuarioId !== $arrMensagens["idUsuario"]) {
			echo '<div class="message-group">';
			$ultimoUsuarioId = $arrMensagens["idUsuario"];
		}

		echo '<div class="message-item ' . ($isOwnMessage ? 'own' : '') . ($isPrivateMessage ? ' private' : '') . '" data-msg-id="' . $arrMensagens["id"] . '" data-msg-user="' . $arrMensagens["idUsuario"] . '" data-private="' . ($isPrivateMessage ? '1' : '0') . '">';
		
		// Avatar e ações (coluna vertical)
		echo '<div class="avatar-actions-wrapper">';
			echo '<div class="message-avatar" title="' . htmlspecialchars($arrMensagens["nome"]) . '">';
			
			// Se houver foto, exibir como imagem; senão, exibir inicial
			if (!empty($arrMensagens["fotoUsuario"])) {
				echo '<img src="' . htmlspecialchars($arrMensagens["fotoUsuario"]) . '" alt="' . htmlspecialchars($arrMensagens["nome"]) . '" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;" onerror="this.parentElement.innerHTML = \"' . strtoupper($inicial) . '\";">';
			} else {
				echo strtoupper($inicial);
			}
			
			echo '</div>';
			
			// Botões de ação (editar/deletar) - apenas para mensagens do usuário
			if ($isOwnMessage) {
				echo '<div class="message-actions">';
				echo '<button class="action-btn btn-edit" data-msg-id="' . $arrMensagens["id"] . '" title="Editar"><i class="bi bi-pencil"></i></button>';
				echo '<button class="action-btn btn-delete" data-msg-id="' . $arrMensagens["id"] . '" title="Deletar"><i class="bi bi-trash"></i></button>';
				echo '</div>';
			}
		echo '</div>';

		// Conteúdo
		echo '<div class="message-content">';
		echo '<div class="message-bubble">';
		
		// Header da mensagem
		$destinoInfo = '';
		
		if ($isPrivateMessage) {
			// Mensagem privada
			$nomeDest = htmlspecialchars($arrMensagens["nome_destinatario"]);
			if ($isOwnMessage) {
				$destinoInfo = ' <span class="message-tag private-tag"><i class="bi bi-lock-fill"></i> Privado para ' . $nomeDest . '</span>';
			} else {
				$destinoInfo = ' <span class="message-tag private-tag"><i class="bi bi-lock-fill"></i> Privado para você</span>';
			}
		} else {
			// Mensagem pública
			if ($temDestino) {
				$destinoInfo = ' <span class="message-tag">' . htmlspecialchars($arrMensagens["departamento"]) . '</span>';
			} else {
				$destinoInfo = ' <span class="message-tag">Todos</span>';
			}
		}
		
		echo '<div class="message-header">' . htmlspecialchars($arrMensagens["nome"]) . $destinoInfo . '</div>';
		
		// Renderizar anexo se existir
		if (!empty($arrMensagens["anexo_base64"])) {
			$anexoBase64 = $arrMensagens["anexo_base64"];
			$tipoArquivo = $arrMensagens["tipo_arquivo"];
			$nomeArquivo = !empty($arrMensagens["nome_arquivo"]) ? htmlspecialchars($arrMensagens["nome_arquivo"]) : "arquivo";
			
			// Usar tipo_arquivo da tabela tbanexos para renderização
			if ($tipoArquivo === 'IMAGE') {
				// É uma imagem
				echo '<div style="margin: 8px 0; border-radius: 8px; overflow: hidden;">';
				echo '<img src="' . htmlspecialchars($anexoBase64) . '" style="max-width: 100%; max-height: 400px; display: block; cursor: pointer;" onclick="abrirImagemGrande(this.src)" alt="' . $nomeArquivo . '" />';
				echo '</div>';
			} else if ($tipoArquivo === 'AUDIO') {
				// É um áudio
				$tipoAudio = 'audio/mpeg'; // padrão
				if (preg_match('/^data:([^;]+);/', $anexoBase64, $matches)) {
					$tipoAudio = $matches[1];
				}
				echo '<div style="margin: 8px 0;">';
				echo '<audio controls style="width: 100%; border-radius: 8px;">';
				echo '<source src="' . htmlspecialchars($anexoBase64) . '" type="' . htmlspecialchars($tipoAudio) . '">';
				echo 'Seu navegador não suporta reprodução de áudio.';
				echo '</audio>';
				echo '</div>';
			} else if ($tipoArquivo === 'PDF') {
				// É um PDF
				echo '<div class="anexo-container">';
				echo '<i class="bi bi-file-pdf"></i>';
				echo '<a href="' . htmlspecialchars($anexoBase64) . '" download="' . $nomeArquivo . '">📄 ' . $nomeArquivo . '</a>';
				echo '<i class="bi bi-download"></i>';
				echo '</div>';
			} else {
				// Arquivo genérico
				echo '<div class="anexo-container" style="border-left-color: #ffc107;">';
				echo '<i class="bi bi-file-earmark" style="color: #ffc107;"></i>';
				echo '<a href="' . htmlspecialchars($anexoBase64) . '" download="' . $nomeArquivo . '">' . $nomeArquivo . '</a>';
				echo '</div>';
			}
		}
		
		// Texto da mensagem
		echo '<div class="message-text" data-original="' . htmlspecialchars($arrMensagens["mensagem"]) . '">' . nl2br(htmlspecialchars($arrMensagens["mensagem"])) . '</div>';
		
		// Timestamp
		echo '<div class="message-time">' . $arrMensagens["hora"] . '</div>';
		
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
?>
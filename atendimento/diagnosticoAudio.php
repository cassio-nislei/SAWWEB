<?php
    require_once("../includes/padrao.inc.php");

    // Retorna informações sobre um arquivo de áudio no banco
    $id_atendimento = isset($_POST["id"]) ? $_POST["id"] : "";
    $seq = isset($_POST["seq"]) ? $_POST["seq"] : "";

    if (empty($id_atendimento) || empty($seq)) {
        echo json_encode([
            "status" => "erro",
            "msg" => "ID Atendimento ou Sequência não fornecidos"
        ]);
        exit;
    }

    // Buscar arquivo
    $sql = "SELECT seq, arquivo, nome_arquivo, tipo_arquivo FROM tbanexos 
            WHERE id = '" . $id_atendimento . "' AND seq = '" . $seq . "' AND tipo_arquivo = 'PTT'";
    
    $result = mysqli_query($conexao, $sql);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        echo json_encode([
            "status" => "erro",
            "msg" => "Arquivo não encontrado: ID=$id_atendimento, SEQ=$seq"
        ]);
        exit;
    }

    $binario = $row['arquivo'];
    $nomeArquivo = $row['nome_arquivo'];
    $tamanho = strlen($binario);
    
    // Detectar tipo real do arquivo pelos bytes iniciais (magic bytes)
    $tipoDetetado = "desconhecido";
    $extensaoReal = "";
    
    // Primeiros bytes para identificação
    $primeirosByte = substr($binario, 0, 12);
    $hex = bin2hex($primeirosByte);

    // Magic bytes para MP3: FF FA ou FF FB (MPEG Audio)
    if (strpos($hex, "fffa") === 0 || strpos($hex, "fffb") === 0) {
        $tipoDetetado = "MP3 (MPEG Audio válido)";
        $extensaoReal = "mp3";
    }
    // Magic bytes para WebM: 1A 45 DF A3
    else if (strpos($hex, "1a45dfa3") === 0) {
        $tipoDetetado = "WebM (Matroska valid)";
        $extensaoReal = "webm";
    }
    // Magic bytes para OGG: 4F 67 67 53
    else if (strpos($hex, "4f676753") === 0) {
        $tipoDetetado = "OGG/Opus (válido)";
        $extensaoReal = "ogg";
    }
    // Magic bytes para WAV: 52 49 46 46
    else if (strpos($hex, "52494646") === 0) {
        $tipoDetetado = "WAV (válido)";
        $extensaoReal = "wav";
    }
    // Magic bytes para M4A/MP4: 00 00 00 18/20/28 ftypmp4a
    else if (preg_match('/^00000...[^0]ftypisom|^00000...[^0]ftypmp4a/i', $hex)) {
        $tipoDetetado = "MP4/M4A (válido)";
        $extensaoReal = "m4a";
    }
    else {
        $tipoDetetado = "Desconhecido (pode estar corrompido)";
        $extensaoReal = "?";
    }

    // Verificar se arquivo é válido
    $ehValido = in_array($extensaoReal, ['mp3', 'webm', 'ogg', 'wav', 'm4a']);

    // Verificar MIME type
    $mimeType = mime_content_type($binario); // Deprecated mas funciona
    if (!$mimeType) {
        $mimeType = "não detectado";
    }

    // Estimativa de duração (aproximada)
    // WebM: ~1000 bytes por 1 segundo (muito aproximado)
    // MP3 128kbps: ~16000 bytes por 1 segundo
    $duracao_estimada = "?";
    if ($extensaoReal === "mp3") {
        $duracao_estimada = round($tamanho / 16000, 1) . "s";
    } else if ($extensaoReal === "webm") {
        $duracao_estimada = round($tamanho / 1000, 1) . "s (aproximado)";
    }

    // Extrair extensão do nome do arquivo
    $extensaoArmazenada = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

    // Status de compatibilidade
    $compatibilidade = [];
    if ($extensaoReal === "mp3") {
        $compatibilidade = [
            "WhatsApp Web" => "✅ Sim",
            "WhatsApp Windows" => "✅ Sim",
            "WhatsApp Android" => "✅ Sim (recomendado)",
            "WhatsApp iOS" => "✅ Sim"
        ];
    } else if ($extensaoReal === "webm") {
        $compatibilidade = [
            "WhatsApp Web" => "✅ Sim",
            "WhatsApp Windows" => "✅ Sim",
            "WhatsApp Android" => "❌ Pode dar erro",
            "WhatsApp iOS" => "❌ Pode dar erro"
        ];
    } else if ($extensaoReal === "ogg") {
        $compatibilidade = [
            "WhatsApp Web" => "⚠️ Parcial",
            "WhatsApp Windows" => "⚠️ Parcial",
            "WhatsApp Android" => "❌ Pode dar erro",
            "WhatsApp iOS" => "❌ Pode dar erro"
        ];
    } else {
        $compatibilidade = [
            "WhatsApp Web" => "❌ Pode falhar",
            "WhatsApp Windows" => "❌ Pode falhar",
            "WhatsApp Android" => "❌ Pode falhar",
            "WhatsApp iOS" => "❌ Pode falhar"
        ];
    }

    // Recomendação
    $recomendacao = "";
    if ($extensaoReal === "mp3" && $ehValido) {
        $recomendacao = "✅ Arquivo está OK para Android! Se ainda der erro, tente reinstalar WhatsApp Android.";
    } else if ($extensaoReal === "webm") {
        $recomendacao = "⚠️ Arquivo é WebM - ISSO É O PROBLEMA! Android não consegue abrir WebM do WhatsApp. Instale FFmpeg no servidor para converter para MP3.";
    } else if (!$ehValido) {
        $recomendacao = "❌ ARQUIVO CORROMPIDO! Os bytes iniciais não batem com nenhum formato válido. Erro na conversão FFmpeg ou na transmissão.";
    }

    echo json_encode([
        "status" => "sucesso",
        "analise" => [
            "nomeArquivo" => $nomeArquivo,
            "tamanho" => $tamanho . " bytes",
            "extensaoArmazenada" => $extensaoArmazenada,
            "tipoDetetado" => $tipoDetetado,
            "extensaoReal" => $extensaoReal,
            "ehValido" => $ehValido ? "Sim" : "Não",
            "primeirosBytesHex" => $hex,
            "duracao_estimada" => $duracao_estimada,
            "mimeType" => $mimeType
        ],
        "compatibilidade" => $compatibilidade,
        "recomendacao" => $recomendacao,
        "acoes" => [
            "Se não é MP3" => "Instale FFmpeg no servidor (apt-get install ffmpeg)",
            "Se arquivo corrompido" => "Verifique logs em /var/log/php-fpm.log ou /var/log/apache2/error.log",
            "Para Android" => "Sempre use MP3, não WebM ou OGG"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>

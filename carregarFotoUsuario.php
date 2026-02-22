<?php
require_once("includes/padrao.inc.php");

// Pegar ID do usuário logado
$idUsuario = isset($_SESSION["usuariosaw"]["id"]) ? $_SESSION["usuariosaw"]["id"] : '';

if (empty($idUsuario)) {
    // Retornar imagem padrão se sem usuário
    header('Location: images/user-default.png');
    exit;
}

// Buscar foto do usuário
$sql = "SELECT foto FROM tbusuario WHERE id = '" . intval($idUsuario) . "' LIMIT 1";
$resultado = mysqli_query($conexao, $sql);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    
    // Se tem foto gravada
    if (!empty($usuario['foto'])) {
        $fotoData = $usuario['foto'];
        
        // Detectar tipo de imagem do prefixo data URI
        $tipoImagem = 'image/png'; // padrão
        if (strpos($fotoData, 'data:image/') === 0) {
            // Extrair tipo: data:image/TYPE;base64,
            preg_match('/data:image\/(.*?);base64,/', $fotoData, $matches);
            if (!empty($matches[1])) {
                $tipoImagem = 'image/' . $matches[1];
            }
            // Remover prefixo para obter apenas base64
            $fotoData = preg_replace('/data:image\/.*?;base64,/', '', $fotoData);
        }
        
        // Decodificar base64 e retornar como imagem binária
        $imagemBinaria = base64_decode($fotoData);
        if ($imagemBinaria !== false) {
            header('Content-Type: ' . $tipoImagem);
            header('Content-Length: ' . strlen($imagemBinaria));
            header('Cache-Control: public, max-age=3600');
            echo $imagemBinaria;
            exit;
        }
    }
}

// Se não encontrou foto, retorna um placeholder SVG como imagem
$placeholderSVG = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><rect fill="#e0e0e0" width="200" height="200"/><circle cx="100" cy="70" r="30" fill="#999"/><ellipse cx="100" cy="140" rx="40" ry="35" fill="#999"/></svg>';
header('Content-Type: image/svg+xml; charset=utf-8');
header('Content-Length: ' . strlen($placeholderSVG));
header('Cache-Control: public, max-age=3600');
echo $placeholderSVG;
exit;
?>

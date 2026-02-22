<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

if ($_SESSION["chat"]["menu"]==true){
     echo'<div id="ListarMenu">'; 
     include("menu.php");
     echo '</div>';  
}else{
    //Mensagem Inicial do Sistema
    echo '
    <div class="message">
        <div class="message-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="bi bi-robot" style="font-size: 0.9rem;"></i>
        </div>
        <div class="message-content">
            <div class="message-info">Sistema</div>
            <div class="message-text">
                <i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i>
                Em breve um de nossos atendentes ira atende-lo, aguarde por favor.
            </div>
        </div>
    </div>';
    
    $numero = $_SESSION["chat"]["numero"];
    $mensagens = mysqli_query($conexao, "select * from tbmsgatendimento where canal = 0 and numero = '$numero' order by seq asc");
    
    while ($listarMensagens = mysqli_fetch_assoc($mensagens)){
        // Se a mensagem é do Atendente
        if ($listarMensagens["id_atend"] > 0){
            $initials = substr($listarMensagens["nome_chat"], 0, 1);
            echo '
            <div class="message">
                <div class="message-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    ' . $initials . '
                </div>
                <div class="message-content">
                    <div class="message-info">' . htmlspecialchars($listarMensagens["nome_chat"]) . '</div>
                    <div class="message-text">
                        ' . nl2br(htmlspecialchars($listarMensagens["msg"])) . '
                    </div>
                </div>
            </div>';
        }
        // Mensagem do Cliente
        else{
            $initials = substr($listarMensagens["nome_chat"], 0, 1);
            echo '
            <div class="message own">
                <div class="message-content">
                    <div class="message-info" style="text-align: right;">Você</div>
                    <div class="message-text">
                        ' . nl2br(htmlspecialchars($listarMensagens["msg"])) . '
                    </div>
                </div>
                <div class="message-avatar" style="background: linear-gradient(135deg, #198754 0%, #0d6efd 100%);">
                    ' . $initials . '
                </div>
            </div>';
        }
    }
}
?>



<?php
   require_once("../../../includes/padrao.inc.php");

   if (!validarCSRF()) { echo "Token de segurança inválido."; exit; }

   $msg_aguardando_atendimento   = quebraDeLinha($_POST['msg_aguardando_atendimento']);
   $msg_inicio_atendimento       = quebraDeLinha($_POST['msg_inicio_atendimento']);
   $msg_inicio_atendente         = quebraDeLinha($_POST['msg_inicio_atendente']); 
   $msg_fim_atendimento          = quebraDeLinha($_POST['msg_fim_atendimento']);
   $msg_sem_expediente           = quebraDeLinha($_POST['msg_sem_expediente']);
   $msg_desc_inatividade         = quebraDeLinha($_POST['msg_desc_inatividade']);
   $title                        = @$_POST['title'];
   $minutosOffline               = @$_POST['minutos_offline'];
   $color                        = @$_POST['color'];
   $nome_atendente               = !empty($_POST['nome_atendente']) ? $_POST['nome_atendente'] : "0";
   $chat_operadores              = !empty($_POST['chat_operadores']) ? $_POST['chat_operadores'] : "0";
   $atend_triagem                = !empty($_POST['atend_triagem']) ? $_POST['atend_triagem'] : "0";
   $historico_conversas          = !empty($_POST['historico_conversas']) ? $_POST['historico_conversas'] : "0";
   $iniciar_conversa             = !empty($_POST['iniciar_conversa']) ? $_POST['iniciar_conversa'] : "0";
   $env_resprapida_aut           = !empty($_POST['enviar_resprapida_aut']) ? $_POST['enviar_resprapida_aut'] : "0";
   $enviar_audio_aut             = !empty($_POST['enviar_audio_aut']) ? $_POST['enviar_audio_aut'] : "0";
   $enviar_foto_aut              = !empty($_POST['enviar_foto_aut']) ? $_POST['enviar_foto_aut'] : "0";
   $qrcode                       = !empty($_POST['qrcode']) ? $_POST['qrcode'] : "0";
   $op_naoenv_ultmsg             = !empty($_POST['op_naoenv_ultmsg']) ? $_POST['op_naoenv_ultmsg'] : "0";
   $exibir_foto_perfil           = !empty($_POST['exibir_foto_perfil']) ? $_POST['exibir_foto_perfil'] : "0";
   $alerta_sonoro                = !empty($_POST['alerta_sonoro']) ? $_POST['alerta_sonoro'] : "0";
   $mostra_todos_chats           = !empty($_POST['mostra_todos_chats']) ? $_POST['mostra_todos_chats'] : "0";
   $transferencia_offline        = !empty($_POST['transferencia_offline']) ? $_POST['transferencia_offline'] : "0";
   $envia_uma_msg_sem_expediente = !empty($_POST['envia_uma_msg_sem_expediente']) ? $_POST['envia_uma_msg_sem_expediente'] : "0";
   $nao_usar_menu                = !empty($_POST['nao_usar_menu']) ? $_POST['nao_usar_menu'] : "0";
   $departamento_atendente       = !empty($_POST['departamento_atendente']) ? $_POST['departamento_atendente'] : "0";
   $contar_tempo_espera_so_dos_clientes = !empty($_POST['contar_tempo_espera_so_dos_clientes']) ? $_POST['contar_tempo_espera_so_dos_clientes'] : "0";
   $historico_atendimento       = !empty($_POST['historico_atendimento']) ? $_POST['historico_atendimento'] : "0";
   $usar_protocolo              = !empty($_POST['usar_protocolo']) ? $_POST['usar_protocolo'] : "0";
   $usar_pesquisa_satisfacao    = !empty($_POST['usar_pesquisa_satisfacao']) ? $_POST['usar_pesquisa_satisfacao'] : "0";
   $tipo_menu                   = !empty($_POST['tipo_menu']) ? $_POST['tipo_menu'] : "0";
   
   $foto = !empty($_POST["foto2"]) ? mysqli_real_escape_string($conexao, trim($_POST["foto2"])) : "";

	// DEBUG - removido log verboso
	
	// Verificar se a coluna enviar_foto_aut existe (pode não existir em versões antigas)
	$colEnviarFotoAut = mysqli_query($conexao, "SHOW COLUMNS FROM tbparametros LIKE 'enviar_foto_aut'");
	$temColEnviarFotoAut = ($colEnviarFotoAut && mysqli_num_rows($colEnviarFotoAut) > 0);
	if (!$temColEnviarFotoAut) {
		// Tentar criar a coluna automaticamente
		mysqli_query($conexao, "ALTER TABLE tbparametros ADD COLUMN enviar_foto_aut TINYINT(1) DEFAULT 0");
		$colEnviarFotoAut2 = mysqli_query($conexao, "SHOW COLUMNS FROM tbparametros LIKE 'enviar_foto_aut'");
		$temColEnviarFotoAut = ($colEnviarFotoAut2 && mysqli_num_rows($colEnviarFotoAut2) > 0);
	}

	// Sanitizar os inputs com escape
	$title = mysqli_real_escape_string($conexao, $title);
	$minutosOffline = intval($minutosOffline);
	$color = mysqli_real_escape_string($conexao, $color);

	// Atualiza a cor da tarja na Sessão //
	$_SESSION["parametros"]["color"] = $color;
	
   // Atualiza o parametro na Sessão //
	$_SESSION["parametros"]["mostra_todos_chats"] = $mostra_todos_chats;
	
	// Verifico se já existe um departamento com o mesmo nome Cadastrado
   $existe = mysqli_query($conexao,"select * from tbparametros ");

   if( mysqli_num_rows($existe) <= 0 ){
      $sql = "INSERT tbparametros VALUES (1
         , (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_inicio_atendimento.")
         , (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_aguardando_atendimento.")
         , (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_fim_atendimento.")
         , (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_sem_expediente.")
         , (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_desc_inatividade.")
         , '".$title."'
         , '".$minutosOffline."'
         , '".$color."'
         , '".$nome_atendente."'
         , '".$chat_operadores."'
         , '".$atend_triagem."'
         , '".$historico_conversas."'
         , '".$iniciar_conversa."'
         , '".$env_resprapida_aut."'
         , '".$enviar_audio_aut."'
         , '".$qrcode."'
         , '".$op_naoenv_ultmsg."'
         , '".$exibir_foto_perfil."'
         , '".$alerta_sonoro."'
         , '".$mostra_todos_chats."'
         , '".$transferencia_offline."'
         , '".$envia_uma_msg_sem_expediente."'
         , '".$nao_usar_menu."'
         , '".$departamento_atendente."'
         , '".$contar_tempo_espera_so_dos_clientes."'
         , '".$historico_atendimento."'
         , '".$usar_protocolo."'
         , '".$usar_pesquisa_satisfacao."'
         , '".$tipo_menu."'
         , '".$msg_inicio_atendente."'";

      if( $foto != "" ){
         $sql .= ",'".$foto."')";
      }
      else{ $sql .= ")"; }

      $atualizar = mysqli_query($conexao,$sql);
      
      if ($atualizar){ echo "1"; }
   }
   else{
      $sql = "UPDATE tbparametros 
                  SET   
                    msg_inicio_atendimento = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_inicio_atendimento.")
                  , msg_aguardando_atendimento = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_aguardando_atendimento.")
                  , msg_fim_atendimento        = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_fim_atendimento.")
                  , msg_sem_expediente = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_sem_expediente.")
                  , msg_desc_inatividade = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_desc_inatividade.")
                  , title = '".$title."'
                  , minutos_offline = '".$minutosOffline."'
                  , color = '".$color."'
                  , nome_atendente = '".$nome_atendente."'
                  , chat_operadores = '".$chat_operadores."'
                  , atend_triagem = '".$atend_triagem."'
                  , historico_conversas = '".$historico_conversas."'
                  , iniciar_conversa = '".$iniciar_conversa."'
                  , enviar_resprapida_aut = '".$env_resprapida_aut."'
                  , enviar_audio_aut = '".$enviar_audio_aut."'";
      
      // Só incluir enviar_foto_aut se a coluna existir
      if ($temColEnviarFotoAut) {
         $sql .= ", enviar_foto_aut = '".$enviar_foto_aut."'";
      }
      
      $sql .= "
                  , qrcode = '".$qrcode."'
                  , op_naoenv_ultmsg = '".$op_naoenv_ultmsg."'
                  , exibe_foto_perfil = '".$exibir_foto_perfil."'
                  , alerta_sonoro = '".$alerta_sonoro."'
                  , mostra_todos_chats = '".$mostra_todos_chats."'
                  , transferencia_offline = '".$transferencia_offline."'
                  , envia_uma_msg_sem_expediente = '".$envia_uma_msg_sem_expediente."'
                  , nao_usar_menu = '".$nao_usar_menu."'
                  , departamento_atendente = '".$departamento_atendente."'
                  , contar_tempo_espera_so_dos_clientes = '".$contar_tempo_espera_so_dos_clientes."'
                  , historico_atendimento = '".$historico_atendimento."'
                  , usar_protocolo = '".$usar_protocolo."'
                  , usar_pesquisa_satisfacao = '".$usar_pesquisa_satisfacao."'
                  , tipo_menu = '".$tipo_menu."'
                  , msg_inicio_atendente = (CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$msg_inicio_atendente.")";

      if( $foto != "" ){
         $sql .= ", imagem_perfil = '".$foto."'";
      }

      $sql .= " WHERE id = 1";

      $atualizar = mysqli_query($conexao,$sql);

      if (!$atualizar) {
         error_log("Erro ao atualizar parametros: " . mysqli_error($conexao));
      }

      if ($atualizar){ 
         echo "1"; 
      }
      else{
         error_log("Erro UPDATE parametros: " . mysqli_error($conexao));
      }
   }
<?php
 session_start();
  if (isset($_SESSION["usuariosaw"])){
    header("Location: conversas.php");
    exit;
  }
  session_write_close(); // Libera session lock o mais cedo possível
?>
<html class="" dir="ltr" loc="pt-BR" lang="pt-BR">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Saw login</title>
	<meta name="viewport" content="width=device-width">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/style.css">

	<script src="js/jquery-3.6.0.min.js"></script>
	<script>
		$(function () {
			$('.senha').click(function () {
				$('.viewCampo').slideToggle();
				$(this).toggleClass('active');
				return false;
			});
			$('#usuario').focus();
		});
	</script>
</head>

<body class="login">
	<div class="colluns">
		<div class="col">
			<div class="content-login">
				<img src="img/uptalk-logo.png">
				<h2>Login</h2>
				<form id="FormLogin" method="post">
					<?php 
						session_start();
						if (!isset($_SESSION['csrf_token'])) {
							$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
						}
						echo '<input type="hidden" name="csrf_token" value="'.htmlspecialchars($_SESSION['csrf_token']).'">';
						session_write_close(); // Libera lock antes de conexão DB
					?>
					<label>Usuário</label>
					<input type="text" name="usuario" id="usuario" placeholder="Digite seu usuário" class="form-control">
					<label>Senha</label>
					<input type="password" name="senha" id="senha" placeholder="Digite sua senha" class="form-control login-form">
					<input type="button" id="btnLogin" value="Entrar" name="" class="btn btn-azul">
				</form>

				<?php
				  require("includes/conexao.php");

				  //Verifico se possui parametros Cadastrado
				  $parametros = mysqli_query($conexao, "select * from tbparametros limit 1" ) or die (mysqli_error($conexao));
				  if (mysqli_num_rows( $parametros)<1){ //Se não possuir algum usuário
					$insereParametro = mysqli_query($conexao, "INSERT INTO tbparametros (id, msg_inicio_atendimento, msg_aguardando_atendimento, msg_inicio_atendente, msg_fim_atendimento, msg_sem_expediente, msg_desc_inatividade, imagem_perfil, title, minutos_offline, color, nome_atendente, chat_operadores, atend_triagem, historico_conversas, iniciar_conversa, enviar_resprapida_aut, enviar_audio_aut, qrcode, op_naoenv_ultmsg, exibe_foto_perfil, alerta_sonoro, mostra_todos_chats, transferencia_offline, id_atendente_triagem, enviar_foto_aut) VALUES
					(1, 'Olá,seja bem-vindo(a) ao *Auto atendimento* 😄 _Selecione uma das opções a baixo para continuar o atendimento_ 😉', 
					'Seu atendimento foi transferido para *<<setor>>*.', 
					'Olá você está no setor *<<setor>>*, me chamo *<<atendente>>* em que posso lhe ajudar?*.', 
					'O seu atendimento foi finalizado, agradecemos pelo seu contato, tenha um ótimo dia 😉*', 					
					'Nosso horario de funcionamento é de segunda a sexta das 07:30 às 18:00 e aos sábados das 08:00 às 12:00, responderemos seu chamado assim que possivel!', '', 
					'', 'Sistema de Atendimento', '5', '#ff9214', 0, 1, 1, 1, 0, 0, 0, 1, 0, 0, 1, 0, 1, 0, 0);")or die (mysqli_error($conexao)); 
					 echo "<font color='red'>Parametros padrões configurados</font><br>";
				  }

				   //Verifico se possui Horarios de Funcionamento Cadastrados
				   $parametros = mysqli_query($conexao, "select * from tbhorarios limit 1" ) or die (mysqli_error($conexao));
				   if (mysqli_num_rows( $parametros)<1){ //Se não possuir algum usuário
					 $insereParametro = mysqli_query($conexao, "INSERT INTO tbhorarios (id, dia_semana, hr_inicio, hr_fim, fechado) VALUES
					 (1, 6, NULL, NULL, 1),
					 (2, 0, '07:30:00', '17:30:00', 0),
					 (3, 1, '07:30:00', '17:30:00', 0),
					 (4, 2, '07:30:00', '17:30:00', 0),
					 (5, 3, '07:30:00', '17:30:00', 0),
					 (6, 4, '07:30:00', '17:30:00', 0),
					 (7, 5, '08:30:00', '12:00:00', 0);")or die (mysqli_error($conexao)); 
					  echo "<font color='red'>Horarios de funcionamento padrões configurados</font><br>";
				   }
			
				  //Verifico se possui algum usuário, se não possuir, crio um novo usuário
				  $usuarios = mysqli_query($conexao, "select * from tbusuario limit 2" ) or die (mysqli_error($conexao));
                     if (mysqli_num_rows($usuarios)<1){ //Se não possuir algum usuário
                       $senhaAdmin = password_hash('123456', PASSWORD_BCRYPT);
                       $stmtAdmin = mysqli_prepare($conexao, "INSERT INTO tbusuario VALUES (0, 'Administrador','admin', ?, 'A', null, 'Administrador', 0, now(), 0, '', 'administrador@saw.com.br')");
                       mysqli_stmt_bind_param($stmtAdmin, 's', $senhaAdmin);
                       $insereUsuario = mysqli_stmt_execute($stmtAdmin);
                       mysqli_stmt_close($stmtAdmin); 
                        echo "<font color='red'>Usuário padrão:admin Senha: 123456 </font>";
                     }
                     if (mysqli_num_rows($usuarios)==1){
                       $administrador = mysqli_fetch_assoc($usuarios);
                       // Default admin check removed for security
                     }
				?>

				<!-- Wagner: Feature ainda não implementado! <a href="" class="senha"><small>Esqueceu a senha?</small></a> -->

				<!-- Wagner: Feature ainda não implementado! 
				<div class="viewCampo">
					<span class="senha fechar">x</span>

					<img src="img/uptalk-logo.png">
					<h2>Redefinir senha</h2>
					<small>Digite seu email abaixo para recuperar sua senha</small>
					<form action="" method="">
						<label>Email</label>
						<input type="text" placeholder="Digite seu email" class="form-control">
						<input type="submit" value="Recuperar senha" name="" class="btn btn-azul">
						<a href="" class="senha voltar"><i class="fas fa-arrow-left"></i> Voltar para login</a>
					</form>
				</div>
				-->
			</div>
		</div>
	</div>

	<script src="js/main.js"></script>
	<script src="js/login.js"></script>
</body>
</html>
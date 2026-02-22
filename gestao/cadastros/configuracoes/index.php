<?php
	require_once("../includes/padrao.inc.php");

	$existe = mysqli_query(
		$conexao
		, "SELECT * FROM tbparametros"
	);

	$msg_aguardando 		= "";
	$msg_inicio     		= "";
	$msg_inicio_atendente   = "";
	$msg_fim        		= "";
	$title					= "";
	$minutosOffline			= "";
	$color					= "";
	$foto           		= "images/sem_foto.png";
	$isCkdNomeAtendente		= "";
	$isCkdChat				= "";
	$isCkdHistorico			= "";
	$isCkdAtendTriagem		= "";
	$isCkdIniciarConversa	= "";
	$isCkdEnvRespRapidaAut	= "";
	$isCkdEnvAudioAut		= "";
	$isCkdQRCode			= "";
	$isCkdNaoEnvUltMensagem	= "";
    $isCkdExibirFotoPerfil	= "";
	$isCkdAlertaSonoro		= "";
	$isCkdMostraTodosChats	= "";
	$isCkdTransfAtendOffline= "";
	$isCkdenvia_uma_msg_sem_expediente = "";
	$isCkdnao_usar_menu     = "";
	$isCkdDepartamntoAtendente = "";
	$isCkdcontar_tempo_espera_so_dos_clientes = "";
	$isCkdhistorico_atendimento = "";
	$isCkdusar_protocolo        = "";
	$isCkdusar_pesquisa_satisfacao = "";
//	$isCkdund_neg           = "";
//	$link1                  = "";
//	$link2                  = "";
//	$link3                  = "";
//	$msg_equipe_atende 		= "";
//	$msg_equipe_atende2     = "";
//	$msg_cliente_canvas     = "";

	if( mysqli_num_rows($existe) > 0 ){
		$msg = mysqli_fetch_array($existe);
		$msg_aguardando 		= $msg["msg_aguardando_atendimento"];
		$msg_inicio     		= str_replace("\\n","<br/>",$msg["msg_inicio_atendimento"]); //Essa mensagem é a resposta automatica assim que uma mensagem é enviada pelo cliente
		$msg_fim        		= $msg["msg_fim_atendimento"];
		$msg_inicio_atendente   = $msg["msg_inicio_atendente"]; //Essa mensagem será exibida quando um Atendente Aceita um atendimento
		$msg_sem_expediente 	= $msg["msg_sem_expediente"];
		$msg_desc_inatividade	= $msg["msg_desc_inatividade"];
		$title					= $msg["title"];
		$minutosOffline			= $msg["minutos_offline"];
		$color					= empty($msg["color"]) ? colorTarja : $msg["color"];
		$nome_atendente			= $msg["nome_atendente"];
		$chat_operadores		= $msg["chat_operadores"];
		$atend_triagem			= $msg["atend_triagem"];
		$historico_conversas	= $msg["historico_conversas"];
		$iniciar_conversa		= $msg["iniciar_conversa"];
		$env_resprapida_aut		= $msg["enviar_resprapida_aut"];
		$enviar_audio_aut		= $msg["enviar_audio_aut"];
		$qrcode					= $msg["qrcode"];
		$op_naoenv_ultmsg		= $msg["op_naoenv_ultmsg"];
		$exibe_foto_perfil  	= $msg["exibe_foto_perfil"];
		$alerta_sonoro			= $msg["alerta_sonoro"];
		$mostra_todos_chats		= $msg["mostra_todos_chats"];
		$transf_offline			= $msg["transferencia_offline"];
		$envia_uma_msg_sem_expediente = $msg["envia_uma_msg_sem_expediente"];
		$nao_usar_menu          = $msg["nao_usar_menu"];
		$departamento_atendente = $msg["departamento_atendente"];
		$contar_tempo_espera_so_dos_clientes = $msg["contar_tempo_espera_so_dos_clientes"];
		$historico_atendimento  = $msg["historico_atendimento"];
		$usar_protocolo         = $msg["usar_protocolo"];
		$tipo_menu              = $msg["tipo_menu"];
		$usar_pesquisa_satisfacao = $msg["usar_pesquisa_satisfacao"];
	//	$und_neg                = $msg["und_neg"];
	//	$link1                  = $msg["link1"];
	//	$link2                  = $msg["link2"];
	//	$link3                  = $msg["link3"];
    //    $msg_equipe_atende      = $msg["msg_equipe_atende"];
	//	$msg_equipe_atende2     = $msg["msg_equipe_atende2"];
	//	$msg_cliente_canvas     = $msg["msg_cliente_canvas"];
	//Comentei essas colunas que foram adicionadas pelo Cassio para criar alguns recursos como mensagem personalizada por atendente e envio dos links das redes sociais
	//não acho interessante esses recursos ainda nesse momento //André Luiz 23/03/2023
		
	
		if( empty($msg["imagem_perfil"]) ){
			$foto = 'images/sem_foto.png';	
		}
		else{ $foto = $msg["imagem_perfil"]; }

		// Mostra Nome do Atendente/Departamento //
		if( $nome_atendente === "1" ){ $isCkdNomeAtendente = "checked"; }

		// Mostra Nome do Atendente/Departamento //
		if( $departamento_atendente === "1" ){ $isCkdDepartamntoAtendente = "checked"; }
		

		// Libera Chat //
		if( $chat_operadores === "1" ){ $isCkdChat = "checked"; }

		// Libera 'Atendimentos sem Setor' para os Operadores //
		if( $atend_triagem === "1" ){ $isCkdAtendTriagem = "checked"; }

		// Libera 'Histórico da Conversa' para os Operadores //
		if( $historico_conversas === "1" ){ $isCkdHistorico = "checked"; }

		// Libera 'Iniciar Conversa' para os Operadores //
		if( $iniciar_conversa === "1" ){ $isCkdIniciarConversa = "checked"; }

		// Enviar 'Resposta Rápida' Automaticamente //
		if( $env_resprapida_aut === "1" ){ $isCkdEnvRespRapidaAut = "checked"; }

		// Enviar 'Áudio' Automaticamente ao finalizar a Gravação //
		if( $enviar_audio_aut === "1" ){ $isCkdEnvAudioAut = "checked"; }

		// Permitir a Leitura do 'QRCode' via WEB //
		if( $qrcode === "1" ){ $isCkdQRCode = "checked"; }

		// Permitir o 'Não Envio' da Mensagem de Finalização de Atendimento? //
		if( $op_naoenv_ultmsg === "1" ){ $isCkdNaoEnvUltMensagem = "checked"; }
		
		// Mostrar uma imagem fixa para todos os perfis de clientes //
		if( $exibe_foto_perfil === "1" ){ $isCkdExibirFotoPerfil = "checked"; }

		// Habilitar o Aviso Sonoro de Novas Conversas //
		if( $alerta_sonoro === "1" ){ $isCkdAlertaSonoro = "checked"; }

		// Mostrar atendimentos de todos usuários
		if( $mostra_todos_chats === "1" ){ $isCkdMostraTodosChats = "checked"; }

		// Liberar Transferência para Atendentes Offline
		if( $transf_offline === "1" ){ $isCkdTransfAtendOffline = "checked"; }

		//Enviar Uma Única mensagem Quando o atendimento estiver fora do Horario de Expediente
        if( $envia_uma_msg_sem_expediente === "1" ){ $isCkdenvia_uma_msg_sem_expediente = "checked"; }

		//Não Usar Menu de Atendimento
        if( $nao_usar_menu === "1" ){ $isCkdnao_usar_menu = "checked"; }

		//Contar o tempo apenas das mensagens enviadas pelos Clientes
        if( $contar_tempo_espera_so_dos_clientes === "1" ){ $isCkdcontar_tempo_espera_so_dos_clientes = "checked"; }

		//Filtra as mensagens de Histórico apenas pelo atendimento e não todas as conversas anteriores do número
        if( $historico_atendimento === "1" ){ $isCkdhistorico_atendimento = "checked"; }
		
		//Parametro para Usar numero de protocolos nos atendimentos
        if($usar_protocolo === "1" ){ $isCkdusar_protocolo = "checked"; }

		//Parametro para Usar Pesquisa de Satisfacao
        if($usar_pesquisa_satisfacao === "1" ){ $isCkdusar_pesquisa_satisfacao = "checked"; }



		

		// Ativa Multiplas Unidades de negocios
		//if( $und_neg === "1" ){ $isCkdund_neg = "checked"; }
		
		
	}
?>
<input type="hidden" id="aguardando_atendimento" value="<?php echo $msg["msg_aguardando_atendimento"]; ?>">
<input type="hidden" id="inicio_atendimento" value="<?php echo $msg["msg_inicio_atendimento"]; ?>">
<input type="hidden" id="msg_fim" value="<?php echo $msg["msg_fim_atendimento"]; ?>">
<input type="hidden" id="sem_expediente" value="<?php echo $msg["msg_sem_expediente"]; ?>">
<input type="hidden" id="desc_inatividade" value="<?php echo $msg["msg_desc_inatividade"]; ?>">
<input type="hidden" id="inicio_atendente" value="<?php echo $msg["msg_inicio_atendente"]; ?>">
<script type="text/javascript" src="../js/farbtastic.js"></script>
<link rel="stylesheet" href="../css/farbtastic.css" type="text/css" />
<link href="css/croppie.css" rel="stylesheet">
<script src="js/croppie.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script>
	$( document ).ready(function() {
		$("#msg_aguardando_atendimento").val($("#aguardando_atendimento").val());
		$("#msg_inicio_atendimento").val($("#inicio_atendimento").val());
		$("#msg_inicio_atendente").val($("#inicio_atendente").val());
		$("#msg_fim_atendimento").val($("#msg_fim").val());
		$("#msg_sem_expediente").val($("#sem_expediente").val());
		$("#msg_desc_inatividade").val($("#desc_inatividade").val());
		
		// Fechar a Janela //
		$('.fechar').on("click", function(){ fecharModal(); });

	});
</script>


<div class="container-fluid">
 <div class="panel panel-default">
	<div class="panel-heading"><b>Configurações</b></div>
    <div class="panel-body">
		<form method="post" id="gravaConfiguracoes" name="grava" action="cadastros/configuracoes/salvar.php">
	     <ul class="nav nav-tabs" id="myTab" role="tablist">
         <li class="nav-item" role="presentation">
           <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Descrições</button>
         </li>
          <li class="nav-item" role="presentation">
           <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Opções</button>
          </li>
         
         </ul>
		 
		  <div class="tab-content" id="myTabContent">
           <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
				<div class="row">
					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem de [Aguardando Atendimento], use <font color=red><< setor >></font> caso queira exibir o nome do setor na frase </b> </div>
						<textarea class="uk-textarea" type="text" id="msg_aguardando_atendimento" name="msg_aguardando_atendimento" rows="4" 
							placeholder="Informe a mensagem de [Aguardando Atendimento]"></textarea>
					</div>

					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem de Início de Atendimento</b> </div>
						<textarea class="uk-textarea" type="text" id="msg_inicio_atendimento" name="msg_inicio_atendimento" rows="4" 
							placeholder="Informe a mensagem de [Início de Atendimento]"></textarea>
					</div>

					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem Aceite de atendimento , use <font color=red><< setor >></font> caso queira exibir o nome do setor ou <font color=red><< atendente >></font> caso queira exibir o nome do atendente na frase</b> </div>
						<textarea class="uk-textarea" type="text" id="msg_inicio_atendente" name="msg_inicio_atendente" rows="4" 
							placeholder="Informe a mensagem de [Início de Aceite]"></textarea>
					</div>

					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem de Fim de Atendimento</b> </div>
						<textarea class="uk-textarea" type="text" id="msg_fim_atendimento" name="msg_fim_atendimento" rows="4" 
							placeholder="Informe a mensagem de [Fim de Atendimento]"></textarea>
					</div>

					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem de Sem Expediente</b> </div>
						<textarea class="uk-textarea" type="text" id="msg_sem_expediente" name="msg_sem_expediente" rows="4" 
							placeholder="Informe a mensagem de [Sem Expediente]"></textarea>
					</div>

					<div class="col-md-6 col-12 mb-3">
						<div class="uk-form-label"> <b>Mensagem de Desconexão por Inatividade</b> </div>
						<textarea class="uk-textarea" type="text" id="msg_desc_inatividade" name="msg_desc_inatividade" rows="4" 
							placeholder="Informe a mensagem de [Desconexão por Inatividade]"></textarea>
					</div>
				</div>
			</div>
           <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
			
   			<div style="width: 49%; float: left;">
				<div class="uk-width-1-1@m">
					<div class="uk-form-label"> <b>Parâmetros Adicionais</b> </div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="nome_atendente" name="nome_atendente" value="1" <?php echo $isCkdNomeAtendente; ?> /> Mostra Nome do Atendente no Chat
					</div>
				</div>
				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="departamento_atendente" name="departamento_atendente" value="1" <?php echo $isCkdDepartamntoAtendente; ?> /> Mostra Departamento no Chat
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="chat_operadores" name="chat_operadores" value="1" <?php echo $isCkdChat; ?> /> Liberar Chat entre Setores
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="atend_triagem" name="atend_triagem" value="1" <?php echo $isCkdAtendTriagem; ?> /> Liberar 'Atendimentos sem Setor' para Operadores
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="historico_conversas" name="historico_conversas" value="1" <?php echo $isCkdHistorico; ?> /> Liberar 'Histórico da Conversa' para Operadores
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="iniciar_conversa" name="iniciar_conversa" value="1" <?php echo $isCkdIniciarConversa; ?> /> Liberar 'Iniciar Conversa' para Operadores
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="enviar_resprapida_aut" name="enviar_resprapida_aut" value="1" <?php echo $isCkdEnvRespRapidaAut; ?> /> Enviar 'Resposta Rápida' Automaticamente
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="enviar_audio_aut" name="enviar_audio_aut" value="1" <?php echo $isCkdEnvAudioAut; ?> /> Enviar 'Áudio' Automaticamente após finalizar a Gravação
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="qrcode" name="qrcode" value="1" <?php echo $isCkdQRCode; ?> /> Permitir a Leitura do 'QRCode' via WEB
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="op_naoenv_ultmsg" name="op_naoenv_ultmsg" value="1" <?php echo $isCkdNaoEnvUltMensagem; ?> /> Permitir o 'Não Envio' da Mensagem de Finalização de Atendimento
					</div>
				</div>
				
				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="exibir_foto_perfil" name="exibir_foto_perfil" value="1" <?php echo $isCkdExibirFotoPerfil; ?> /> Exibir Fotos de Perfil
					</div>
				</div>
				
				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="alerta_sonoro" name="alerta_sonoro" value="1" <?php echo $isCkdAlertaSonoro; ?> /> Habilitar o Aviso Sonoro de Novas Conversas
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="mostra_todos_chats" name="mostra_todos_chats" value="1" <?php echo $isCkdMostraTodosChats; ?> /> Mostrar todos os Atendimentos
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="transferencia_offline" name="transferencia_offline" value="1" <?php echo $isCkdTransfAtendOffline; ?> /> Liberar Transferência para Atendentes Offline
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="envia_uma_msg_sem_expediente" name="envia_uma_msg_sem_expediente" value="1" <?php echo $isCkdenvia_uma_msg_sem_expediente; ?> /> Enviar Mensagem de Sem Expediente uma única vez
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="nao_usar_menu" name="nao_usar_menu" value="1" <?php echo $isCkdnao_usar_menu; ?> /> Não usar Menu (Se Marcar essa Opção o menu não será exibido ao iniciar uma conversa)
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="contar_tempo_espera_so_dos_clientes" name="contar_tempo_espera_so_dos_clientes" value="1" <?php echo $isCkdcontar_tempo_espera_so_dos_clientes; ?> /> Contar tempo de espera da resposta apenas dos clientes.
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="historico_atendimento" name="historico_atendimento" value="1" <?php echo $isCkdhistorico_atendimento; ?> /> Mostrar Histórico somente do Atendimento Selecionado.
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="usar_protocolo" name="usar_protocolo" value="1" <?php echo $isCkdusar_protocolo; ?> /> Usar Protocolo.
					</div>
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label">
						<input class="uk-checkbox" type="checkbox" id="usar_pesquisa_satisfacao" name="usar_pesquisa_satisfacao" value="1" <?php echo $isCkdusar_pesquisa_satisfacao; ?> /> Usar Pesquisa de satisfação no fim do atendimento.
					</div>
				</div>
				
			</div>

		
			     
			<div style="width: 49%; float: right;">

					<div class="uk-width-1-1@m">
				<div class="uk-form-label">Escolha o tipo de Menu! </div>
				<select name='tipo_menu' id="tipo_menu" class='uk-select'>
					<?php
					  if ($tipo_menu==0){
                        echo "<option value='0' selected>Menu em Lista</option>";
					  }else{
						echo "<option value='0'>Menu em Lista</option>";
					  }
					  if ($tipo_menu==1){
                        echo "<option value='1' selected>Menu em Texto Númerico</option>";
					  }else{
						echo "<option value='1'>Menu em Texto Númerico</option>";
					  }
					  if ($tipo_menu==2){
                        echo "<option value='2' selected>Menu em Botão</option>";
					  }else{
						echo "<option value='2'>Menu em Botão</option>";
					  }
					?>			
					
					
				</select>
				<div id="valida_menu" style="display: none" class="msgValida">
					Por favor, Selecione um item de Menu.
				</div>
				</div>


				<div class="uk-width-1-1@m">
					<div class="uk-form-label"> <b>Título da Página</b> </div>
					<input class="uk-input" type="text" id="title" name="title" value="<?php echo $title; ?>" />
				</div>

				<div class="uk-width-1-1@m">
					<div class="uk-form-label"> 
						<b>Quantide de Minutos para definir que um Operador está Offline <br />
							<span style="font-style: italic; color: red;">(não pode ser menor que 5 minutos)</span>
						</b>
					</div>
					<input class="uk-input" type="text" id="minutos_offline" name="minutos_offline" value="<?php echo $minutosOffline; ?>" />
				</div>

				<div class="uk-width-1-1@m" style="height: 230px; width: 45%; float: left;">
					<div class="uk-form-label"> <b>Escolha uma cor para a tarja de fundo!</b> </div>
					<input class="uk-input" type="text" id="color" name="color" value="<?php echo $color; ?>" style="width: 85px;" />
					<div id="colorpicker" style="float: right;"></div>
				</div>

				<div class="uk-width-1-1@m" style="width: 45%; float: right;">
					<div class="uk-form-label"> <b>Foto do Perfil</b> </div>
					<div class="col-md-2">
						<div id="visualizar">
							<img src="<?php echo $foto; ?>" id="foto_carregada" width="120" height="120" style="border-radius: 60px;" />
						</div>
					</div>

					<div class="col-md-10" align="left" style="margin-top: 15px;">
						<div class="uk-form-file">
							<input type="file" name="foto" id="foto" />
							<input type="hidden" name="foto2" id="foto2" />
						</div>		
					</div>
				</div>
			 </div>
			 </div>
            
             </div>	
		    			
        	
			<div style="clear: both;"></div>
		</form>
	

     </div>
  </div>
</div>

<div class="container">
    <div class="form-group d-flex justify-content-end">
        <input id="btnGravarConfiguracoes" class="btn btn-primary" type="submit" value="Salvar">
    </div>
</div>

<script type='text/javascript' src="js/funcoes.js"></script>
<script>
	$( document ).ready(function() {

        // JavaScript Document
  $('#colorpicker').farbtastic('#color');

  // Novo Registro //
    $('#btnGravarConfiguracoes').click(function(e){
      e.preventDefault();

      var mensagem  = "<strong>Configurações Gravadas com sucesso!</strong>";
      var mensagem2 = 'Falha ao Efetuar Cadastro!';	 	
      

      $('#gravaConfiguracoes').ajaxForm({
        resetForm: false, 			  
        beforeSend:function() {
          $("#btnGravarConfiguracoes").attr('value', 'Salvando ...');
          $('#FormConfiguracoes').find('input, button').prop('disabled', true);
        },
        success: function( retorno ){
		//	alert(retorno);
          // Mensagem de Atualização efetuado
          if (retorno == 1) { mostraDialogo(mensagem, "success", 2500); }
          // Mensagem de Erro durante a Atualização 
          else{ 
            mostraDialogo(mensagem2, "danger", 2500); }
          //  alert(retorno);
          // Altera a Cor da Tarja //
          $(".backgroundLine").removeAttr( "style");
          $(".backgroundLine").attr("style", "background: " + $("#color").val());
        },		 
        complete:function() {
          $("#btnGravarConfiguracoes").attr('value', 'Salvar Alterações');
          $('#FormConfiguracoes').find('input, button').prop('disabled', false);
        },
        error: function (retorno) { 
          mostraDialogo(mensagem2, "danger", 2500); }
      }).submit();
    });
  // FIM Novo Registro //
	
	
	 //Ações para Efetuar o corte da imagem
	$image_crop = $('#image_demo').croppie({
    enableExif: true,
    viewport: {
      width:200,
      height:200,
      type:'square' //circle
    },
    boundary:{
      width:500,
      height:400
    }
  });

  $('#foto').on('change', function(){
    var reader = new FileReader();
    reader.onload = function (event) {
      $image_crop.croppie('bind', {
        url: event.target.result
      }).then(function(){});
    }
    reader.readAsDataURL(this.files[0]);
    abrirModal('#modalUpload');
  });

  $('.crop_image').click(function(event){
    $image_crop.croppie('result', {
      type: 'canvas',
      size: 'viewport'
    }).then(function(response){
      $.ajax({
        url:"cadastros/configuracoes/upload.php",
        type: "POST",
        data:{"image": response},
        success:function(data){
          // $('#uploadimageModal').modal('hide');
          $('#modalUpload').hide();
          $("#foto_carregada").attr("src",data);
          $("#foto2").val(data);
        }
      });
    })
  });

  // Cancelando o Upload da Imagem //
  $('#btnCancelaUpload').on('click', function (){
    // Fechando a Modal de Confirmação //
    $('#modalUpload').attr('style', 'display: none');
  });

       



	});
</script>
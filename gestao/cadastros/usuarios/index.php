    <link href="gestao/css/estiloinputlabel.css" rel="stylesheet">
	<link href="gestao/cadastros/usuarios/foto-usuario.css" rel="stylesheet">
	<script>
$( document ).ready(function() {	
	
	$('.form_campos').on('focus blur',
function (e) {
  $(this).parents('.form-group').toggleClass('focused', (e.type==='focus' || this.value.length > 0));
}
).trigger('blur');
 $('.select').on('change blur',
function (e) {
  $(this).parents('.form-group-select').toggleClass('focused', (e.type==='focus' || this.value !==''));
}
).trigger('blur');

document.getElementById("login").onkeypress = function(e) {
         var chr = String.fromCharCode(e.which);
         if ("1234567890qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM.".indexOf(chr) < 0)
           return false;
       };

// ===== INÍCIO: Código de Upload de Foto =====
let fotoUploadCurrentStream = null;

// Abrir galeria para selecionar arquivo
$("#btnFotoGaleria").on("click", function () {
	console.log("✓ Clicou em Galeria");
	$("#input-foto-usuario").click();
});

// Quando arquivo é selecionado
$("#input-foto-usuario").on("change", function (e) {
	console.log(
		"✓ Arquivo selecionado",
		e.target.files[0] ? e.target.files[0].name : "nenhum"
	);
	const file = e.target.files[0];
	if (file) {
		processarFotoUpload(file);
	}
});

// Abrir câmera
$("#btnFotoCamera").on("click", function () {
	console.log("✓ Clicou em Câmera");
	abrirCameraFoto();
});

// Fechar modal de câmera
$("#btnFecharCamera").on("click", function () {
	console.log("✓ Clicou em Fechar Câmera");
	fecharCameraFoto();
});

// Capturar foto da câmera
$("#btnCapturar").on("click", function () {
	console.log("✓ Clicou em Capturar");
	capturarFotoCameraFoto();
});

// Limpar foto selecionada
$("#btnLimparFoto").on("click", function () {
	console.log("✓ Clicou em Limpar");
	limparFotoUpload();
});

// Processar imagem
function processarFotoUpload(file) {
	if (!file.type.startsWith("image/")) {
		mostraDialogo("Por favor, selecione um arquivo de imagem válido!", "warning", 3000);
		return;
	}
	const maxSize = 5 * 1024 * 1024;
	if (file.size > maxSize) {
		mostraDialogo("A imagem não pode ultrapassar 5MB!", "warning", 3000);
		return;
	}
	const reader = new FileReader();
	reader.onload = function (event) {
		const base64String = event.target.result;
		$("#foto-base64").val(base64String);
		mostrarPreviewFoto(base64String);
		mostraDialogo("Imagem selecionada com sucesso!", "success", 2000);
	};
	reader.readAsDataURL(file);
}

// Mostrar preview
function mostrarPreviewFoto(base64String) {
	const previewImg = $("#preview-foto");
	const previewPlaceholder = $("#preview-placeholder");
	previewImg.attr("src", base64String).removeClass("hidden");
	previewPlaceholder.addClass("hidden");
}

// Abrir câmera
function abrirCameraFoto() {
	const modal = $("#camera-modal");
	const video = $("#camera-video");
	modal.removeClass("hidden");
	const constraints = {
		video: {
			facingMode: "user",
			width: { ideal: 1280 },
			height: { ideal: 720 },
		},
		audio: false,
	};
	navigator.mediaDevices
		.getUserMedia(constraints)
		.then(function (stream) {
			fotoUploadCurrentStream = stream;
			video.get(0).srcObject = stream;
			video.removeClass("hidden");
		})
		.catch(function (error) {
			console.error("Erro ao acessar câmera:", error);
			let mensagem = "Erro ao acessar a câmera. ";
			if (error.name === "NotAllowedError") {
				mensagem += "Permissão negada para acessar a câmera.";
			} else if (error.name === "NotFoundError") {
				mensagem += "Nenhuma câmera encontrada no dispositivo.";
			} else {
				mensagem += error.message;
			}
			mostraDialogo(mensagem, "danger", 3000);
			fecharCameraFoto();
		});
}

// Capturar foto
function capturarFotoCameraFoto() {
	const video = $("#camera-video").get(0);
	const canvas = $("#camera-canvas").get(0);
	if (!video || !canvas) return;
	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;
	const ctx = canvas.getContext("2d");
	ctx.drawImage(video, 0, 0);
	const base64String = canvas.toDataURL("image/jpeg", 0.9);
	$("#foto-base64").val(base64String);
	mostrarPreviewFoto(base64String);
	fecharCameraFoto();
	mostraDialogo("Foto capturada com sucesso!", "success", 2000);
}

// Fechar câmera
function fecharCameraFoto() {
	const modal = $("#camera-modal");
	const video = $("#camera-video");
	if (fotoUploadCurrentStream) {
		fotoUploadCurrentStream.getTracks().forEach((track) => track.stop());
		fotoUploadCurrentStream = null;
	}
	video.addClass("hidden");
	modal.addClass("hidden");
}

// Limpar foto
function limparFotoUpload() {
	$("#foto-base64").val("");
	$("#input-foto-usuario").val("");
	$("#preview-foto").attr("src", "").addClass("hidden");
	$("#preview-placeholder").removeClass("hidden");
	mostraDialogo("Foto removida!", "info", 1500);
}

console.log("✓ Upload de foto inicializado");
// ===== FIM: Código de Upload de Foto =====

// ===== INÍCIO: Buscar Usuário por Nome =====
$("#btnBuscarPorNome").on("click", function(e) {
	e.preventDefault();
	const nomeUsuario = $("#nome_usuario").val().trim();
	
	if (!nomeUsuario) {
		mostraDialogo("Por favor, informe o nome do usuário!", "warning", 2000);
		return;
	}
	
	console.log("🔍 Buscando usuário: " + nomeUsuario);
	
	$.getJSON('cadastros/usuarios/carregarPorNome.php?nome=' + encodeURIComponent(nomeUsuario), function(registro) {
		if (registro.erro) {
			mostraDialogo("Usuário '" + nomeUsuario + "' não encontrado!", "warning", 2000);
			return;
		}
		
		// Carregar dados do usuário encontrado
		$("#id_usuarios").val(registro.id);
		$("#nome_usuario").val(registro.nome);
		$("#login").val(registro.login);
		$("#email").val(registro.email);
		$("#senha").val(registro.senha);
		$("#senha2").val(registro.senha);
		$("#perfil").val(registro.perfil);
		
		// Verificar situação (Ativo/Inativo)
		if (registro.situacao === 'A') {
			$("#usuario_ativo").prop("checked", true);
		} else {
			$("#usuario_ativo").prop("checked", false);
		}
		
		// Muda para modo edição
		$("#acaoUsuario").val("1");
		
		mostraDialogo("Usuário '" + nomeUsuario + "' carregado para edição!", "success", 2000);
		console.log("✓ Usuário carregado: " + nomeUsuario);
		
	}).fail(function(jqxhr, textStatus, error) {
		console.error("Erro: " + textStatus + ", " + error);
		mostraDialogo("Erro ao buscar usuário!", "danger", 2000);
	});
});
// ===== FIM: Buscar Usuário por Nome =====
	
	});
</script>
	<h1>Usuários</h1>
	<form method="post" action="cadastros/usuarios/salvar.php" name="gravaUsuario" id="gravaUsuario">	
		<input type="hidden" id="id_usuarios" name="id_usuarios" value="0" />
		<input type="hidden" value="0" name="acaoUsuario" id="acaoUsuario" />
		
		<!-- SEÇÃO 1: INFORMAÇÕES BÁSICAS -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0">Informações Básicas</h5>
			</div>
			<div class="card-body">
				<!-- Linha 1: Nome e Botão Buscar -->
				<div class="row mb-3">
					<div class="col-md-8">
						<div class='form-group'>
							<label class='control-label'>Nome</label>
							<input type="text" id="nome_usuario" name="nome_usuario" class="form_campos form-control" placeholder="Digite o nome completo"> 
							<div id="valida_nome" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, informe o Nome.
							</div> 
						</div>         
					</div>  
					<div class="col-md-4 d-flex align-items-end">
						<button type="button" id="btnBuscarPorNome" class="btn btn-info w-100 mb-0" title="Buscar usuário existente">
							<i class="fas fa-search"></i> Buscar Usuário
						</button>
					</div>
				</div>
				
				<!-- Linha 2: Login, Nível de Usuário e Ativo -->
				<div class="row">
					<div class="col-md-4">
						<div class='form-group'>
							<label class='control-label'>Login</label>
							<input type="text" id="login" name="login" class="form_campos form-control" placeholder="Usuário para login"> 
							<div id="valida_login" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, informe o Login.
							</div> 
						</div>         
					</div>  
					<div class="col-md-5">
						<div class='form-group-select'>
							<label class='control-label'>Nível de Usuário*</label>
							<select class="select form_campos form-control" id="perfil" name="perfil">
								<option value="0">Administrador</option>
								<option value="2">Coordenador</option>
								<option value="1">Operador</option>            
							</select> 
							<div id="valida_categoria" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, informe o nível do Usuário.
							</div> 
						</div>  
					</div>
					<div class="col-md-3 d-flex align-items-end">
						<div class='form-group w-100 mb-0'>					  
							<div class="form-check">
								<input type="checkbox" class="form-check-input" name="usuario_ativo" id="usuario_ativo">
								<label class="form-check-label" for="usuario_ativo">Ativo</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- SEÇÃO 2: DADOS DE ACESSO -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0">Dados de Acesso</h5>
			</div>
			<div class="card-body">
				<!-- Linha 1: Email -->
				<div class="row mb-3">
					<div class="col-md-12">
						<div class='form-group'>
							<label class='control-label'>Email</label>
							<input type="email" id="email" name="email" class="form_campos form-control" placeholder="Digite o email do usuário">
							<div id="valida_email" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, informe um email válido.
							</div>
						</div>         
					</div>  
				</div>
				
				<!-- Linha 2: Senha e Confirmação -->
				<div class="row">
					<div class="col-md-6">
						<div class='form-group'>
							<label class='control-label'>Senha</label>
							<input type="password" id="senha" name="senha" class="form_campos form-control" placeholder="Digite a senha"> 
							<div id="valida_senha" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, informe a Senha.
							</div>
						</div>         
					</div>  
					<div class="col-md-6">
						<div class='form-group'>
							<label class='control-label'>Confirmação de Senha</label>
							<input type="password" id="senha2" name="senha2" class="form_campos form-control" placeholder="Confirme a senha"> 
							<div id="valida_senha2" style="display: none" class="msgValida text-danger small mt-1">
								Por favor, confirme a Senha.
							</div>
						</div>         
					</div>  
				</div>
			</div>
		</div>

		<!-- SEÇÃO 3: FOTO DO USUÁRIO -->
		<div class="card mb-4">
			<div class="card-header bg-primary text-white">
				<h5 class="mb-0">Foto do Usuário</h5>
			</div>
			<div class="card-body">
				<div class="row">
					<!-- Coluna de Pré-visualização -->
					<div class="col-md-4">
						<div class="preview-box">
							<label class="preview-label d-block mb-2"><strong>Pré-visualização</strong></label>
							<div id="preview-foto-container" class="preview-image" style="border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align: center; background-color: #f9f9f9; min-height: 250px; display: flex; align-items: center; justify-content: center;">
								<img id="preview-foto" src="" alt="Pré-visualização" style="max-width: 100%; max-height: 100%; display: none;">
								<span id="preview-placeholder" style="color: #999; font-size: 14px;">Selecione uma imagem</span>
							</div>
						</div>
					</div>
					
					<!-- Coluna de Botões e Controles -->
					<div class="col-md-8">
						<div class="foto-controls">
							<label class="controls-label d-block mb-3"><strong>Selecione a Forma de Captura</strong></label>
							<div class="buttons-group mb-4">
								<button type="button" class="btn btn-secondary me-2" id="btnFotoGaleria">
									<i class="fas fa-folder"></i> Galeria
								</button>
								<button type="button" class="btn btn-warning" id="btnLimparFoto">
									<i class="fas fa-times"></i> Limpar
								</button>
							</div>
							
							<!-- Hidden inputs -->
							<input type="file" id="input-foto-usuario" name="foto" accept="image/*" style="display: none;">
							<input type="hidden" id="foto-base64" name="foto_base64" value="">
							
							<div class="alert alert-info" role="alert">
								<small><i class="fas fa-info-circle"></i> Você pode selecionar uma imagem da galeria do seu dispositivo.</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<!-- SEÇÃO 4: AÇÕES -->
		<div class="row mt-4 mb-3">
			<div class="col-md-12 d-flex gap-2 justify-content-end">
				<input type="reset" value="Cancelar" id="btnCancelar" class="btn btn-danger" style="visibility:hidden;">
				<input type="submit" value="Gravar" id="btnGravar" class="btn btn-primary">
			</div>
		</div>
	</form>
	
	<!-- Camera Modal -->
	<div id="camera-modal" class="camera-modal hidden" style="display: none;">
		<div class="camera-modal-content">
			<div class="camera-modal-header">
				<h5 class="camera-title">Capturar Foto</h5>
			</div>
			<div class="camera-modal-body">
				<video id="camera-video" class="camera-video hidden"></video>
				<canvas id="camera-canvas" class="hidden"></canvas>
			</div>
			<div class="camera-modal-footer">
				<button type="button" class="btn btn-primary" id="btnCapturar">Capturar</button>
				<button type="button" class="btn btn-danger" id="btnFecharCamera">Fechar</button>
			</div>
		</div>
	</div>
	
	<div id="ListaUsuarios"></div>
	





    <script> 
        // wait for the DOM to be loaded 
        $(document).ready(function() { 
		//Botão Cancelar Ao CLicar deve ser oculto
		$("#btnCancelar").click(function(){
			$("#btnCancelar").css({"visibility" : "hidden"});
			// Reset de todos os campos
			$("#gravaUsuario")[0].reset();
			$("#id_usuarios").val("0");
			$("#acaoUsuario").val("0");
			$("#nome_usuario").focus();
			console.log("✓ Formulário resetado para novo cadastro");
		})

		 // Cadastro/Alteração de Usuário //
	 $('#btnGravar').click(function(e){
	   	e.preventDefault();
	   
		var mensagemErro = 'Falha ao Efetuar Cadastro!';
	  
		$("input:text").css({"border-color" : "#999"});
		$("input:password").css({"border-color" : "#999"});
		$(".msgValida").css({"display" : "none"});
	    
		if ($.trim($("#nome_usuario").val()) == ''){
			$("#valida_nome").css({"display" : "inline", "color" : "red"});
			$("#nome_usuario").css({"border-color" : "red"});
			$("#nome_usuario").focus();
			return false;
		}

		if ($.trim($("#login").val()) == ''){	
			$("#valida_login").css({"display" : "inline", "color" : "red"});
			$("#login").css({"border-color" : "red"});
			$("#login").focus();
			return false;
		}	

		if ($.trim($("#senha").val()) == ''){	
			$("#valida_senha").html("Por favor, informe a Senha");
			$("#valida_senha").css({"display" : "inline", "color" : "red"});
			$("#senha").css({"border-color" : "red"});
			$("#senha").focus();
			return false;
		}

		if ($.trim($("#senha2").val()) == ''){	
			$("#valida_senha2").html("Por favor, informe a Senha");
			$("#valida_senha2").css({"display" : "inline", "color" : "red"});
			$("#senha2").css({"border-color" : "red"});
			$("#senha2").focus();
			return false;
		}

		if ($.trim($("#senha").val()) != $.trim($("#senha2").val())){	
			$("#valida_senha").html("A Senha e a confirmação não conferem");
			$("#valida_senha").css({"display" : "inline", "color" : "red"});
			$("#senha").css({"border-color" : "red"});
			$("#senha2").css({"border-color" : "red"});
			$("#senha").focus();
			return false;
		}

		// Gravando os dados do Usuário //
	    $('#gravaUsuario').ajaxForm({
			resetForm: true,
        	beforeSend:function() {
				$("#btnGravarUsuario").attr('value', 'Salvando ...');
				$('#btnGravarUsuario').attr('disabled', true);
				$('#btnFecharCadastro').attr('disabled', true);
				$('#FormUsuarios').find('input').prop('disabled', true);
        	},
			success: function( retorno ){			
				if (retorno == 1) { mostraDialogo("<strong>Usuário Cadastrado com sucesso!</strong>", "success", 2500); }
				else if (retorno == 2){ mostraDialogo('Usuário Atualizado com Sucesso!', "success", 2500); }
				else if (retorno == 3){ mostraDialogo('Usuário Já Cadastrado com este Login!', "warning", 2500); }
				else if (retorno == 4){ mostraDialogo('Você não pode desativar seu próprio Usuário!', "warning", 2500); return false }
				else if (retorno == 5){ mostraDialogo('Você não pode desativar o Administrador principal!', "danger", 2500); return false }
				else{ mostraDialogo(mensagemErro+retorno, "danger", 2500); }

				$.ajax("cadastros/usuarios/listar.php").done(function(data) {
					$("#btnCancelar").css({"visibility" : "hidden"});
					$("#ListaUsuarios").html("<img src='imgs/loader.gif'  width='100'>");
			        $("#ListaUsuarios").load("cadastros/usuarios/listar.php");					
				});
			},		 
			complete:function() {
				$("#btnGravarUsuario").attr('value', 'Salvar');
				$('#btnGravarUsuario').attr('disabled', false);
				$('#FormUsuarios').find('input, button').prop('disabled', false);
				$("#ListaUsuarios").css("display","block");
				$("#FormUsuarios").css("display","none");
				
		 	},
		 	error: function (retorno) {
				mostraDialogo(mensagemErro, "danger", 2500);
            }
		}).submit();
		// FIM Gravando os dados do Usuário //
	});
	// FIM Cadastro/Alteração de Usuário //
			
			$("#ListaUsuarios").html("<img src='imgs/loader.gif'  width='100'>");
			$("#ListaUsuarios").load("cadastros/usuarios/listar.php");
             
        }); 
    </script> 

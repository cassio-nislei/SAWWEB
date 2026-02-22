// Gerenciador de Upload de Foto para Cadastro de Usuários
function initFotoUpload() {
  console.log("✓ initFotoUpload iniciado");
  let currentStream = null;

  // Abrir galeria para selecionar arquivo
  $("#btnFotoGaleria").on("click", function () {
    console.log("✓ Clicou em Galeria");
    $("#input-foto-usuario").click();
  });

  // Quando arquivo é selecionado
  $("#input-foto-usuario").on("change", function (e) {
    console.log(
      "✓ Arquivo selecionado",
      e.target.files[0] ? e.target.files[0].name : "nenhum",
    );
    const file = e.target.files[0];
    if (file) {
      processarFoto(file);
    }
  });

  // Abrir câmera
  $("#btnFotoCamera").on("click", function () {
    console.log("✓ Clicou em Câmera");
    abrirCamera();
  });

  // Fechar modal de câmera
  $("#btnFecharCamera").on("click", function () {
    console.log("✓ Clicou em Fechar Câmera");
    fecharCamera();
  });

  // Capturar foto da câmera
  $("#btnCapturar").on("click", function () {
    console.log("✓ Clicou em Capturar");
    capturarFotoCamera();
  });

  // Limpar foto selecionada
  $("#btnLimparFoto").on("click", function () {
    console.log("✓ Clicou em Limpar");
    limparFoto();
  });

  // Função para processar imagem (converter para base64)
  function processarFoto(file) {
    // Validar tipo de arquivo
    if (!file.type.startsWith("image/")) {
      mostraDialogo(
        "Por favor, selecione um arquivo de imagem válido!",
        "warning",
        3000,
      );
      return;
    }

    // Validar tamanho (máximo 5MB)
    const maxSize = 5 * 1024 * 1024; // 5MB
    if (file.size > maxSize) {
      mostraDialogo("A imagem não pode ultrapassar 5MB!", "warning", 3000);
      return;
    }

    // Converter para base64
    const reader = new FileReader();
    reader.onload = function (event) {
      const base64String = event.target.result;

      // Armazenar base64 no input hidden
      $("#foto-base64").val(base64String);

      // Mostrar preview
      mostrarPreview(base64String);

      mostraDialogo("Imagem selecionada com sucesso!", "success", 2000);
    };
    reader.readAsDataURL(file);
  }

  // Função para mostrar preview da imagem
  function mostrarPreview(base64String) {
    const previewImg = $("#preview-foto");
    const previewPlaceholder = $("#preview-placeholder");

    previewImg.attr("src", base64String).removeClass("hidden");
    previewPlaceholder.addClass("hidden");
  }

  // Função para abrir câmera
  function abrirCamera() {
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
        currentStream = stream;
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
        fecharCamera();
      });
  }

  // Função para capturar foto da câmera
  function capturarFotoCamera() {
    const video = $("#camera-video").get(0);
    const canvas = $("#camera-canvas").get(0);

    if (!video || !canvas) {
      return;
    }

    // Configurar canvas com as dimensões do vídeo
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Desenhar frame atual do vídeo no canvas
    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0);

    // Converter canvas para base64
    const base64String = canvas.toDataURL("image/jpeg", 0.9);

    // Armazenar base64
    $("#foto-base64").val(base64String);

    // Mostrar preview
    mostrarPreview(base64String);

    // Fechar câmera
    fecharCamera();

    mostraDialogo("Foto capturada com sucesso!", "success", 2000);
  }

  // Função para fechar câmera
  function fecharCamera() {
    const modal = $("#camera-modal");
    const video = $("#camera-video");

    // Parar stream de vídeo
    if (currentStream) {
      currentStream.getTracks().forEach((track) => track.stop());
      currentStream = null;
    }

    video.addClass("hidden");
    modal.addClass("hidden");
  }

  // Função para limpar foto selecionada
  function limparFoto() {
    $("#foto-base64").val("");
    $("#input-foto-usuario").val("");
    $("#preview-foto").attr("src", "").addClass("hidden");
    $("#preview-placeholder").removeClass("hidden");
    mostraDialogo("Foto removida!", "info", 1500);
  }

  // Integração com o formulário de envio
  $("#gravaUsuario").on("submit", function (e) {
    // A foto base64 já está no campo hidden #foto-base64
    // O servidor receberá automaticamente via POST
  });
}

// Inicializar quando jQuery estiver pronto
console.log(
  "✓ Verificando jQuery...",
  typeof jQuery !== "undefined" ? "DISPONÍVEL" : "NÃO DISPONÍVEL",
);

if (typeof jQuery !== "undefined") {
  console.log("✓ jQuery disponível, inicializando...");
  jQuery(document).ready(function () {
    console.log("✓ Document ready, chamando initFotoUpload");
    initFotoUpload();
  });
} else {
  console.warn(
    "⚠ jQuery não está disponível no escopo global, tentando novamente...",
  );
  // Fallback: tentar novamente em 500ms
  setTimeout(function () {
    if (typeof jQuery !== "undefined") {
      console.log("✓ jQuery disponível (retry), inicializando...");
      jQuery(document).ready(function () {
        console.log("✓ Document ready (retry), chamando initFotoUpload");
        initFotoUpload();
      });
    } else {
      console.error(
        "✗ jQuery NÃO FOI CARREGADO! Verifica se jquery está no HTML.",
      );
    }
  }, 500);
}

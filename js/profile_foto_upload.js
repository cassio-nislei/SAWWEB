/**
 * Script para gerenciar uploads de foto de perfil em base64
 * Integrado ao painel de edição de perfil
 */

function setupFotoProfileUpload() {
  // Quando o usuário clica na foto para trocar
  $("#input-profile-photo").on("change", function (e) {
    const file = e.target.files[0];

    if (!file) return;

    // Validar tipo de arquivo
    if (!file.type.match("image.*")) {
      alert("Por favor, selecione uma imagem válida");
      return;
    }

    // Validar tamanho (máximo 5MB)
    if (file.size > 5000000) {
      alert("A imagem é muito grande. Máximo: 5MB");
      return;
    }

    // Converter para base64
    const reader = new FileReader();

    reader.onload = function (event) {
      const base64String = event.target.result;

      // Mostrar preview
      $("#img-panel-edit-profile").attr("src", base64String).show();
      $("#img-default-panel-edit-profile").hide();

      // Salvar no banco de dados
      saveFotoToDatabase(base64String);
    };

    reader.readAsDataURL(file);
  });

  // Quando clica no container da foto
  $("#photo-container-edit-profile, #img-default-panel-edit-profile").on(
    "click",
    function () {
      $("#input-profile-photo").click();
    },
  );

  // Carregar foto salva quando o painel é aberto
  loadSavedFoto();
}

function saveFotoToDatabase(base64String) {
  $.ajax({
    url: "api_salvar_foto_operador.php",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({
      fotoBase64: base64String,
    }),
    success: function (response) {
      if (response.sucesso) {
        console.log("Foto salva com sucesso");
        // Mostrar notificação
        mostraDialogo("Foto salva com sucesso!", "success", 2000);
      } else {
        console.error("Erro ao salvar foto:", response.mensagem);
        mostraDialogo(
          "Erro ao salvar foto: " + response.mensagem,
          "danger",
          3000,
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Erro na requisição:", error);
      let mensagemErro = "Erro ao salvar foto";

      if (xhr.status === 413) {
        mensagemErro = "Arquivo muito grande (máximo 5MB)";
      } else if (xhr.status === 401) {
        mensagemErro = "Usuário não autenticado";
      }

      mostraDialogo(mensagemErro, "danger", 3000);
    },
  });
}

function loadSavedFoto() {
  $.ajax({
    url: "api_obter_foto_operador.php",
    type: "GET",
    contentType: "application/json",
    success: function (response) {
      if (response.sucesso && response.foto) {
        // Foto encontrada, exibir
        $("#img-panel-edit-profile").attr("src", response.foto).show();
        $("#img-default-panel-edit-profile").hide();
        console.log("Foto carregada com sucesso");
      } else {
        // Nenhuma foto, manter padrão
        $("#img-panel-edit-profile").hide();
        $("#img-default-panel-edit-profile").show();
      }
    },
    error: function (xhr, status, error) {
      // Em caso de erro, manter foto padrão
      console.log("Usando foto padrão");
      $("#img-panel-edit-profile").hide();
      $("#img-default-panel-edit-profile").show();
    },
  });
}

// Inicializar ao carregar a página
$(document).ready(function () {
  setupFotoProfileUpload();
});

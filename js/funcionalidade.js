$(function () {
  // Sincronizar nome do usuário no header do painel de perfil
  function syncProfileName() {
    var userName = $("#input-name-panel-edit-profile").text().trim();
    if (userName) {
      $("#panel-edit-profile ._1xGbt").text(userName);
    }
  }

  // Sincronizar ao carregar
  syncProfileName();

  // Sincronizar quando houver mudanças
  $(document).on("input", "#input-name-panel-edit-profile", function () {
    var currentName = $(this).text().trim();
    $("#panel-edit-profile ._1xGbt").text(currentName || "Perfil");
  });

  /*** chama operadores **/
  $("#menu-usuarios").click(function (ev) {
    ev.preventDefault();
    $("#box-operadores").css("left", "0");
  });
  $(document).on("click", ".voltar", function (ev) {
    ev.preventDefault();
    $("#box-operadores").css("left", "-360px");
    $("#box-contatos").css("left", "-360px");
  });

  /*** chama lista de contatos **/
  $(document).on("click", "#contatos-bt-lista", function (ev) {
    ev.preventDefault();
    $("#box-contatos").css("left", "0");
  });

  /*** abri perfil **/
  $(document).on("click", "#my-photo", function (ev) {
    ev.preventDefault();
    $(".panel-left").addClass("open");
  });
  $(document).on("click", "#btn-close-panel-edit-profile", function (ev) {
    ev.preventDefault();
    $(".panel-left").removeClass("open");
  });

  /*** chama chat **/
  $(document).on("click", ".action_arrow", function (e) {
    if ($("#chatOperadores").val() === "0") {
      mostraDialogo("Chat não liberado pelo Administrador!", "danger", 2500);
    } else {
      e.preventDefault();
      $(".changebtchat .fa-chevron-left").toggleClass("rotateIconClose");
      $("#Verchat").css("right", "0");
      $("._3zJZ2").css("width", "76%");
      $("._3oju3").css("width", "76%");
      $(".sair").show();

      // Habilita o Carregamento das Mensagens do WebChat //
      $("#carregaWebChat").val("1");
    }
  });

  /*** Fecha o Chat **/
  $(document).on("click", ".sair", function (e) {
    e.preventDefault();
    $("#Verchat").css("right", "-235px");
    $("._3zJZ2").css("width", "100%");
    $("._3oju3").css("width", "auto");
    $(".sair").hide();

    // Desabilita o Carregamento das Mensagens do WebChat //
    $("#carregaWebChat").val("0");
  });

  /*** mostra etiquetas **/
  $(document).on("click", ".uk-flutua", function () {
    $("#EtiQueta").slideToggle();
    $(this).toggleClass("active");
    return false;
  });

  /*** submenu mensagem **/
  $("#susp_menu").click(function () {
    $("#poup1").slideToggle();
    return false;
  });

  $("#menu-options").click(function () {
    $("#poup2").slideToggle();
    return false;
  });

  /*** mostra seta opções **/
  $("#mb_status").mouseover(function () {
    $("#susp_menu").css("transform", "translateX(0px)");
  });
  $("#mb_status").mouseout(function () {
    $("#susp_menu").css("transform", "translateX(40px)");
  });

  /*** mostra filtro **/
  $(document).on("click", ".filtrar", function () {
    $("#_filtro").slideToggle();
    return false;
  });

  /** abrir arquivo**/
  $("#anexo").click(function (aq) {
    $(".m_arquivo").slideToggle();
    return false;
  });
});

function atualizaContatos() {
  var pesquisaContato = $("#pesquisaContato").val();
  var etiqueta = $("#etiqueta option:selected").val();
  var tipo_pesquisa = $("#tipo_pesquisa option:selected").val();
  $("#msgContatos").html("Carregando...");

  $.post(
    "atendimento/contatos.php",
    {
      pesquisaContato: pesquisaContato,
      etiqueta: etiqueta,
      tipo_pesquisa: tipo_pesquisa,
    },
    function (retorno) {
      $("#ListaViewContatos").html(retorno);
    },
  );
}

// Auxílio para inicializar Select2 em elementos dinâmicos
window.initSelect2IfNeeded = function (selector) {
  if (typeof $.fn.select2 === "function") {
    $(selector).each(function () {
      if (!$(this).hasClass("select2-hidden-accessible")) {
        try {
          $(this).select2({
            placeholder: "TAGS",
            maximumSelectionLength: 10,
            language: "pt-BR",
          });
        } catch (e) {
          console.error("Erro ao inicializar Select2:", e);
        }
      }
    });
  } else {
    console.warn("Select2 ainda não está disponível");
  }
};

// Tentar recarregar select2 periodicamente até funcionar
window.retrySelect2Loading = function () {
  var maxAttempts = 60; // 30 segundos (60 * 500ms)
  var attempts = 0;
  var interval = setInterval(function () {
    attempts++;
    if (typeof $.fn.select2 === "function") {
      clearInterval(interval);
      console.log("✅ Select2 disponível após " + attempts + " tentativas");
      window.initSelect2IfNeeded(".pesqEtiquetas");
    } else if (attempts >= maxAttempts) {
      clearInterval(interval);
      console.warn(
        "⚠️ Select2 não foi carregado após " +
          maxAttempts +
          " tentativas (" +
          maxAttempts * 500 +
          "ms)",
      );
    }
  }, 500);
};

// Iniciar tentativa ao carregar script
window.retrySelect2Loading();

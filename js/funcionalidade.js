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
  // Método 1: Event delegation (padrão)
  $(document).on("click", "#my-photo", function (ev) {
    console.log("🎯 Click detectado em #my-photo (method 1 - delegation)");
    ev.preventDefault();
    ev.stopPropagation();

    var $panel = $(".panel-left");
    console.log("🔍 .panel-left encontrado?", $panel.length > 0);
    if ($panel.length > 0) {
      $panel.addClass("open");
      console.log(
        "✅ Classe 'open' adicionada. Novo estado:",
        $panel.attr("class"),
      );
      console.log(
        "🔍 opacity:",
        $panel.css("opacity"),
        ", visibility:",
        $panel.css("visibility"),
      );

      // Verificar estado após um pequeno delay
      setTimeout(function () {
        console.log(
          "🔍 APÓS 100ms - opacity:",
          $panel.css("opacity"),
          ", visibility:",
          $panel.css("visibility"),
        );
        console.log("🔍 APÓS 100ms - display:", $panel.css("display"));
        console.log("🔍 APÓS 100ms - classes:", $panel.attr("class"));
      }, 100);
    } else {
      console.warn("❌ .panel-left NÃO ENCONTRADO!");
      // Tentar encontrar qualquer elemento com panel-left
      console.log("🔍 Procurando elementos com 'panel-left':");
      $("[class*='panel-left']").each(function () {
        console.log(
          "   Encontrado:",
          this.id || "(sem id)",
          "classe:",
          this.className,
        );
      });
    }
  });

  // Método 2: Listener direto (fallback - em caso de problema)
  $(function () {
    var $myPhoto = $("#my-photo");
    if ($myPhoto.length > 0) {
      $myPhoto.off("click").on("click", function (ev) {
        console.log("🎯 Click detectado em #my-photo (method 2 - direct)");
        ev.preventDefault();
        ev.stopPropagation();

        var $panel = $(".panel-left");
        console.log("🔍 .panel-left encontrado (method 2)?", $panel.length > 0);
        if ($panel.length > 0) {
          $panel.addClass("open");
          console.log("✅ Classe 'open' adicionada (method 2)");
        }
      });
      console.log("✅ Listener direto adicionado a #my-photo");
    } else {
      console.warn("⚠️ #my-photo não encontrado para listener direto");
    }
  });

  // Método 3: Mousedown fallback (em caso de preventDefault não funcionar)
  $(document).on("mousedown", "#my-photo", function (ev) {
    if (ev.which === 1) {
      // Apenas click esquerdo
      console.log(
        "🎯 Mousedown detectado em #my-photo (method 3 - mousedown fallback)",
      );
      var $panel = $(".panel-left");
      if ($panel.length > 0) {
        $panel.addClass("open");
        console.log("✅ Painel aberto via mousedown fallback");
      }
    }
  });

  // Verificar se elemento tem pointer-events disabled
  $(function () {
    var $myPhoto = $("#my-photo");
    if ($myPhoto.length > 0) {
      var pointerEvents = $myPhoto.css("pointer-events");
      var cursor = $myPhoto.css("cursor");
      console.log(
        "🔍 #my-photo CSS - pointer-events:",
        pointerEvents,
        ", cursor:",
        cursor,
      );

      if (pointerEvents === "none") {
        console.warn(
          "⚠️ PROBLEMA: #my-photo tem pointer-events: none - corrigindo...",
        );
        $myPhoto.css("pointer-events", "auto");
      }
    }
  });
  $(document).on("click", "#btn-close-panel-edit-profile", function (ev) {
    console.log("🎯 Click detectado em #btn-close-panel-edit-profile");
    ev.preventDefault();
    ev.stopPropagation();
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
    // Sempre tentar restaurar plugins antes de verificar
    if (typeof window.restaurarPlugins === "function") {
      window.restaurarPlugins();
    }
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

// Função para inicializar jQuery Mask em elementos dinâmicos
window.initMaskIfNeeded = function (selector, maskPattern) {
  if (typeof $.fn.mask === "function") {
    $(selector).each(function () {
      try {
        if (maskPattern && typeof maskPattern === "function") {
          var options = {
            onKeyPress: function (val, e, field, options) {
              field.mask(maskPattern.apply({}, arguments), options);
            },
          };
          $(this).mask(maskPattern, options);
        } else if (maskPattern && typeof maskPattern === "string") {
          $(this).mask(maskPattern);
        }
      } catch (e) {
        console.error("Erro ao inicializar Mask:", e);
      }
    });
  } else {
    console.warn("jQuery Mask ainda não está disponível");
  }
};

// Tentar carregar jQuery Mask periodicamente
window.retryMaskLoading = function () {
  var maxAttempts = 60; // 30 segundos (60 * 500ms)
  var attempts = 0;
  var interval = setInterval(function () {
    attempts++;
    // Sempre tentar restaurar plugins antes de verificar
    if (typeof window.restaurarPlugins === "function") {
      window.restaurarPlugins();
    }
    if (typeof $.fn.mask === "function") {
      clearInterval(interval);
      console.log("✅ jQuery Mask disponível após " + attempts + " tentativas");
    } else if (attempts >= maxAttempts) {
      clearInterval(interval);
      console.warn(
        "⚠️ jQuery Mask não foi carregado após " +
          maxAttempts +
          " tentativas (" +
          maxAttempts * 500 +
          "ms)",
      );
    }
  }, 500);
};

// Iniciar tentativas ao carregar script
window.retrySelect2Loading();
window.retryMaskLoading();

// Função global para reinicializar handlers (em caso de problemas em Docker)
window.reinitializeClickHandlers = function () {
  console.log("🔄 Reinicializando todos os click handlers...");

  // Remover handlers antigos
  $(document).off("click", "#my-photo");
  $(document).off("click", "#btn-close-panel-edit-profile");
  $(document).off("mousedown", "#my-photo");

  // Reattach #my-photo handlers
  $(document).on("click", "#my-photo", function (ev) {
    console.log("🎯 Click em #my-photo (reinicialized)");
    ev.preventDefault();
    ev.stopPropagation();

    var $panel = $(".panel-left");
    console.log(
      "🔍 .panel-left encontrado na reinicialização?",
      $panel.length > 0,
    );

    if ($panel.length > 0) {
      $panel.addClass("open");
      console.log(
        "✅ Painel aberto (reinicialized), classe:",
        $panel.attr("class"),
      );
      console.log(
        "🔍 Novo CSS - opacity:",
        $panel.css("opacity"),
        ", visibility:",
        $panel.css("visibility"),
      );
    } else {
      console.error(
        "❌ CRÍTICO: .panel-left NÃO ENCONTRADO NA REINICIALIZAÇÃO!",
      );
    }
  });

  $(document).on("mousedown", "#my-photo", function (ev) {
    if (ev.which === 1) {
      console.log("🎯 Mousedown em #my-photo (reinicialized)");
      var $panel = $(".panel-left");
      if ($panel.length > 0) {
        $panel.addClass("open");
      }
    }
  });

  // Reattach #btn-close-panel-edit-profile handlers
  $(document).on("click", "#btn-close-panel-edit-profile", function (ev) {
    console.log("🎯 Click em #btn-close-panel-edit-profile (reinicialized)");
    ev.preventDefault();
    ev.stopPropagation();
    $(".panel-left").removeClass("open");
  });

  // Verificar CSS
  var $myPhoto = $("#my-photo");
  if ($myPhoto.length > 0) {
    var pointerEvents = $myPhoto.css("pointer-events");
    if (pointerEvents === "none") {
      console.warn("🔧 Corrigindo pointer-events em #my-photo");
      $myPhoto.css("pointer-events", "auto");
    }
  }

  // Verificar .panel-left
  var $panelLeft = $(".panel-left");
  console.log("🔍 DIAGNÓSTICO PAINEL:");
  console.log("   .panel-left encontrado?", $panelLeft.length > 0);
  if ($panelLeft.length > 0) {
    console.log("   .panel-left classes:", $panelLeft.attr("class"));
    console.log("   .panel-left display:", $panelLeft.css("display"));
    console.log("   .panel-left opacity:", $panelLeft.css("opacity"));
    console.log("   .panel-left visibility:", $panelLeft.css("visibility"));
    console.log("   .panel-left z-index:", $panelLeft.css("z-index"));
    console.log("   .panel-left position:", $panelLeft.css("position"));
  } else {
    console.error("   ❌ PAINEL NÃO ENCONTRADO!");
  }

  console.log("✅ Click handlers reinicializados");
};

// Funções globais para controlar o painel (útil para debugging)
window.abrirPainel = function () {
  console.log("🔓 Abrindo painel manualmente...");
  var $panel = $(".panel-left");
  if ($panel.length > 0) {
    $panel.addClass("open");
    console.log("✅ Painel aberto com sucesso");
    console.log("   Classes:", $panel.attr("class"));
    console.log("   Opacity:", $panel.css("opacity"));
    console.log("   Visibility:", $panel.css("visibility"));
  } else {
    console.error("❌ .panel-left não encontrado!");
  }
};

window.fecharPainel = function () {
  console.log("🔒 Fechando painel manualmente...");
  var $panel = $(".panel-left");
  if ($panel.length > 0) {
    $panel.removeClass("open");
    console.log("✅ Painel fechado com sucesso");
  } else {
    console.error("❌ .panel-left não encontrado!");
  }
};

window.verificarPainel = function () {
  console.log("🔍 VERIFICAÇÃO COMPLETA DO PAINEL:");
  var $panel = $(".panel-left");
  console.log("   Encontrado?", $panel.length > 0);
  if ($panel.length > 0) {
    console.log("   ID:", $panel.attr("id"));
    console.log("   Classes:", $panel.attr("class"));
    console.log("   Display:", $panel.css("display"));
    console.log("   Opacity:", $panel.css("opacity"));
    console.log("   Visibility:", $panel.css("visibility"));
    console.log("   Z-index:", $panel.css("z-index"));
    console.log("   Position:", $panel.css("position"));
    console.log("   Tem classe 'open'?", $panel.hasClass("open"));

    // Verificar btn-close
    var $btnClose = $("#btn-close-panel-edit-profile");
    console.log("   Botão fechar existe?", $btnClose.length > 0);
  } else {
    console.error("   ❌ .panel-left NÃO EXISTE");
  }
};

// Chamar reinitialize após 1 segundo para garantir que tudo está pronto
setTimeout(function () {
  if (typeof window.reinitializeClickHandlers === "function") {
    window.reinitializeClickHandlers();
  }
}, 1000);

// Também chamar quando document está totalmente pronto (para casos com latência alta)
$(document).ready(function () {
  setTimeout(function () {
    if (typeof window.reinitializeClickHandlers === "function") {
      window.reinitializeClickHandlers();
    }
  }, 500);
});

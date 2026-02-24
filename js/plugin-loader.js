/**
 * Plugin Loader - Garante que plugins jQuery estejam disponíveis
 * Usado em ambiente Docker onde CDN pode ter latência
 */

// Objeto global para rastrear status dos plugins
window.pluginStatus = {
  tabs: false,
  select2: false,
  mask: false,
  checkTimeouts: {
    tabs: null,
    select2: null,
    mask: null,
  },
};

/**
 * Aguarda um plugin estar disponível e executa callback
 * @param {string} plugin - Nome do plugin ('tabs', 'select2', 'mask')
 * @param {function} callback - Função a executar quando plugin estiver pronto
 * @param {number} timeout - Timeout em ms (padrão 30000)
 */
window.whenPluginReady = function (plugin, callback, timeout) {
  timeout = timeout || 30000;
  var startTime = Date.now();
  var checkInterval = 100;

  function check() {
    var isReady = false;
    var pluginFn = null;

    switch (plugin) {
      case "tabs":
        isReady = typeof $.fn.tabs === "function";
        break;
      case "select2":
        isReady = typeof $.fn.select2 === "function";
        break;
      case "mask":
        isReady = typeof $.fn.mask === "function";
        break;
      default:
        console.warn("Plugin desconhecido: " + plugin);
        return;
    }

    if (isReady) {
      window.pluginStatus[plugin] = true;
      console.log("✅ " + plugin + " está pronto");
      if (callback) callback();
    } else if (Date.now() - startTime < timeout) {
      setTimeout(check, checkInterval);
    } else {
      console.error("⏱️ Timeout aguardando " + plugin + " (" + timeout + "ms)");
    }
  }

  check();
};

/**
 * Inicializa Select2 em seletores, aguardando disponibilidade
 */
window.initSelect2Safe = function (selector, options) {
  options = options || {
    placeholder: "TAGS",
    maximumSelectionLength: 10,
    language: "pt-BR",
  };

  window.whenPluginReady("select2", function () {
    try {
      $(selector).each(function () {
        if (!$(this).hasClass("select2-hidden-accessible")) {
          $(this).select2(options);
        }
      });
      console.log("✅ Select2 inicializado em: " + selector);
    } catch (e) {
      console.error("Erro ao inicializar Select2:", e);
    }
  });
};

/**
 * Inicializa jQuery UI Tabs, aguardando disponibilidade
 */
window.initTabsSafe = function (selectors) {
  selectors = selectors || ["#tabs", "#tabs2", "#tabs3", "#tabs4"];

  window.whenPluginReady("tabs", function () {
    try {
      selectors.forEach(function (selector) {
        $(selector).tabs();
      });
      console.log("✅ jQuery UI Tabs inicializado");
    } catch (e) {
      console.error("Erro ao inicializar Tabs:", e);
    }
  });
};

/**
 * Health check dos plugins
 */
window.checkPluginsHealth = function () {
  console.group("🔍 Plugin Health Check");
  console.log(
    "jQuery:",
    typeof $ !== "undefined" ? "✅ Disponível" : "❌ Não disponível",
  );
  console.log(
    "jQuery UI Tabs:",
    typeof $.fn.tabs === "function" ? "✅ Disponível" : "❌ Não disponível",
  );
  console.log(
    "Select2:",
    typeof $.fn.select2 === "function" ? "✅ Disponível" : "❌ Não disponível",
  );
  console.log(
    "jQuery Mask:",
    typeof $.fn.mask === "function" ? "✅ Disponível" : "❌ Não disponível",
  );
  console.groupEnd();

  return window.pluginStatus;
};

// Auto-check ao carregar este script
console.log("Plugin Loader inicializado");

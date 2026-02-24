/**
 * Profile Panel Fix
 * Garante que o painel de perfil apareça corretamente ao clicar na foto do usuário
 * Implementa múltiplas estratégias para contornar problemas no Docker
 */

(function () {
  "use strict";

  // Configuração do painel
  const PANEL_CONFIG = {
    selector: ".panel-left, #panel-edit-profile",
    photoSelector: "#my-photo",
    closeButtonSelector: "#btn-close-panel-edit-profile, .btn-close",
    openClass: "open",
    initialDelay: 500,
  };

  /**
   * Força o painel a ser visível com CSS inline
   */
  function forceShowPanel() {
    const panels = document.querySelectorAll(PANEL_CONFIG.selector);
    panels.forEach((panel) => {
      if (panel.classList.contains(PANEL_CONFIG.openClass)) {
        // Forçar CSS inline para garantir visibilidade
        panel.style.cssText = `
                    display: block !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 450px !important;
                    height: 100% !important;
                    z-index: 9999 !important;
                    transform: translateX(0) !important;
                    opacity: 1 !important;
                    visibility: visible !important;
                    pointer-events: auto !important;
                    background: #fff !important;
                    box-shadow: 2px 0 10px rgba(0,0,0,0.2) !important;
                    transition: all 0.3s ease-in-out !important;
                `;
      } else {
        // Painel fechado
        panel.style.cssText = `
                    transform: translateX(-450px) !important;
                    left: -450px !important;
                `;
      }
    });
  }

  /**
   * Abre o painel
   */
  function openPanel() {
    const panels = document.querySelectorAll(PANEL_CONFIG.selector);
    panels.forEach((panel) => {
      // Remover classe open primeiro para resetar
      panel.classList.remove(PANEL_CONFIG.openClass);
      // Trigger reflow
      void panel.offsetWidth;
      // Adicionar classe open
      panel.classList.add(PANEL_CONFIG.openClass);
      // Forçar CSS
      forceShowPanel();
    });

    console.log("✅ Painel de perfil ABERTO com sucesso");
    return true;
  }

  /**
   * Fecha o painel
   */
  function closePanel() {
    const panels = document.querySelectorAll(PANEL_CONFIG.selector);
    panels.forEach((panel) => {
      panel.classList.remove(PANEL_CONFIG.openClass);
    });

    // Limpar CSS inline após transição
    setTimeout(function () {
      panels.forEach((panel) => {
        if (!panel.classList.contains(PANEL_CONFIG.openClass)) {
          panel.style.cssText = "";
        }
      });
    }, 350);

    console.log("✅ Painel de perfil FECHADO com sucesso");
    return true;
  }

  /**
   * Verifica estado do painel
   */
  function checkPanelState() {
    const panels = document.querySelectorAll(PANEL_CONFIG.selector);
    let state = {
      found: panels.length > 0,
      panels: [],
    };

    panels.forEach((panel, idx) => {
      state.panels.push({
        index: idx,
        id: panel.id,
        classes: panel.className,
        display: window.getComputedStyle(panel).display,
        visibility: window.getComputedStyle(panel).visibility,
        zIndex: window.getComputedStyle(panel).zIndex,
        transform: window.getComputedStyle(panel).transform,
        isOpen: panel.classList.contains(PANEL_CONFIG.openClass),
      });
    });

    console.log("📊 Estado do painel:", state);
    return state;
  }

  /**
   * Inicializa os event listeners
   */
  function initEventListeners() {
    // Listener para clique na foto
    const photos = document.querySelectorAll(PANEL_CONFIG.photoSelector);
    if (photos.length > 0) {
      photos.forEach((photo) => {
        photo.addEventListener(
          "click",
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("🖱️ Clique na foto detectado");
            openPanel();
          },
          true,
        ); // useCapture = true para garantir que funcione

        photo.addEventListener(
          "mousedown",
          function (e) {
            if (e.button === 0) {
              // Botão esquerdo
              e.preventDefault();
              e.stopPropagation();
              openPanel();
            }
          },
          true,
        ); // useCapture = true
      });
      console.log(`✅ ${photos.length} listener(s) de foto adicionado(s)`);
    } else {
      console.warn("⚠️ Elemento #my-photo não encontrado");
    }

    // Listener para botão fechar
    const closeButtons = document.querySelectorAll(
      PANEL_CONFIG.closeButtonSelector,
    );
    if (closeButtons.length > 0) {
      closeButtons.forEach((btn) => {
        btn.addEventListener(
          "click",
          function (e) {
            e.preventDefault();
            e.stopPropagation();
            console.log("❌ Clique no botão fechar detectado");
            closePanel();
          },
          true,
        );
      });
      console.log(
        `✅ ${closeButtons.length} listener(s) de fechar adicionado(s)`,
      );
    }
  }

  /**
   * Monitora e força CSS do painel continuamente
   */
  function startPanelMonitor() {
    setInterval(function () {
      const panels = document.querySelectorAll(PANEL_CONFIG.selector);
      panels.forEach((panel) => {
        if (panel.classList.contains(PANEL_CONFIG.openClass)) {
          // Se painel está com classe open, garantir que tem CSS correto
          const computed = window.getComputedStyle(panel);
          if (
            computed.display === "none" ||
            computed.visibility === "hidden" ||
            computed.opacity === "0"
          ) {
            console.warn(
              '⚠️ Painel com classe "open" mas não visível - corrigindo...',
            );
            forceShowPanel();
          }
        }
      });
    }, 500);

    console.log("📊 Monitor de painel iniciado (intervalo: 500ms)");
  }

  /**
   * Garante que element tem pointer-events correto
   */
  function fixPointerEvents() {
    const photos = document.querySelectorAll(PANEL_CONFIG.photoSelector);
    photos.forEach((photo) => {
      if (window.getComputedStyle(photo).pointerEvents === "none") {
        console.warn("⚠️ #my-photo tem pointer-events: none - corrigindo...");
        photo.style.pointerEvents = "auto";
      }
    });
  }

  /**
   * Inicialização principal
   */
  function init() {
    console.log("🚀 Profile Panel Fix iniciandoó...");

    // Esperar um pouco para garantir que DOM está pronto
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", completeInit);
    } else {
      completeInit();
    }
  }

  /**
   * Inicialização completa
   */
  function completeInit() {
    setTimeout(function () {
      checkPanelState();
      fixPointerEvents();
      initEventListeners();
      forceShowPanel(); // Verificação inicial
      startPanelMonitor();

      // Exportar funções globalmente para uso manual
      window.panelUtils = {
        open: openPanel,
        close: closePanel,
        check: checkPanelState,
        forceShow: forceShowPanel,
        fixPointerEvents: fixPointerEvents,
      };

      console.log("✅ Profile Panel Fix ATIVADO");
      console.log(
        "📝 Comandos disponíveis: window.panelUtils.open(), .close(), .check()",
      );
    }, PANEL_CONFIG.initialDelay);
  }

  // Iniciar quando o script for carregado
  init();
})();

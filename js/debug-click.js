// ====== DEBUG COMPLETO: PLUGINS + CLICK ======
console.log("╔════════════════════════════════════════════╗");
console.log("║         DEBUG PLUGINS E CLICK EVENTS        ║");
console.log("╚════════════════════════════════════════════╝");

// 1. VERIFICAR JQUERY
console.log("\n🔍 jQuery Status:");
console.log(
  "  jQuery versão:",
  typeof jQuery !== "undefined" ? jQuery.fn.jquery : "❌ NÃO CARREGADO",
);
console.log("  $ disponível:", typeof $ !== "undefined" ? "✅ SIM" : "❌ NÃO");
console.log(
  "  $ é jQuery:",
  typeof $ === "function" && $.fn && $.fn.jquery ? "✅ SIM" : "❌ NÃO",
);

// 2. VERIFICAR PLUGINS
console.log("\n🔌 Status dos Plugins:");
console.log(
  "  Select2:",
  typeof $.fn.select2 !== "undefined" ? "✅ ATIVO" : "❌ N/A",
);
console.log(
  "  jQuery UI Tabs:",
  typeof $.fn.tabs !== "undefined" ? "✅ ATIVO" : "❌ N/A",
);
console.log(
  "  jQuery Mask:",
  typeof $.fn.mask !== "undefined" ? "✅ ATIVO" : "❌ N/A",
);

// 3. USAR A FUNÇÃO DO PLUGIN-LOADER
setTimeout(function () {
  console.log("\n📊 Diagnóstico de Saúde dos Plugins:");
  if (typeof window.checkPluginsHealth === "function") {
    window.checkPluginsHealth();
  } else {
    console.log(
      "  ⚠️ checkPluginsHealth não disponível. Verificando manualmente...",
    );
    console.log(
      "  Select2:",
      typeof $.fn.select2 !== "undefined" ? "✅ OK" : "❌ FALTANDO",
    );
    console.log(
      "  jQuery UI:",
      typeof $.fn.tabs !== "undefined" ? "✅ OK" : "❌ FALTANDO",
    );
    console.log(
      "  jQuery Mask:",
      typeof $.fn.mask !== "undefined" ? "✅ OK" : "❌ FALTANDO",
    );
  }
}, 500);

// 4. VERIFICAR ELEMENTOS DO DOM
setTimeout(function () {
  console.log("\n🎯 Elementos no DOM:");
  console.log(
    "  #my-photo encontrado:",
    $("#my-photo").length > 0 ? "✅ SIM" : "❌ NÃO",
  );
  console.log(
    "  .panel-left encontrado:",
    $(".panel-left").length > 0 ? "✅ SIM" : "❌ NÃO",
  );
  console.log(
    "  .action_arrow encontrado:",
    $(".action_arrow").length > 0 ? "✅ SIM" : "❌ NÃO",
  );

  if ($("#my-photo").length > 0) {
    console.log("  #my-photo HTML:", $("#my-photo").html());
  }
}, 1000);

// 5. VERIFICAR HANDLERS DE EVENTOS
setTimeout(function () {
  console.log("\n📌 Event Handlers Registrados:");
  var myPhoto = document.getElementById("my-photo");
  if (myPhoto) {
    try {
      var events = $(myPhoto).data("events") || $._data(myPhoto, "events");
      console.log(
        "  Handlers em #my-photo:",
        events ? "✅ " + JSON.stringify(Object.keys(events)) : "❌ Nenhum",
      );
    } catch (e) {
      console.log("  Handlers em #my-photo: ⚠️ Não foi possível verificar");
    }
  }
}, 1500);

// 6. TEST CLICK
setTimeout(function () {
  console.log("\n🧪 Testando Click Event:");
  console.log("  Tentando disparar click em #my-photo...");

  var myPhoto = $("#my-photo");
  if (myPhoto.length === 0) {
    console.log("  ❌ Elemento #my-photo não encontrado!");
    return;
  }

  var beforeClass = $(".panel-left").attr("class");
  console.log("  Classes de .panel-left ANTES:", beforeClass);

  // Disparar click
  myPhoto.trigger("click");

  // Verificar mudança
  setTimeout(function () {
    var afterClass = $(".panel-left").attr("class");
    console.log("  Classes de .panel-left DEPOIS:", afterClass);

    if (beforeClass !== afterClass) {
      console.log("  ✅ CLICK FUNCIONOU! Classes foram alteradas");
    } else {
      console.log(
        "  ⚠️ Classes não mudaram. Verifique se há handlers registrados",
      );
    }
  }, 200);
}, 2000);

// INSTRUÇÕES PARA USUARIO
console.log("\n" + "═".repeat(50));
console.log("📋 INSTRUÇÕES:");
console.log("═".repeat(50));
console.log("1. Verifique os Status acima (✅ = OK, ❌ = Problema)");
console.log("2. Se Status OK, o problema é no CSS ou nas classes");
console.log("3. Se algum plugin está ❌, aguarde (carrega de CDN)");
console.log("4. Digite no console: window.checkPluginsHealth()");
console.log("   para checar novamente a qualquer momento");
console.log("═".repeat(50) + "\n");

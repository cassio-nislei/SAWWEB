// ====== DEBUG CLICK EVENTS ======
console.log("=== DEBUG CLICK EVENTS ===");

// Verificar jQuery
console.log(
  "jQuery versão:",
  typeof jQuery !== "undefined" ? jQuery.fn.jquery : "NÃO CARREGADO",
);
console.log("$ disponível:", typeof $ !== "undefined");

// Esperar um pouco para DOM estar pronto
setTimeout(function () {
  console.log("\n--- Verificando #my-photo ---");

  // Procurar por #my-photo
  var myPhoto = $("#my-photo");
  console.log("Elemento #my-photo encontrado:", myPhoto.length > 0);
  console.log("HTML de #my-photo:", myPhoto.html());
  console.log("CSS transform atual:", myPhoto.css("transform"));

  // Procurar por .panel-left
  var panelLeft = $(".panel-left");
  console.log("\nElemento .panel-left encontrado:", panelLeft.length > 0);
  console.log("CSS transform de .panel-left:", panelLeft.css("transform"));

  // Tentar clicar programaticamente
  console.log("\n--- Testando click programático ---");
  myPhoto.click();
  console.log(
    "Após click(), transform de .panel-left:",
    panelLeft.css("transform"),
  );

  // Tentar trigger
  console.log("\n--- Testando trigger('click') ---");
  myPhoto.trigger("click");
  console.log(
    "Após trigger('click'), transform de .panel-left:",
    panelLeft.css("transform"),
  );

  // Listar todos os handlers de #my-photo
  console.log("\n--- Verificando handlers de #my-photo ---");
  try {
    var events = $._data(myPhoto[0], "events");
    console.log("Eventos em #my-photo:", events);
  } catch (e) {
    console.log("Não foi possível listar eventos:", e.message);
  }

  // Adicionar novo handler para testar
  console.log("\n--- Adicionando novo handler de teste ---");
  $(document).off("click", "#my-photo"); // Remover handlers antigos

  $(document).on("click", "#my-photo", function (ev) {
    console.log(">>> CLICK EVENT DISPARADO! <<<");
    ev.preventDefault();
    $(".panel-left").addClass("open");
    console.log(
      "Panel-left classes após click:",
      $(".panel-left").attr("class"),
    );
    console.log("Panel-left display:", $(".panel-left").css("display"));
  });

  console.log(
    "Novo handler adicionado. Teste um clique real na imagem de perfil.",
  );
}, 1000);

// Auto-test: simular click após 3 segundos
setTimeout(function () {
  console.log("\n--- AUTO-TEST: Simulando click automático ---");
  $("#my-photo").trigger("click");
}, 3000);

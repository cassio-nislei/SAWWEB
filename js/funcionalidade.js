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
  $(".voltar").click(function (ev) {
    ev.preventDefault();
    $("#box-operadores").css("left", "-445px");
  });

  /*** chama lista de contatos **/
  $("#contatos-bt-lista").click(function (ev) {
    ev.preventDefault();
    $("#box-contatos").css("left", "0");
  });
  $(".voltar").click(function (ev) {
    ev.preventDefault();
    $("#box-contatos").css("left", "-445px");
  });

  /*** abri perfil **/
  $("#my-photo").click(function (ev) {
    ev.preventDefault();
    $(".panel-left").addClass("open");
  });
  $("#btn-close-panel-edit-profile").click(function (ev) {
    ev.preventDefault();
    $(".panel-left").removeClass("open");
  });

  /*** chama chat **/
  $(".action_arrow").click(function (e) {
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
  $(".sair").click(function (e) {
    e.preventDefault();
    $("#Verchat").css("right", "-235px");
    $("._3zJZ2").css("width", "100%");
    $("._3oju3").css("width", "auto");
    $(".sair").hide();

    // Desabilita o Carregamento das Mensagens do WebChat //
    $("#carregaWebChat").val("0");
  });

  /*** mostra etiquetas **/
  $(".uk-flutua").click(function () {
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
  $(".filtrar").click(function () {
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

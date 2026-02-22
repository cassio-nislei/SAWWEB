// JavaScript Document
console.log("contatosForms.js carregado com sucesso!");

// PRIMEIRA COISA - Bloquear jquery.form.js IMEDIATAMENTE
$(function () {
  // Bloquear TODOS os submits do formulário
  $("#gravaContato").on("submit.bloqueio", function (e) {
    console.log("BLOQUEADO: Tentativa de submit no formulário");
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
  });

  // Remover qualquer handler anterior de 'click' no botão
  $("#btnGravarContato").off("click");

  // Bloquear submit (type=submit confunde, então vamos bloquear mesmo com type=button)
  $(document).off("submit");
});

// Formatando o 'Número do Telefone' //
$(document).ready(function () {
  if (typeof $.fn.mask === "function") {
    var behavior = function (val) {
        return val.replace(/\D/g, "").length === 13
          ? "+00 (00) 00000-0000"
          : "+00 (00) 0000-00009";
      },
      options = {
        onKeyPress: function (val, e, field, options) {
          field.mask(behavior.apply({}, arguments), options);
        },
      };

    $("#numero_contato").mask(behavior, options);
  } else {
    console.log("jQuery Mask Plugin não está carregado");
  }

  // REMOVER Comportamento padrão de form submit
  $(document).off("submit", "#gravaContato");
  $("#gravaContato").off("submit");
});
// FIM Formatando o 'Número do Telefone' //

// Adicionar/Alterar Registro //
// HANDLER PRINCIPAL - Executar PRIMEIRO
$(function () {
  // Garantir que este handler execute primeiro
  $(document).on("click.contatos", "#btnGravarContato", function (e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    console.log("✓ Botão Salvar clicado (handler principal)!");

    // Declaração de Variáveis //
    var mensagem = "<strong>Contato Cadastrado com sucesso!</strong>";
    var mensagem2 = "<strong>Contato Atualizado com sucesso!</strong>";
    var mensagem3 = "Já existe um Contato cadastrado com estes dados!";
    var mensagem4 = "Falha ao Efetuar Cadastro!";
    var mensagem8 = "Não é permitido o cadastro de Telefones Internacionais";
    var nome = $("#nome_contato").val();
    var numero = $("#numero_contato").val();
    // FIM Declaração de Variáveis //

    console.log("Nome:", nome, "Número:", numero);

    $("input:text").css({ "border-color": "#999" });
    $(".msgValida").css({ display: "none" });

    // Tratamento de Exceções //
    if (
      numero.replace(/\D/g, "").length < 12 ||
      numero.replace(/\D/g, "").length > 13
    ) {
      $("#valida_numero_contato").html(
        "Número do Telefone fora do padrão. Por favor informe o número corretamente!",
      );
      $("#valida_numero_contato").css({ display: "inline", color: "red" });
      $("#numero_contato").css({ "border-color": "red" });
      $("#numero_contato").focus();
      return false;
    }

    if ($.trim(numero) == "") {
      $("#valida_numero_contato").css({ display: "inline", color: "red" });
      $("#numero_contato").css({ "border-color": "red" });
      $("#numero_contato").focus();
      return false;
    }

    if ($.trim(nome) == "") {
      $("#valida_nome_contato").css({ display: "inline", color: "red" });
      $("#nome_contato").css({ "border-color": "red" });
      $("#nome_contato").focus();
      return false;
    }
    // FIM Tratamento de Exceções //

    console.log("Validações passaram, enviando formulário...");

    // Coleta os dados do formulário
    var formData = new FormData(document.getElementById("gravaContato"));

    // Log dos dados que serão enviados
    console.log("FormData items:");
    for (var pair of formData.entries()) {
      console.log(pair[0] + ": " + pair[1]);
    }

    // Desabilita botões
    $("#gravaContato").find("input, button").prop("disabled", true);
    $("#btnGravarContato").attr("value", "Salvando ...");

    var urlCompleta =
      window.location.origin + "/cadastros/contatos/ContatoController.php";
    console.log(
      "Enviando para URL relativa (a partir de gestao/):",
      urlCompleta,
    );
    console.log(
      "Resolução: sai de gestao/ com ../, volta à raiz e entra em cadastros/contatos/",
    );

    $.ajax({
      type: "POST",
      url: urlCompleta,
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (retorno) {
        console.log("Resposta recebida:", retorno);

        if (retorno == 1) {
          console.log("✓ Contato cadastrado com sucesso!");
          if (typeof mostraDialogo === "function") {
            mostraDialogo(mensagem, "success", 2500);
          } else {
            alert(mensagem);
          }
        } else if (retorno == 2) {
          console.log("✓ Contato atualizado com sucesso!");
          if (typeof mostraDialogo === "function") {
            mostraDialogo(mensagem2, "success", 2500);
          } else {
            alert(mensagem2);
          }
        } else if (retorno == 3) {
          console.log("✗ Contato duplicado!");
          if (typeof mostraDialogo === "function") {
            mostraDialogo(mensagem3, "danger", 2500);
          } else {
            alert(mensagem3);
          }
        } else if (retorno == 8) {
          console.log("✗ Número internacional!");
          if (typeof mostraDialogo === "function") {
            mostraDialogo(mensagem8, "danger", 2500);
          } else {
            alert(mensagem8);
          }
        } else {
          console.log("✗ Erro:", retorno);
          var erro =
            typeof retorno === "object" && retorno.erro
              ? retorno.erro
              : JSON.stringify(retorno);
          if (typeof mostraDialogo === "function") {
            mostraDialogo(erro, "danger", 2500);
          } else {
            alert(erro);
          }
        }

        // Atualiza a Lista de Contatos //
        if (typeof atualizaContatos === "function") {
          atualizaContatos();
        }

        // Fechando a Modal //
        if (typeof fecharModal === "function") {
          fecharModal();
        }
      },
      error: function (xhr, status, error) {
        console.log("✗ Erro AJAX:", status, error);
        console.log("Status Code:", xhr.status);
        console.log("Response:", xhr.responseText);
        if (typeof mostraDialogo === "function") {
          mostraDialogo(mensagem4 + " - " + error, "danger", 2500);
        } else {
          alert(mensagem4 + " - " + error);
        }
      },
      complete: function () {
        // Re-habilita botões
        $("#btnGravarContato").attr("value", "Salvar");
        $("#gravaContato").find("input, button").prop("disabled", false);
      },
    });

    return false;
  });
}); // FECHAR $(function() do bloqueio
// FIM Adicionar/Alterar Registro //

// Exclusão de Registro //
$(document).on("click", "#btnConfirmaRemoveContato", function () {
  // Desabilitando os Botões //
  $("#btnConfirmaRemoveContato").attr("value", "Removendo ...");
  $("#btnConfirmaRemoveContato").attr("disabled", true);
  $("#btnCancelaRemoveContato").attr("disabled", true);

  // Recupera o 'Número' do Contato Selecionado //
  var idContato = $("#idContatoExcluir").val();
  var acaoContato = $("#acaoContatoExcluir").val();

  $.post(
    window.location.origin + "/cadastros/contatos/ContatoController.php",
    { id: idContato, acao: acaoContato },
    function (resultado) {
      var mensagem = "<strong>Contato Removido com sucesso!</strong>";
      var mensagem2 = "Falha ao Remover Contato!";

      if (resultado == 2) {
        if (typeof mostraDialogo === "function") {
          mostraDialogo(mensagem, "success", 2500);
        } else {
          alert(mensagem);
        }

        // Atualiza a Lista de Contatos //
        if (typeof atualizaContatos === "function") {
          atualizaContatos();
        }
      } else {
        if (typeof mostraDialogo === "function") {
          mostraDialogo(mensagem2, "danger", 2500);
        } else {
          alert(mensagem2);
        }
      }

      // Habilitando os Botões //
      $("#btnConfirmaRemoveContato").attr("value", "Confirmar Exclusão!");
      $("#btnConfirmaRemoveContato").attr("disabled", false);
      $("#btnCancelaRemoveContato").attr("disabled", false);

      // Fechando a Modal //
      if (typeof fecharModal === "function") {
        fecharModal();
      }
    },
  );
});
// FIM Exclusão de Registro //

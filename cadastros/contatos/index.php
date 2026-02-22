<?php 
// Este arquivo é incluído de gestao/index.php que já carrega padrao.inc.php
?>

<div class="box-modal">
    <h2 class="title" id="titleCadastroContato">Adicionar Novo Contato</h2>
    <form method="post" id="gravaContato" name="gravaContato" action="/cadastros/contatos/ContatoController.php">
        <input type="hidden" name="id" id="idContato" />
        <input type="hidden" value="1" name="acao" id="acaoContato" />

        <div class="form-group">
            <label for="numero_contato">Celular com Whatsapp <b>(DDI Brasil 55)</b></label>
            <input type="text" id="numero_contato" name="numero_contato" class="form-control" placeholder="Informe o Celular com DDD e DDI (com Whatsapp)" />
            <div id="valida_numero_contato" style="display: none" class="text-danger small mt-1">
                Por favor, informe o Número do Telefone com DDD e DDI.
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nome_contato">Nome</label>
                    <input type="text" id="nome_contato" name="nome_contato" class="form-control" placeholder="Informe o Nome do Contato" />
                    <div id="valida_nome_contato" style="display: none" class="text-danger small mt-1">
                        Por favor, informe o NOME do Contato.
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="razao_social">Razão Social</label>
                    <input type="text" id="razao_social" name="razao_social" class="form-control" placeholder="Informe a Razão Social" />
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cpf_cnpj">CPF/CNPJ</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" class="form-control" placeholder="Informe o CPF ou CNPJ" />
                    <div id="valida_cpf_cnpj" style="display: none" class="text-danger small mt-1">
                        Por favor, informe um CPF ou CNPJ Válido.
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="id_etiqueta2">Escolha as TAGs</label>
            <select class="form-control js-example-basic-multiple pesqEtiquetas" name="id_etiqueta2[]" multiple="multiple" id="id_etiqueta2">
                <?php
                    //Crio a lista de etiquetas e defino as cores a exibir
                    $query = mysqli_query($conexao, "SELECT * FROM tbetiquetas");                       
                    while ($ListarEtiquetas = mysqli_fetch_array($query)){       
                        echo  '<option value="'.$ListarEtiquetas["id"].'" data-color="'.$ListarEtiquetas["cor"].'" >'.$ListarEtiquetas["descricao"].'</option>';                     
                    }
                ?>
            </select>
        </div>

        <div class="form-group mt-4">
            <button id="btnCancelaContato" class="btn btn-secondary" type="button">Cancelar</button>
            <button id="btnGravarContato" class="btn btn-primary" type="button">Salvar</button>
        </div>
    </form>
</div>

<!-- BLOQUEIO IMEDIATO DO JQUERY.FORM.JS -->
<script>
// Bloquear jquery.form.js ANTES dele interceptar o formulário
if (typeof $.fn.ajaxForm !== 'undefined') {
  // Se jquery.form.js já carregou, desabilitar ele
  console.log("jquery.form.js detectado - bloqueando...");
  $.fn.ajaxForm = function() { return this; };
  $.fn.ajaxSubmit = function() { return this; };
}
</script>


<script>
$(document).ready(function() {
  $('.pesqEtiquetas').select2({
    placeholder: 'TAGS',
    maximumSelectionLength: 10,
    "language": "pt-BR"
  });

  function ajustarCoresSelect2() {
    
  var selectedColors = {};

    $('.pesqEtiquetas').on('select2:select', function(e) {
    var selectedOption = e.params.data.element;
    var selectedColor = $(selectedOption).attr('data-color');
    var selectedId = $(selectedOption).val();
    var selectedTag = $(this).next().find('.select2-selection__choice[title="' + e.params.data.text + '"]');
    selectedColors[selectedId] = selectedColor;
    for (var id in selectedColors) {
      var selectedOption = $(this).find('option[value="' + id + '"]');
      selectedOption.css('background-color', selectedColors[id]);
      var selectedTag = $(this).next().find('.select2-selection__choice[title="' + selectedOption.text() + '"]');
      selectedTag.css('background-color', selectedColors[id]);
    }
    selectedTag.css('background-color', selectedColor);
  });
}

$('#id_etiqueta2').on('select2:open', function() {
  ajustarCoresSelect2();
});


  $('.pesqEtiquetas').on('select2:unselect', function(e) {
    var unselectedOption = e.params.data.element;
    var unselectedId = $(unselectedOption).val();
    delete selectedColors[unselectedId];
    $(this).find('option[value="' + unselectedId + '"]').css('background-color', '');
    var selectedTag = $(this).next().find('.select2-selection__choice[title="' + e.params.data.text + '"]');
    selectedTag.css('background-color', '');
  });

  // Fechar a Janela //
  $('.fechar').on("click", function() {
    fecharModal();
  });
});

// Fallback para mostraDialogo se não estiver definido
if (typeof mostraDialogo === 'undefined') {
  window.mostraDialogo = function(msg, tipo, tempo) {
    alert(msg);
    console.log(msg);
  };
}
</script>

<script src="/gestao/js/funcoes.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script src="/cadastros/contatos/contatosForms.js"></script>
<!-- Cadastro de Resposta Rápida -->
<div class="window medio" id="modalRespostaRapida">
   

    <div class="box-modal" id="FormRespostaRapida" style="display: none;">
        <form method="post" id="gravaRespostaRapida" name="gravaRespostaRapida" action="cadastros/respostasrapidas/salvar.php">
        <input type="hidden" value="0" name="acaoRespostaRapida" id="acaoRespostaRapida">
        <input type="hidden" id="IdRespostaRapida" name="IdRespostaRapida" value="0" />

        <h2 class="title" id="titleCadastroRespostaRapida">Adicionar Nova Resposta Rápida</h2>

        <div class="uk-grid">
            <div class="uk-width-1-2@m">
            <div class="uk-form-label"> Título </div>
            <input class="uk-input" type="text" id="titulo" name="titulo" placeholder="Informe um Título para a Resposta Rápida" maxlength="50" />
            <div id="valida_titulo" style="display: none" class="msgValida">
                Por favor, Informe um Título para a Resposta Rápida.
            </div>
            </div>

            <div class="uk-width-1-2@m">
            <div class="uk-form-label"> Visibilidade </div>
                <select class="uk-select" id="id_usuario" name="id_usuario">
                    <option value="">Informe a visibilidade desta Resposta Rápida</option>
                    <option value="1">Para todos</option>
                    <option value="2">Somente para meu operador</option>
                </select>
                <div id="valida_id_usuario" style="display: none" class="msgValida">
                    Por favor, Informe a visibilidade desta Resposta Rápida.
                </div>
            </div>
        </div>

        <div class="uk-width-1-1@m">
            <div class="uk-form-label"> Mensagem </div>
            <textarea class="uk-textarea" type="text" id="resposta" name="resposta" rows="10" placeholder="Informe qual será a Resposta Rápida"></textarea>
            <div id="valida_resposta" style="display: none" class="msgValida">
              Por favor, Informe qual será a Resposta Rápida.
            </div>
        </div>

        <div class="uk-width-1-1@m" style=" float: left;">
					<div class="uk-form-label"> <b>Arquivo de envio Automatico</b> </div>
					
          <div class="col-md-2">
            <h2 class="title" id="arquivo_carregado"></h2>						
					</div>

					<div class="col-md-10" align="left" style="margin-top: 15px;">
						<div class="uk-form-file">
							<input type="file" name="foto" id="foto" />
			
						</div>		
					</div>

			</div>


        </form>

        <p class="uk-text-right" style="margin-top:1rem">
            <button id="btnFecharCadastroRespostaRapida" class="uk-button uk-button-default uk-modal-close" type="button">Cancelar</button>
            <input id="btnGravarRespostaRapida" class="uk-button uk-button-primary " type="submit" value="Salvar">
        </p>
    </div>

    <div class="box-modal" id="ListaRespostasRapidas">
    <h2 class="title">Respostas Rápidas Cadastradas <a id="btnNovaRespostaRapida" href="#" class="uk-align-right" uk-icon="plus-circle" title="Nova Resposta Rápida"></a></h2>

    <div class="uk-clearfix" style="display: none;">
        <div class="uk-float-left">
            <p style="padding-left: 17px; padding-bottom: 10px;font-size: 15px;">No campo de texto, digite o atalho <b>/titulo</b> para filtrar as respostas rápidas.</p>
        </div>
    </div> 
    
    <div id="tabs4">
        <ul>
            <li><a href="#aba-1">Todas (<span id="countTodas"></span>)</a></li>
            <li><a href="#aba-2">Minhas (<span id="countMinhas"></span>)</a></li>
        </ul>

        <div id="aba-1">
        <div class="uk-active">
            <div class="uk-padding-smaluk-text-left uk-height-medium" id="todaLista">
            <!-- Todas as Respostas Rápidas -->
            </div>
        </div>
        </div>

        <div id="aba-2">
            <div class="uk-padding-smaluk-text-left uk-height-medium" id="minhaLista">
            <!-- Somente as Minhas Respostas Rápidas -->
            </div>
        </div>

        <hr class=" uk-margin-remove">
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button id="btnCancelarRespostasRapidas" class="uk-button uk-button-default uk-modal-close fechar" type="button">Cancelar</button>
    </p>
    </div>
</div>

<script type='text/javascript' src="/cadastros/respostasrapidas/acoes.js"></script>
<script>
  $(document).ready(function() {
    $.ajax("cadastros/respostasrapidas/listar.php").done(function(data) {
      var lista = data.split("#@#");
      $("#todaLista").html(lista[0]);
      $("#minhaLista").html(lista[1]);
      $("#countTodas").html(lista[2]);
      $("#countMinhas").html(lista[3]);
    });

    // Fechar a Janela //
    $('.fechar').on("click", function(){ fecharModal(); });
  });

  
</script>
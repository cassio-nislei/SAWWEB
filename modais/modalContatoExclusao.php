<!-- Cadastro de Contato - Exclusão -->
<div class="window menor" id="modalContatoExclusao">
    <div class="box-modal">
        <h2 class="title">Confirma a Remoção do Contato?</h2>
        <p>Se realmente excluir este Registro, todos os dados deste Contato serão perdidos</p>

        <p class="uk-text-right" style="margin-top: 148px;">
            <button type="button" id="btnCancelaRemoveContato" class="uk-button uk-button-default uk-modal-close fechar">Cancelar</button>
            <button type="button" id="btnConfirmaRemoveContato" class="uk-button uk-button-primary">Confirmar Exclusão!</button>
        </p>
    </div>

    <input type="hidden" name="id" id="idContatoExcluir" />
    <input type="hidden" name="acao" id="acaoContatoExcluir" value="9" />
</div>
<script type='text/javascript' src="/cadastros/contatos/contatosForms.js"></script>
<script type='text/javascript' src="cadastros/usuarios/redefinir.js"></script>

<div class="box-modal">
    <h2 class="title" id="titleCadastroUser">Redefinir Senha do Usuário</h2>
    <div class="">
        <form method="post" id="redefinirSenha" name="redefinirSenha" action="cadastros/usuarios/redefinir.php">
            <div class="uk-width-1-1@m">
                <div class="uk-form-label">Digite a sua Nova Senha </div>
                <input type="password" id="senha" name="senha" maxlength="8" class="uk-input" />
                <div id="valida_senha" style="display: none" class="msgValida">
                    Por favor, informe a senha que será utilizado no acesso.
                </div>
            </div>
        </form>
    </div>

    <p class="uk-text-right" style="margin-top:1rem">
        <button class="uk-button uk-button-default uk-modal-close fechar" type="button">Cancelar</button>
        <input id="btnRedefinirSenha" class="uk-button uk-button-primary " type="submit" value="Gravar Nova Senha">
    </p>
</div>
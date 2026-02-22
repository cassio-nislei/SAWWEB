<div id="fundo_preto"></div>

<style>
    .collapse-row.collapsed + tr {
        display: none;
    }
    .sublist li{
        float: left;
        display: flex;
        min-width: 15%;
    }
    .sublist li:nth-child(1){
        min-width: 20%
    }
    .sublist li:nth-child(5){
        min-width: 20%
    }
    .titlesTable{
        width: 15%;
        float: left;
        border-bottom: 1px solid #eee;
        padding: 15px 0;
    }
    .titlesTableTitulo{
        width: 15%;
        float: left;
        padding: 15px 0;
    }
    .clear{
        clear: both;
    }
    .uk-accordion-title::before{
        background: none !important;
    }
    .uk-accordion-title, .titlesTable{
        font-size: 1em !important;
        margin: 0;
        height: 1em !important;
    }
    .uk-accordion-content{
        margin: 0 !important;
    }
    .uk-accordion li{
        margin-top: 0 !important;
    }
    .w10p{ width: 10% !important; }
    .w16p{ width: 16%; }
    .w25p{ width: 24%; }
    .w20p{ width: 20%; }
    .w41p{ width: 41%; }
    .w45p{ width: 41%; }
    .w60p{ width: 58%; }
    .w65p{ width: 65%; }
    .sublist{
        padding: 0;
        margin: 0;
    }
    .sublist li{
        float: left;
        width: 100%;
        padding: 10px 0;
    }
    .sublist li:nth-child(odd){
        background: #eee
    }
    .sublist li:nth-child(even){
        background: #fff
    }
    .sublist li:nth-last-child(1){
        border-bottom: 2px solid #ccc
    }
    .sublist li div:nth-child(1){
        width: 70%;
        padding: 5px 10px;
    }
    .topLine{
        background: #333;
        color: #FFF
    }
    .topLine div:nth-child(1){
        padding-left: 1.5%;
    }
    .pl1m{
        padding-left: 1.5%;
    }
</style>

<!-- Finalizar o Atendimento -->
<?php require_once("modalFinalizarAtendimento.php"); ?>

<!-- Transferir o Atendimento -->
<?php require_once("modalTransferirAtendimento.php"); ?>

<!-- Histórico de Conversas -->
<?php require_once("modalHistorico.php"); ?>

<!-- Cadastro Contato -->
<?php require_once("modalContato.php"); ?>

<!-- Cadastro Contato - Exclusão -->
<?php require_once("modalContatoExclusao.php"); ?>

<!-- Cadastro Respota Rápida -->
<?php require_once("modalRespostaRapida.php"); ?>

<!-- Cadastro Respota Rápida - Exclusão -->
<?php require_once("modalRespostaRapidaExclusao.php"); ?>

<!-- Cadastro Configuração - Upload -->
<?php require_once("modalUpload.php"); ?>

<!-- Modal Dashboard -->
<?php require_once("modalDashboard.php"); ?>

<!-- Modal Relatorio -->
<?php require_once("modalRelatorio.php"); ?>

<!-- Modal QRCode -->
<?php require_once("modalQRCode.php"); ?>

<!-- Modal Base de Conhecimento -->
<?php require_once("modalBaseConhecimento.php"); ?>

<!-- Modal Base de Conhecimento - Exclusão -->
<?php require_once("modalBaseConhecimentoExclusao.php"); ?>


<!-- Modal Redefinir Senha -->
<?php require_once("modalRedefinirSenha.php"); ?>

<!-- Modal Redefinir Senha -->
<?php require_once("modalNovaConversa.php"); ?>

<!-- Cadastro de Etiqueta -->
<?php require_once("modalEtiqueta.php"); ?>

<!-- Cadastro de Menu - Exclusão -->
<?php require_once("modalEtiquetaExclusao.php"); ?>

<!-- Histórico de Conversas -->
<?php require_once("modalObsAtendimentos.php"); ?>

<!-- Modal -->
<div id="mdlTiraFoto" data-backdrop="false" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Visualização da Camera</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <video autoplay id="video"></video>      
        <canvas class="is-hidden" id="canvas" style="display:none"></canvas>
        
      </div>
      <div class="modal-footer">
        <button id="btnChangeCamera" type="button" class="btn btn-secondary" alt="Alterar Camera" title="Alterar Camera"><img src="img/camera-rotate.png" width="16" height="16"></button>
        <button id="btnScreenshot" type="button" class="btn btn-primary" alt="Tirar Foto" title="Tirar Foto"> <i class="fas fa-camera"></i></button>
      </div>
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="mdlAlmocando" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Em Horario de almoço</h5>
      </div>
      <div class="modal-body">
         <button type="button" class="btn btn-primary" id="btnVoltardoAlmoco">Clique aqui para retornar ao trabalho!</button>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>
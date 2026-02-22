<?php
   // Requires //
   require_once("padrao.inc.php");

   // Definições de Variáveis //
      $id = isset($_GET["idDepartamento"]) ? $_GET["idDepartamento"] : "";
      $condicao = ($id !== "") ? " AND tud.id_departamento = '$id'" : " GROUP BY tu.id";
   // FIM Definições de Variáveis //

   $usuarios = mysqli_query(
      $conexao
      , "SELECT tu.id, tu.nome, tu.datetime_online
          FROM tbusuario tu
             INNER JOIN tbusuariodepartamento tud ON tud.id_usuario = tu.id
             WHERE tu.situacao NOT IN('I')"
                . $condicao
   );

   // Recupera o Tempo para definir se o Usuário está Offline //
   $qryParametros = mysqli_query($conexao , "SELECT minutos_offline FROM tbparametros");
   $arrParametros = mysqli_fetch_assoc($qryParametros);

   if(mysqli_num_rows($usuarios) == 0){
      echo  '<option value="0">'.htmlentities('Não há usuários nesse departamentos').'</option>';
   }
   else{
      while($ln = mysqli_fetch_assoc($usuarios)){
         if( userOnline($ln["datetime_online"], $arrParametros["minutos_offline"])
            || ( isset($_SESSION["parametros"]["transferencia_offline"]) && intval($_SESSION["parametros"]["transferencia_offline"]) === 1 ) ){
            echo '<a id="u'.$ln['id'].'" href="javascript:;" onclick="return false;" class="uk-flex-middle uk-grid-small uk-grid lnkOperador" style="padding:0">
                     <div class="uk-width-3-4">
                        <input type="hidden" id="oper'.$ln['id'].'" value="'.$ln["nome"].'" />
                        <p class="uk-margin-remove" style="font-size:14px;">'.$ln['nome'].'</p>
                        <!--<p class="uk-margin-remove-top uk-text-small uk-text-muted" id=""><span style="color:green; font-size:12px">Está online</span></p>-->
                     </div>
                     <div class="uk-width-1-5 uk-flex-first uk-first-column">
                        <img src="img/ico-contact.svg" alt="Image" class="uk-border-circle" width="40">
                     </div>
               </a>';
         }
      }
   }
?>

<script>
   $(document).ready(function() {
      // Seleciona o Operador para Transferência //
      $('.lnkOperador').click(function() {
         var idOperador = ($(this).attr('id')).substring(1,$(this).attr('id').length);
         var nomeOperador = $("#oper"+idOperador).val();

         $("#operSelecionado").html(nomeOperador);
         $("#idOperadorSelecionado").val(idOperador);
      });
   });
</script>
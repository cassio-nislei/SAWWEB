<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");

  if (!isset($_POST["itemMenu"])){
    $menu = mysqli_query($conexao, "select * from tbmenu where pai is null or pai = 0 order by id;");
   
    while($listar=mysqli_fetch_assoc($menu)){
      echo '<button id='.$listar["id"].' type="button" class="btn btn-warning btn-flat btnmenu">'.$listar["descricao"].'</button>';
    }
   
  }else{
    $menu = $_POST["itemMenu"];
    $Listamenu = mysqli_query($conexao, "select * from tbmenu where pai = '$menu' order by id;");
    if (mysqli_num_rows($Listamenu)==0){
      $_SESSION["chat"]["menu"] = false;       
      //Gero um atendimeno na Triagem do SAW 
      $numero = $_SESSION["chat"]["numero"];  //Gravo o Número aleatorio que gerei no inicio do atendimento no lugar do Celular
      $qryId = mysqli_query($conexao, "select coalesce(max(id),0)+1 ID from tbatendimento where numero = '$numero' " );
      $novaID = mysqli_fetch_assoc($qryId);
      $s_id_atendimento     = $novaID["ID"];        //Gero o ID do novo atendimento
      $nome   = $_SESSION["chat"]["nome"];    //Gravo o nome que está na sessão no novo atendimento
      $situacao = 'T'; //Jogo na Triagem  depois verifico se tem Setor

      //Verifico se Possui departamento Vinculado ao Menu Selecionado
      $id_departamento = '';
      $departamento = mysqli_query($conexao, "select id from tbdepartamentos where id_menu = '$menu'");
      if (mysqli_num_rows($departamento)>0){
        $idDepartamento = mysqli_fetch_assoc($departamento);
        $id_departamento = $idDepartamento["id"];
        $situacao = 'P'; //Coloco a Situação como Atendimento Pendente para direcionar ao Setor
      }
          
     
      $qryaux = mysqli_query(
        $conexao
        , "INSERT INTO tbatendimento(id, situacao, numero, dt_atend, hr_atend, id_atend, nome, canal, setor)
          VALUES('".$s_id_atendimento."', '$situacao', '$numero', now(), now(), 0, '$nome', 0, '$id_departamento')"
      );
      $_SESSION["chat"]["id_atendimento"] = $s_id_atendimento;
      echo 1;
      return false;
    }

      while($listar=mysqli_fetch_assoc($Listamenu)){
        echo '<button id='.$listar["id"].' type="button" class="btn btn-warning btn-flat btnmenu">'.$listar["descricao"].'</button>';
      }


  }
  
  
?>

<script>
        $(document).ready(function() {
          $(".btnmenu").click(function(e){
             e.preventDefault();
             var itemMenu = $(this).attr("id");    
             $(".btnmenu").attr("disabled",true); 
             $.post("menu.php", {itemMenu:itemMenu},function(retorno){
              
               if (retorno==1){
                //  alert(retorno);
                  location.reload(true);   
                  return false;            
               }else{                
                 $( "#ListarMenu" ).html(retorno);
               }   
            
            
               $(".btnmenu").attr("disabled",false)
             })
          })


    


        });
    </script> 
<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
    // Declaração de Variáveis //
     $_numero = (isset($_POST['numero_contato']) && !empty($_POST['numero_contato'])) ? $_POST['numero_contato'] : null;
     $_numero = trim(SomenteNumero($_numero));

     
            //Independente de fazer insert ou update, eu apago todas as etiquetas gravadas para o registro atual e grvo novamente
            $dados= mysqli_query($conexao ,"select id_etiqueta from tbetiquetascontatos where numero = '$_numero'")or die (mysqli_error($conexao));

            if (mysqli_num_rows($dados)>0){
                $etiquetas = '';
                while ($resultado = mysqli_fetch_array($dados)){
                    $etiquetas .= $resultado["id_etiqueta"] . ',';
                }
                $etiquetas = substr( $etiquetas, 0, -1);
                echo  $etiquetas;
            }else{
                echo 0;
            }
     ?>
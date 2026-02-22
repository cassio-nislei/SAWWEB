<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
    
    // Envolver tudo em try-catch para garantir resposta
    try {
        // Declaração de Variáveis //
            $_method = (isset($_POST) && !empty($_POST)) ? "_Post" : "_Get";
            $_id     = isset($_GET["id"]) ? mysqli_real_escape_string($conexao, $_GET["id"]) : (isset($_POST["id"]) ? mysqli_real_escape_string($conexao, $_POST["id"]) : null);
            $_acao   = (isset($_POST['acao']) && !empty($_POST['acao'])) ? mysqli_real_escape_string($conexao, $_POST['acao']) : null;
            $_numero = (isset($_POST['numero_contato']) && !empty($_POST['numero_contato'])) ? mysqli_real_escape_string($conexao, $_POST['numero_contato']) : null;
            $_nome   = (isset($_POST['nome_contato']) && !empty($_POST['nome_contato'])) ? mysqli_real_escape_string($conexao, $_POST['nome_contato']) : null;
            $_razao_social   = (isset($_POST['razao_social']) && !empty($_POST['razao_social'])) ? mysqli_real_escape_string($conexao, $_POST['razao_social']) : null;
            $_cpf_cnpj   = (isset($_POST['cpf_cnpj']) && !empty($_POST['cpf_cnpj'])) ? mysqli_real_escape_string($conexao, $_POST['cpf_cnpj']) : null;
            $_etiqueta   = (isset($_POST['tag']) && !empty($_POST['tag'])) ? mysqli_real_escape_string($conexao, $_POST['tag']) : '0';
            $tabela  = "tbcontatos";
            $result  = "";
        // FIM Declaração de Variáveis //

    // Carrega Dados //
        if( $_method !== "_Post" && $_id !== null ){
            $dados = mysqli_query($conexao,"SELECT numero, nome, razao_social, cpf_cnpj  FROM $tabela WHERE numero = '".$_id."'")
                        or die(mysqli_error($conexao));
            $result = mysqli_fetch_object($dados);    
            $result->numero = Mask($result->numero);
        }
    // FIM Carrega Dados //

    // Excluir //
        else if( $_acao == "9" ){
            $sql = "DELETE FROM tbcontatos WHERE numero = '".$_id."'";
            $excluir = mysqli_query($conexao, $sql)
                        or die(mysqli_error($conexao));
        
            if($excluir){ $result = "2"; }
            else{ $result = "1"; }
        }
    // FIM Excluir //

    // Salvar //
        else if( $_acao !== null ){
            // Tratamento Adicional dos Dados //
                $_id	    = SomenteNumero($_id);
                $_numero    = SomenteNumero($_numero);
                $_etiqueta  = SomenteNumero($_etiqueta);
                $ddi	    = substr($_numero, 0, 2);
                $ddd	    = substr($_numero, 2, 2);
            // FIM Tratamento Adicional dos Dados //

                 //Independente de fazer insert ou update, eu apago todas as etiquetas gravadas para o registro atual e grvo novamente
                 $deletaEtiqueta = mysqli_query(
                    $conexao
                    ,"delete from tbetiquetascontatos where numero = '".$_numero."'");

                //Gravo todas as etiquetas selecionadas
                if(isset($_POST['id_etiqueta2'])){
                    $etiquetas_selecionadas = $_POST['id_etiqueta2'];
                    foreach ($etiquetas_selecionadas as $etiqueta) {
                       // echo "A etiqueta selecionada foi: " . $etiqueta . "<br>";
                        $deletaEtiqueta = mysqli_query( $conexao,"INSERT into tbetiquetascontatos (numero, id_etiqueta) VALUES ('$_numero', '$etiqueta')");
                    }
                  }


            // Não permite o Cadastro de Números Internacionais //
            if( $ddi === "55" ){
                // Validação do DDD //
                /* Essa validação é necessária pois o Whats não reconhece os 9 dígitos para os DDD maiores de 30 */
                if( strlen($_numero) === 13 && intval($ddd) > 30  ){
                    $_numero = $ddi . $ddd . substr($_numero, 5, 8);
                }

           
                // Inserção //
                if( $_acao == "1" ){
                    $existe = mysqli_query(
                        $conexao
                        , "SELECT 1 FROM tbcontatos WHERE numero = '".$_numero."' OR nome = '".$_nome."'"
                    );
            
                    if( mysqli_num_rows($existe) <= 0 ){
                        $sql = "INSERT INTO tbcontatos (numero, nome, razao_social, cpf_cnpj ) VALUES ('".$_numero."', '".$_nome."','".$_razao_social."','".$_cpf_cnpj."')";
                        $inserir = mysqli_query($conexao,$sql);
            
                        if( $inserir ){ 
                            $result = 1;
                        }
                        else{ 
                            $result = array("erro" => "Erro ao inserir: " . mysqli_error($conexao) . " SQL: " . $sql);
                        }
                    }
                    else{ $result = 3; }
                }
                // Atualização //
                else{
                    $existe = mysqli_query(
                        $conexao
                        , "SELECT 1 FROM tbcontatos WHERE ( numero = '".$_numero."' OR nome = '".$_nome."' ) AND numero != '".$_id."'"
                    );
            
                    if( mysqli_num_rows($existe) <= 0 ){
                        $atualizar = mysqli_query(
                            $conexao
                            , "UPDATE tbcontatos SET nome = '".$_nome."', numero = '".$_numero."', razao_social = '".$_razao_social."', cpf_cnpj = '".$_cpf_cnpj."' WHERE numero = '".$_id."'"
                        );
            
                        if( $atualizar ){ $result = 2; }
                        else{ $result = 4; }
                    }
                    else{ $result = 3; }
                }
            }
            else{ $result = 8; }
        }
    // FIM Salvar //

    // Tratamento de Exceção //
    else{
        $result = array(
            "erro" => "Interface não Implementada!"
        );
    }
    // FIM Tratamento de Exceção //

    } catch (Exception $e) {
        $result = array(
            "erro" => "Erro no sistema: " . $e->getMessage()
        );
    }

    // Header para garantir resposta JSON
    header("Content-Type: application/json");

    // Imprime o Resultado //
    echo json_encode($result);
    exit;
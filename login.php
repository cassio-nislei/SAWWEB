<?php
  session_cache_expire(180000);
  session_start();
  require("includes/conexao.php");

  // Definição de Variáveis //
    $usuario = isset($_POST["usuario"]) ? trim($_POST["usuario"]) : "";
    $senha = isset($_POST["senha"]) ? trim($_POST["senha"]) : "";
  // FIM Definição de Variáveis //

  // Validação dos Dados de Login //
    if( $usuario === "" 
      || preg_match('/[^[:alnum:]_@.-]/', $usuario) === 1 
      || $senha === "" ){
      echo "Dados inválidos!";
    }
    else{
      $validaLogin = mysqli_query(
        $conexao
        , "SELECT *
            FROM tbusuario 
              WHERE login = '".$usuario."'"
      );
  
      $arrUsuario = mysqli_fetch_assoc($validaLogin);
      
      if ( mysqli_num_rows($validaLogin)<=0 ){
        //Aqui não encontrou usuário 
        echo "Usuário não encontrado.";
        exit;
      }
      //Aqui o usuário está inativo
      else{
        if ($arrUsuario["situacao"] != 'A'){
          echo "Usuário inativo.";
          exit;
        }
        //Se a senha estiver incorreta
        if (trim($arrUsuario["senha"]) != trim($senha)){
          echo "Senha incorreta.";
          exit;
        }
        // Zera a Senha para não carregar na Sessão //
        else{ unset($arrUsuario["senha"]); }
      }
  
      // Carrego os Parametros na Sessao //
      $param = mysqli_query(
        $conexao
        , "SELECT * FROM tbparametros LIMIT 1"
      );  
      $parametros = mysqli_fetch_assoc($param);
  
      //Gravo os parametros na sessão
      if( empty($parametros["color"]) ){ $parametros["color"] = 'colorTarja'; }
      $_SESSION["parametros"] = $parametros;
      
      //Caso esteja tudo certo prosseguimos com o login
      $_SESSION["usuariosaw"] = $arrUsuario;

      // Buscando as Informações do Departamento //
      $qryDepto = mysqli_query(
        $conexao
        , "SELECT dp.id AS id, dp.departamento AS departamento
            FROM tbdepartamentos dp
              LEFT JOIN tbusuariodepartamento ud ON(ud.id_departamento=dp.id)
                WHERE ud.id_usuario = '".intval($arrUsuario["id"])."'
                  order by ud.seq LIMIT 1"
      );
      

      if( mysqli_num_rows($qryDepto) > 0 ){
        $resDepto = mysqli_fetch_assoc($qryDepto);

        $_SESSION["usuariosaw"]["idDepartamento"] = $resDepto['id'];
        $_SESSION["usuariosaw"]["nomeDepartamento"] = $resDepto['departamento'];

        // Sucesso //
        if ($_SESSION["usuariosaw"]["perfil"] == 0){
          echo "0";
        }else{
          echo "1";
        }
      } else{ 
         // echo "Usuário sem vínculo com algum Departamento."; 
         if ($_SESSION["usuariosaw"]["perfil"] == 0){
            echo "0";
          }else{
            echo "1";
         }
      }
      // Buscando as Informações do Departamento //
    }
  // FIM Validação dos Dados de Login //
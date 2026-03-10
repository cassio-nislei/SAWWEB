<?php
  session_cache_expire(180000);
  session_start();
  require("includes/conexao.php");
  define('AJAX_CALL', true);
  require_once("includes/padrao.inc.php");

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
      // Prepared statement para prevenir SQL Injection
      $stmt = mysqli_prepare($conexao, "SELECT * FROM tbusuario WHERE login = ?");
      mysqli_stmt_bind_param($stmt, "s", $usuario);
      mysqli_stmt_execute($stmt);
      $validaLogin = mysqli_stmt_get_result($stmt);
  
      $arrUsuario = mysqli_fetch_assoc($validaLogin);
      
      if ( mysqli_num_rows($validaLogin)<=0 ){
        echo "Usuário não encontrado.";
        mysqli_stmt_close($stmt);
        exit;
      }
      else{
        if ($arrUsuario["situacao"] != 'A'){
          echo "Usuário inativo.";
          mysqli_stmt_close($stmt);
          exit;
        }
        // Verificação de senha com suporte a hash bcrypt e plain text (migração gradual)
        if (!verificarSenha($senha, $arrUsuario["senha"])){
          echo "Senha incorreta.";
          mysqli_stmt_close($stmt);
          exit;
        }
        // Se a senha ainda está em plain text, migrar para hash
        if (strpos($arrUsuario["senha"], '$2y$') !== 0) {
          try {
            // Garante que a coluna suporta 60+ chars antes de migrar
            mysqli_query($conexao, "ALTER TABLE tbusuario MODIFY COLUMN senha VARCHAR(255) NOT NULL");
            $senhaHash = hashSenha($senha);
            $stmtUpd = mysqli_prepare($conexao, "UPDATE tbusuario SET senha = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpd, "si", $senhaHash, $arrUsuario["id"]);
            mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);
          } catch (Exception $e) {
            // Migração falhou mas login continua normalmente
          }
        }
        // Zera a Senha para não carregar na Sessão
        unset($arrUsuario["senha"]);
      }
      mysqli_stmt_close($stmt);

      // Regenerar Session ID para prevenir session fixation
      session_regenerate_id(true);
  
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

      // Buscando as Informações do Departamento com prepared statement
      $stmtDepto = mysqli_prepare($conexao, 
        "SELECT dp.id AS id, dp.departamento AS departamento
            FROM tbdepartamentos dp
              LEFT JOIN tbusuariodepartamento ud ON(ud.id_departamento=dp.id)
                WHERE ud.id_usuario = ?
                  ORDER BY ud.seq LIMIT 1"
      );
      mysqli_stmt_bind_param($stmtDepto, "i", $arrUsuario["id"]);
      mysqli_stmt_execute($stmtDepto);
      $qryDepto = mysqli_stmt_get_result($stmtDepto);

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
         if ($_SESSION["usuariosaw"]["perfil"] == 0){
            echo "0";
          }else{
            echo "1";
         }
      }
      mysqli_stmt_close($stmtDepto);
    }
  // FIM Validação dos Dados de Login //
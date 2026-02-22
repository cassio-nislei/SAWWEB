<?php
  require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/padrao.inc.php");
  $idBC = isset($_POST["IdBC"]) ? $_POST["IdBC"] : "";

  $sql = "DELETE FROM base_conhecimento WHERE id = '".$idBC."'";
  $excluir = mysqli_query($conexao,$sql)
    or die("Erro BC: " . mysqli_error($conexao));

  // Limpo os arquivos da Sessão //
  emptySessionFilesBC();
   
  if( $excluir ){
    // Apago os Anexos //
    $excluir = mysqli_query(
        $conexao,
        "DELETE FROM base_conhecimento_anexos WHERE id_base_conhecimento = '".$idBC."'"
    ) or die("Erro BCA: " . mysqli_error($conexao));

    echo "1";
  }
  else{ echo "9"; }
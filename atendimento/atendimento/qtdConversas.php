<?php
	// Requires //
    require_once("../includes/padrao.inc.php");

	if( isset($_POST["id"]) ){
		$idAtendimento = $_POST["id"];
		$numero = $_POST["numero"];
		$idCanal = !empty($_POST["id_canal"]) ? $_POST["id_canal"] : 0;

		// Inicialização da Consulta //
		$strSQL = "SELECT coalesce(count(ID),0) AS total_notificacoes FROM tbmsgatendimento WHERE numero = '".$numero."'";

		// Verifica se Adiciona ou não o Filtro por Canal //
		if( intval($idAtendimento) > 0 ){ $strSQL .= " AND id = '".$idAtendimento."'"; }

		// Verifica se Adiciona ou não o Filtro por Canal //
		if( intval($idCanal) > 0 ){ $strSQL .= " AND canal = '".$idCanal."'"; }

		// Recupera a Qtde de Mensagens //
		$qryConversa = mysqli_query(
			$conexao
			, $strSQL
		) or die(mysqli_error($conexao));

		$qtdNotificacoes = mysqli_fetch_array($qryConversa);

		if( mysqli_num_rows($qryConversa) > 0 ){
			echo $qtdNotificacoes["total_notificacoes"];  
		}
		else{ echo 0; }
	}
	else{ sleep(2); }
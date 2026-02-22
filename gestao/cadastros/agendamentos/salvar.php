<?php
require_once("../../../includes/padrao.inc.php");
//print_r($_FILES);


$canal          = (int)$_POST['menu_canal'];
$titulo         = $_POST['titulo'];
$msg            = $_POST['msgs'];
$qtdemsgs       = (int)$_POST["qtdemsg"];
// Obtém a data do seletor HTML

$data_seletor = $_POST['seletorData'];
// Converte a data para o formato desejado (YYYY-MM-DD)
$data_agendada = date('Y-m-d', strtotime($data_seletor));

// Obtém a hora do seletor HTML
$hora_seletor = $_POST['seletorHora'];
$hora_agendada = date('H:i:s', strtotime($hora_seletor));
   
	$arqData="";
    $arquivoautomatico   = '';
	if(isset($_FILES['anexoa'])){
		$arquivoautomatico   = $_FILES['anexoa']['name'];
		$file_tmp            = $_FILES['anexoa']['tmp_name'];
        $ext = pathinfo($arquivoautomatico, PATHINFO_EXTENSION);
		 $mimetype = mime_content_type($file_tmp);
		if ($ext=='mp3') {
			$arqData ='data:audio/ogg;base64,';
		} else if ($ext=='ogg') {
			$arqData ='data:audio/ogg;base64,';
		} else if ($ext=='pdf') {
			$arqData ='data:application/pdf;base64,';
		} else if ($ext=='mp4') {
			$arqData ='data:video/mp4;base64,';
		} else if ($ext=='avi') {
			$arqData ='data:video/avi;base64,';
		} else if ($ext=='mpeg') {
			$arqData ='data:video/mpeg;base64,';
		} else {
			$arqData = "data:". $_FILES['foto']['type'].";base64,";
		}
		$arqData = $arqData .base64_encode(file_get_contents($file_tmp));
	}


if ($canal > 0) {
    // Verifico se já existe um canal Cadastrado com esse numero
    $existe = mysqli_query($conexao, "SELECT 1 FROM tbmsgagendadasawcsv WHERE canal = '$canal'")
        or die(mysqli_error($conexao));

    if (mysqli_num_rows($existe) > 0) {
        echo "3";
        exit();
    }

    // Removi a verificação $canal > -1, pois já está dentro de $canal == 0
    // ... (código existente)

    $sql = "INSERT INTO tbmsgagendadasawcsv (data_agendada, hora_agendada, arquivocsv, anexo, msg, canal, titulo, qtde_msgs) VALUES ('$data_agendada', '$hora_agendada', '$arquivoautomatico', '$arqData', '$msg','$canal','$titulo','$qtdemsgs')";
		  $inserir = mysqli_query($conexao, $sql) or die($sql . "<br/>" . mysqli_error($conexao));
			

    if ($inserir) {
        echo "1";
       
    }
} else {
    if ($arqData != "") {
        $sql = "UPDATE tbmsgagendadasawcsv SET canal = '$canal', msg = '$msgs', data_agendada = '$data_agendada', hora_agendada = '$hora_agendada', anexo='$arqData', arquivocsv = '$arquivoautomatico', titulo = '$titulo', qtde_msgs = '$qtdemsgs' WHERE canal = '$canal' and enviado = 0 ";
    } else {
        $sql = "UPDATE tbmsgagendadasawcsv SET canal = '$canal', msg = '$msgs', data_agendada = '$data_agendada', hora_agendada = '$hora_agendada', titulo = '$titulo', qtde_msgs = '$qtdemsgs' WHERE canal = '$canal'";
    }

    $atualizar = mysqli_query($conexao, $sql);

    if ($atualizar) {
        echo "2";
    }
}
?>
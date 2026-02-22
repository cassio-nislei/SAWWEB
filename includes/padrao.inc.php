<?php
// Inicializar session cache ANTES de qualquer header
if (session_status() === PHP_SESSION_NONE) {
    session_cache_expire(180000); // Deixar a sessão um bom tempo - ANTES de session_start()
}

// Headers //
        if( intval(strpos($_SERVER["REQUEST_URI"], "controller.php")) > 0 ){
            header('Content-type: application/json');
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Headers: *");
        }
        else if ( !defined('AJAX_CALL') ) {
            // Apenas redireciona se NÃO é uma chamada AJAX
            // Inicializando a Sessão //
            if (session_status() === PHP_SESSION_NONE) {
                session_start(); // Inicia a sessão
            }

            // Validação de Sessão //
            if( !isset($_SESSION["usuariosaw"]) ){ header("Location:index.php"); exit; }
        }
        else {
            // É uma chamada AJAX - apenas inicia a sessão sem validar
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
        }
    // FIM Headers //

    // Requires //
    require_once("conexao.php");

    // Default Timezone //
    date_default_timezone_set('America/Sao_Paulo');

    // Declaração de Constantes - Defines //
        define("colorTarja", "#979596");    // Definindo a Cor Padrão da Tarja //
        define("fotoPerfil", "images/cliente.png");    // Foto de Perfil Default - Quando não temos a própria do Cliente //
    // FIM Declaração de Constantes - Defines //

    // Função para registro de Quebra de Linha no Conteúdo das Mensagens Enviadas //
    function quebraDeLinha($strMensagem){
        $index = 0;
        $strMsgRetorno = "";
        $strMensagem = str_replace("<br>", "<br />",$strMensagem);
        $strMensagem = str_replace("<br/>", "<br />",$strMensagem);
        $strMensagem = str_replace("<BR>", "<br />",$strMensagem);
        $strMensagem = str_replace("<BR/>", "<br />",$strMensagem);
        $strMensagem = str_replace("<BR />", "<br />",$strMensagem);
        $strMensagem = str_replace("\\r", "",$strMensagem);
        $strMensagem = str_replace("\\n", "",$strMensagem); //Alterado por Marcelo 08/02/2023
        $strMensagem = str_replace("'", "\\'",$strMensagem);
        $strMensagem = str_replace("<br />", "\\n",$strMensagem); //Alterado por Marcelo 08/02/2023
        //$arrResposta = explode("<br />", nl2br($strMensagem)); //Comentado por Marcelo 08/02/2023

        // Caso não existe nenhuma Quebra de Linha //
        if( !isset($arrResposta[1]) ){ $strMsgRetorno = "'" . $strMensagem . "')"; }
        else{
            while( $index <= count($arrResposta) ){
                if( isset($arrResposta[$index]) ){
                    //$strMsgRetorno .= "'".trim($arrResposta[$index])."',"; //Comentado por Marcelo 08/02/2023
                    $strMsgRetorno .= "'".($arrResposta[$index])."',"; //Ajust Sem o Trim por Marcelo 08/02/2023
                }

                $index++;
            }
            
            $strMsgRetorno .= ")";
            $strMsgRetorno = str_replace(",)", ")", str_replace(",''", "", $strMsgRetorno));
        }

        return $strMsgRetorno;
    }

    // Função que define se o está online //
    function userOnline($lastDate,$minutosOffline){
        $blnOnline = true;

        try {
            // Validação se a data está vazia
            if(empty($lastDate)){
                return false;
            }

            // Tenta criar DateTime da data recebida
            $start_date = new DateTime($lastDate);
            $now = new DateTime();
            $since_start = $start_date->diff($now);
            
            // Calcula o total de minutos
            $minutosTotais = (intval($since_start->days)*(24*60))
                                + (intval($since_start->h)*(60))
                                + (intval($since_start->i));
            
            // Se passou mais tempo que os minutos offline, marca como offline
            if( intval($minutosTotais) >= intval($minutosOffline) ){
                $blnOnline = false;
            }
        } catch (Exception $e) {
            // Se houver erro na data, considera como offline
            error_log("Erro ao verificar data de usuário: " . $e->getMessage() . " - Data: " . $lastDate);
            $blnOnline = false;
        }

        return $blnOnline;
    }

    function formataTelefone($numero){
        if (strlen($numero)==12){
          $numero = '+'.substr($numero,0,2) . ' ' . "(".substr($numero,2,2).") ".substr($numero,4,-4)." - ".substr($numero,-4);	
        }else{
            $numero = '+'.substr($numero,0,2) . ' ' . "(".substr($numero,2,2).") ".substr($numero,4,5)." - ".substr($numero,-4);
        }
         
        return($numero);
    }
    
    function SomenteNumero($str){ 
      return preg_replace("/[^0-9]/", "", $str);
    }
    
    function extrairContatoWhats($card){
        //Pego o Nome
        $separar = explode(":",$card);
        $nome = explode(';', $separar[3]);
        $nomeContato =$nome[1] . ' ' . $nome[0];
        //Pego o Número
        $inicioNumero = strpos($card,"waid="); 
        $telefone = SomenteNumero(substr($card, $inicioNumero+5, 13));
        $telefoneContato = formataTelefone($telefone);
        //Monto o retorno
        $retorno = $nomeContato . '<br>' . $telefoneContato;
        return($retorno);
    }

    function reverseDate($data, $padrao = "/"){
        $arrReversao = array(
            "/" => "-",
            "-" => "/"
        );

        return implode($arrReversao[$padrao],array_reverse(explode($padrao,$data)));
    }

    // Adicionar máscara no número para exibir 
    function Mask($str){
        if( strlen($str) == 10 ){
            $mask = "+# (###) ###-####";
        }
        // DDI com 2 Dígito e Número com 8 Dígitos (Brasil)
        else if( strlen($str) == 12 ){
            $mask = "+## (##) ####-####";
        }
        // DDI com 2 Dígito e Número com 9 Dígitos (Brasil)
        else if( strlen($str) == 13 ){
            $mask = "+## (##) # ####-####";
        }
        else{ $mask = "+## ### ### ###"; }

        $str = str_replace(" ","", $str);

        for( $i = 0; $i < strlen($str); $i++ ){
            @$mask[strpos($mask,"#")] = $str[$i];
        }

        $mask = str_replace("#", "", $mask);
        
        return $mask;
    }

    function limpaNome($nome){
		return trim(preg_replace('/[^a-zàèìòùáéíóú ]+/ui', '', $nome));
	}

    // Deletar Arquivos //
    function deletarArquivos($conexao, $numero, $idAtendimento = null){
        $strAnexos = "SELECT id, nome_arquivo, seq FROM tbanexos WHERE numero = '".$numero."'";

        // Id Atendimento //
        if( $idAtendimento !== null ){ $strAnexos .= " AND id = '".$idAtendimento."';"; }

        // Execução da Query //
		$qryAnexos = mysqli_query($conexao, $strAnexos);
		
		while( $objAnexos = mysqli_fetch_object($qryAnexos) ){
            $fileExtension = explode(".", $objAnexos->nome_arquivo);
			$fileExtension = $fileExtension[count($fileExtension)-1];
			$fileName = "../images/conversas/" . $objAnexos->id . "_" . $numero . "_" . $objAnexos->seq . "." . $fileExtension;

			// Excluindo o Arquivo //
			if( file_exists($fileName) ){ unlink($fileName); }
		}
    }

    // Retorna a Foto Perfil do Cliente //
    function getFotoPerfil($conexao, $numero){
        $qryFotoPerfil = mysqli_query($conexao, "SELECT foto FROM tbfotoperfil WHERE numero = '".$numero."'")
            or die( mysqli_error($conexao) );

        // Verifica se Existe um Registro //
        if( mysqli_num_rows($qryFotoPerfil) > 0 ){
            $arrFotoPerfil = mysqli_fetch_object($qryFotoPerfil);

            // Verifica se Existe a Foto //
            if (!empty($arrFotoPerfil->foto)){
                return 'data:image/jpeg;base64,' .$arrFotoPerfil->foto;
            }
            else{ return fotoPerfil; }
        }
        else{ return fotoPerfil; }
    }

    // Retorna o ID do Atendimento pelo Número //
    function newId($conexao, $numero){
        $strNewID = "SELECT id FROM tbatendimento WHERE situacao IN ('A','T','P') AND numero = '".$numero."'";
        $qryNewID = mysqli_query($conexao, $strNewID);
        $objNewID = mysqli_fetch_object($qryNewID);
        return $objNewID->id;
    }

    // Retorna a Foto Perfil do Cliente //
    function newSequence($conexao, $idAtendimento, $numero, $idCanal){
        $strNewSequence = "SELECT coalesce(max(seq),0)+1 newSequence FROM tbmsgatendimento WHERE id = '".$idAtendimento."' AND canal = '".$idCanal."' AND numero = '".$numero."'";
        $qryNewSequence = mysqli_query( $conexao, $strNewSequence );
        $objNewSequence = mysqli_fetch_object($qryNewSequence);

        return $objNewSequence->newSequence;
    }

    // Limpando a Sessão do Upload de Arquivos da Base de Conhecimento //
    function emptySessionFilesBC(){
        if( isset($_SESSION["anexos"]) ){
            unset($_SESSION["anexos"]);
        }
        if( isset($_SESSION["i"]) ){
            unset($_SESSION["i"]);
        }
    }

    // Retorna a IMG do Canal //
    function getCanal($conexao, $idCanal){
        $idCanal = intval($idCanal) > 0 ? $idCanal : 1;

        $qry = mysqli_query($conexao, "SELECT tipo FROM tbcanais WHERE id = '".$idCanal."'")
            or die( mysqli_error($conexao) );

        // Verifica se Existe um Registro //
        if( mysqli_num_rows($qry) > 0 ){
            $registros = mysqli_fetch_object($qry);

            // Verifica se Existe a Foto //
            return '<img src="images/canais/'.$registros->tipo.'.png" style="width: 25px; border-radius: 50%; margin-right: 5px;" />';
        }
    }


    function RetornaNomeAbreviado($nome){
        $nomes      = explode(" ", $nome);
        $letra1nome = substr($nomes[0],0,1);
        $letraultimonome = substr($nomes[count($nomes)-1],0,1);
        return $letra1nome . $letraultimonome;
      }

      function trataTempoOciosodoAtendente($tempoEmMinutos){
        if (intval($tempoEmMinutos) > 2880 ){
            $tempoEspera = intval(intval($tempoEmMinutos) / 1440);
            $msgtempoEspera = "Aguardando a Mais de $tempoEspera Dias";  
            $estiloEspera = 'color:red;';        
        }else if((intval($tempoEmMinutos) > 1440 )){
            $tempoEspera = intval($tempoEmMinutos) / 1440;
            $msgtempoEspera = "Aguardando a Mais de $tempoEspera Dia"; 
            $estiloEspera = 'color:red;';    
        }else if((intval($tempoEmMinutos) > 60 )){
            $tempoEspera = intval(intval($tempoEmMinutos) / 60);
            $msgtempoEspera = "Aguardando a Mais de $tempoEspera Horas";
            $estiloEspera = 'color:red;';
        }else if((intval($tempoEmMinutos) > 5 )){
            $tempoEspera = intval($tempoEmMinutos);
            $msgtempoEspera = "Aguardando a Mais de $tempoEspera minutos";
            $estiloEspera = 'color:yellow;';
        }else if((intval($tempoEmMinutos) > 1 )){
            $tempoEspera = intval($tempoEmMinutos);
            $msgtempoEspera = "Aguardando a Mais de $tempoEspera minutos";
            $estiloEspera = 'color:green;';
        }else{
            $estiloEspera = 'display:none;'; //Não exibe o Relógio
            $msgtempoEspera = '';
        }
        $array = [$msgtempoEspera, $estiloEspera];
        return $array;
    }  


    function ValidarImagemBase64($ImgRaw){
    $ImgRaw         = explode("base64,", $ImgRaw);
    $ImgRaw         = $ImgRaw[1];
    $ImgRaw         = str_replace("base64,", "", $ImgRaw);
    $ImgDecode      = base64_decode($ImgRaw);
    $ObjImg         = imagecreatefromstring($ImgDecode);
    if(!$ObjImg)  {
        return false;
    }
    $ObjData        = getimagesizefromstring($ImgDecode);    
    if( !$ObjData || $ObjData[0] == 0 || $ObjData[1] == 0 || !$ObjData["mime"])  {
        return false;
    }
    return true;
}

    

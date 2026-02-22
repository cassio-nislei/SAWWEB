<?php
	// Requires //
	require_once("../includes/padrao.inc.php");

	// Transfere o Atendimento //
	$s_celular_atendimento = $_POST["numero"]; //Pego o número do atendimento
	$s_id_atendimento      = isset($_POST["id_atendimento"]) ? trim($_POST["id_atendimento"]) : NULL;	
	$idCanal = isset($_REQUEST["id_canal"]) ? $_REQUEST["id_canal"] : "";

	//echo "ID ATENDIMENTO:".$s_id_atendimento." CELULAR: ".$s_celular_atendimento." CANAL".$idCanal;
	//exit();

   //Se for transferir para o Próprio usuario\
   if ($_POST["paramim"]=='S'){
     	// Dados do Atendente //
	  $id_departamento  = isset($_SESSION["usuariosaw"]["idDepartamento"]) ? trim($_SESSION["usuariosaw"]["idDepartamento"]) : NULL;	
	  $id_usuario       = $_SESSION["usuariosaw"]["id"];
	  $s_nome           = $_SESSION["usuariosaw"]["nome"];	  
	  $nomeDepartamento = isset($_SESSION["usuariosaw"]["nomeDepartamento"]) ? trim($_SESSION["usuariosaw"]["nomeDepartamento"]) : '';	
   }else{
	 $id_departamento = $_POST["departamento"];
	 $id_usuario      = (intval($_POST["usuario"]) > 0) ? $_POST["usuario"] : 0;
	 $s_nome = $_POST["nome"];

     // Buscado o ID e Nome do Departamento //
	if( intval($id_departamento) > 0 ){
		$sqlDepto = "SELECT dp.id AS id, dp.departamento AS departamento
						FROM tbdepartamentos dp
							LEFT JOIN tbusuariodepartamento ud ON(ud.id_departamento=dp.id)
								WHERE 1";

		// Filtro por Usuário //
		if( intval($id_usuario) > 0 ){
			$sqlDepto .= " AND ud.id_usuario = '".intval($id_usuario)."'";	
		}

		// Filtro por Departamento //
		if( intval($id_departamento) > 0 ){
			$sqlDepto .= " AND dp.id = '".intval($id_departamento)."'";
		}

		// Limit //
		$sqlDepto .= " LIMIT 1";

		$qryDepto = mysqli_query($conexao, $sqlDepto);
		$arrDepto = mysqli_fetch_assoc($qryDepto);
		$id_departamento = $arrDepto['id'];
		$nomeDepartamento = $arrDepto['departamento'];
	}
	else{
		$id_departamento = $_SESSION["usuariosaw"]["idDepartamento"];
		$nomeDepartamento = $_SESSION["usuariosaw"]["nomeDepartamento"];
	}
	// FIM Buscado o ID e Nome do Departamento //
     


   }//Fim da Verificação se está transferindo para mim mesmo

   //Dados do Atendente
	$id_atend = $_SESSION["usuariosaw"]["id"];
	$nome_atend = $_SESSION["usuariosaw"]["nome"];
   




// Recupera a Sequência da próxima Mensagem //
$newSequence = newSequence($conexao, $s_id_atendimento, $s_celular_atendimento, $idCanal);
// FIM Recupera a Sequência da próxima Mensagem //

$S_FINALIZADO = 'Transferencia';

// Parametrizo a mensagem de Final de Atendimento //
   $existe = mysqli_query($conexao,"select msg_aguardando_atendimento from tbparametros");

if( mysqli_num_rows($existe) > 0 ){
	$msg = mysqli_fetch_assoc($existe);
	$strMensagem = $msg["msg_aguardando_atendimento"];

  if( trim($strMensagem) !== "" ){
	$strMensagem = str_replace("<<setor>>", $nomeDepartamento, $strMensagem);
	$strMensagem = quebraDeLinha($strMensagem);
  }
  else{ $strMensagem  = ''; }
}
else{ $strMensagem  = ''; }

// Antes a mensagem estava fixa agora adicionei a mensagem do Parametro
if( trim($strMensagem) !== "" ){
	$sqlInsert = "INSERT INTO tbmsgatendimento(id,seq,numero,msg,nome_chat,situacao,dt_msg,hr_msg,id_atend,canal)
					VALUES('".$s_id_atendimento."','".$newSequence."','".$s_celular_atendimento."',(CONCAT_WS(REPLACE('\\\ n', ' ', ''), ".$strMensagem."),'".$s_nome."','E',CURDATE(),CURTIME(),'".$id_atend."','".$idCanal."')";
	$qryaux = mysqli_query($conexao, $sqlInsert)
		or die($sqlInsert ."<br/>".mysqli_error($conexao));
}

  // Seta o Atendimento atual como finalizado e marca Finalizado por Transferencia
	  $sqlUpdate = "UPDATE tbatendimento 
					SET situacao = 'F'
						, id_atend = '".$id_atend."'
						, nome_atend = '".$nome_atend."'
						, finalizado_por = '".$S_FINALIZADO."'
							WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'";
   $qryaux = mysqli_query($conexao, $sqlUpdate)
   or die($sqlUpdate ."<br/>".mysqli_error($conexao));

// Gera um novo atendimento pendente de Atendimento no departamento selecionado
$qryaux = mysqli_query(
	$conexao
	, "SELECT coalesce(max(id),0)+1 newId 
		FROM tbatendimento
			WHERE numero = '".$s_celular_atendimento."'"
);
$listaqryaux = mysqli_fetch_object($qryaux);
$newId = $listaqryaux->newId;

// Insere o novo atendimento como Pendente
$jatransferiu = mysqli_query(
	$conexao
	, "SELECT id FROM tbatendimento WHERE situacao = 'P' AND numero = '".$s_celular_atendimento."' AND setor = '".$id_departamento."'"
);

if( mysqli_num_rows($jatransferiu) > 0 ){
	echo "3"; 
	exit();
}

// Pendente porque só selecionou o 'Departamento' //
if( $id_usuario == 0 ){ $atendimento = 'P'; }
// Jogo o novo atendimento já iniciado para o usuário atual //
else{ $atendimento = 'A'; }

$sqlInsertTbAtendimento = "INSERT INTO tbatendimento(id, situacao,numero,dt_atend,hr_atend,id_atend,nome,setor,canal)
							VALUES('".$newId."','".$atendimento."','".$s_celular_atendimento."',CURDATE(),CURTIME(),'".$id_usuario."','".$s_nome."','".$id_departamento."','".$idCanal."')";
  $qryaux = mysqli_query($conexao, $sqlInsertTbAtendimento)
  or die($sqlInsertTbAtendimento ."<br/>".mysqli_error($conexao));

   // Se já existia uma conversa anterior iniciada na Triagem atualizo as conversas para o novo atendimento
   $sqlUpdateTbMsgAtendimento = "UPDATE tbmsgatendimento SET id = '".$newId."' WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'";
   $gravoaconversaanterior = mysqli_query($conexao, $sqlUpdateTbMsgAtendimento)  
	or die($sqlUpdateTbMsgAtendimento ."<br/>".mysqli_error($conexao));

// Se já existem Anexos muda para o Novo Atendimento //
	$mudaAnexos = mysqli_query(
		$conexao,
		"UPDATE tbanexos SET id = '".$newId."' WHERE id = '".$s_id_atendimento."' AND numero = '".$s_celular_atendimento."' AND canal = '".$idCanal."'"
	) or die(mysqli_error($conexao));
// FIM Se já existem Anexos muda para o Novo Atendimento //


//Trato o retorno, caso está transferindo para Sí mesmo exibo o novo id, senão exibo 1 caso tenha sucesso
if ($_POST["paramim"]=='S'){	
	if( $qryaux ){ echo $newId; } else{ echo "-1"; }
}else{
	if( $qryaux ){ echo "1"; }
	else{ echo "2"; }
}

	
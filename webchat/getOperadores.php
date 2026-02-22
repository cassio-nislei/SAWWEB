<?php
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
	
	// Endpoint que retorna lista de operadores em JSON
	header('Content-Type: application/json; charset=utf-8');

	// Flag para avisar padrao.inc.php que é uma chamada AJAX
	define('AJAX_CALL', true);

	// Incluir conexão com banco de dados
	require_once(__DIR__ . "/../includes/padrao.inc.php");

	try {
		// Inicializar usuário se não autenticado
		if (!isset($_SESSION["usuariosaw"]["id"])) {
			$_SESSION["usuariosaw"]["id"] = 0;
		}

		// Variáveis de filtro
		$idDepto = isset($_GET["idDepto"]) ? intval($_GET["idDepto"]) : 0;
		$search = isset($_GET["search"]) ? mysqli_real_escape_string($conexao, trim($_GET["search"])) : "";

		// Montar SQL para listar operadores
		// Simples: apenas usuários da tabela
		$sql = "SELECT 
					u.id,
					u.nome,
					u.email
				FROM tbusuario u
				WHERE u.id != '" . intval($_SESSION["usuariosaw"]["id"]) . "' AND u.id > 1";

		// Opcional: se houver tabela de departamentos do usuário, descomentar depois
		// LEFT JOIN tbdepartamentos d ON u.id_departamento = d.id // Excluir o próprio usuário

		// Filtrar por departamento se informado
		if ($idDepto > 0) {
			$sql .= " AND (u.id_departamento = '" . $idDepto . "' OR u.id_departamento IS NULL)";
		}

		// Pesquisa por nome ou email
		if (!empty($search)) {
			$sql .= " AND (u.nome LIKE '%$search%' OR u.email LIKE '%$search%')";
		}

		$sql .= " ORDER BY u.nome ASC LIMIT 100";

		$result = mysqli_query($conexao, $sql);

		if (!$result) {
			throw new Exception('Erro ao buscar operadores: ' . mysqli_error($conexao));
		}

		$operadores = [];
		while ($row = mysqli_fetch_assoc($result)) {
			$operadores[] = [
				'id' => intval($row['id']),
				'nome' => $row['nome'],
				'email' => $row['email']
			];
		}

		echo json_encode([
			'success' => true,
			'operadores' => $operadores,
			'total' => count($operadores)
		]);

	} catch (Exception $e) {
		http_response_code(500);
		echo json_encode([
			'success' => false,
			'error' => $e->getMessage()
		]);
	}
?>

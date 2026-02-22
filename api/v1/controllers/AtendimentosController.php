<?php
/**
 * AtendimentosController - Gerencia endpoints de atendimentos
 * Q3: Verificar atendimento pendente
 * P1: Finalizar atendimento
 * P2: Criar novo atendimento
 * P3: Gravar mensagem de atendimento
 * P8: Atualizar setor do atendimento
 * Q16: Buscar atendimentos inativos
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class AtendimentosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q3: Verificar atendimento pendente
     * GET /atendimentos/verificar-pendente?numero=11999999999&canal=WhatsApp
     */
    public function verificarPendente()
    {
        try {
            $numero = $_GET['numero'] ?? '';
            $canal = $_GET['canal'] ?? '';

            if (empty($numero) || empty($canal)) {
                Response::error('Número e canal são obrigatórios', 400);
                return;
            }

            $sql = "SELECT * FROM tbatendimento
                   WHERE situacao IN ('P','A','T')
                   AND login = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numero]);
            $atendimento = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success([
                'existe' => !!$atendimento,
                'atendimento' => $atendimento
            ]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P2: Criar novo atendimento
     * POST /atendimentos
     * Body: { numero, nome, id_atendente, nome_atendente, situacao, canal, setor }
     */
    public function criar()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || empty($data['numero']) || empty($data['nome'])) {
                Response::error('Número e nome são obrigatórios', 400);
                return;
            }

            $numero = $data['numero'];
            $nome = $data['nome'];
            $idAtendente = (int)($data['id_atendente'] ?? 0);
            $nomeAtendente = $data['nome_atendente'] ?? '';
            $situacao = $data['situacao'] ?? 'P';
            $canal = $data['canal'] ?? '';
            $setor = $data['setor'] ?? '';

            $sql = "CALL sprGeraNovoAtendimento(:pNumero, :pNome, :pIdAtendente, :pNomeAtendente, :pSituacao, :pCanal, :pSetor)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':pNumero', $numero, \PDO::PARAM_STR);
            $stmt->bindParam(':pNome', $nome, \PDO::PARAM_STR);
            $stmt->bindParam(':pIdAtendente', $idAtendente, \PDO::PARAM_INT);
            $stmt->bindParam(':pNomeAtendente', $nomeAtendente, \PDO::PARAM_STR);
            $stmt->bindParam(':pSituacao', $situacao, \PDO::PARAM_STR);
            $stmt->bindParam(':pCanal', $canal, \PDO::PARAM_STR);
            $stmt->bindParam(':pSetor', $setor, \PDO::PARAM_STR);
            $stmt->execute();

            // Buscar o atendimento criado
            $sqlSelect = "SELECT * FROM tbatendimento WHERE login = ? ORDER BY id DESC LIMIT 1";
            $stmtSelect = $this->db->prepare($sqlSelect);
            $stmtSelect->execute([$numero]);
            $atendimento = $stmtSelect->fetch(\PDO::FETCH_ASSOC);

            Response::success([
                'id' => $atendimento['id'] ?? null,
                'atendimento' => $atendimento
            ], 'Atendimento criado com sucesso', 201);
        } catch (\PDOException $e) {
            Response::error('Erro ao criar atendimento: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P1: Finalizar atendimento
     * PUT /atendimentos/finalizar
     * Body: { id_atendimento, numero, canal }
     */
    public function finalizar()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !$data['id_atendimento']) {
                Response::error('ID atendimento obrigatório', 400);
                return;
            }

            $sql = "UPDATE tbatendimento
                   SET situacao = 'F', data_finalizacao = NOW()
                   WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['id_atendimento']]);

            Response::success(null, 'Atendimento finalizado');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P3: Gravar mensagem de atendimento
     * POST /atendimentos/gravar-mensagem
     * Form-data: id_atendimento, mensagem, tipo_arquivo, arquivo
     */
    public function gravarMensagem()
    {
        try {
            $id_atendimento = (int)($_POST['id_atendimento'] ?? 0);
            $mensagem = $_POST['mensagem'] ?? '';
            $tipo_arquivo = $_POST['tipo_arquivo'] ?? '';

            if (!$id_atendimento || empty($mensagem)) {
                Response::error('Parâmetros obrigatórios ausentes', 400);
                return;
            }

            $arquivo_blob = null;
            $nome_arquivo = '';

            if (isset($_FILES['arquivo'])) {
                $arquivo = $_FILES['arquivo'];
                if ($arquivo['error'] === UPLOAD_ERR_OK) {
                    $arquivo_blob = file_get_contents($arquivo['tmp_name']);
                    $nome_arquivo = $arquivo['name'];
                }
            }

            $sql = "INSERT INTO tbmsgatendimento (id, mensagem, arquivo_blob, arquivo_nome, data_msg)
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $id_atendimento,
                $mensagem,
                $arquivo_blob,
                $nome_arquivo
            ]);

            Response::success(null, 'Mensagem registrada', 201);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P8: Atualizar setor do atendimento
     * PUT /atendimentos/atualizar-setor
     * Body: { id_atendimento, setor }
     */
    public function atualizarSetor()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            $sql = "UPDATE tbatendimento SET situacao = 'P' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$data['id_atendimento'] ?? 0]);

            Response::success(null, 'Setor atualizado');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q16: Buscar atendimentos inativos
     * GET /atendimentos/inativos?tempo_minutos=5
     */
    public function inativos()
    {
        try {
            $tempo = (int)($_GET['tempo_minutos'] ?? 5);

            $sql = "SELECT a.id, a.login, a.nome, a.situacao
                   FROM tbatendimento a
                   WHERE a.situacao IN ('A','T')
                   AND TIMESTAMPDIFF(MINUTE, a.data_criacao, NOW()) >= ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$tempo]);
            $inativos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            Response::success(['inativos' => $inativos]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>

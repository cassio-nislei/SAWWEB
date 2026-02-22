<?php
/**
 * AvisosController - Gerencia endpoints de avisos
 * P7: Registrar aviso sem expediente
 * P11: Limpar avisos antigos
 * P14: Limpar avisos de número
 * P15: Verificar aviso existente
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class AvisosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * P7: Registrar aviso sem expediente
     * POST /avisos/registrar-sem-expediente
     * Body: { numero, mensagem }
     */
    public function registrar()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || empty($data['numero'])) {
                Response::error('Número obrigatório', 400);
                return;
            }

            $sql = "INSERT INTO tbavisos (numero, mensagem, data_criacao)
                    VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['numero'],
                $data['mensagem'] ?? 'Sem expediente'
            ]);

            Response::success(['id' => $this->db->lastInsertId()], 'Aviso registrado', 201);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P11: Limpar avisos antigos
     * DELETE /avisos/limpar-antigos
     */
    public function limparAntigos()
    {
        try {
            $sql = "DELETE FROM tbavisos WHERE DATE(data_criacao) < CURDATE()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            Response::success([
                'rows_deleted' => $stmt->rowCount()
            ], 'Avisos antigos removidos');
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P14: Limpar avisos de número específico
     * DELETE /avisos/limpar-numero?numero=11999999999
     */
    public function limparNumero()
    {
        try {
            $numero = $_GET['numero'] ?? '';

            if (empty($numero)) {
                Response::error('Número obrigatório', 400);
                return;
            }

            $sql = "DELETE FROM tbavisos WHERE numero = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numero]);

            Response::success(['rows_deleted' => $stmt->rowCount()]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * P15: Verificar aviso existente
     * GET /avisos/verificar-existente?numero=11999999999
     */
    public function verificarExistente()
    {
        try {
            $numero = $_GET['numero'] ?? '';

            if (empty($numero)) {
                Response::error('Número obrigatório', 400);
                return;
            }

            $sql = "SELECT id FROM tbavisos WHERE numero = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numero]);
            $existe = $stmt->fetch(\PDO::FETCH_ASSOC);

            Response::success([
                'existe' => !!$existe,
                'resultado' => $existe ? '1' : '0'
            ]);
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q18: Buscar avisos por número (com limpeza de antigos)
     * GET /avisos/buscar-por-numero?numero=11999999999
     * 
     * Remove avisos antigos da data atual e retorna avisos existentes do número
     */
    public function buscarPorNumero()
    {
        try {
            $numero = $_GET['numero'] ?? '';

            if (empty($numero)) {
                Response::error('Número obrigatório', 400);
                return;
            }

            // Limpar avisos antigos de hoje
            $sqlLimpar = "DELETE FROM tbavisosemexpediente 
                         WHERE dt_aviso < DATE(NOW())";
            $stmtLimpar = $this->db->prepare($sqlLimpar);
            $stmtLimpar->execute();
            $linhasRemovidas = $stmtLimpar->rowCount();

            // Limpar avisos do número específico com data anterior a hoje
            $sqlLimparNumero = "DELETE FROM tbavisosemexpediente 
                               WHERE numero = ? AND dt_aviso < DATE(NOW())";
            $stmtLimparNumero = $this->db->prepare($sqlLimparNumero);
            $stmtLimparNumero->execute([$numero]);
            $linhasRemovidasNumero = $stmtLimparNumero->rowCount();

            // Buscar avisos existentes do número
            $sqlBuscar = "SELECT * FROM tbavisosemexpediente 
                         WHERE numero = ? 
                         ORDER BY dt_aviso DESC";
            $stmtBuscar = $this->db->prepare($sqlBuscar);
            $stmtBuscar->execute([$numero]);
            $avisos = $stmtBuscar->fetchAll(\PDO::FETCH_ASSOC);

            Response::success([
                'numero' => $numero,
                'avisos' => $avisos,
                'total_avisos' => count($avisos),
                'linhas_limpas_gerais' => $linhasRemovidas,
                'linhas_limpas_numero' => $linhasRemovidasNumero
            ], 'Avisos retornados com sucesso');
        } catch (\PDOException $e) {
            Response::error('Erro ao buscar avisos: ' . $e->getMessage(), 500);
        }
    }
}

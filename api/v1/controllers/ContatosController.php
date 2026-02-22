<?php
/**
 * ContatosController - Gerencia endpoints de contatos
 * Q1: Exportar contatos com paginação
 * Q7: Buscar nome do contato
 */

namespace App\Controllers;

use App\Database;
use App\Response;

class ContatosController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    /**
     * Q1: Exportar contatos com paginação
     * GET /contatos/exportar?inicio=0&fim=100
     */
    public function exportar()
    {
        try {
            $inicio = (int)($_GET['inicio'] ?? 0);
            $fim = (int)($_GET['fim'] ?? 100);

            if ($inicio < 0 || $fim <= 0) {
                Response::error('Parâmetros início/fim inválidos', 400);
                return;
            }

            // Buscar contatos com paginação
            $sql = "SELECT id, numero, nome, aceite, data_criacao
                    FROM tbusuario
                    LIMIT :inicio, :fim";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':inicio', $inicio, \PDO::PARAM_INT);
            $stmt->bindParam(':fim', $fim, \PDO::PARAM_INT);
            $stmt->execute();
            $contatos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Total
            $sqlTotal = "SELECT COUNT(*) as total FROM tbusuario";
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(\PDO::FETCH_ASSOC)['total'];

            Response::success([
                'contatos' => $contatos,
                'paginacao' => [
                    'total' => $total,
                    'inicio' => $inicio,
                    'fim' => $fim,
                    'por_pagina' => $fim - $inicio
                ]
            ], 'Contatos exportados');
        } catch (\PDOException $e) {
            Response::error('Erro ao exportar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Q7: Buscar nome do contato
     * GET /contatos/buscar-nome?numero=11999999999
     */
    public function buscarNome()
    {
        try {
            $numero = $_GET['numero'] ?? '';

            if (empty($numero)) {
                Response::error('Número é obrigatório', 400);
                return;
            }

            $sql = "SELECT nome FROM tbusuario WHERE login = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numero]);
            $contato = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($contato) {
                Response::success(['nome' => $contato['nome']], 'Contato encontrado');
            } else {
                Response::success(['nome' => null], 'Contato não encontrado', 404);
            }
        } catch (\PDOException $e) {
            Response::error('Erro: ' . $e->getMessage(), 500);
        }
    }
}
?>

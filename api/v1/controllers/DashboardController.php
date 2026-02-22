<?php
/**
 * SAW API v1 - Controller Dashboard
 * 
 * Endpoints:
 * - GET /dashboard/ano-atual
 * - GET /dashboard/atendimentos-mensais
 */

class DashboardController {
    
    /**
     * GET /api/v1/dashboard/ano-atual
     * Retorna estatísticas do ano atual
     */
    public static function yearStats() {
        try {
            $db = Database::getInstance();
            $ano = date('Y');
            
            // Contar atendimentos por situação
            $stmt = $db->prepare("
                SELECT 
                    situacao,
                    COUNT(*) as total
                FROM tbatendimento
                WHERE YEAR(dt_atend) = ?
                GROUP BY situacao
            ");
            $stmt->execute([$ano]);
            $byStatus = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Totalizadores por status
            $triagem = 0;
            $pendentes = 0;
            $atendendo = 0;
            $finalizados = 0;
            $total = 0;
            
            foreach ($byStatus as $row) {
                $status = strtoupper($row['situacao']);
                $count = (int)$row['total'];
                
                if ($status === 'T') $triagem = $count;
                elseif ($status === 'P') $pendentes = $count;
                elseif ($status === 'A') $atendendo = $count;
                elseif ($status === 'F') $finalizados = $count;
                
                $total += $count;
            }
            
            // Calcular taxa de finalização
            $taxaFinalizacao = $total > 0 ? round(($finalizados / $total) * 100, 1) : 0;
            
            // Tempo médio de atendimento
            $stmt = $db->prepare("
                SELECT AVG(TIMESTAMPDIFF(MINUTE, dt_atend, dt_finalizacao)) as tempo_medio
                FROM tbatendimento
                WHERE YEAR(dt_atend) = ? AND situacao = 'F' AND dt_finalizacao IS NOT NULL
            ");
            $stmt->execute([$ano]);
            $tempoResult = $stmt->fetch(PDO::FETCH_ASSOC);
            $tempoMedio = $tempoResult['tempo_medio'] ? round($tempoResult['tempo_medio'], 0) : 0;
            
            // Canais mais usados
            $stmt = $db->prepare("
                SELECT canal, COUNT(*) as total
                FROM tbatendimento
                WHERE YEAR(dt_atend) = ?
                GROUP BY canal
                ORDER BY total DESC
                LIMIT 5
            ");
            $stmt->execute([$ano]);
            $canais = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $response = [
                'ano' => $ano,
                'triagem' => $triagem,
                'pendentes' => $pendentes,
                'atendendo' => $atendendo,
                'finalizados' => $finalizados,
                'total' => $total,
                'taxa_finalizacao' => $taxaFinalizacao,
                'tempo_medio_minutos' => $tempoMedio,
                'canais' => $canais,
                'atualizado_em' => date('c')
            ];
            
            Response::success($response, 'Estatísticas do ano atual');
            
        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }
    
    /**
     * GET /api/v1/dashboard/atendimentos-mensais
     * Retorna atendimentos agrupados por mês
     */
    public static function monthlyStats() {
        try {
            $query = Router::getQueryParams();
            $ano = isset($query['ano']) ? (int)$query['ano'] : date('Y');
            
            $db = Database::getInstance();
            
            // Atendimentos por mês
            $stmt = $db->prepare("
                SELECT 
                    MONTH(dt_atend) as mes,
                    MONTHNAME(dt_atend) as mes_nome,
                    situacao,
                    COUNT(*) as total
                FROM tbatendimento
                WHERE YEAR(dt_atend) = ?
                GROUP BY MONTH(dt_atend), situacao
                ORDER BY mes ASC
            ");
            $stmt->execute([$ano]);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Organizar dados por mês
            $meses = [];
            for ($m = 1; $m <= 12; $m++) {
                $meses[$m] = [
                    'mes' => $m,
                    'mes_nome' => self::getNomeMes($m),
                    'triagem' => 0,
                    'pendentes' => 0,
                    'atendendo' => 0,
                    'finalizados' => 0,
                    'total' => 0
                ];
            }
            
            foreach ($dados as $row) {
                $mes = (int)$row['mes'];
                $status = strtoupper($row['situacao']);
                $total = (int)$row['total'];
                
                if ($status === 'T') $meses[$mes]['triagem'] = $total;
                elseif ($status === 'P') $meses[$mes]['pendentes'] = $total;
                elseif ($status === 'A') $meses[$mes]['atendendo'] = $total;
                elseif ($status === 'F') $meses[$mes]['finalizados'] = $total;
                
                $meses[$mes]['total'] += $total;
            }
            
            $response = [
                'ano' => $ano,
                'data' => array_values($meses)
            ];
            
            Response::success($response, 'Estatísticas mensais');
            
        } catch (Exception $e) {
            Response::internalError($e->getMessage());
        }
    }
    
    /**
     * Retorna nome do mês em português
     */
    private static function getNomeMes($mes) {
        $meses = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];
        return $meses[$mes] ?? '';
    }
}
?>

<?php
/**
 * SAW API v1 - Model Atendimento
 */

class Atendimento
{
    /**
     * Cria novo atendimento
     */
    public static function create($numero, $nome, $idAtende, $nomeAtende, $situacao = 'P', $canal = 1, $setor = 1)
    {
        try {
            // Gera novo ID - busca o máximo ID globalmente (não por número)
            $result = Database::query(
                "SELECT COALESCE(MAX(id), 0) + 1 as newId FROM tbatendimento",
                []
            );

            $newId = isset($result[0]['newId']) ? (int)$result[0]['newId'] : 1;
            $protocolo = (int)date('YmdHis');
            $dtAtend = date('Y-m-d');
            $hrAtend = date('H:i:s');

            // Usa query SEM prepared statement para evitar problemas de tipo
            $conn = Database::connect();
            if (!$conn) {
                return false;
            }

            // Escapar strings
            $nome = mysqli_real_escape_string($conn, $nome);
            $nomeAtende = mysqli_real_escape_string($conn, $nomeAtende);
            $numero = mysqli_real_escape_string($conn, $numero);

            $sql = "INSERT INTO tbatendimento (id, situacao, nome, id_atend, nome_atend, numero, setor, dt_atend, hr_atend, canal, protocolo) 
                    VALUES ($newId, '$situacao', '$nome', $idAtende, '$nomeAtende', '$numero', '$setor', '$dtAtend', '$hrAtend', '$canal', $protocolo)";

            if (!mysqli_query($conn, $sql)) {
                error_log("SQL Error: " . mysqli_error($conn));
                return false;
            }

            return self::getById($newId, $numero);
        } catch (Exception $e) {
            error_log("Create Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtém atendimento por ID
     */
    public static function getById($id, $numero)
    {
        $conn = Database::connect();
        if (!$conn) {
            return null;
        }

        $numero_esc = mysqli_real_escape_string($conn, $numero);
        $sql = "SELECT * FROM tbatendimento WHERE id = " . (int)$id . " AND numero = '" . $numero_esc . "'";
        
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            error_log("Query Error in getById: " . mysqli_error($conn));
            return null;
        }

        $row = mysqli_fetch_assoc($result);
        return $row ?? null;
    }

    /**
     * Lista atendimentos ativos
     */
    public static function listActive($filters = [])
    {
        $sql = "SELECT * FROM tbatendimento WHERE situacao IN ('P', 'A', 'T')";
        $params = [];

        if (!empty($filters['canal'])) {
            $sql .= " AND canal = ?";
            $params[] = $filters['canal'];
        }

        if (!empty($filters['numero'])) {
            $sql .= " AND numero = ?";
            $params[] = $filters['numero'];
        }

        if (!empty($filters['setor'])) {
            $sql .= " AND setor = ?";
            $params[] = $filters['setor'];
        }

        $sql .= " ORDER BY dt_atend DESC, hr_atend DESC";

        return Database::query($sql, $params);
    }

    /**
     * Lista todos os atendimentos com paginação
     */
    public static function list($page = 1, $perPage = 20, $filters = [])
    {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT * FROM tbatendimento WHERE 1=1";
        $params = [];

        if (!empty($filters['situacao'])) {
            $sql .= " AND situacao = ?";
            $params[] = $filters['situacao'];
        }

        if (!empty($filters['canal'])) {
            $sql .= " AND canal = ?";
            $params[] = $filters['canal'];
        }

        if (!empty($filters['numero'])) {
            $sql .= " AND numero = ?";
            $params[] = $filters['numero'];
        }

        if (!empty($filters['setor'])) {
            $sql .= " AND setor = ?";
            $params[] = $filters['setor'];
        }

        // Contar total
        $countResult = Database::query("SELECT COUNT(*) as total FROM (" . $sql . ") as filtered", $params);
        $total = $countResult[0]['total'] ?? 0;

        // Paginar
        $sql .= " ORDER BY dt_atend DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $data = Database::query($sql, $params);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    /**
     * Atualiza situação do atendimento
     */
    public static function updateSituacao($id, $numero, $situacao)
    {
        $sql = "UPDATE tbatendimento SET situacao = ? WHERE id = ? AND numero = ?";
        return Database::execute($sql, [$situacao, $id, $numero]);
    }

    /**
     * Atualiza setor do atendimento
     */
    public static function updateSetor($id, $numero, $setor)
    {
        $sql = "UPDATE tbatendimento SET setor = ? WHERE id = ? AND numero = ?";
        return Database::execute($sql, [$setor, $id, $numero]);
    }

    /**
     * Finaliza atendimento
     */
    public static function finalize($id, $numero)
    {
        $sql = "UPDATE tbatendimento SET situacao = 'F' WHERE id = ? AND numero = ?";
        return Database::execute($sql, [$id, $numero]);
    }

    /**
     * Verifica se existe atendimento ativo
     */
    public static function checkActive($numero)
    {
        $result = Database::query(
            "SELECT id FROM tbatendimento WHERE numero = ? AND situacao NOT IN ('F') LIMIT 1",
            [$numero]
        );

        return count($result) > 0 ? $result[0] : null;
    }

    /**
     * Busca atendimento por número de telefone (ativo)
     */
    public static function getByNumber($numero)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    id,
                    numero,
                    nome,
                    situacao,
                    canal,
                    setor,
                    dt_atend,
                    hr_atend,
                    id_atend,
                    nome_atend,
                    protocolo,
                    classifica_atendimento,
                    created_at,
                    updated_at
                FROM tbatendimento
                WHERE numero = ? AND situacao IN ('T', 'P', 'A')
                ORDER BY dt_atend DESC
                LIMIT 1
            ");
            $stmt->execute([$numero]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Exception $e) {
            error_log("getByNumber Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista anexos de um atendimento
     */
    public static function getAnexos($atendimentoId)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    id,
                    seq,
                    numero,
                    nome_arquivo,
                    tipo_arquivo,
                    tamanho_bytes,
                    caminho,
                    created_at
                FROM tbanexo
                WHERE id_atend = ?
                ORDER BY seq ASC
            ");
            $stmt->execute([$atendimentoId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        } catch (Exception $e) {
            error_log("getAnexos Error: " . $e->getMessage());
            return [];
        }
    }
}
?>

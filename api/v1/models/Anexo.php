<?php
/**
 * SAW API v1 - Model Anexo
 */

class Anexo
{
    /**
     * Cria novo anexo
     */
    public static function create($id, $seq, $numero, $arquivo, $nomeArquivo, $nomeOriginal, $tipoArquivo, $canal = 1, $enviado = 1)
    {
        $sql = "INSERT INTO tbanexos (id, seq, numero, arquivo, nome_arquivo, nome_original, tipo_arquivo, canal, enviado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        return Database::execute($sql, [
            $id,
            $seq,
            $numero,
            $arquivo,
            $nomeArquivo,
            $nomeOriginal,
            $tipoArquivo,
            $canal,
            $enviado
        ]);
    }

    /**
     * Obtém anexo por ID
     */
    public static function getById($id)
    {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                SELECT 
                    id,
                    seq,
                    numero,
                    arquivo,
                    nome_arquivo,
                    nome_original,
                    tipo_arquivo,
                    tamanho_bytes,
                    caminho,
                    canal,
                    enviado,
                    created_at
                FROM tbanexo
                WHERE id = ?
                LIMIT 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        } catch (Exception $e) {
            error_log("getById Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém anexo por ID, número e sequência (método legado)
     */
    public static function getByIdNumeroSeq($id, $numero, $seq)
    {
        $result = Database::query(
            "SELECT * FROM tbanexos WHERE id = ? AND numero = ? AND seq = ?",
            [$id, $numero, $seq]
        );

        return $result[0] ?? null;
    }

    /**
     * Lista anexos de um atendimento
     */
    public static function listByAtendimento($id, $numero)
    {
        return Database::query(
            "SELECT * FROM tbanexos WHERE id = ? AND numero = ? ORDER BY seq",
            [$id, $numero]
        );
    }

    /**
     * Deleta anexo
     */
    public static function delete($id, $numero, $seq = null)
    {
        $sql = "DELETE FROM tbanexos WHERE id = ? AND numero = ?";
        $params = [$id, $numero];

        if ($seq !== null) {
            $sql .= " AND seq = ?";
            $params[] = $seq;
        }

        return Database::execute($sql, $params);
    }
}
?>

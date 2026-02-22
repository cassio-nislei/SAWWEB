<?php
/**
 * SAW API v1 - Model Parametro
 */

class Parametro
{
    /**
     * Obtém todos os parâmetros
     */
    public static function getAll()
    {
        $result = Database::query("SELECT * FROM tbparametros ORDER BY id LIMIT 1", []);
        return $result[0] ?? null;
    }

    /**
     * Atualiza parâmetro
     */
    public static function update($id, $data)
    {
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $sql = "UPDATE tbparametros SET " . implode(', ', $fields) . " WHERE id = ?";

        return Database::execute($sql, $values);
    }
}
?>

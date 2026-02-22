<?php
/**
 * SAW API v1 - Classe Database
 * Gerencia conexão e operações com MySQL
 */

class Database
{
    private static $connection = null;
    private static $lastError = null;

    /**
     * Obtém conexão com o banco de dados
     * @return mysqli
     */
    public static function connect()
    {
        if (self::$connection === null) {
            self::$connection = @mysqli_connect(
                DB_HOST,
                DB_USER,
                DB_PASS,
                DB_NAME
            );

            if (!self::$connection) {
                self::$lastError = "Conexão com banco de dados falhou: " . mysqli_connect_error();
                return false;
            }

            mysqli_set_charset(self::$connection, DB_CHARSET);
        }

        return self::$connection;
    }

    /**
     * Alias para connect() - compatibilidade com código que espera getInstance()
     * @return mysqli
     */
    public static function getInstance()
    {
        return self::connect();
    }

    /**
     * Executa uma query SELECT
     * @param string $sql
     * @param array $params
     * @return array|false
     */
    public static function query($sql, $params = [])
    {
        $connection = self::connect();

        if (!$connection) {
            return false;
        }

        // Prepare statement
        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            self::$lastError = "Erro ao preparar query: " . $connection->error;
            return false;
        }

        // Bind parameters se houver
        if (!empty($params)) {
            $types = '';
            $values = [];

            foreach ($params as $param) {
                // Detecta tipo de forma mais eficiente
                if (is_null($param)) {
                    $types .= 's'; // NULL como string
                } elseif (is_int($param)) {
                    // Verifica se o inteiro cabe em um INT (32-bit signed: -2147483648 a 2147483647)
                    if ($param >= -2147483648 && $param <= 2147483647) {
                        $types .= 'i';
                    } else {
                        // Números muito grandes precisam ser strings
                        $param = (string)$param;
                        $types .= 's';
                    }
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = &$param;
            }

            array_unshift($values, $types);
            call_user_func_array([$stmt, 'bind_param'], $values);
        }

        if (!$stmt->execute()) {
            self::$lastError = "Erro ao executar query: " . $stmt->error;
            return false;
        }

        $result = $stmt->get_result();
        $data = [];

        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $stmt->close();
        return $data;
    }

    /**
     * Executa uma query INSERT/UPDATE/DELETE
     * @param string $sql
     * @param array $params
     * @return bool|int - retorna o ID inserido em case de INSERT ou número de linhas afetadas
     */
    public static function execute($sql, $params = [])
    {
        $connection = self::connect();
        
        error_log("SQL: " . $sql);
        error_log("Params: " . print_r($params, true));

        if (!$connection) {
            return false;
        }

        $stmt = $connection->prepare($sql);

        if (!$stmt) {
            self::$lastError = "Erro ao preparar query: " . $connection->error;
            return false;
        }

        if (!empty($params)) {
            $types = '';
            $values = [];

            foreach ($params as $param) {
                // Detecta tipo de forma mais eficiente
                if (is_null($param)) {
                    $types .= 's'; // NULL como string
                } elseif (is_int($param)) {
                    // Verifica se o inteiro cabe em um INT (32-bit signed: -2147483648 a 2147483647)
                    if ($param >= -2147483648 && $param <= 2147483647) {
                        $types .= 'i';
                    } else {
                        // Números muito grandes precisam ser strings
                        $param = (string)$param;
                        $types .= 's';
                    }
                } elseif (is_float($param)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = &$param;
            }

            array_unshift($values, $types);
            call_user_func_array([$stmt, 'bind_param'], $values);
        }

        if (!$stmt->execute()) {
            self::$lastError = "Erro ao executar query: " . $stmt->error;
            return false;
        }

        // Retorna ID inserido se for INSERT
        if (strpos(strtoupper($sql), 'INSERT') === 0) {
            $id = $stmt->insert_id;
            $stmt->close();
            return $id;
        }

        // Retorna linhas afetadas
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    /**
     * Começa uma transação
     */
    public static function beginTransaction()
    {
        $connection = self::connect();
        if ($connection) {
            $connection->begin_transaction();
        }
    }

    /**
     * Confirma uma transação
     */
    public static function commit()
    {
        $connection = self::connect();
        if ($connection) {
            $connection->commit();
        }
    }

    /**
     * Desfaz uma transação
     */
    public static function rollback()
    {
        $connection = self::connect();
        if ($connection) {
            $connection->rollback();
        }
    }

    /**
     * Obtém o último erro
     */
    public static function getLastError()
    {
        return self::$lastError;
    }

    /**
     * Fecha a conexão
     */
    public static function close()
    {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
?>

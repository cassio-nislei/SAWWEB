<?php
// Simular POST /auth/login
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/v1/auth/login';
$_SERVER['HTTP_AUTHORIZATION'] = '';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_ACCEPT'] = '*/*';
$_SERVER['HTTP_USER_AGENT'] = 'Test';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Simular input PHP
$input_data = json_encode(['usuario' => 'admin', 'senha' => '123456']);

// Criar stream de entrada
$fp = fopen('php://memory', 'r+');
fwrite($fp, $input_data);
rewind($fp);

// Redirect php://input para nosso stream
stream_wrapper_unregister('php');
stream_wrapper_register('php', 'TestStreamWrapper');

class TestStreamWrapper {
    private $data;
    private $position = 0;
    private static $fp;
    
    public function stream_open($path, $mode, $options, &$opened_path) {
        global $fp, $input_data;
        if ($path === 'php://input') {
            $this->data = $input_data;
            return true;
        }
        return false;
    }
    
    public function stream_read($count) {
        $result = substr($this->data, $this->position, $count);
        $this->position += strlen($result);
        return $result;
    }
    
    public function stream_eof() {
        return $this->position >= strlen($this->data);
    }
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (strpos($errfile, 'index.php') === false) {
        echo "ERROR [$errno]: $errstr in $errfile:$errline\n";
    }
});

try {
    include 'api/v1/index.php';
} catch (Exception $e) {
    echo "CAUGHT EXCEPTION: " . $e->getMessage() . "\n";
}
?>


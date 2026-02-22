<?php
$url = 'http://localhost:8000/api/v1/atendimentos/3/mensagens';
$data = [
    'numero' => '5511999999997',
    'msg' => 'Teste',
    'situacao' => 'E',
    'canal' => 1
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

file_put_contents('api_error.log', $result);
echo "Response written to api_error.log\n";
?>

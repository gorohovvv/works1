<?php

$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

$params = $_GET;

if(isset($headers['Authorization'])) {
    $auth_header = $headers['Authorization'];
    echo "Авторизация через заголовок: $auth_header\n";
} else {
    echo "Авторизация через заголовок: Отсутствует\n";
}

if(isset($params['token'])) {
    $token_param = $params['token'];
    echo "Авторизация через параметр: $token_param\n";
} else {
    echo "Авторизация через параметр: Отсутствует\n";
}

http_response_code(200);
?>

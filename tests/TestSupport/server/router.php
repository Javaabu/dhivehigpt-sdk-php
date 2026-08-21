<?php

$sleep_seconds = isset($_GET['sleep']) ? (float) $_GET['sleep'] : 0.0;

if ($sleep_seconds > 0) {
    usleep((int) ($sleep_seconds * 1_000_000));
}

$status_code = isset($_GET['status']) ? (int) $_GET['status'] : 200;

http_response_code($status_code);

header('Content-Type: application/json');

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'path' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
    'headers' => $_SERVER,
    'body' => file_get_contents('php://input'),
]);

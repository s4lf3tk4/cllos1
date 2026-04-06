<?php
session_start();

$allowed_origins = [
    'http://localhost:5173',
    'http://localhost:8080',
    'http://' . $_SERVER['SERVER_NAME'],
    'http://' . $_SERVER['HTTP_HOST']
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://" . $_SERVER['HTTP_HOST']);
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (isset($_SESSION['isauth']) && $_SESSION['isauth'] === true && isset($_SESSION['username'])){
    $response = [
        'auth' => true,
        'username' => $_SESSION['username'],
    ];
}
else{
        $response = [
        'auth' => false,
        'username' => null,
    ];
}
echo json_encode($response);
?>
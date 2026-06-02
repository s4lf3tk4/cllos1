<?php
session_start();

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
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
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

function isExists($conn, $name){
    $sql = "SELECT name, password FROM users WHERE name = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if(!$stmt){
        return ['exists' => false, 'error' => 'Database error'];
    }
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();   
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return ['exists' => $exists, 'user' => $user];
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        require_once 'php_config.php';

        if ($conn->connect_error) {
            throw new Exception("Ошибка подключения: " . $conn->connect_error);
        }

        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($name) || empty($password)) {
            echo json_encode([
                'status' => 'fail',
                'message' => 'Имя пользователя и пароль обязательны'
            ], JSON_UNESCAPED_UNICODE);
            $conn->close();
            exit();
        }

        $exists = isExists($conn, $name);
        
        if ($exists['exists']) {
            if (password_verify($password, $exists['user']['password'])) {
                $response = [
                    'status' => 'success',
                    'message' => 'Успешный вход',
                ];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                $_SESSION['username'] = $name;
                $_SESSION['isauth'] = true;
                
            } else {
                $response = [
                    'status' => 'fail',
                    'message' => 'Пароль неверный'
                ];  
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        } else {
            $response = [
                'status' => 'fail',
                'message' => 'Такого логина не существует'
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        
        $conn->close();
        
    } catch(Exception $e) {
        $response = [
            'status' => 'fail',
            'message' => 'Ошибка входа: ' . $e->getMessage()
        ];
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
        if (isset($conn)) {
            $conn->close();
        }
    }
}
?>
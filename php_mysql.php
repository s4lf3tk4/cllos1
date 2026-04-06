<?php

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

// header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

function isUnique($conn, $name, $email){
    
    $sql = "SELECT name, email FROM users WHERE name = ? OR email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("isUnique: Ошибка prepare - " . $conn->error);
        return ['exists' => false, 'error' => 'Database error'];
    }
    $stmt->bind_param("ss", $name, $email);
    $stmt->execute();
    $result = $stmt->get_result();    
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return ['exists' => $exists];
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   try{
        require_once 'php_config.php';

        if ($conn->connect_error) {
            throw new Exception("Ошибка подключения: " . $conn->connect_error);
        }

        $name = isset($_POST['name'])?trim($_POST['name']) : '';
        $password = isset($_POST['password'])?trim($_POST['password']) : '';
        $email = isset($_POST['email'])?trim($_POST['email']) : '';

        $checkUnique = isUnique($conn, $name, $email);
        if ($checkUnique['exists']){
            $response = [
                'status' => 'error',
                'message' => 'Пользователь с таким логином или email уже существует'
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            $conn->close();
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Данные успешно сохранены',
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);

        } else {
            throw new Exception("Ошибка выполнения запроса: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
   
    
    }
    catch(Exception $e){
            $response = [
                'status' => 'fail',
                'message' => 'Данные не сохранены',
            ];
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}

?>
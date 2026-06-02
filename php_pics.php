<?php

session_start();

<<<<<<< HEAD
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true"); 
=======
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
>>>>>>> 15c193843c244d7b55af932a936bd2b5fa3b55cc
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    try{
         if (!isset($_SESSION['isauth']) || $_SESSION['isauth'] !== true) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Не авторизован'
            ]);
            exit();
        }
         if (!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] != UPLOAD_ERR_OK) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Файл не загружен или ошибка загрузки'
            ]);
            exit();
        }

        require_once 'php_config.php';

         if ($conn->connect_error) {
            throw new Exception("Ошибка подключения: " . $conn->connect_error);
        }
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

<<<<<<< HEAD
        $fileExtension = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
=======
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'];

        $fileExtension = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_EXTENSION);
        if (!in_array($fileExtension, $allowedExtensions)){
            $response = [
                'isloaded' => false,
                'message' => 'неверный формат файла',
                ];
            echo json_encode($response);
            exit();
        }
>>>>>>> 15c193843c244d7b55af932a936bd2b5fa3b55cc
        $newFileName = $_SESSION['username'] . '_' . uniqid() . '.' . $fileExtension;

        $targetFile = $targetDir.$newFileName;

        $username = $_SESSION['username'];
        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetFile)) {
                $sql = "INSERT INTO files (user, filename, filepath) VALUES (?, ?, ?)";
                    
            $stmt = $conn->prepare($sql);
            
            $stmt->bind_param("sss", $username, $newFileName, $targetFile);
            
            if ($stmt->execute()) {
                $fileId = $stmt->insert_id;
                
            $response = [
                'isloaded' => true,
                'message' => 'Успех',
                ];
                echo json_encode($response);
            }
             else {
                throw new Exception("Ошибка сохранения в БД: " . $stmt->error);
            }
            
            $stmt->close();
        
        }else {
                throw new Exception("Ошибка перемещения файла");
            }
            
            $conn->close();

    
    }catch (Exception $e) {
   $response = [
                'isloaded' => false,
                'message' => 'Успех',
                ];
                echo json_encode($response);
            }
}


?>
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

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// ЗАПУСК
// нужно войти в папку в cmd (cd) с bridge.php и запустить через php -S localhost:54321 
// чтобы проверить работу перейти по пути localhost:54321/bridge.php 

// ФАЙЛ ПИТОНА
// Перейти в директорию и выполнить python server.py
// ЕСЛИ НЕ ВЫПОЛНЯЕТСЯ:
// выполнить pip install fastapi uvicorn
// после запуска в командной строке можно отслеживать запросы http (должно быть POST ... 200)


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


//читаем данные которые пришли от vue.js и переносим в массив input
//Затем одготавливаем myMessage в котором само сообщение: если сообщения нет то будет Привет из Vue!
$input = json_decode(file_get_contents('php://input'), true);
$myMessage = $input['text'] ?? 'Привет из Vue!';

//Подготовка к отправке в питон
$pythonUrl = 'http://localhost:12345/message/';
$data = json_encode(['text' => $myMessage]);

//настройка cURL(отправка самого запроса)
//curl_init()	Создает новый сеанс cURL (инициализация)
//curl_setopt()	Устанавливает опцию для cURL сеанса
//CURLOPT_POST	Опция "использовать метод POST"
//CURLOPT_POSTFIELDS	Опция "данные для отправки"
//CURLOPT_HTTPHEADER	Опция "заголовки HTTP запроса"
//CURLOPT_RETURNTRANSFER	Опция "вернуть ответ как строку"
//curl_exec($ch); - выполнение запроса
$ch = curl_init($pythonUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo $response;
} else {
    echo json_encode([
        'status' => 'error',
        'reply' => 'Python сервер не отвечает (код: ' . $httpCode . ')'
    ]);
}
?>
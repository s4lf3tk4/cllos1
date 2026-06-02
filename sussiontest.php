<?php
// Начинаем сессию (обязательно в самом начале, до любого вывода)

session_name('SESSION111');
session_start();
$allowed_origin = 'http://localhost:5173'; // обратите внимание на протокол: http://
header("Access-Control-Allow-Origin: $allowed_origin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

echo "📊 Даqwhjhwhewgfhjgweнные сессии:<br><br>". session_id();
echo "путь сессии: " . session_save_path() . "<br>";
// Проверяем, существует ли сессия
if (isset($_SESSION['username'])) {
    echo "👤 Имя пользователя: " . $_SESSION['username'] . "<br>";
    echo "🆔 ID пользователя: " . $_SESSION['user_id'] . "<br>";
    echo "🔑 Статус: " . ($_SESSION['is_logged_in'] ? 'Авторизован' : 'Не авторизован') . "<br>";
    echo "⏰ Время входа: " . date('Y-m-d H:i:s', $_SESSION['login_time']) . "<br>";
    echo "🎫 ID сессии: " . session_id() . "<br>";
} else {
    echo "❌ Сессия не найдена или данные не установлены<br>";
}
?> 
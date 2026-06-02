<?php
// Начинаем сессию (обязательно в самом начале, до любого вывода)
//session_name('APP');
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Устанавливаем переменные сессии
$_SESSION['username'] = 'Иван Петров';
$_SESSION['user_id'] = 123;
$_SESSION['login_time'] = time();
$_SESSION['is_logged_in'] = true;

echo "✅ Сессия создана!<br>";
echo "Привет, " . $_SESSION['username'] . "<br>";
echo "путь сессии: " . session_save_path() . "<br>";
echo "🎫 ID сессии: " . session_id() . "<br>";
?> 
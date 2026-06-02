<?php
// В самом начале файла - НИКАКОГО вывода до session_start()
// Даже пробелов или пустых строк быть не должно!

session_start(); // Должна быть самой первой командой

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

    $_SESSION['username'] = 'asdasd';
    $_SESSION['isauth'] = true;

    echo json_encode($response);
?>
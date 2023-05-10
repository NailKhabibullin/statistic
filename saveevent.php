<?php

$servername = "db4free.net";
$dbname = "statistic_test";
$username = 'vk_test';
$password = 'vktest123';

// Создаем соединение с базой данных

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверяем соединение
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Проверяем, существует ли таблица events
$result = $conn->query("SHOW TABLES LIKE 'events'");
if ($result->num_rows == 0) {
  // Если таблицы events не существует, создаем ее
  $sql = "CREATE TABLE events (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(30) NOT NULL,
    user_status VARCHAR(30) NOT NULL,
    user_ip VARCHAR(30) NOT NULL,
    event_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  )";
  if ($conn->query($sql) === TRUE) {
    echo "Таблица events успешно создана!";
  } else {
    echo "Ошибка создания таблицы events: " . $conn->error;
  }
}

// Подготавливаем запрос для добавления события
$stmt = $conn->prepare("INSERT INTO events (event_name, user_status, user_ip) VALUES (?, ?, ?)");

// Привязываем параметры запроса к значениям переменных
$stmt->bind_param("sss", $event_name, $user_status, $user_ip);

// Устанавливаем значения переменных
$event_name = $_REQUEST['event_name'];
$user_status = $_REQUEST['status'];
$user_ip = rand(0, 255) . "." . rand(0, 255) . "." . rand(0, 255) . "." . rand(0, 255);

// Выполняем запрос
$stmt->execute();

// Закрываем запрос и соединение
$stmt->close();
$conn->close();
?>

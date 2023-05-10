<?php
try {
    $dsn = "mysql:host=db4free.net;dbname=statistic_test;charset=utf8mb4";
    $pdo = new PDO($dsn, 'vk_test', 'vktest123');
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

function getStats($pdo, $eventName, $dateFrom, $dateTo, $groupBy = 'event') {
    $dateFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
    $dateTo = date('Y-m-d H:i:s', strtotime($dateTo));
    $query = "SELECT ";
    if ($groupBy == 'event') {
        $query .= "event_name AS `event`, COUNT(*) AS `count` ";
    } elseif ($groupBy == 'ip') {
        $query .= "user_ip AS `ip`, COUNT(*) AS `count` ";
    } elseif ($groupBy == 'status') {
        $query .= "user_status AS `status`, COUNT(*) AS `count` ";
    }
    $query .= "FROM events WHERE event_name = :event_name AND event_date BETWEEN :date_from AND :date_to ";
    if ($groupBy == 'event') {
        $query .= "GROUP BY event_name ORDER BY `count` DESC";
    } elseif ($groupBy == 'ip') {
        $query .= "GROUP BY user_ip ORDER BY `count` DESC";
    } elseif ($groupBy == 'status') {
        $query .= "GROUP BY user_status ORDER BY `count` DESC";
    }
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'event_name' => $eventName,
        'date_from' => $dateFrom,
        'date_to' => $dateTo,
    ]);
    $result = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }
    http_response_code(200);
    echo json_encode($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['event_name']) && isset($_GET['date_from']) && isset($_GET['date_to'])) {
        $eventName = $_GET['event_name'];
        $dateFrom = $_GET['date_from'];
        $dateTo = $_GET['date_to'];
        $groupBy = isset($_GET['group_by']) ? $_GET['group_by'] : 'event';
        getStats($pdo, $eventName, $dateFrom, $dateTo, $groupBy);
    } else {
        http_response_code(400);
        echo 'Bad request';
    }
}

<?php
// Set Default Timezone
date_default_timezone_set('Asia/Manila'); 
$host = 'localhost'; 
$db   = 'design-1c-class_management';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$port = '3306';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
     PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

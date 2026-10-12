<?php
// Base commune aux trois modules.
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'stagebdmerge';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('La connexion à la base de données est indisponible.');
}
<?php
// Base de test séparée : tables conformes à DB_OK.sql, sans les triggers du groupe.
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'grille_sql_test_54_20261008'; // Pour l'intégration finale : stagebdmerge.

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('La connexion à la base de données est indisponible.');
}
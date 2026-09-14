<?php    
    // Fichier pour créer la connexions à la base de donnée

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "evaluationstages";

    try
    {
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e)
    {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
?>
<?php
    function getUtilisateurBackOffice($pdo)
    {
        $sql = "SELECT identifiant, nom, prenom, email
                FROM utilisateur
                ORDER BY nom, prenom";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
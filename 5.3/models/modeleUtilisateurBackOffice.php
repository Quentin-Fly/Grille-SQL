<?php
    // Récupère tous les utilisateurs back-office
    function getUtilisateurBackOffice($pdo)
    {
        $sql = "SELECT Identifiant, nom, prenom, mail
                FROM utilisateursbackoffice
                ORDER BY nom, prenom";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Crée un nouvel utilisateur back-office
    function creerUtilisateurBackOffice($pdo, $identifiant, $nom, $prenom, $mail, $mdp)
    {
        $sql = "INSERT INTO utilisateursbackoffice (Identifiant, nom, prenom, mail, mdp)
                VALUES (:identifiant, :nom, :prenom, :mail, :mdp)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':identifiant', $identifiant, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':mail', $mail);
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);
        $stmt->bindParam(':mdp', $mdpHash);
        return $stmt->execute();
    }
?>
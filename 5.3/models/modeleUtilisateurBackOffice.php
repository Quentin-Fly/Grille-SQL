<?php
function getUtilisateurBackOffice($pdo)
{
    $sql = "SELECT Identifiant, nom, prenom, mail
            FROM utilisateursbackoffice
            ORDER BY nom, prenom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
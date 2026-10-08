<?php
// Récupère tous les utilisateurs back-office.
function getUtilisateurBackOffice($pdo)
{
    $stmt = $pdo->prepare('SELECT Identifiant, nom, prenom, mail FROM utilisateursbackoffice ORDER BY Identifiant ASC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function creerUtilisateurBackOffice($pdo, $nom, $prenom, $mail, $mdp)
{
    $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

    // Sans AUTO_INCREMENT, le verrou évite deux créations avec le même identifiant.
    // La table fournie est MyISAM : une transaction ne suffit pas pour ce calcul.
    $pdo->exec('LOCK TABLES utilisateursbackoffice WRITE');
    try {
        $identifiant = (int) $pdo->query('SELECT COALESCE(MAX(Identifiant), 0) + 1 FROM utilisateursbackoffice')->fetchColumn();
        if ($identifiant > 32767) {
            throw new RuntimeException('La capacité des identifiants SMALLINT est atteinte.');
        }
        $stmt = $pdo->prepare('INSERT INTO utilisateursbackoffice (Identifiant, nom, prenom, mail, mdp) VALUES (:identifiant, :nom, :prenom, :mail, :mdp)');
        return $stmt->execute([
            ':identifiant' => $identifiant,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':mail' => $mail,
            ':mdp' => $mdpHash
        ]);
    } finally {
        $pdo->exec('UNLOCK TABLES');
    }
}
// Fonction qui permet de supprimer un utilisateur du back-office en fonction de son identifiant.
function supprimerUtilisateurBackOffice($pdo, $identifiant)
{
    $stmt = $pdo->prepare( 'DELETE FROM utilisateursbackoffice 
                            WHERE Identifiant = :identifiant');
    $stmt->execute([':identifiant' => $identifiant]);
    return $stmt->rowCount() > 0;
}
<?php
    // Fonction qui récupère toutes les années unniversitaire
    function getAnneesUniversitaires($pdo)
    {
        $stmt = $pdo->prepare(
            'SELECT anneeDebut, fin
            FROM anneesuniversitaires
            ORDER BY anneeDebut DESC'
        );
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
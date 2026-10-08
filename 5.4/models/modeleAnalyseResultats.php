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
    // Fonction qui récupère les évaluation de stage terminés pour l'année choisie
    function getResultatsParAnnee($pdo, $anneeDebut)
    {
        $stmt = $pdo->prepare(
            'SELECT e.IdEtudiant, e.nom, e.prenom, ev.noteStage, ev.Statut, ev.anneeDebut
            FROM etudiantsbut2ou3 e
            JOIN evalStage ev ON e.IdEtudiant = ev.IdEtudiant
            WHERE ev.anneeDebut = :anneeDebut AND ev.Statut IN ("BLOQUEE", "DIFFUSEE")
            ORDER BY e.nom, e.prenom)'
        );
        $stmt->bindParam(':anneeDebut', $anneeDebut, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
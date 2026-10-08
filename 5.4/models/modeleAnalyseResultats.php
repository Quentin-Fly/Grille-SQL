<?php
    // Récupère les années universitaires du schéma DB_OK.sql.
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

    // Récupère les évaluations de stage terminées pour l'année choisie.
    function getResultatsParAnnee($pdo, $anneeDebut)
    {
        $stmt = $pdo->prepare(
            "SELECT e.IdEtudiant, e.nom, e.prenom, ev.noteStage, ev.Statut, ev.anneeDebut
            FROM etudiantsbut2ou3 e
            JOIN evalstage ev ON e.IdEtudiant = ev.IdEtudiant
            WHERE ev.anneeDebut = :anneeDebut
            AND ev.Statut IN ('BLOQUEE', 'DIFFUSEE')
            ORDER BY e.nom, e.prenom"
        );
        $stmt->bindValue(':anneeDebut', $anneeDebut, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Récupère le fiche étudiante complète
    function getFicheEtudiant($pdo, $idEtudiant, $anneeDebut)
    {
        $stmt = $pdo->prepare(
            "SELECT e.IdEtudiant,
                    e.nom,
                    e.prenom,
                    a.but3sinon2,
                    a.anneeDebut,
                    ev.noteStage,
                    ev.noteEntreprise,
                    ev.noteTuteur,
                    ev.noteSoutenance,
                    evr.note AS noteRapport,
                    evp.note AS notePortfolio,
                    eva.note AS noteAnglais
            FROM etudiantsbut2ou3 e
            JOIN anneestage a
            ON e.IdEtudiant = a.IdEtudiant
            LEFT JOIN evalstage ev
            ON a.IdEtudiant = ev.IdEtudiant
            AND a.anneeDebut = ev.anneeDebut
            LEFT JOIN evalrapport evr
            ON a.IdEtudiant = evr.IdEtudiant
            AND a.anneeDebut = evr.anneeDebut
            LEFT JOIN evalportfolio evp
            ON a.IdEtudiant = evp.IdEtudiant
            AND a.anneeDebut = evp.anneeDebut
            LEFT JOIN evalanglais eva
            ON a.IdEtudiant = eva.IdEtudiant
            AND a.anneeDebut = eva.anneeDebut
            WHERE e.IdEtudiant = :idEtudiant
            AND a.anneeDebut = :anneeDebut"
        );

        $stmt->bindValue(':idEtudiant', $idEtudiant, PDO::PARAM_INT);
        $stmt->bindValue(':anneeDebut', $anneeDebut, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>
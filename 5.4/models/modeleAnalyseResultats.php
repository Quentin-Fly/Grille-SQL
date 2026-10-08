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
    // Calcule la moyenne des note de stage de la promotion par an
    function getMoyenneStageParAnnee($pdo, $anneeDebut)
    {
        $stmt = $pdo->prepare(
            "SELECT AVG(ev.noteStage) AS moyenneStage, COUNT(ev.noteStage) AS nombreNotes
            FROM evalstage ev
            WHERE ev.anneeDebut = :anneeDebut
            AND ev.Statut IN ('BLOQUEE', 'DIFFUSEE')"
        );
        $stmt->bindValue(':anneeDebut', $anneeDebut, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Calcule de la moyenne selon le type d'évaluation
    function getMoyennesParType($pdo, $anneeDebut, $typeSelectionne = 'TOUS')
    {
        $types = [
            'STAGE'      => ['evalstage', 'noteStage', 'Stage'],
            'ENTREPRISE' => ['evalstage', 'noteEntreprise', 'Entreprise'],
            'TUTEUR'     => ['evalstage', 'noteTuteur', 'Tuteur'],
            'SOUTENANCE' => ['evalstage', 'noteSoutenance', 'Soutenance'],
            'RAPPORT'    => ['evalrapport', 'note', 'Rapport'],
            'PORTFOLIO'  => ['evalportfolio', 'note', 'Portfolio'],
            'ANGLAIS'    => ['evalanglais', 'note', 'Anglais']
        ];

        if ($typeSelectionne !== 'TOUS' && !isset($types[$typeSelectionne])) 
        {
            throw new InvalidArgumentException("Type d'évaluation invalide.");
        }

        $typesDemandes = $typeSelectionne === 'TOUS'
            ? $types
            : [$typeSelectionne => $types[$typeSelectionne]];

        $requetes = [];
        $parametres = [];

        foreach ($typesDemandes as $type => [$table, $colonne, $libelle]) 
        {
            $parametreAnnee = ':annee_' . $type;
            $parametreLibelle = ':libelle_' . $type;

            $requetes[] = "
                SELECT $parametreLibelle AS typeEvaluation,
                    AVG($colonne) AS moyenne,
                    COUNT($colonne) AS nombreNotes
                FROM $table
                WHERE anneeDebut = $parametreAnnee
                AND Statut IN ('BLOQUEE', 'DIFFUSEE')
            ";

            $parametres[$parametreAnnee] = $anneeDebut;
            $parametres[$parametreLibelle] = $libelle;
        }

        $stmt = $pdo->prepare(implode(' UNION ALL ', $requetes));

        foreach ($parametres as $parametre => $valeur)
        {
            $stmt->bindValue(
                $parametre,
                $valeur,
                str_starts_with($parametre, ':annee_')
                    ? PDO::PARAM_INT
                    : PDO::PARAM_STR
            );
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function getMoyennesParEnseignant($pdo, $anneeDebut)
    {
        $stmt = $pdo->prepare(
            "SELECT e.IdEnseignant,
                    e.nom,
                    e.prenom,
                    AVG(ev.noteStage) AS moyenne,
                    COUNT(ev.noteStage) AS nombreNotes
            FROM enseignants e
            JOIN evalstage ev
            ON e.IdEnseignant = ev.IdEnseignant
            WHERE ev.anneeDebut = :anneeDebut
            AND ev.Statut IN ('BLOQUEE', 'DIFFUSEE')
            GROUP BY e.IdEnseignant, e.nom, e.prenom
            ORDER BY e.nom, e.prenom"
        );

        $stmt->bindValue(':anneeDebut', $anneeDebut, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
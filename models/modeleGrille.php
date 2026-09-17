<?php

    function getNaturesGrilleValides()
    {
        return ['ANGLAIS', 'RAPPORT', 'SOUTENANCE', 'STAGE', 'PORTFOLIO'];
    }

    function getAllModele($pdo)
    {
        // Récupérer tous les modèles de la base de données
        $sql = "SELECT * FROM modelesgrilleeval";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();


        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function addModele($pdo, $natureGrille, $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut)
    {
        // Récupération de la dernier ID
        $sql = "SELECT IdModeleEval FROM modelesgrilleeval ORDER BY IdModeleEval DESC LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $dernierID = $stmt->fetchColumn();

        $ID = $dernierID + 1;

        // Verifie si la date exite déjà dans la base de donnée
        $sql = "SELECT anneeDebut FROM anneesuniversitaires WHERE anneeDebut = :anneeDebut";
        $anneeExiste = $stmt->fetchColumn();

        if (!$anneeExiste) 
        {
            // Si l'année n'existe pas, l'ajouter à la table anneesuniversitaires
            $sqlInsert = "INSERT INTO anneesuniversitaires (anneeDebut, fin) VALUES (:anneeDebut, :anneeFin)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([':anneeDebut' => $anneeDebut, ':fin' => $anneeDebut + 1]);
        }

        // Ajouter un nouveau modèle à la base de données
        $sql = "INSERT INTO modelesgrilleeval (IdModeleEval, natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut) 
                VALUES (:ID, :natureGrille, :noteMaxGrille, :nomModuleGrilleEvaluation, :anneeDebut)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ID', $ID);
        $stmt->bindParam(':natureGrille', $natureGrille);
        $stmt->bindParam(':noteMaxGrille', $noteMaxGrille);
        $stmt->bindParam(':nomModuleGrilleEvaluation', $nomModuleGrilleEvaluation);
        $stmt->bindParam(':anneeDebut', $anneeDebut);

        return $stmt->execute();
    }
?>

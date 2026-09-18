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

    function addModele($pdo, $natureGrille, $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut, $pointDepart)
    {
        $pdo->beginTransaction();
        try
        {
            $sql = "SELECT anneeDebut FROM anneesuniversitaires WHERE anneeDebut = :anneeDebut";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':anneeDebut' => $anneeDebut]);
            $anneeExiste = $stmt->fetchColumn();

            if ($anneeExiste === false)
            {
                $sqlInsert = "INSERT INTO anneesuniversitaires (anneeDebut, fin) VALUES (:anneeDebut, :anneeFin)";
                $stmtInsert = $pdo->prepare($sqlInsert);
                $stmtInsert->execute([':anneeDebut' => $anneeDebut, ':anneeFin' => $anneeDebut + 1]);
            }

            // Ajouter un nouveau modèle à la base de données
            $sql = "INSERT INTO modelesgrilleeval (natureGrille, noteMaxGrille, nomModuleGrilleEvaluation, anneeDebut) 
                    VALUES (:natureGrille, :noteMaxGrille, :nomModuleGrilleEvaluation, :anneeDebut)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':natureGrille', $natureGrille);
            $stmt->bindParam(':noteMaxGrille', $noteMaxGrille);
            $stmt->bindParam(':nomModuleGrilleEvaluation', $nomModuleGrilleEvaluation);
            $stmt->bindParam(':anneeDebut', $anneeDebut);

            $stmt->execute();
            $idNouveauModele = $pdo->lastInsertId();
            
            if ($pointDepart === 'vierge')
            {
                // Ajouter une section par défaut pour le nouveau modèle dans la table SectionCritereEval
                $sqlSection = "INSERT INTO SectionCritereEval (titre) VALUES (:titre)";
                $stmtSection = $pdo->prepare($sqlSection);
                $stmtSection->execute([':titre' => 'Section 1']);

                $idNouvelleSection = $pdo->lastInsertId();
                // Associe IdSection à IdModeleEval dans la table SectionsEval
                $sqlAssociation = "INSERT INTO SectionsEval (IdModeleEval, IdSection) VALUES (:idModeleEval, :idSection)";
                $stmtAssociation = $pdo->prepare($sqlAssociation);
                $stmtAssociation->execute([':idModeleEval' => $idNouveauModele, ':idSection' => $idNouvelleSection]);
            }   
            $pdo->commit();
            return $idNouveauModele;
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
    function getModeleParId($pdo, $idModeleEval)
    {
        $sql = "SELECT * FROM modelesgrilleeval WHERE IdModeleEval = :ID";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ID' => $idModeleEval]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
?>

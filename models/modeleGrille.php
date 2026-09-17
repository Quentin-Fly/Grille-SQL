    <?php

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
            $pdo->commit();
            return true;
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
?>

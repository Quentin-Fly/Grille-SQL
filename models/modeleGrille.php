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
    function nomModeleExiste($pdo, $nomModule)
    // Vérifie si le nom du module existe déjà dans la base de données
    {
        $sql = "SELECT COUNT(*) FROM modelesgrilleeval WHERE nomModuleGrilleEvaluation = :nomModule";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nomModule' => $nomModule]);

        return $stmt->fetchColumn() > 0;
    }
    function natureModeleExistePourAnnee($pdo, $natureGrille, $anneeDebut)
    // Vérifie si la nature existe déjà dans la base de données pour l'année donnée
    {
        $sql = "SELECT COUNT(*) FROM modelesgrilleeval WHERE natureGrille = :natureGrille AND anneeDebut = :anneeDebut";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':natureGrille' => $natureGrille, ':anneeDebut' => $anneeDebut]);

        return $stmt->fetchColumn() > 0;
    }
    function getCriteresParIdModele($pdo, $idModeleEval)
    // Récupère les critères associés à un modèle spécifique
    {
        $sql = "
            SELECT
                critereseval.IdCritere,
                critereseval.descCourte,
                critereseval.descLongue,
                modelecontenircriteres.ValeurMaxCritereEVal,
                modelecontenircriteres.NumOrdre
            FROM modelecontenircriteres
            JOIN critereseval
                ON modelecontenircriteres.IdCritere = critereseval.IdCritere
            WHERE modelecontenircriteres.IdModeleEval = :ID
            ORDER BY modelecontenircriteres.NumOrdre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ID' => $idModeleEval]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    function addCritere($pdo, $idModeleEval, $descCourteCritere, $descLongueCritere, $valeurMaxCritere)
    {
        $pdo->beginTransaction();
        try
        {
           // Creation du critère
            $sql = "INSERT INTO CriteresEval (descCourte, descLongue) 
                    VALUES (:descCourte, :descLongue)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':descCourte', $descCourteCritere);
            $stmt->bindParam(':descLongue', $descLongueCritere);

            $stmt->execute();

            // Identifiant créé automatiquement dans CriteresEval
            $idCritereEval = $pdo->lastInsertId();            

            // Trouver le prochain numé d'odre de ce modele
            $sql = "SELECT COALESCE(MAX(NumOrdre), 0)
                    FROM ModeleContenirCriteres
                     WHERE IdModeleEval = :idModeleEval";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':idModeleEval' => $idModeleEval]);

            $ordreMaximum = (int) $stmt->fetchColumn();
            $nouvelOrdre = $ordreMaximum + 1;

            // Associer le nouveau critère au modèle

            $sql = "INSERT INTO ModeleContenirCriteres (
                                            IdCritere,
                                            IdModeleEval,
                                            ValeurMaxCritereEval,
                                            NumOrdre
                                            )
                                VALUES (
                                            :idCritere,
                                            :idModeleEval,
                                            :valeurMaxCritere,
                                            :numOrdre
                                        )";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':idCritere' => $idCritereEval,
                ':idModeleEval' => $idModeleEval,
                ':valeurMaxCritere' => $valeurMaxCritere,
                ':numOrdre' => $nouvelOrdre
                ]);
            $pdo->commit();

            return $idCritereEval;
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
    function getCriteresDisponibles($pdo, $idModeleEval)
    {
        $sql = "
            SELECT *
            FROM critereseval
            WHERE IdCritere NOT IN (
                SELECT IdCritere
                FROM modelecontenircriteres
                WHERE IdModeleEval = :ID
            )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':ID' => $idModeleEval]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  
?>

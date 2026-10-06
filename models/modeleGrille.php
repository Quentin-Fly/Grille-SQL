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
        $gereTransaction = !$pdo->inTransaction();
        if ($gereTransaction)
        {
            $pdo->beginTransaction();
        }
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
            
            if ($gereTransaction)
            {
                $pdo->commit();
            }
            return $idNouveauModele;
        }
        catch (Throwable $e)
        {
            if ($gereTransaction && $pdo->inTransaction())
            {
                $pdo->rollBack();
            }
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
    function retirerCritere($pdo, $idModeleEval, $idCritere)
    {
        $pdo->beginTransaction();
        try
        {
            // Supprimer l'association du critère avec le modèle
            $sql = "DELETE FROM ModeleContenirCriteres 
                    WHERE IdModeleEval = :idModeleEval AND IdCritere = :idCritere";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':idModeleEval' => $idModeleEval, ':idCritere' => $idCritere]);

            $liaisonRetiree = $stmt->rowCount() > 0;
            $pdo->commit();
            return $liaisonRetiree;
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
    // Fonction qui associe le critère existant à un modèle spécifique de la grille
    function associerCritere($pdo, $idModeleEval, $idCritereEval, $valeurMaxCritere)
    {
        $pdo ->beginTransaction();
        try
        {
            // Trouver le prochain numé d'odre de ce modele
            $sql = "SELECT COALESCE(MAX(NumOrdre), 0)
                    FROM ModeleContenirCriteres
                     WHERE IdModeleEval = :idModeleEval";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':idModeleEval' => $idModeleEval]);

            $ordreMaximum = (int) $stmt->fetchColumn();
            $nouvelOrdre = $ordreMaximum + 1;

            // Associer le critère existant au modèle
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
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
    // Fonction qui modifer la valeur maximale d'un critère associé à un modèle spécifique de la grille
    function modifierValeurMaxCritere($pdo, $idModeleEval, $idCritereEval, $valeurMaxCritere)
    {
        $pdo ->beginTransaction();
        try
        {
            $sql = "UPDATE ModeleContenirCriteres
                    SET ValeurMaxCritereEval = :valeurMaxCritere
                    WHERE IdModeleEval = :idModeleEval AND IdCritere = :idCritere";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
            ':valeurMaxCritere' => $valeurMaxCritere,
            ':idModeleEval' => $idModeleEval,
            ':idCritere' => $idCritereEval
        ]);
            $pdo->commit();
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
    // Créer une nouvelle version du critère et remplacer seulement la liaison du modèle choisi.
    function modifierDescriptionCritere($pdo, $idModeleEval, $idCritereEval, $descCourteCritere, $descLongueCritere, $valeurMaxCritere)
    {
        $pdo->beginTransaction();
        try
        {
            $sql = "INSERT INTO CriteresEval (descCourte, descLongue)
                    VALUES (:descCourte, :descLongue)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':descCourte' => $descCourteCritere,
                ':descLongue' => $descLongueCritere
            ]);
            $nouvelleIdCritereEval = $pdo->lastInsertId();

            // Conserver les points et l'ordre ; changer uniquement l'identifiant du critère.
            $sql = "UPDATE ModeleContenirCriteres
                    SET IdCritere = :nouvelIdCritere, ValeurMaxCritereEval = :valeurMaxCritere
                    WHERE IdModeleEval = :idModeleEval AND IdCritere = :ancienIdCritere";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nouvelIdCritere' => $nouvelleIdCritereEval,
                ':valeurMaxCritere' => $valeurMaxCritere,
                ':idModeleEval' => $idModeleEval,
                ':ancienIdCritere' => $idCritereEval
            ]);

            // Annuler aussi la création si la liaison à remplacer n'existe pas.
            if ($stmt->rowCount() !== 1)
            {
                throw new RuntimeException("Ce critère n'est pas associé à ce modèle.");
            }

            $pdo->commit();
            return $nouvelleIdCritereEval;
        }
        catch (Throwable $e)
        {
            $pdo->rollBack();
            throw $e;
        }
    }
    // Créer le modèle et copier ses associations dans une seule transaction.
    function copierModele($pdo, $idModeleSource, $natureGrille, $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut)
    {
        $pdo->beginTransaction();
        try
        {
            $modeleSource = getModeleParId($pdo, $idModeleSource);
            if ($modeleSource === false)
            {
                throw new RuntimeException("Le modèle source n'existe pas.");
            }

            $idNouveauModele = addModele($pdo, $modeleSource['natureGrille'], $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut);

            // Partager les critères, en conservant exactement les points et les numéros d'ordre.
            $sql = "INSERT INTO ModeleContenirCriteres
                        (IdCritere, IdModeleEval, ValeurMaxCritereEval, NumOrdre)
                    SELECT IdCritere, :idNouveauModele, ValeurMaxCritereEval, NumOrdre
                    FROM ModeleContenirCriteres
                    WHERE IdModeleEval = :idModeleSource";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':idNouveauModele' => $idNouveauModele,
                ':idModeleSource' => $idModeleSource
            ]);

            $pdo->commit();
            return $idNouveauModele;
        }
        catch (Throwable $e)
        {
            if ($pdo->inTransaction())
            {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
    // Détecter une référence au modèle dans une évaluation, même sans notes saisies.
    function modeleEstUtilise($pdo, $idModeleEval)
    {
        $sql = "SELECT 1 FROM EvalAnglais WHERE IdModeleEval = :idAnglais
                UNION ALL
                SELECT 1 FROM EvalRapport WHERE IdModeleEval = :idRapport
                UNION ALL
                SELECT 1 FROM EvalSoutenance WHERE IdModeleEval = :idSoutenance
                UNION ALL
                SELECT 1 FROM EvalStage WHERE IdModeleEval = :idStage
                UNION ALL
                SELECT 1 FROM EvalPortfolio WHERE IdModeleEval = :idPortfolio
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idAnglais' => $idModeleEval,
            ':idRapport' => $idModeleEval,
            ':idSoutenance' => $idModeleEval,
            ':idStage' => $idModeleEval,
            ':idPortfolio' => $idModeleEval
        ]);

        return $stmt->fetchColumn() !== false;
    }
    // Détecter les notes de critères via les évaluations qui référencent le modèle.
    function modelePossedeNotes($pdo, $idModeleEval)
    {
        $sql = "SELECT 1 FROM EvalAnglais AS e
                JOIN LesCriteresNotesAnglais AS n ON n.IdEvalAnglais = e.IdEvalAnglais
                WHERE e.IdModeleEval = :idAnglais
                UNION ALL
                SELECT 1 FROM EvalRapport AS e
                JOIN LesCriteresNotesRapport AS n ON n.IdEvalRapport = e.IdEvalRapport
                WHERE e.IdModeleEval = :idRapport
                UNION ALL
                SELECT 1 FROM EvalSoutenance AS e
                JOIN LesCriteresNotesSoutenance AS n ON n.IdEvalSoutenance = e.IdEvalSoutenance
                WHERE e.IdModeleEval = :idSoutenance
                UNION ALL
                SELECT 1 FROM EvalStage AS e
                JOIN LesCriteresNotesStage AS n ON n.IdEvalStage = e.IdEvalStage
                WHERE e.IdModeleEval = :idStage
                UNION ALL
                SELECT 1 FROM EvalPortfolio AS e
                JOIN LesCriteresNotesPortFolio AS n ON n.IdEvalPortfolio = e.IdEvalPortfolio
                WHERE e.IdModeleEval = :idPortfolio
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idAnglais' => $idModeleEval,
            ':idRapport' => $idModeleEval,
            ':idSoutenance' => $idModeleEval,
            ':idStage' => $idModeleEval,
            ':idPortfolio' => $idModeleEval
        ]);

        return $stmt->fetchColumn() !== false;
    }
?>

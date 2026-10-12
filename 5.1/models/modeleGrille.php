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
                throw new RuntimeException("Crée d'abord l'année universitaire avec ses six dates de soutenance.");
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
    function verifierCapaciteModele($pdo, $idModeleEval)
    {
        $stmt = $pdo->prepare('SELECT IdModeleEval FROM modelesgrilleeval WHERE IdModeleEval = :id FOR UPDATE');
        $stmt->execute([':id' => $idModeleEval]);
        if ($stmt->fetchColumn() === false) {
            throw new RuntimeException("Le modèle n'existe pas.");
        }
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM modelecontenircriteres WHERE IdModeleEval = :id');
        $stmt->execute([':id' => $idModeleEval]);
        if ((int) $stmt->fetchColumn() >= 15) {
            throw new RuntimeException('Une grille ne peut pas avoir plus de 15 critères.');
        }
    }
    // Le dump d'origine impose un ordre unique dans toute la table.
    function verrouillerOrdreCritere($pdo)
    {
        $stmt = $pdo->query("SELECT GET_LOCK('grille_sql_ordre_critere', 5)");
        if ((int) $stmt->fetchColumn() !== 1) {
            throw new RuntimeException("Un ajout de critère est en cours. Réessaie dans quelques secondes.");
        }
    }

    function prochainOrdreCritere($pdo)
    {
        return (int) $pdo->query('SELECT COALESCE(MAX(NumOrdre), 0) + 1 FROM modelecontenircriteres')->fetchColumn();
    }

    function messageErreurCritere($e)
    {
        if ($e instanceof PDOException) {
            $code = $e->errorInfo[1] ?? null;
            $detail = (string) ($e->errorInfo[2] ?? '');
            if ($code == 1062) {
                return stripos($detail, 'NumOrdre') !== false || stripos($detail, 'OrdreParModele') !== false
                    ? "Le numéro d'ordre est déjà utilisé. Réessaie l'ajout du critère."
                    : "Ce critère est déjà associé à ce modèle.";
            }
            if ($code == 1644 && stripos($detail, '15') !== false) {
                return "La base refuse la création : sa limite de critères est atteinte. Dans le modèle SQL d'origine, cette limite est comptée sur toutes les grilles réunies.";
            }
        } elseif ($e instanceof RuntimeException) {
            return $e->getMessage();
        }
        return "L'enregistrement du critère a échoué. Veuillez réessayer.";
    }

    function addCritere($pdo, $idModeleEval, $descCourteCritere, $descLongueCritere, $valeurMaxCritere)
    {
        $verrouOrdre = false;
        $idCritereCree = null;
        $pdo->beginTransaction();
        try
        {
            verrouillerOrdreCritere($pdo);
            $verrouOrdre = true;
           verifierCapaciteModele($pdo, $idModeleEval);
            // Création du critère
            $sql = "INSERT INTO critereseval (descCourte, descLongue) 
                    VALUES (:descCourte, :descLongue)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':descCourte', $descCourteCritere);
            $stmt->bindParam(':descLongue', $descLongueCritere);

            $stmt->execute();

            // Identifiant créé automatiquement dans critereseval
            $idCritereEval = $pdo->lastInsertId();
            $idCritereCree = $idCritereEval;            

            $nouvelOrdre = prochainOrdreCritere($pdo);

            // Associer le nouveau critère au modèle

            $sql = "INSERT INTO modelecontenircriteres (
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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            // MyISAM ne rollback pas l'INSERT : retirer uniquement le critère créé sans association.
            if ($idCritereCree !== null) {
                try {
                    $stmt = $pdo->prepare('DELETE c FROM critereseval c LEFT JOIN modelecontenircriteres m ON m.IdCritere = c.IdCritere WHERE c.IdCritere = :id AND m.IdCritere IS NULL');
                    $stmt->execute([':id' => $idCritereCree]);
                } catch (Throwable $nettoyage) {
                    error_log($nettoyage->getMessage());
                }
            }
            throw $e;
        }
        finally
        {
            if ($verrouOrdre) {
                $pdo->query("SELECT RELEASE_LOCK('grille_sql_ordre_critere')");
            }
        }
    }
    function retirerCritere($pdo, $idModeleEval, $idCritere)
    {
        $pdo->beginTransaction();
        try
        {
            // Supprimer l'association du critère avec le modèle
            $sql = "DELETE FROM modelecontenircriteres 
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
        $verrouOrdre = false;
        $idCritereCree = null;
        $pdo->beginTransaction();
        try
        {
            verrouillerOrdreCritere($pdo);
            $verrouOrdre = true;
            verifierCapaciteModele($pdo, $idModeleEval);
            $nouvelOrdre = prochainOrdreCritere($pdo);

            // Associer le critère existant au modèle
            $sql = "INSERT INTO modelecontenircriteres (
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
        finally
        {
            if ($verrouOrdre) {
                $pdo->query("SELECT RELEASE_LOCK('grille_sql_ordre_critere')");
            }
        }
    }
    // Fonction qui modifer la valeur maximale d'un critère associé à un modèle spécifique de la grille
    function modifierValeurMaxCritere($pdo, $idModeleEval, $idCritereEval, $valeurMaxCritere)
    {
        $pdo ->beginTransaction();
        try
        {
            $sql = "UPDATE modelecontenircriteres
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
            $sql = "INSERT INTO critereseval (descCourte, descLongue)
                    VALUES (:descCourte, :descLongue)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':descCourte' => $descCourteCritere,
                ':descLongue' => $descLongueCritere
            ]);
            $nouvelleIdCritereEval = $pdo->lastInsertId();

            // Conserver les points et l'ordre ; changer uniquement l'identifiant du critère.
            $sql = "UPDATE modelecontenircriteres
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
        $verrouOrdre = false;
        $idCritereCree = null;
        $pdo->beginTransaction();
        try
        {
            verrouillerOrdreCritere($pdo);
            $verrouOrdre = true;
            $modeleSource = getModeleParId($pdo, $idModeleSource);
            if ($modeleSource === false)
            {
                throw new RuntimeException("Le modèle source n'existe pas.");
            }

            if (count(getCriteresParIdModele($pdo, $idModeleSource)) > 15) {
                throw new RuntimeException('Le modèle source dépasse 15 critères.');
            }
            $idNouveauModele = addModele($pdo, $modeleSource['natureGrille'], $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut);

            // Partager les critères avec de nouveaux ordres globaux, en conservant leur ordre relatif.
            $criteresSource = getCriteresParIdModele($pdo, $idModeleSource);
            $ordre = prochainOrdreCritere($pdo);
            $stmt = $pdo->prepare('INSERT INTO modelecontenircriteres (IdCritere, IdModeleEval, ValeurMaxCritereEval, NumOrdre) VALUES (:critere, :modele, :maximum, :ordre)');
            foreach ($criteresSource as $critere) {
                $stmt->execute([':critere' => $critere['IdCritere'], ':modele' => $idNouveauModele,
                    ':maximum' => $critere['ValeurMaxCritereEVal'], ':ordre' => $ordre++]);
            }

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
        finally
        {
            if ($verrouOrdre) {
                $pdo->query("SELECT RELEASE_LOCK('grille_sql_ordre_critere')");
            }
        }
    }
    // Schéma actuel : deux évaluations de soutenance ; EvalStage ne référence aucun modèle.
    function modeleEstUtilise($pdo, $idModeleEval)
    {
        $sql = "SELECT 1 FROM evalanglais WHERE IdModeleEval = :anglais
                UNION ALL SELECT 1 FROM evalrapport WHERE IdModeleEval = :rapport
                UNION ALL SELECT 1 FROM evalportfolio WHERE IdModeleEval = :portfolio
                UNION ALL SELECT 1 FROM evalsoutenanceenstuteur WHERE IdModeleEval = :tuteur
                UNION ALL SELECT 1 FROM evalsoutenanceenssecond WHERE IdModeleEval = :second
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':anglais' => $idModeleEval, ':rapport' => $idModeleEval,
            ':portfolio' => $idModeleEval, ':tuteur' => $idModeleEval, ':second' => $idModeleEval]);
        return $stmt->fetchColumn() !== false;
    }

    function modelePossedeNotes($pdo, $idModeleEval)
    {
        $sql = "SELECT 1 FROM evalanglais e
                JOIN lescriteresnotesanglais n ON n.IdEvalAnglais = e.IdEvalAnglais
                WHERE e.IdModeleEval = :anglais AND n.noteCritere IS NOT NULL
                UNION ALL
                SELECT 1 FROM evalrapport e
                JOIN lescriteresnotesrapport n ON n.IdEvalRapport = e.IdEvalRapport
                WHERE e.IdModeleEval = :rapport AND n.noteCritere IS NOT NULL
                UNION ALL
                SELECT 1 FROM evalportfolio e
                JOIN lescriteresnotesportfolio n ON n.IdEvalPortfolio = e.IdEvalPortfolio
                WHERE e.IdModeleEval = :portfolio AND n.noteCritere IS NOT NULL
                UNION ALL
                SELECT 1 FROM evalsoutenanceenstuteur e
                JOIN lescriteresnotessoutenanceenstut n ON n.IdEvalSoutenanceEnsTut = e.IdEvalSoutenanceEnsTut
                WHERE e.IdModeleEval = :tuteur AND n.noteCritere IS NOT NULL
                UNION ALL
                SELECT 1 FROM evalsoutenanceenssecond e
                JOIN lescriteresnotessoutenanceenssecond n ON n.IdEvalSoutenanceEnsSecond = e.IdEvalSoutenanceEnsSecond
                WHERE e.IdModeleEval = :second AND n.noteCritere IS NOT NULL
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':anglais' => $idModeleEval, ':rapport' => $idModeleEval,
            ':portfolio' => $idModeleEval, ':tuteur' => $idModeleEval, ':second' => $idModeleEval]);
        return $stmt->fetchColumn() !== false;
    }

    function getAnneesDisponiblesPourGrille($pdo)
    {
        return $pdo->query('SELECT anneeDebut, fin FROM anneesuniversitaires ORDER BY anneeDebut DESC')->fetchAll(PDO::FETCH_ASSOC);
    }

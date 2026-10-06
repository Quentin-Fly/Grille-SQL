<?php
    // Fichier du contrôleur pour gérer les modèles de grille d'évaluation
    require_once __DIR__ . "/../config/database.php";
    require_once __DIR__ . "/../models/ModeleGrille.php";

    // Variable pour déterminer si l'affichage de la création d'un modèle est activé ou non
    $afficherCreation = false;
    $afficherEdition = false;
    $erreurCreation = null;
    $succesCreation = null;
    $modeleSelectionne = null;
    $afficherFormulaireCritere = false;
    $afficherFormulaireAssociation = false;
    $idCritereSelectionne = null;
    $criteresModele = [];
    $valeursFormulaire = [
        'natureGrille' => '',
        'nomModule' => '',
        'noteMax' => '',
        'anneeDebut' => ''
    ];
    
    if (isset($_POST['selectionnerModele'])) 
    {
        $choixModele = $_POST['modele'] ?? '';

        if ($choixModele === 'nouveau')
        // Si l'utilisateur choisit de créer un nouveau modèle, on active l'affichage du formulaire de création
        {
            $afficherCreation = true;
        }
        else
        // Si l'utilisateur choisit un modèle existant, on récupère les informations de ce modèle pour l'édition
        {
            $idModeleEval = filter_var(
                $choixModele,
                FILTER_VALIDATE_INT, // Validation de l'ID du modèle sélectionné
                ['options' => ['min_range' => 1]]
            );

            if ($idModeleEval === false)
            {
                $erreurCreation = "Le modèle sélectionné est invalide.";
            }
            else
            {
                try
                {
                    $modeleSelectionne = getModeleParId(
                        $pdo,
                        $idModeleEval
                    );

                    if ($modeleSelectionne === false)
                    {
                        $erreurCreation =
                            "Le modèle sélectionné n'existe pas.";
                    }
                    else
                    {
                        $afficherEdition = true;

                        $criteresModele = getCriteresParIdModele(
                            $pdo,
                            $idModeleEval
                        );
                    }
                }
                catch (PDOException $e)
                {
                    error_log($e->getMessage());

                    $erreurCreation =
                        "Impossible de charger le modèle sélectionné.";
                }
            }
        }
    }
    
    if (isset($_POST['creerModele'])) 
    {
        $afficherCreation = true;
        $pointDepart = $_POST['pointDepart'] ?? 'vierge';
        foreach ($valeursFormulaire as $champ => $_)
        {
            $valeursFormulaire[$champ] = trim((string) ($_POST[$champ] ?? ''));
        }

        $natureGrille = $valeursFormulaire['natureGrille'];
        $idModeleSource = $_POST['modeleSource'] ?? '';
        $modeleSource = null;
        if ($pointDepart === 'copie' && $idModeleSource !== '')
        {
            $modeleSource = getModeleParId($pdo, $idModeleSource);
            $natureGrille = $modeleSource['natureGrille'] ?? '';
        }

        $noteMax = filter_var($valeursFormulaire['noteMax'], FILTER_VALIDATE_FLOAT);
        $anneeDebut = filter_var($valeursFormulaire['anneeDebut'], FILTER_VALIDATE_INT);

        if (($pointDepart !== 'vierge' && $pointDepart !== 'copie')
            || ($pointDepart === 'vierge' && !in_array($natureGrille, getNaturesGrilleValides(), true))
            || ($pointDepart === 'copie' && !$modeleSource)
            || $valeursFormulaire['nomModule'] === ''
            || strlen($valeursFormulaire['nomModule']) > 80
            || $noteMax === false || $noteMax < 0.5
            || $anneeDebut === false || $anneeDebut < 1 || $anneeDebut >= 9999)
        {
            $erreurCreation = $pointDepart === 'copie'
                ? "Vérifie le modèle source, le nom du module (80 caractères maximum), la note maximale et l'année."
                : "Vérifie la nature, le nom du module (80 caractères maximum), la note maximale et l'année.";
        }
        else
        {
            try
            {
                $nomModuleExiste = nomModeleExiste($pdo, $valeursFormulaire['nomModule']);
                $natureExistePourAnnee = natureModeleExistePourAnnee($pdo, $natureGrille, $anneeDebut);

                if ($nomModuleExiste)
                // Vérifie si le nom du module existe déjà dans la base de données
                {
                    $erreurCreation = "Le nom du module existe déjà. CHoisis un autre nom.";
                }
                elseif ($natureExistePourAnnee)
                // Vérifie si la nature existe déjà dans la base de données pour l'année donnée
                {
                    $erreurCreation = "La nature existe déjà pour l'année donnée. Choisis une autre année ou une autre nature.";
                }
                else
                {
                    $idNouveauModele = addModele($pdo, $natureGrille, $noteMax, $valeursFormulaire['nomModule'], $anneeDebut);
                    if ($pointDepart === 'copie' && $modeleSource)
                    {
                        // Copier les critères du modèle source vers le nouveau modèle
                        copierCriteres($pdo, $modeleSource['IdModeleEval'], $idNouveauModele);
                    }
                    $succesCreation = "Le modèle a été créé avec succès.";
                    // Réinitialiser le formulaire après la création réussie
                    $valeursFormulaire = [
                        'natureGrille' => '',
                        'nomModule' => '',
                        'noteMax' => '',
                        'anneeDebut' => ''
                    ];
                    // Récupérer le nouveau modèle pour l'affichage
                    $modeleSelectionne = getModeleParId($pdo, $idNouveauModele);
                    $criteresModele = getCriteresParIdModele($pdo, $idNouveauModele);
                    // Fermer le formulaire de création et ouvrir le formulaire d'édition pour le nouveau modèle
                    $afficherCreation = false;
                    $afficherEdition = true;

                    // Ne pas encore ouvrir le formulaire de création de critère, attendre que l'utilisateur clique sur "Ajouter un critère"
                    $afficherFormulaireCritere = false;
            
                }
            }
            catch (PDOException $e)
            {
                $error_log($e->getMessage());

                if ($e->getCode() === '23000' && ($e->errorInfo[1] ?? null) == 1062) // Code d'erreur pour violation de contrainte d'unicité
                {
                    $erreurCreation = "Un modèle possédant ces informations existe déjà.";
                }
                else
                {
                    $erreurCreation = "La création du modèle a échoué. Veuillez réessayer.";
                }
            }        
        }
    }
    if (isset($_POST['selectionnerCritere'])) 
    {
        // Récupérer l'ID du modèle sélectionné pour l'édition
        $idModeleEval = filter_var($_POST['idModeleEval'] ?? ' ', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        // Vérifier si l'ID du modèle est valide
        if ($idModeleEval === false)
        {
            $erreurCreation = "Le modèle sélectionné est invalide.";
        }
        else
        {
            try
            {
                // Chaque clic recharge la page : retrouver le modèle avec l'identifiant du formulaire.
                $modeleSelectionne = getModeleParId($pdo, $idModeleEval);

                if ($modeleSelectionne === false)
                {
                    $erreurCreation = "Le modèle sélectionné n'existe pas.";
                }
                else
                {
                    $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                    $afficherEdition = true;
        
                    $choixCritere = $_POST['critere'] ?? '';
        
                    if ($choixCritere === 'nouveau') 
                    {
                        $afficherFormulaireCritere = true;
                    } 
                    else 
                    {
                        $idCritereSelectionne = filter_var($choixCritere, FILTER_VALIDATE_INT,['options' => ['min_range' => 1]]);              
        
                        if ($idCritereSelectionne === false) 
                        {
                            $erreurCreation = "Le critère sélectionné est invalide.";
                        } 
                        else 
                        {
                            $criteresDisponibles = getCriteresDisponibles($pdo, $idModeleEval);
                            $critereDisponible = false;
        
                            foreach ($criteresDisponibles as $critere) 
                            {
                                if ((int) $critere['IdCritere'] === $idCritereSelectionne) 
                                {
                                    $critereDisponible = true;
                                    break;
                                }
                            }
        
                            if ($critereDisponible) 
                            {
                                $afficherFormulaireAssociation = true;
                            } 
                            else 
                            {
                                $erreurCreation = "Ce critère n'existe pas ou est déjà associé au modèle.";
                            }
                        }
                    } 
                        }
            }
            catch (PDOException $e)
            {
                error_log($e->getMessage());
                $afficherEdition = false;
                $afficherFormulaireCritere = false;
                $afficherFormulaireAssociation = false;
                $erreurCreation = "Impossible de charger le modèle ou les critères disponibles.";
            }
        }
    }
    // Gestion de la création d'un critère pour un modèle existant
    if (isset($_POST['creerCritere'])) 
    {
        $idModeleEval = filter_var($_POST['idModeleEval'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $descCourteCritere = trim((string) ($_POST['descCourteCritere'] ?? ''));
        $descLongueCritere = trim((string) ($_POST['descLongueCritere'] ?? ''));
        $valeursMaxCritere = filter_var($_POST['valeurMaxCritere'] ?? '', FILTER_VALIDATE_FLOAT);
        
        $erreurCritere = [];

        if ($idModeleEval === false)
        {
            $erreurCritere[] = "Modèle invalide.";
        }
        if ($descCourteCritere === '' || mb_strlen($descCourteCritere) > 100)
        {
            $erreurCritere[] = "La description courte est obligatoire et doit contenir au maximum 100 caractères.";
        }
        if (mb_strlen($descLongueCritere) > 500)
        {
            $erreurCritere[] = "La description longue doit contenir au maximum 500 caractères.";
        }
        if ($valeursMaxCritere === false)
        {
            $erreurCritere[] = "La valeur maximale doit être un nombre.";
        }
        else if ($valeursMaxCritere < 0.5)
        {
            $erreurCritere[] = "La valeur maximale doit être supérieure ou égale à 0,5.";
        }
        if (empty($erreurCritere))
        {
            try
            {
                $idNouveauCritere = addCritere($pdo, $idModeleEval, $descCourteCritere, $descLongueCritere, $valeursMaxCritere);
                $succesCreation = "Le critère a été créé avec succès.";
                $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                // Réinitialiser le formulaire après la création réussie
                $modeleSelectionne = getModeleParId($pdo, $idModeleEval);
                $afficherFormulaireCritere = false;
                $afficherEdition = true;
            }
            catch (PDOException $e)
            {
                error_log($e->getMessage());
                if ($e->getCode() === '23000' && ($e->errorInfo[1] ?? null) == 1062) // Code d'erreur pour violation de contrainte d'unicité
                {
                    $erreurCritere[] = "Un critère possédant ces informations existe déjà.";
                }
                else
                {
                    $erreurCritere[] = "La création du critère a échoué. Veuillez réessayer.";
                }
            }
        }
        if (!empty($erreurCritere))
        {
            $erreurCreation = implode(" ", $erreurCritere);

            // Conserver la grille et le formulaire visibles après l'erreur
            if ($idModeleEval !== false)
            {
                $modeleSelectionne = getModeleParId(
                    $pdo,
                    $idModeleEval
                );

                if ($modeleSelectionne !== false)
                {
                    $criteresModele = getCriteresParIdModele(
                        $pdo,
                        $idModeleEval
                    );

                    $afficherEdition = true;
                    $afficherFormulaireCritere = true;
                }
            }
        }
    }
    if (isset($_POST['affecterCritere']))
    {
        $idModeleEval = filter_var($_POST['idModeleEval'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $idCritere = filter_var($_POST['idCritere'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $valeurMaxCritere = filter_var($_POST['valeurMaxCritere'] ?? '', FILTER_VALIDATE_FLOAT);

        if ($idModeleEval === false || $idCritere === false || $valeurMaxCritere === false || $valeurMaxCritere < 0.5)
        {
            $erreurCreation = "Les informations fournies pour l'association sont invalides.";
        }
        else
        {
            try
            {
                // Retrouver le modèle après l'envoi du formulaire et vérifier son existence.
                $modeleSelectionne = getModeleParId($pdo, $idModeleEval);
                if ($modeleSelectionne === false)
                {
                    $erreurCreation = "Le modèle sélectionné n'existe pas.";
                }
                else
                {
                    $afficherEdition = true;
                    $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);

                    // La liste disponible exclut les critères inexistants ou déjà associés.
                    $criteresDisponibles = getCriteresDisponibles($pdo, $idModeleEval);
                    $critereDisponible = false;
                    foreach ($criteresDisponibles as $critere)
                    {
                        if ((int) $critere['IdCritere'] === $idCritere)
                        {
                            $critereDisponible = true;
                            break;
                        }
                    }

                    if (!$critereDisponible)
                    {
                        $erreurCreation = "Ce critère n'existe pas ou est déjà associé au modèle.";
                    }
                    else
                    {
                        associerCritere($pdo, $idModeleEval, $idCritere, $valeurMaxCritere);
                        $succesCreation = "Le critère a été associé avec succès au modèle.";
                        $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                        $afficherFormulaireAssociation = false;
                    }
                }
            }
            catch (PDOException $e)
            {
                error_log($e->getMessage());
                if ($e->getCode() === '23000' && ($e->errorInfo[1] ?? null) == 1062)
                {
                    $erreurCreation = "Ce critère est déjà associé à ce modèle.";
                }
                else
                {
                    $erreurCreation = "L'association du critère a échoué. Veuillez réessayer.";
                }
            }
        }
    }
    if (isset($_POST['retirerCritere']))
    {
        $idModeleEval = filter_var($_POST['idModeleEval'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $idCritere = filter_var($_POST['idCritere'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($idModeleEval === false || $idCritere === false)
        {
            $erreurCreation = "Les informations fournies pour le retrait sont invalides.";
        }
        else
        {
            try
            {
                // Retrouver le modèle après l'envoi du formulaire et vérifier son existence.
                $modeleSelectionne = getModeleParId($pdo, $idModeleEval);
                if ($modeleSelectionne === false)
                {
                    $erreurCreation = "Le modèle sélectionné n'existe pas.";
                }
                else
                {
                    $liaisonRetiree = retirerCritere($pdo, $idModeleEval, $idCritere);
                    if ($liaisonRetiree)
                    {
                        $succesCreation = "Le critère a été retiré avec succès du modèle.";
                    }
                    else
                    {
                        $erreurCreation = "Ce critère n'est pas associé à ce modèle.";
                    }
                    $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                    $afficherEdition = true;
                }
            }
            catch (PDOException $e)
            {
                error_log($e->getMessage());
                $erreurCreation = "Le retrait du critère a échoué. Veuillez réessayer.";
            }
        }
    }
    if (isset($_POST['modifierValeurMax']))
    {
        $idModeleEval = filter_var($_POST['idModeleEval'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $idCritere = filter_var($_POST['idCritere'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $valeurMaxCritere = filter_var($_POST['valeurMaxCritere'] ?? '', FILTER_VALIDATE_FLOAT);

        if ($idModeleEval === false || $idCritere === false || $valeurMaxCritere === false || $valeurMaxCritere < 0.5)
        {
            $erreurCreation = "Les informations fournies pour la modification sont invalides.";
        }
        else
        {
            try
            {
                // Retrouver le modèle après l'envoi du formulaire et vérifier son existence.
                $modeleSelectionne = getModeleParId($pdo, $idModeleEval);
                if ($modeleSelectionne === false)
                {
                    $erreurCreation = "Le modèle sélectionné n'existe pas.";
                }
                else
                {
                    $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                    $afficherEdition = true;
                    $critereAssocie = false;

                    // Vérifier que le critère appartient bien au modèle avant la modification.
                    foreach ($criteresModele as $critere)
                    {
                        if ((int) $critere['IdCritere'] === $idCritere)
                        {
                            $critereAssocie = true;
                            break;
                        }
                    }

                    if (!$critereAssocie)
                    {
                        $erreurCreation = "Ce critère n'est pas associé à ce modèle.";
                    }
                    else
                    {
                        modifierValeurMaxCritere($pdo, $idModeleEval, $idCritere, $valeurMaxCritere);
                        $succesCreation = "La valeur maximale du critère a été modifiée avec succès.";
                        $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                    }
                }
            }
            catch (PDOException $e)
            {
                error_log($e->getMessage());
                $erreurCreation = "La modification de la valeur maximale a échoué. Veuillez réessayer.";
            }
        }
    }

    // Charger les listes après les traitements pour afficher les données actualisées.
    $listModele = getAllModele($pdo);
    $listCritere = [];
    if ($modeleSelectionne)
    {
        $listCritere = getCriteresDisponibles($pdo, $modeleSelectionne['IdModeleEval']);
    }

    require __DIR__ . "/../view/modeles_grille/index.php";

?>

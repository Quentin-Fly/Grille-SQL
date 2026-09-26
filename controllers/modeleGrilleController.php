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
    if (isset($_POST['afficherCreationCritere'])) 
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
            $modeleSelectionne = getModeleParId($pdo, $idModeleEval);
            if ($modeleSelectionne === false)
            {
                $erreurCreation = "Le modèle sélectionné n'existe pas.";
            }
            else
            {
                $criteresModele = getCriteresParIdModele($pdo, $idModeleEval);
                if ($criteresModele === false)
                {
                    $erreurCreation = "Le modèle sélectionné n'existe pas.";
                }
                else
                {
                    // Afficher le formulaire de création de critère pour le modèle sélectionné
                    $afficherEdition = true;
                    $afficherFormulaireCritere = true;
                }
            }
           
        }
    }

    $listModele = getAllModele($pdo);
    require __DIR__ . "/../view/modeles_grille/index.php";

?>

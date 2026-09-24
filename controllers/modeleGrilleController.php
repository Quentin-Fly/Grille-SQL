<?php
    // Fichier du contrôleur pour gérer les modèles de grille d'évaluation
    require_once __DIR__ . "/../config/database.php";
    require_once __DIR__ . "/../models/ModeleGrille.php";

    // Variable pour déterminer si l'affichage de la création d'un modèle est activé ou non
    $afficherCreation = false;
    $erreurCreation = null;
    $succesCreation = null;
    $valeursFormulaire = [
        'natureGrille' => '',
        'nomModule' => '',
        'noteMax' => '',
        'anneeDebut' => ''
    ];
    
    if (isset($_POST['selectionnerModele'])) 
    {
        // Si le formulaire de création de modèle est soumis, activer l'affichage du formulaire
        if (($_POST['modele'] ?? '') === 'nouveau')
        {
            $afficherCreation = true;
        } 
        else 
        {
            // Sinon, récupérer les informations du modèle sélectionné et les pré-remplir dans le formulaire de création
            $idModeleEval = $_POST['modele'] ?? '';
            $modele = getModeleParId($pdo, $idModeleEval);
            if ($modele)
            {
                $valeursFormulaire['natureGrille'] = $modele['natureGrille'];
                $valeursFormulaire['nomModule'] = $modele['nomModuleGrilleEvaluation'];
                $valeursFormulaire['noteMax'] = $modele['noteMaxGrille'];
                $valeursFormulaire['anneeDebut'] = $modele['anneeDebut'];
                $afficherCreation = true;
            }
            else
            {
                $erreurCreation = "Le modèle sélectionné n'existe pas.";
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

    $listModele = getAllModele($pdo);
    require __DIR__ . "/../view/modeles_grille/index.php";
?>

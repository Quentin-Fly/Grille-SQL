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
            // Sinon plus tard, vous pouvez ajouter ici le code pour gérer la sélection d'un modèle existant
        }
    }

    if (isset($_POST['creerModele'])) 
    {
        $afficherCreation = true;
        foreach ($valeursFormulaire as $champ => $_)
        {
            $valeursFormulaire[$champ] = trim((string) ($_POST[$champ] ?? ''));
        }

        $naturesPossibles = ['ANGLAIS', 'RAPPORT', 'SOUTENANCE', 'STAGE', 'PORTFOLIO'];
        $noteMax = filter_var($valeursFormulaire['noteMax'], FILTER_VALIDATE_FLOAT);
        $anneeDebut = filter_var($valeursFormulaire['anneeDebut'], FILTER_VALIDATE_INT);

        if (!in_array($valeursFormulaire['natureGrille'], $naturesPossibles, true)
            || $valeursFormulaire['nomModule'] === ''
            || strlen($valeursFormulaire['nomModule']) > 80
            || $noteMax === false || $noteMax < 0.5
            || $anneeDebut === false || $anneeDebut < 1 || $anneeDebut >= 9999)
        {
            $erreurCreation = "Vérifie la nature, le nom du module (80 caractères maximum), la note maximale et l'année.";
        }
        else
        {
            try
            {
                addModele($pdo, $valeursFormulaire['natureGrille'], $noteMax,
                    $valeursFormulaire['nomModule'], $anneeDebut);
                $succesCreation = "Modèle créé avec succès.";
                $afficherCreation = false;
            }
            catch (PDOException $e)
            {
                if ($e->getCode() === '23000' && ($e->errorInfo[1] ?? null) == 1062)
                {
                    $erreurCreation = "Ce nom de module existe déjà. Choisis un autre nom.";
                }
                else
                {
                    error_log($e->getMessage());
                    $erreurCreation = "La création du modèle a échoué. Réessaie plus tard.";
                }
            }
        }
    }

    $listModele = getAllModele($pdo);
    require __DIR__ . "/../view/modeles_grille/index.php";
?>

<?php
    // Fichier du contrôleur pour gérer les modèles de grille d'évaluation
    require_once __DIR__ . "/../config/database.php";
    require_once __DIR__ . "/../models/ModeleGrille.php";

    $listModele = getAllModele($pdo);

    // Variable pour déterminer si l'affichage de la création d'un modèle est activé ou non
    $afficherCreation = false;  
    
    if (isset($_POST['selectionnerModele'])) 
    {
        // Si le formulaire de création de modèle est soumis, activer l'affichage du formulaire
        if ($_POST['modele'] === 'nouveau') 
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
    $pointDepart = $_POST['pointDepart'] ?? 'vierge';
    $nomModuleGrilleEvaluation = $_POST['nomModule'];
    $noteMaxGrille = $_POST['noteMax'];
    $anneeDebut = $_POST['anneeDebut'];

    if ($pointDepart === 'copie') 
    {
        $idModeleSource = $_POST['modeleSource'] ?? '';
        $modeleSource = $idModeleSource !== '' ? getModeleParId($pdo, $idModeleSource) : null;

        if (!$modeleSource) 
        {
            $erreurCreation = "Veuillez sélectionner un modèle source valide à copier.";
        } 
        else 
        {
            $idNouveauModele = addModele($pdo, $modeleSource['natureGrille'], $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut);

            if ($idNouveauModele) 
            {
                // TODO (checklist suivante "Copier toute la structure") :
                // copierStructureModele($pdo, $idModeleSource, $idNouveauModele);
                echo "Modèle créé (structure à dupliquer à l'étape suivante).";
            } 
            else 
            {
                $erreurCreation = "Erreur lors de la création du modèle.";
            }
        }
    } 
    else 
    {
        $natureGrille = $_POST['natureGrille'] ?? '';

        if (!in_array($natureGrille, getNaturesGrilleValides(), true)) 
        {
            $erreurCreation = "Nature de grille invalide.";
        } 
        else if (addModele($pdo, $natureGrille, $noteMaxGrille, $nomModuleGrilleEvaluation, $anneeDebut)) 
        {
            echo "Modèle créé avec succès.";
        } 
        else 
        {
            $erreurCreation = "Erreur lors de la création du modèle.";
        }
    }
}
    require __DIR__ . "/../view/modeles_grille/index.php";
?>

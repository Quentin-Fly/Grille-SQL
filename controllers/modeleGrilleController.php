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
        $natureGrille = $_POST['natureGrille'];
        $nomModuleGrilleEvaluation = $_POST['nomModule'];
        $noteMaxGrille = $_POST['noteMax'];
        $anneeDebut = $_POST['anneeDebut'];

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
    require __DIR__ . "/../view/modeles_grille/index.php";
?>

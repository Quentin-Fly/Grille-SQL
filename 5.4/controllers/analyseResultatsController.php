<?php
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/modeleAnalyseResultats.php';

    $anneesUniversitaires = getAnneesUniversitaires($pdo);
    $anneeSelectionnee = null;
    $erreurFiltre = null;
    $ficheEtudiant = null;
    $moyenneStage = null;

    if (isset($_GET['anneeDebut'])) 
    {
        $anneeDemandee = filter_var($_GET['anneeDebut'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($anneeDemandee === false) 
        {
            $erreurFiltre = 'Veuillez sélectionner une année universitaire valide.';
        } 
        else 
        {
            foreach ($anneesUniversitaires as $annee) 
            {
                if ((int) $annee['anneeDebut'] === $anneeDemandee) 
                {
                    $anneeSelectionnee = $anneeDemandee;
                    break;
                }
            }
            if ($anneeSelectionnee === null) {
                $erreurFiltre = "L'année universitaire demandée n'existe pas.";
            }
        }
    } 
    elseif (!empty($anneesUniversitaires)) 
    {
        // La liste est triée par année décroissante dans le modèle.
        $anneeSelectionnee = (int) $anneesUniversitaires[0]['anneeDebut'];
    }
    $resultats = [];

    if (isset($_GET['idEtudiant']))
    {
        $idEtudiant = filter_var($_GET['idEtudiant'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($idEtudiant === false)
        {
            $erreurFiltre = "Identifiant d'étudiant invalide.";
        }
        elseif ($anneeSelectionnee !== null)
        {
            $ficheEtudiant = getFicheEtudiant($pdo, $idEtudiant, $anneeSelectionnee);
            if ($ficheEtudiant === false)
            {
                $ficheEtudiant = null;
    $moyenneStage = null;
                $erreurFiltre = 'Aucun stage trouvé pour cet étudiant et cette année.';
            }
        }
    }
    $typeSelectionne = $_GET['typeEvaluation'] ?? 'TOUS';
    $moyennesParType = [];
    if (!is_string($typeSelectionne))
    {
        $erreurFiltre = "Type d'évaluation invalide.";
        $typeSelectionne = 'TOUS';
    } 
    elseif ($anneeSelectionnee !== null) 
    {
        try
        {
            $moyennesParType = getMoyennesParType(
                $pdo,
                $anneeSelectionnee,
                $typeSelectionne
            );
        } 
        catch (InvalidArgumentException $e) 
        {
            $erreurFiltre = $e->getMessage();
            $typeSelectionne = 'TOUS';
        }
    }
    $moyennesParEnseignant = [];
    $alertes = [];

    if ($anneeSelectionnee !== null) 
    {
        $resultats = getResultatsParAnnee($pdo, $anneeSelectionnee);
        $moyenneStage = getMoyenneStageParAnnee($pdo, $anneeSelectionnee);
        $moyennesParEnseignant = getMoyennesParEnseignant($pdo, $anneeSelectionnee);
        $alertes = getAlertesParAnnee($pdo, $anneeSelectionnee);
    }
    // Charger la vue après la récupération des résultats de l'année validée.
    require __DIR__ . '/../view/analyse_resultats/index.php';
?>
<?php
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../models/modeleUtilisateurBackOffice.php';

    $afficherCreation = isset($_POST['nouvelUtilisateur']);
    $erreurCreation = null;
    $succesCreation = null;
    $valeursFormulaire = ['nom' => '', 'prenom' => '', 'mail' => ''];

    if (isset($_POST['creerUtilisateur'])) {
        $afficherCreation = true;
        foreach ($valeursFormulaire as $champ => $valeur) {
            $valeursFormulaire[$champ] = is_string($_POST[$champ] ?? null) ? trim($_POST[$champ]) : '';
        }
        $nom = $valeursFormulaire['nom'];
        $prenom = $valeursFormulaire['prenom'];
        $mail = $valeursFormulaire['mail'];
        $mdp = is_string($_POST['motdepasse'] ?? null) ? $_POST['motdepasse'] : '';

        if ($nom === '' || $prenom === '' || trim($mdp) === '') 
        {
            $erreurCreation = 'Tous les champs sont obligatoires.';
        }
        elseif (mb_strlen($nom) > 50 || mb_strlen($prenom) > 50)
        {
            $erreurCreation = 'Le nom et le prénom ne doivent pas dépasser 50 caractères.';
        } 
        elseif (mb_strlen($mail) > 150 || filter_var($mail, FILTER_VALIDATE_EMAIL) === false) 
        {
            $erreurCreation = 'Veuillez saisir une adresse mail valide de 150 caractères maximum.';
        } 
        elseif (strlen($mdp) > 72 || strpos($mdp, "\0") !== false) 
        {
            $erreurCreation = 'Le mot de passe est trop long ou contient un caractère invalide.';
        }
        else 
        {
            try 
            {
                creerUtilisateurBackOffice($pdo, $nom, $prenom, $mail, $mdp);
                $succesCreation = "L'utilisateur a été créé.";
                $afficherCreation = false;
                $valeursFormulaire = ['nom' => '', 'prenom' => '', 'mail' => ''];
            } 
            catch (PDOException $e) 
            {
                error_log($e->getMessage());
                $erreurCreation = ($e->errorInfo[1] ?? null) == 1062
                    ? 'Cette adresse mail est déjà utilisée.'
                    : "La création de l'utilisateur a échoué.";
            }
            catch (RuntimeException $e) 
            {
                error_log($e->getMessage());
                $erreurCreation = "La création de l'utilisateur a échoué.";
            }
        }
    }
    if (isset($_POST['supprimerUtilisateur'])) {
        $identifiant = is_numeric($_POST['identifiant'] ?? null) ? (int) $_POST['identifiant'] : null;
        if ($identifiant !== null) 
        {
            try 
            {
                if (supprimerUtilisateurBackOffice($pdo, $identifiant)) 
                {
                    $succesCreation = "L'utilisateur a été supprimé.";
                } 
                else 
                {
                    $erreurCreation = "Aucun utilisateur trouvé avec cet identifiant.";
                }
            } 
            catch (PDOException $e) 
            {
                error_log($e->getMessage());
                $erreurCreation = "La suppression de l'utilisateur a échoué.";
            }
        } 
        else 
        {
            $erreurCreation = "Identifiant invalide pour la suppression.";
        }
    }

    // Charger la liste après le traitement pour afficher le compte nouvellement créé.
    $utilisateursBackOffice = getUtilisateurBackOffice($pdo);
    require __DIR__ . '/../view/utilisateurBackOffice/index.php';
?>
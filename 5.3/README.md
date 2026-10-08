# Section 5.3 — Statuts et droits des utilisateurs

Mise à jour : 8 octobre 2026.

## Objectif demandé par le sujet

Afficher les utilisateurs du back-office et permettre l'ajout ou la suppression des accès. Tous les utilisateurs du back-office ont les mêmes droits.

Les comptes du back-office sont conservés dans `utilisateursbackoffice`. Les comptes du front-office restent dans `enseignants`. Un enseignant peut posséder deux comptes indépendants, un dans chaque table, sans lien obligatoire entre eux.

## Périmètre de cette checklist

Cette checklist suit uniquement la liste, l'ajout et la suppression des comptes du back-office dans le dossier `5.3`. Les contrôles de saisie et le hachage du mot de passe font partie de l'ajout. Les tâches de connexion et d'intégration générale restent des dépendances du groupe.

## État actuel

- [x] Créer le point d'entrée `index.php` de la section 5.3.
- [x] Charger la connexion PDO existante depuis `config/database.php`.
- [x] Créer le modèle et la fonction `getUtilisateurBackOffice($pdo)`.
- [x] Récupérer les colonnes réelles : `Identifiant`, `nom`, `prenom`, `mail`.
- [x] Trier les utilisateurs par identifiant croissant.
- [x] Créer le contrôleur et transmettre `$utilisateursBackOffice` à la vue.
- [x] Afficher le tableau des utilisateurs et échapper les textes affichés.
- [x] Afficher « Aucun utilisateur » lorsque la table est vide.
- [x] Afficher le formulaire sous la liste au clic sur « Nouvel utilisateur », puis le fermer après une création réussie.

## Ajout d'un utilisateur

- [x] Compléter le formulaire de création : nom, prénom, mail et mot de passe.
- [x] Vérifier les champs obligatoires, les longueurs et la validité du mail dans le contrôleur.
- [x] Refuser un mail déjà utilisé dans `utilisateursbackoffice` et gérer la contrainte UNIQUE.
- [x] Hacher le mot de passe avec `password_hash` avant l'enregistrement.
- [x] Définir la génération de `Identifiant` : la colonne n'est pas AUTO_INCREMENT dans `DB_OK.sql`.
- [x] Écrire la fonction d'insertion dans le modèle avec une requête préparée.
- [x] Traiter l'envoi du formulaire en POST dans le contrôleur.
- [x] Afficher un message de réussite ou d'erreur, puis recharger la liste.

## Suppression d'un accès

- [x] Ajouter un bouton de suppression dans chaque ligne, avec un formulaire POST.
- [x] Demander confirmation avant la suppression.
- [x] Valider l'identifiant et vérifier que le compte existe.
- [x] Écrire la fonction de suppression dans le modèle.
- [x] Supprimer uniquement le compte de `utilisateursbackoffice`, sans toucher à `enseignants`.
- [x] Afficher le résultat de l'action et recharger la liste.

## Règles à respecter dans la section 5.3

- [x] Vérifier que la gestion des comptes ne crée pas de niveaux de droits différents entre utilisateurs du back-office.
- [x] Permettre un compte back-office indépendant d'un éventuel compte enseignant : aucune obligation de partager les identifiants ou les mots de passe.
- [x] Vérifier que l'ajout et la suppression d'un compte back-office ne modifient pas la table `enseignants`.

## Dépendances avec le travail du groupe

La connexion, les sessions, la déconnexion, la protection globale des pages et la navigation commune doivent être coordonnées avec la personne responsable de ces éléments. Elles ne sont pas à développer dans cette checklist. Le module 5.3 devra utiliser le mécanisme d'accès du groupe lors de son intégration.

Le format des mots de passe hachés créés ici devra être compatible avec leur système de connexion. Cette compatibilité sera à confirmer avec le groupe.

Les sections 5.1, 5.2, 5.4 et 4.5 ainsi que les fonctionnalités du front-office ne font pas partie du travail suivi ici.

## Vérifications

- [x] Vérifier la syntaxe PHP des fichiers créés ou corrigés pour l'affichage de la liste.
- [x] Exécuter `index.php` avec la base locale : aucun avertissement PHP, état vide correctement affiché.
- [x] Vérifier que le modèle récupère le compte ajouté manuellement : 1 utilisateur trouvé dans `stagebdmerge` lors du contrôle.
- [ ] Vérifier l'affichage dans le navigateur avec plusieurs utilisateurs.
- [x] Tester l'ajout d'un compte valide, son affichage dans la liste et le hachage du mot de passe (table temporaire).
- [x] Tester le refus d'un mail invalide ou déjà utilisé (table temporaire).
- [ ] Tester les champs vides et les longueurs excessives.
- [x] Tester la suppression, le rechargement de la liste et le cas d'un identifiant inexistant (table temporaire).
- [x] Tester le refus des identifiants invalides : décimal, notation scientifique, zéro, négatif, texte et tableau.
- [x] Vérifier dans le code que la suppression cible uniquement `utilisateursbackoffice`.
- [ ] Confirmer par un test avec un compte enseignant indépendant que celui-ci reste présent.
- [ ] Tester les opérations du module avec un utilisateur possédant aussi un compte enseignant, sans modifier ce dernier.
- [x] Vérifier dans le code que le module ne différencie pas les droits des comptes back-office.

Les tests d'ajout et de suppression ont utilisé une table MySQL temporaire reproduisant la définition de la table réelle, sans créer ni supprimer de comptes persistants. Sept vérifications ont passé pour l'ajout et l'affichage du formulaire, et huit pour la suppression. L'affichage visuel dans le navigateur et l'intégration au système de connexion du groupe restent à vérifier.

## Structure du code

| Fichier | Rôle |
|---|---|
| `index.php` | Point d'entrée : charge le contrôleur. |
| `config/database.php` | Connexion PDO à la base configurée. |
| `models/modeleUtilisateurBackOffice.php` | Requêtes SQL de gestion des utilisateurs. |
| `controllers/utilisateurBackOfficeController.php` | Traite les ajouts et suppressions, puis recharge la liste. |
| `view/utilisateurBackOffice/index.php` | Assemble la page. |
| `view/utilisateurBackOffice/listeUtilisateurs.php` | Affiche le tableau des utilisateurs. |
| `view/utilisateurBackOffice/formulaireCréation.php` | Formulaire d'ajout, affiché sous la liste au clic sur « Nouvel utilisateur ». |

Parcours : `index.php` → contrôleur → modèle → vue. Ouvrir le point d'entrée depuis le serveur local, plutôt que la vue directement : le contrôleur doit préparer les variables avant leur affichage.

## Prochaine étape

Terminer les tests des champs vides et des longueurs, vérifier l'interface dans le navigateur et tester la conservation d'un compte enseignant indépendant. Les fonctionnalités principales du module sont implémentées. La modification des comptes et des niveaux de droits différents ne sont pas demandées par la section 5.3.
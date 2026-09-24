# Grille SQL

# Checklist de reprise — modèles de grille

**Périmètre :** section 5.1 du sujet R3.07 et règles directement utiles à la création, la copie, la modification et l’historique des modèles de grille.

**Légende :** `[x]` réalisé et vérifié ; `[ ]` à réaliser ou à tester.

> **Mise à jour de la base :** le schéma actuel relie directement les critères aux modèles avec `ModeleContenirCriteres`. Les anciennes tables de sections ne sont plus utilisées. La gestion des sections demandée dans le sujet doit être clarifiée avec l’enseignant.

## Socle fonctionnel

- [x] **Connexion à la base.** Connexion PDO à `evaluationstages` avec gestion des erreurs.
- [x] **Architecture MVC.** Le contrôleur prépare les données, le modèle exécute les requêtes SQL et les vues affichent les résultats.
- [x] **Lecture des modèles.** `getAllModele($pdo)` récupère les modèles.
- [x] **Écran de sélection.** La liste affiche les modèles existants et l’option « Créer un nouveau modèle ».
- [x] **Cinq natures fixes.** `ANGLAIS`, `RAPPORT`, `SOUTENANCE`, `STAGE` et `PORTFOLIO`.
- [x] **Choix du point de départ dans l’interface.** Structure vierge ou copie d’un modèle existant.

## Création d’un modèle

- [x] **Création d’une structure vierge.** Le modèle est enregistré dans `ModelesGrilleEval` sans critère initial.
- [x] **Gestion de l’année universitaire.** L’année est recherchée puis créée avec sa valeur `fin` si nécessaire.
- [x] **Identifiant automatique.** `IdModeleEval` est produit par `AUTO_INCREMENT` puis récupéré.
- [x] **Transaction de création.** La création de l’année et du modèle est validée ou annulée ensemble.
- [x] **Validation du formulaire.** La nature, le nom, la note maximale et l’année sont contrôlés.
- [x] **Doublon de nom.** Un message spécifique est affiché.
- [x] **Doublon nature et année.** Un message spécifique est affiché.
- [ ] **Année universitaire automatique.** Créer une fonction commune : année civile si le mois est au moins octobre, sinon année civile moins un.

## Consultation et édition

- [x] **Sélection sécurisée d’un modèle.** L’identifiant reçu est validé avant la recherche.
- [x] **Affichage du modèle sélectionné.** La vue affiche son nom, sa nature, son année et sa note maximale sans réutiliser le formulaire de création.
- [x] **État vide des critères.** La vue affiche un message lorsqu’aucun critère n’est associé.
- [x] **Lecture ordonnée des critères.** `getCriteresParIdModele()` relie `ModeleContenirCriteres` à `CriteresEval` et trie par `NumOrdre ASC`.
- [ ] **Tester la lecture avec des critères réels.** Vérifier descriptions, valeur maximale et ordre.

## Gestion des critères

- [ ] **Créer un nouveau critère.** Enregistrer `descCourte` et `descLongue` dans `CriteresEval`.
- [ ] **Associer le critère au modèle.** Enregistrer `IdCritere`, `IdModeleEval`, `ValeurMaxCritereEval` et `NumOrdre` dans `ModeleContenirCriteres`.
- [ ] **Utiliser une transaction.** La création du critère et son association doivent réussir ou être annulées ensemble.
- [ ] **Calculer l’ordre suivant.** Attribuer le prochain `NumOrdre` disponible pour le modèle.
- [ ] **Ajouter un critère existant.** Proposer les critères qui ne sont pas encore associés au modèle.
- [ ] **Retirer un critère du modèle.** Supprimer uniquement la liaison, sans supprimer un critère partagé.
- [ ] **Modifier valeur maximale et ordre.** Respecter les contraintes de la base.
- [ ] **Descriptions longues avec mise en forme.** Définir les balises HTML autorisées et filtrer le contenu.

## Copie d’un modèle

- [ ] **Implémenter `copierCriteres()`.** La fonction est appelée par le contrôleur mais n’est pas encore définie.
- [ ] **Créer et copier dans une seule transaction.** Éviter de conserver un modèle vide si la copie échoue.
- [ ] **Copier les associations.** Reprendre critères, valeurs maximales et numéros d’ordre avec le nouvel identifiant.
- [ ] **Préserver le modèle source.** La copie ne doit modifier aucune ancienne donnée.
- [ ] **Tester la copie complète.** Comparer la source et la copie.

## Modification et historique

- [ ] **Détecter si un modèle est utilisé.** Rechercher son identifiant dans les tables d’évaluation correspondant à sa nature.
- [ ] **Autoriser la modification avant utilisation.**
- [ ] **Protéger un modèle utilisé.** Le rendre non modifiable ou imposer sa duplication.
- [ ] **Vérifier l’historique.** Les anciens modèles et critères restent consultables.

## Simulation et tests finaux

- [ ] **Simuler une grille sans enregistrer les notes.**
- [ ] **Tester les erreurs de création.** Champs invalides, doublons, identifiant inexistant et indisponibilité de la base.
- [x] **Tester un modèle vierge.** Création, sélection, affichage et message d’absence de critères.
- [ ] **Tester un modèle complet.** Ajout, affichage ordonné, modification et retrait des critères.
- [ ] **Tester la copie complète.**
- [ ] **Corriger la journalisation.** Utiliser `error_log()` et ne pas afficher les détails PDO.

## Écart à clarifier avec l’enseignant

- [ ] **Gestion des sections.** Le sujet prévoit 1 à 3 sections et le scénario Portfolio demande 2 sections de 3 critères. La base actuelle ne permet plus d’associer un critère à une section.

**Sources :** sujet R3.07 2026–2027, section 5.1, règles d’évolution et d’historique, scénario du 20 septembre et mise à jour SQL transmise par l’enseignant.

# Grille-SQL

# Checklist de reprise — structure des grilles

**Périmètre :** section 5.1 du sujet R3.07, avec les seules règles utiles sur la composition et l’historique des grilles, ainsi que le scénario du 20 septembre. Ce fichier est destiné à être complété avec le binôme.

**Légende :** `[x]` fait et montré dans la conversation ; `[ ]` à faire ; `[ ]` à vérifier lorsque l’état actuel du code ou le résultat n’est pas confirmé. Modifiez les cases et les explications au fil du travail.

## Déjà en place dans le projet

- [x] **Connexion à la base.** Une connexion PDO à `evaluationstages` et une gestion des erreurs ont été montrées. Fichier : `config/database.php`.
- [x] **Lecture des modèles.** La fonction `getAllModele($pdo)` et une requête `SELECT` sur `ModelesGrilleEval` ont été montrées. Fichier : `models/ModeleGrille.php`.
- [x] **Base MVC et vues.** Le contrôleur procédural et les vues `index.php`, `selectModele.php` et `formulaireCreation.php` ont été montrés. Fichiers : `controllers/modeleGrilleController.php`, `view/modeles_grille/`.
- [x] **Parcours de sélection.** La liste des modèles, l’option « créer un nouveau modèle » et le bouton « Sélectionner » ont été décrits et codés dans l’échange. Fichier : `view/modeles_grille/selectModele.php`.
- [x] **Limiter les natures aux cinq types prévus.** Proposer `ANGLAIS`, `RAPPORT`, `SOUTENANCE`, `STAGE` et `PORTFOLIO`. Corriger toute valeur `PORFOLIO`. Fichiers : `formulaireCreation.php`, contrôleur.
- [ ] **En cours — formulaire et insertion.** Un formulaire de création et un appel à `addModele` étaient présents, mais l’enregistrement échouait sur des noms de colonnes. Vérifier l’état actuel de `view/modeles_grille/formulaireCreation.php` et `models/ModeleGrille.php`.

## Créer et modifier un modèle

- [ ] **Choisir le point de départ.** Proposer une structure vierge ou la copie d’un modèle de l’année écoulée ; dans le second cas, laisser choisir le modèle source. Fichiers : `formulaireCreation.php`, contrôleur.
- [ ] **Enregistrer le nouveau modèle.** Vérifier la nature, l’année et la note maximale, puis utiliser une requête préparée. Dans `AnneesUniversitaires`, les colonnes sont `anneeDebut` et `fin`. Laisser `AUTO_INCREMENT` attribuer `IdModeleEval`. Fichiers : contrôleur, `models/ModeleGrille.php`.
- [ ] **Gérer les sections et les critères.** Permettre d’ajouter ou supprimer une section ; dans une section, retirer un critère, ajouter un critère déjà en base ou créer puis ajouter un critère. Pour les grilles spécifiques, respecter 1 à 3 sections, 1 à 5 critères par section et une note maximale d’au moins 0,5 par critère. Fichiers : vues d’édition, modèle SQL.
- [ ] **Copier toute la structure.** Copier le modèle, ses sections, ses critères et les liaisons nécessaires. Les nouveaux enregistrements doivent être indépendants des anciens ; regrouper les insertions dans une transaction. Fichier : `models/ModeleGrille.php`.
- [ ] **Modifier un modèle avant son utilisation.** Autoriser les mêmes changements tant que le modèle n’a pas servi à une évaluation. Vérifier les tables d’évaluation et, si nécessaire, les notes de critères avant toute modification. Fichiers : contrôleur, modèle SQL.

## Préserver l’historique et vérifier le résultat

- [ ] **Conserver les anciens modèles.** Ne pas écraser un modèle utilisé. Conserver ses sections, critères et liaisons pour relire les évaluations passées. Créer un nouveau modèle même pour une petite modification. Une même structure peut aussi être utilisée sur plusieurs années. Fichiers : modèle SQL, contrôleur.
- [ ] **Tester la grille avant validation.** Simuler son affichage, la saisie des notes, leurs maxima et la note calculée, sans enregistrer les notes de test. Fichiers : vue de simulation, contrôleur.
- [ ] **Vérifier les deux scénarios du sujet.** Portfolio : partir d’un modèle vierge et créer 2 sections de 3 critères. Soutenance : copier le modèle précédent puis modifier les notes maximales des critères. Dans les deux cas, les anciennes structures doivent rester consultables. Fichiers : interface complète, base de test.
- [ ] **Rejouer les erreurs déjà rencontrées.** Vérifier les corrections `anneDebut` → `anneeDebut` et `anneeFin` → `fin`, l’affichage unique de la vue et l’apparition du formulaire après le clic sur « Sélectionner ». Fichiers : `models/ModeleGrille.php`, contrôleur.

**Note de suivi :** les cases cochées reflètent le code et les explications montrés dans la conversation, pas une inspection du dépôt actuel. Cochez les autres cases après un essai concluant.

**Source :** sujet R3.07 2026–2027, p. 3 (composition), p. 6 (évolution et historique), p. 12 (section 5.1) et p. 17 (scénario du 20 septembre).

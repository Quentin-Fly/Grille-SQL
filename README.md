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

- [x] **Créer un nouveau critère.** Enregistrer `descCourte` et `descLongue` dans `CriteresEval`.
- [x] **Associer le critère au modèle.** Enregistrer `IdCritere`, `IdModeleEval`, `ValeurMaxCritereEval` et `NumOrdre` dans `ModeleContenirCriteres`.
- [x] **Utiliser une transaction.** La création du critère et son association doivent réussir ou être annulées ensemble.
- [x] **Calculer l’ordre suivant.** Attribuer le prochain `NumOrdre` disponible pour le modèle.
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

- [ ] **Simuler une grille sans enregistrer les notes.** Aucune simulation trouvée dans le code actuel ; ancienne coche retirée.
- [x] **Tester les erreurs de création.** Champs invalides, doublons, identifiant inexistant et indisponibilité de la base.
- [x] **Tester un modèle vierge.** Création, sélection, affichage et message d’absence de critères.
- [ ] **Tester un modèle complet.** Ajout, affichage ordonné, modification et retrait des critères.
- [ ] **Tester la copie complète.**
- [ ] **Corriger la journalisation.** Utiliser `error_log()` et ne pas afficher les détails PDO.

## Écart à clarifier avec l’enseignant

- [ ] **Gestion des sections.** Le sujet prévoit 1 à 3 sections et le scénario Portfolio demande 2 sections de 3 critères. La base actuelle ne permet plus d’associer un critère à une section.

**Sources :** sujet R3.07 2026–2027, section 5.1, règles d’évolution et d’historique, scénario du 20 septembre et mise à jour SQL transmise par l’enseignant.

## Point de conformité — section 5.1 (6 octobre 2026)

Comparaison de la page 12 du sujet avec le code actuel. Une fonctionnalité implémentée reste à tester avec la base si son fonctionnement n’a pas été confirmé. Ce périmètre ne comprend pas les ressources de la section 5.2.

| Demande de l’enseignant | Code actuel | Reste à faire |
|---|---|---|
| Créer un modèle vierge et préciser sa nature parmi les cinq types | Implémenté ; création et consultation confirmées par l’utilisateur | Terminer les tests d’erreurs |
| Copier la structure de l’année écoulée | Choix présent, copie non fonctionnelle | Définir `copierCriteres()` ; créer et copier dans une transaction unique |
| Ajouter et supprimer des sections | Absent ; liaison directe entre modèle et critères dans la base utilisée par le code | Clarifier avec l’enseignant l’écart entre le sujet et la base actualisée |
| Retirer un critère de la grille | Absent | Supprimer uniquement la liaison dans `ModeleContenirCriteres` |
| Ajouter un critère existant | Modèle, vue et contrôleur implémentés | Tester insertion, doublon, points et actualisation des listes |
| Créer un critère et l’ajouter à la grille | Implémenté ; ajout et affichage confirmés par l’utilisateur | Compléter les tests, notamment l’ordre |
| Faire évoluer l’intitulé d’un critère ou sa note maximale | Absent | Ajouter l’édition des textes et des points en préservant l’historique |
| Modifier un nouveau modèle tant qu’il n’est pas utilisé | Ajouts possibles sans contrôle d’utilisation | Détecter l’utilisation et protéger toutes les modifications côté serveur |
| Créer un nouveau modèle pour les évolutions annuelles, même minimes | Copie et protection de l’historique absentes | Conserver les anciennes structures et leurs critères |
| Tester la grille en création sans enregistrer les notes | Aucune simulation trouvée | Formulaire temporaire et calcul de note ; factorisation avec le front office proposée comme idéal |

### Précisions sur les exigences

- La modification des intitulés est explicitement citée en 5.1 ; elle manquait dans notre checklist.
- Les critères peuvent être partagés. Pour modifier un texte sans changer les anciens modèles, nous proposons de créer une nouvelle version du critère et de remplacer seulement sa liaison dans le modèle modifiable. C’est un choix technique, pas une méthode imposée par le sujet.
- Le sujet propose trois méthodes de détection d’utilisation : chercher dans les cinq tables d’évaluation, choisir la table selon la nature, ou utiliser un indicateur entretenu par trigger. Le trigger n’est pas obligatoire pour cette détection.
- Le passage sur la modification demande aussi de rechercher les notes dans `LesCriteresNotes...` lorsqu’une évaluation référence le modèle. Distinguer les notes déjà saisies de la simple existence d’une évaluation ; clarifier la règle de verrouillage si nécessaire.
- La section 2.5.1 précise 1 à 3 sections, 1 à 5 critères par section et au moins 0,5 point maximum par critère. La somme des points maximum peut dépasser la note maximale de la grille : la note obtenue est normalisée.
- L’année automatique, le changement d’ordre et le filtrage HTML figurent dans notre checklist, mais ne sont pas explicitement imposés par le texte de la section 5.1. Vérifier les autres consignes avant de les présenter comme des obligations de cette section.

### Checklist actualisée complémentaire

- [x] Afficher les descriptions et les points des critères : confirmé par l’utilisateur ; vérifier explicitement l’ordre.
- [ ] Tester complètement l’association d’un critère existant : code présent.
- [ ] Retirer un critère d’un modèle.
- [ ] Modifier les points maximum d’un critère.
- [ ] Modifier les intitulés/descriptions sans changer les anciens modèles.
- [ ] Copier les modèles et associations en une seule transaction.
- [ ] Vérifier l’utilisation du modèle et les notes déjà saisies selon la règle du sujet.
- [ ] Bloquer les modifications interdites côté serveur, même si la requête contourne l’interface.
- [ ] Vérifier que copie et modification préservent la source et l’historique.
- [ ] Implémenter la simulation sans enregistrement des notes.
- [ ] Clarifier puis traiter les sections.


Sources : sujet R3.07 2026–2027, page 12 (§5.1), page 3 (§2.5.1), et fichiers PHP actuels. L’ancienne checklist ci-dessus reste le détail des tâches ; ce point précise leur conformité et leur niveau de vérification.
# Section 5.4 — Outils d'analyse des résultats

Mise à jour : 8 octobre 2026.

## Objectif et périmètre

Cette partie permet aux utilisateurs du back-office de consulter les évaluations terminées, les fiches de notes, les moyennes, les alertes sur les notes manquantes et la répartition géographique des stages sur plusieurs années.

Elle concerne uniquement la section 5.4 du sujet, page 14. La saisie des notes, leur calcul, la validation des grilles, leur diffusion, les ressources et la connexion des utilisateurs sont considérés comme pris en charge par les autres parties. Le module utilise ces données et ne les modifie pas.

## État actuel

- [x] Relire les exigences de la section 5.4.
- [x] Préparer un jeu de données fictives pour les consultations et statistiques.
- [x] Vérifier le jeu de données : 15 contrôles de cohérence réussis sur tables temporaires.
- [x] Importer le jeu fictif dans `grille_sql_test_54_20261008` et vérifier son accès depuis 5.4.
- [x] Créer les fichiers PHP et les vues ; les vues de résultats et de fiche sont remplies ; la moyenne de stage est affichée ; les moyennes par type sont affichées ; moyennes par enseignant, alertes et répartition restent à réaliser.
- [x] Configurer la connexion à la base de test séparée, conforme aux colonnes de `DB_OK.sql`.
- [x] Récupérer les années et valider le filtre, avec conservation de l'année choisie.
- [x] Écrire et brancher `getResultatsParAnnee` : requête de résultats `BLOQUEE` ou `DIFFUSEE`, filtrée par année.
- [x] Afficher ces résultats dans `resultatsStages.php`.

## 5.4.1 — Résultats des stages

### Consultations

- [x] Afficher les évaluations terminées.
- [x] Proposer la sélection d'un étudiant pour afficher sa fiche complète.
- [x] Afficher les notes disponibles : stage, entreprise, tuteur, rapport, soutenance et portfolio.
- [x] Afficher l'anglais pour les BUT3 lorsqu'une évaluation existe.
- [x] Distinguer une note absente (`NULL`) d'un zéro.

### Moyennes

- [x] Afficher la moyenne de stage pour l'année sélectionnée (BUT2 et BUT3 réunis, évaluations `BLOQUEE` ou `DIFFUSEE`).
- [ ] Afficher les moyennes par enseignant.
- [x] Afficher les moyennes par type : stage, entreprise, tuteur, soutenance, rapport, portfolio et anglais.
- [x] Proposer un filtre « Tous les types » ou un type précis, avec conservation du choix et de l'année.
- [x] Afficher le nombre de notes pris en compte dans la moyenne de stage.
- [x] Afficher le nombre de notes pour les moyennes par type.
- [ ] Afficher le nombre de notes pour les moyennes par enseignant.
- [x] Afficher « Aucune note disponible » lorsque la moyenne de stage ne dispose d'aucune note.
- [x] Afficher « Aucune note » pour les types sans note renseignée.
- [ ] Appliquer ce traitement aux moyennes par enseignant.

Le sujet ne précise pas tous les filtres des moyennes. Définir et documenter les statuts retenus, l'année et les parcours concernés. Pour les enseignants, préciser le rôle considéré : enseignant tuteur, second enseignant ou enseignant d'anglais. Ne pas compter plusieurs fois une note à cause des jointures avec les critères.

### Alertes

- [ ] Repérer les soutenances passées dont les grilles ne sont pas validées.
- [ ] Signaler une note de rapport manquante pour compléter la grille de stage.
- [ ] Signaler une note de soutenance manquante ou une évaluation de jury incomplète.
- [ ] Signaler une note de portfolio manquante après la soutenance concernée.
- [ ] Afficher l'étudiant et les informations manquantes dans chaque alerte.
- [ ] Éviter les alertes de retard pour les soutenances futures.

## 5.4.2 — Analyse géographique des stages

- [ ] Afficher la répartition des stages par département ou par région.
- [ ] Afficher l'évolution de cette répartition sur plusieurs années.
- [ ] Vérifier les codes postaux absents ou inutilisables.

Le schéma fourni possède `entreprises.codePostal` et `villeE`, mais aucune colonne région. Commencer par les départements est conforme au sujet. Définir les cas particuliers avant d'étendre le traitement aux codes postaux corses ou ultramarins ; les deux premiers caractères ne suffisent pas à tous les cas.

## Organisation proposée des fichiers

```text
5.4/
├── README.md
├── index.php
├── config/
│   └── database.php
├── models/
│   └── modeleAnalyseResultats.php
├── controllers/
│   └── analyseResultatsController.php
└── view/
    └── analyse_resultats/
        ├── index.php
        ├── filtres.php
        ├── resultatsStages.php
        ├── ficheEtudiant.php
        ├── moyennes.php
        ├── alerte.php
        ├── repartitionStages.php
        └── style.css
```

| Fichier | Rôle |
|---|---|
| `index.php` | Point d'entrée local : charge le contrôleur. |
| `config/database.php` | Connexion PDO à `grille_sql_test_54_20261008`, base de test conforme aux tables de `DB_OK.sql`. Pour l'intégration finale : connexion commune à `stagebdmerge`. |
| `models/modeleAnalyseResultats.php` | Requêtes SELECT pour les résultats, moyennes, alertes et statistiques géographiques. |
| `controllers/analyseResultatsController.php` | Valide les filtres, appelle le modèle et prépare les variables des vues. |
| `view/analyse_resultats/index.php` | Assemble les vues et affiche les messages. |
| `view/analyse_resultats/filtres.php` | Sélection de l'année et du type d'évaluation ; le filtre de parcours reste à ajouter si retenu. La sélection d'étudiant se fait par le lien « Voir la fiche » dans la liste. |
| `view/analyse_resultats/resultatsStages.php` | Tableau des évaluations terminées. |
| `view/analyse_resultats/ficheEtudiant.php` | Fiche complète de l'étudiant sélectionné. |
| `view/analyse_resultats/moyennes.php` | Moyennes de promotion, par enseignant et par type d'évaluation. |
| `view/analyse_resultats/alerte.php` | Liste des notes manquantes après les soutenances. |
| `view/analyse_resultats/repartitionStages.php` | Répartition géographique et comparaison des années. |
| `view/analyse_resultats/style.css` | Style du module repris des autres sections. |

Les filtres et consultations peuvent utiliser GET, puisqu'ils ne modifient pas la base. Les fonctions du modèle utilisent des requêtes préparées et les vues échappent les textes affichés.

## Tables et champs à utiliser

- `anneesuniversitaires.anneeDebut` : années disponibles.
- `etudiantsbut2ou3` : identité des étudiants.
- `anneestage` : année, étudiant, entreprise et parcours (`but3sinon2`).
- `enseignants` : identité des enseignants.
- `entreprises` : ville et code postal.
- `evalstage` : `noteStage`, `noteEntreprise`, `noteTuteur`, `noteRapport`, `noteSoutenance`, notes des deux enseignants, `date_h` et `Statut`.
- `evalrapport` et `evalportfolio` : colonne `note` et statut.
- `evalanglais` : colonne `note`, date `dateS` et enseignant ; concerne les BUT3.
- `evalsoutenanceenstuteur` : colonne `noteEnsTut` et enseignant.
- `evalsoutenanceenssecond` : colonne `noteEnsSecond` et enseignant.

Relier les évaluations à un stage avec l'étudiant ET l'année. Un même étudiant peut apparaître sur plusieurs années. L'enseignant d'un rapport n'est pas directement stocké dans `evalrapport` : toute attribution par rôle devra passer par le stage et être documentée.

## Jeu de données fictives

Le script généré `donnees_test_5_4.sql` crée une base autonome `grille_sql_test_54_20261008` avec 18 étudiants, 3 enseignants, 4 entreprises et 4 années universitaires. Il ne modifie pas `stagebdmerge`.

Les structures proviennent de `DB_OK.sql`, sans les triggers : les notes sont déjà préparées pour tester les lectures et statistiques de 5.4. Ce jeu ne valide pas les traitements des autres modules.

Choisir 2025-2026 (`anneeDebut = 2025`) pour les résultats terminés et les alertes. Les soutenances de 2026-2027 sont futures au 8 octobre 2026.

Résultats de référence :

- Sur les quatre stages `BLOQUEE` de 2025-2026 : moyenne de stage de 15/20.
- Sur les quatre portfolios `BLOQUEE` de 2025-2026 : moyenne de 9,75/20, avec un vrai zéro.
- Pour les huit stages 2025-2026 : deux stages dans chacun des départements 75, 69, 33 et 59.

Ces moyennes supposent le filtre indiqué ; inclure des notes encore en saisie produit d'autres résultats.

## Tests à réaliser sur le module

- [x] Vérifier la syntaxe des 11 fichiers PHP et l'absence d'avertissements dans les traitements déjà implémentés.
- [x] Vérifier la récupération des résultats sur plusieurs années et l'affichage des fiches BUT2/BUT3.
- [x] Vérifier qu'une demande de fiche pour une année où l'étudiant n'a pas de stage est refusée.
- [ ] Tester un même étudiant avec des stages sur plusieurs années.
- [x] Comparer les moyennes de stage et de portfolio aux valeurs de référence : 15 et 9,75 pour 2025-2026.
- [ ] Vérifier les valeurs de référence des autres types et des moyennes par enseignant.
- [x] Vérifier dans la fiche que `NULL` affiche « Non renseignée » et que zéro reste affiché.
- [x] Vérifier le zéro du portfolio dans la moyenne 9,75 sur 4 notes et les moyennes NULL avec zéro note en 2026-2027.
- [ ] Vérifier les moyennes par enseignant et l'absence de doublons dus aux jointures.
- [ ] Tester les alertes avec des notes manquantes et des soutenances passées ou futures.
- [ ] Comparer les effectifs géographiques au jeu fictif.
- [x] Tester une année sans données et un étudiant inexistant.
- [ ] Vérifier que les textes affichés sont échappés et les filtres validés.
- [ ] Vérifier l'affichage dans le navigateur.

## Ordre de réalisation

1. Connexion et point d'entrée local.
2. Modèle : récupération des années et des évaluations terminées.
3. Contrôleur et vue : affichage de la première liste.
4. Filtres et fiche complète d'un étudiant.
5. Moyennes.
6. Alertes.
7. Analyse géographique sur plusieurs années.
8. Tests, style et intégration au back-office commun.

## Dépendances avec le groupe

Réutiliser leur connexion utilisateur, leurs statuts et leurs notes finales. La protection de la route et la navigation commune seront raccordées lors de l'intégration. Ne pas développer la saisie, la diffusion, la gestion des comptes ou les triggers dans cette partie.

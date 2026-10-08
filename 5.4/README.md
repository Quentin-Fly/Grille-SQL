# Section 5.4 — Outils d'analyse des résultats

Mise à jour : 8 octobre 2026.

## Objectif et périmètre

Cette partie permet aux utilisateurs du back-office de consulter les évaluations terminées, les fiches de notes, les moyennes, les alertes sur les notes manquantes et la répartition géographique des stages sur plusieurs années.

Elle concerne uniquement la section 5.4 du sujet, page 14. La saisie des notes, leur calcul, la validation des grilles, leur diffusion, les ressources et la connexion des utilisateurs sont considérés comme pris en charge par les autres parties. Le module utilise ces données et ne les modifie pas.

## État actuel

- [x] Relire les exigences de la section 5.4.
- [x] Préparer un jeu de données fictives pour les consultations et statistiques.
- [x] Vérifier le jeu de données : 15 contrôles de cohérence réussis sur tables temporaires.
- [ ] Importer le jeu fictif et vérifier son accès depuis le futur module.
- [ ] Créer les fichiers PHP et les vues décrits ci-dessous.

## 5.4.1 — Résultats des stages

### Consultations

- [ ] Afficher les évaluations terminées.
- [ ] Proposer la sélection d'un étudiant pour afficher sa fiche complète.
- [ ] Afficher les notes disponibles : stage, entreprise, tuteur, rapport, soutenance et portfolio.
- [ ] Afficher l'anglais pour les BUT3 lorsqu'une évaluation existe.
- [ ] Distinguer une note absente (`NULL`) d'un zéro.

### Moyennes

- [ ] Afficher la moyenne actuelle de promotion.
- [ ] Afficher les moyennes par enseignant.
- [ ] Afficher les moyennes par type d'évaluation.
- [ ] Identifier le nombre de notes pris en compte dans chaque moyenne.
- [ ] Traiter les groupes sans note disponible sans afficher une moyenne de zéro.

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
│   └── analyseResultats.php
├── controllers/
│   └── analyseResultatsController.php
└── view/
    └── analyse_resultats/
        ├── index.php
        ├── filtres.php
        ├── resultatsStages.php
        ├── ficheEtudiant.php
        ├── moyennes.php
        ├── alertes.php
        ├── repartitionStages.php
        └── style.css
```

| Fichier | Rôle |
|---|---|
| `index.php` | Point d'entrée local : charge le contrôleur. |
| `config/database.php` | Connexion PDO ; utilise la base fictive pour les essais. Lors de la fusion, réutiliser la connexion commune. |
| `models/analyseResultats.php` | Requêtes SELECT pour les résultats, moyennes, alertes et statistiques géographiques. |
| `controllers/analyseResultatsController.php` | Valide les filtres, appelle le modèle et prépare les variables des vues. |
| `view/analyse_resultats/index.php` | Assemble les vues et affiche les messages. |
| `view/analyse_resultats/filtres.php` | Sélection de l'année, du parcours et de l'étudiant. |
| `view/analyse_resultats/resultatsStages.php` | Tableau des évaluations terminées. |
| `view/analyse_resultats/ficheEtudiant.php` | Fiche complète de l'étudiant sélectionné. |
| `view/analyse_resultats/moyennes.php` | Moyennes de promotion, par enseignant et par type d'évaluation. |
| `view/analyse_resultats/alertes.php` | Liste des notes manquantes après les soutenances. |
| `view/analyse_resultats/repartitionStages.php` | Répartition géographique et comparaison des années. |
| `view/analyse_resultats/style.css` | Style du module, à reprendre depuis les autres sections. |

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

- [ ] Vérifier la syntaxe PHP et l'absence d'avertissements.
- [ ] Vérifier les résultats avec plusieurs années et les parcours BUT2/BUT3.
- [ ] Vérifier qu'une fiche ne mélange pas les années d'un étudiant.
- [ ] Comparer les moyennes aux valeurs de référence du jeu fictif.
- [ ] Vérifier que les notes nulles sont exclues des moyennes et que zéro est conservé.
- [ ] Vérifier les moyennes par enseignant et l'absence de doublons dus aux jointures.
- [ ] Tester les alertes avec des notes manquantes et des soutenances passées ou futures.
- [ ] Comparer les effectifs géographiques au jeu fictif.
- [ ] Tester une année sans données et un étudiant inexistant.
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
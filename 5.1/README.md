# Grille SQL — suivi de la section 5.1

Mise à jour : 7 octobre 2026. Périmètre : modèles de grille, section 5.1 du sujet R3.07 2026–2027. La section 5.2 sur les ressources est hors périmètre.

## Bilan des tests du 7 octobre

**51 vérifications automatisées réussies**, sur MySQL/MariaDB local avec des tables temporaires isolées dans une connexion dédiée. Aucune donnée persistante de la base du projet n’a été modifiée. Les huit fichiers PHP passent la vérification de syntaxe.

Les tests exécutent les fonctions réelles du modèle et les traitements réels du contrôleur, avec rendu des vues. Seuls le chargement de la connexion et le chemin des vues sont adaptés dans une copie de test du contrôleur. Les avertissements PHP sont traités comme des erreurs.

### Parcours validés

- [x] Création vierge, création de l’année et fermeture de transaction.
- [x] Sélection et affichage d’une grille vide.
- [x] Création de critères, affichage et échappement HTML.
- [x] Copie MySQL : conservation des critères, points et numéros d’ordre, y compris un ordre avec des trous (1, 3).
- [x] Conservation de la nature du modèle source.
- [x] Ouverture du formulaire de modification.
- [x] Modification regroupée des textes et points ; nouvelle version du critère, source intacte et ordre conservé.
- [x] Retrait de la liaison sans supprimer le critère partagé.
- [x] Retrait d’une liaison inexistante sans annoncer un faux succès.
- [x] Association d’un critère existant et refus du doublon.
- [x] Ancien traitement séparé des points : enregistrement et fermeture de transaction.
- [x] Refus des identifiants invalides ou inexistants.
- [x] Validation des descriptions : courte obligatoire, 100 caractères maximum, longue de 500 caractères maximum, accents compris.
- [x] Refus des points invalides ou inférieurs à 0,5, sans écriture.
- [x] Refus d’une source de copie inexistante ou invalide.
- [x] Refus du doublon de nom et conservation du choix de copie dans le formulaire.
- [x] Annulation complète de la copie sur erreur de création du modèle, y compris l’année créée.
- [x] Annulation complète sur échec de copie des associations.
- [x] Annulation de la nouvelle version du critère si la liaison à remplacer n’existe pas.

### Protection validée

- [x] Détection d’une référence dans chaque table actuelle : anglais, rapport, portfolio, soutenance tuteur et soutenance second enseignant.
- [x] Pour chacun des cinq cas, une ligne de note NULL ne bloque pas ; une note de zéro bloque.
- [x] Blocage de la création d’un critère lorsque le modèle possède des notes.
- [x] Blocage de l’association d’un critère existant.
- [x] Blocage du retrait.
- [x] Blocage de la modification séparée des points.
- [x] Blocage de la modification regroupée des textes et points.
- [x] Pour ces refus : aucun changement dans les tables de travail, aucun message de succès et grille toujours affichée.
- [x] Copie d’un modèle protégé autorisée ; la copie ne possède aucune note et peut être modifiée.

### Portée et limites de la vérification

- La base réelle et son schéma ont été inspectés en lecture seule. Les requêtes de détection corrigées y ont été vérifiées : les modèles RAPPORT 3 et 8 ne possèdent aucune note lors de l’inspection du 7 octobre.
- Les tests d’écriture utilisent des tables temporaires reproduisant les tables de travail. Les clés étrangères de ces copies sont retirées et les tables d’évaluation sont réduites aux champs nécessaires : les relations avec étudiants, enseignants et salles ne sont donc pas validées par cette suite.
- Il s’agit de tests serveur avec rendu HTML, pas d’une automatisation du navigateur : le clic réel, le JavaScript, la confirmation de retrait et l’apparence CSS restent à vérifier manuellement.
- La disponibilité d’une connexion refusée ou d’un serveur arrêté n’a pas été testée.
- Le cas STAGE n’est pas couvert : dans la base actuelle, EvalStage ne contient pas IdModeleEval et aucune table LesCriteresNotesStage n’existe. Ne pas présenter sa protection comme terminée.

## Fonctionnalités présentes

- [x] Connexion PDO, architecture MVC, sélection et consultation des modèles.
- [x] Cinq natures de modèle : ANGLAIS, RAPPORT, SOUTENANCE, STAGE, PORTFOLIO.
- [x] Création vierge avec validation et contrôle des doublons de nom et nature/année.
- [x] Création et association des critères, ordre automatique.
- [x] Ajout de critères existants depuis une liste filtrée.
- [x] Retrait de la liaison uniquement, résultat contrôlé avec rowCount().
- [x] Modification des descriptions et points via un formulaire unique, bouton Modifier à côté de Retirer.
- [x] Copie avec copierModele(), INSERT ... SELECT et transaction commune avec addModele(). L’ancien appel à copierCriteres() a été remplacé.
- [x] Détection des références et des notes selon le schéma réel, avec deux tables de soutenance.
- [x] Protection des écritures côté contrôleur pour les modèles possédant des notes, hors limite STAGE.
- [x] Style CSS provisoire.
- [x] Correction de $error_log(...) en error_log(...).

## Demandes de la section 5.1 et restant

| Demande | État actuel | Reste |
|---|---|---|
| Structure vierge et choix de nature | Implémenté et testé | Vérification navigateur |
| Copie d’une structure existante | Implémenté et testé sur MySQL isolé | Vérification navigateur |
| Ajouter des critères nouveaux ou existants | Implémenté et testé | Cas STAGE à clarifier pour la protection |
| Retirer des critères | Implémenté et testé | Même limite STAGE |
| Modifier intitulés et points | Implémenté et testé, anciens critères conservés | Même limite STAGE |
| Préserver les modèles utilisés et leur historique | Protection contrôleur et préservation de la source testées | Clarifier STAGE et valider sur les relations complètes du schéma |
| Ajouter/supprimer les sections | Non implémenté | Clarifier le sujet avec la base actualisée |
| Simuler l’utilisation sans enregistrer les notes | Non implémenté | Formulaire temporaire et calcul normalisé |


## Règles retenues

- Les changements annuels nécessitent un nouveau modèle, même pour une modification mineure.
- La protection actuelle repose sur les notes de critères réellement renseignées, pas sur la seule existence d’une évaluation. La fonction modeleEstUtilise() détecte séparément une référence.
- Un critère partagé n’est pas supprimé lors d’un retrait. Pour modifier ses textes, une nouvelle version remplace uniquement la liaison ciblée.
- La somme des points maximum peut dépasser la note maximale de la grille : note finale = somme des notes / somme des maximums × note maximale de la grille (§2.5.1).
- Les triggers ne sont pas obligatoires pour la détection d’utilisation. Aucun trigger n’était installé lors de l’inspection de la base.

**Sources :** sujet R3.07 2026–2027, page 12 (§5.1), page 3 (§2.5.1), code actuel, schéma réel et tests du 7 octobre 2026.
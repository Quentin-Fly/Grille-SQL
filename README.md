# Grille SQL — suivi de la section 5.1

Mise à jour : 6 octobre 2026. Périmètre : définir et faire évoluer les modèles de grille, page 12 du sujet R3.07 2026–2027 ; règles de calcul et de conservation associées. La section 5.2 (ressources) est hors périmètre.

**Légende :** `[x]` implémenté et vérifié par lecture du code ; `[ ]` restant. Les tests avec la base sont suivis séparément : présence du code et syntaxe valide ne prouvent pas un fonctionnement complet.

## Fonctionnalités implémentées

- [x] Connexion PDO et architecture MVC.
- [x] Liste et sélection des modèles, validation de leur identifiant.
- [x] Cinq natures : ANGLAIS, RAPPORT, SOUTENANCE, STAGE, PORTFOLIO.
- [x] Création vierge : année recherchée/créée, identifiant automatique, transaction, validation des champs et messages de doublon.
- [x] Affichage du modèle, des descriptions, des points et de l’état sans critère.
- [x] Lecture des critères triée par NumOrdre et calcul du prochain ordre lors des ajouts.
- [x] Création d’un nouveau critère et association transactionnelle au modèle.
- [x] Sélection d’un critère existant parmi ceux encore disponibles, validation et association.
- [x] Retrait de la liaison dans ModeleContenirCriteres uniquement ; critère conservé ; résultat contrôlé avec rowCount().
- [x] Bouton Modifier à côté de Retirer, formulaire unique prérempli : description courte, description longue et points maximum.
- [x] Validation côté serveur : identifiants positifs, appartenance au modèle, description courte obligatoire et limitée à 100 caractères, longue limitée à 500, points au moins égaux à 0,5.
- [x] Modification regroupée dans une transaction : création d’une nouvelle version du critère et remplacement de la liaison ciblée avec les nouveaux points ; ordre et ancien critère conservés.
- [x] Actualisation des listes après les opérations et affichage des messages.

La création d’une nouvelle version protège les autres modèles qui partagent le critère, mais ne protège pas encore le modèle courant s’il a déjà été utilisé pour des évaluations.

## Exigences de l’enseignant face au code actuel

| Demande de la section 5.1 | État | Reste à faire |
|---|---|---|
| Créer une structure vierge et préciser sa nature | Implémenté | Compléter les tests |
| Copier une structure de l’année écoulée | Choix dans la vue, copie non fonctionnelle | Définir copierCriteres() et transaction commune création/copie |
| Ajouter et supprimer des sections | Absent du code actuel | Clarifier le schéma actualisé avec l’enseignant, puis adapter si nécessaire |
| Ajouter des critères existants ou nouveaux | Implémenté | Tester complètement l’association |
| Retirer des critères | Implémenté | Tester le partage et une liaison inexistante |
| Faire évoluer les intitulés et notes maximales | Implémenté via le formulaire regroupé | Tester persistance et préservation des autres modèles |
| Modifier un nouveau modèle non utilisé | Aucun contrôle d’utilisation | Détecter utilisation et protéger toutes les mutations côté serveur |
| Préserver les structures des années précédentes | Préservation du critère partagé implémentée ; copie et verrouillage absents | Copie, protection et tests d’historique |
| Simuler la grille sans enregistrer les notes | Absent | Saisie temporaire, calcul et présentation ; factorisation proposée comme idéal |

## Copie, modification et historique : restant principal

- [ ] Implémenter copierCriteres() : appelée dans le contrôleur mais absente du modèle.
- [ ] Créer le modèle et copier les associations dans une seule transaction ; addModele() valide actuellement avant la copie.
- [ ] Reprendre valeurs maximales et ordre, sans modifier la source.
- [ ] Détecter si le modèle est référencé par des évaluations, et vérifier les notes déjà saisies selon les règles du sujet.
- [ ] Appliquer la protection sur tous les ajouts, retraits et modifications, même lors d’un envoi direct au contrôleur.
- [ ] Préserver la consultation et les données historiques après copie et modification.
- [ ] Simuler une évaluation sans enregistrer de notes, avec calcul normalisé.
- [ ] Clarifier et traiter la gestion des sections : le sujet prévoit 1 à 3 sections, 1 à 5 critères par section ; le code utilise une liaison directe modèle/critère.

## Vérification fonctionnelle

- [x] Création et sélection d’un modèle vierge : fonctionnement confirmé par l’utilisateur.
- [x] Ajout d’un nouveau critère et affichage des descriptions et points : fonctionnement confirmé par l’utilisateur.
- [x] Syntaxe PHP des dernières modifications contrôlée.
- [x] Tester explicitement l’ordre des critères réels. — Validé par tes tests (6 octobre 2026).
- [x] Tester association d’un critère existant, doublon et actualisation de la liste disponible. — Validé par tes tests (6 octobre 2026).
- [x] Tester retrait d’un critère partagé : conservation dans la seconde grille et disponibilité dans la première. — Validé par tes tests (6 octobre 2026).
- [x] Tester une liaison inexistante : aucun faux succès de retrait. — Validé par tes tests (6 octobre 2026).
- [x] Tester la modification regroupée et sa persistance après rechargement. — Validé par tes tests (6 octobre 2026).
- [x] Tester les textes invalides et les points inférieurs à 0,5 ; vérifier la conservation des saisies en erreur. — Validé par tes tests (6 octobre 2026).
- [x] Vérifier que le remplacement d’un texte conserve l’ordre et préserve les autres modèles. — Validé par tes tests (6 octobre 2026).
- [ ] Tester copie complète et annulation en cas d’échec.
- [ ] Tester protection des modèles utilisés et historique.
- [ ] Tester la simulation sans écriture de notes.
- [x] Refaire les cas d’erreurs de création : invalidité, doublons, identifiant inexistant, indisponibilité de la base. — Validé par tes tests (6 octobre 2026).

Les tests des fonctionnalités actuelles sont confirmés terminés par toi le 6 octobre 2026. Les tests de copie, de protection et de simulation restent ouverts, car ces fonctionnalités restent à implémenter. La simulation cochée dans une ancienne version était incorrecte : elle reste à réaliser.

## Corrections techniques et compléments

- [ ] Corriger $error_log(...) en error_log(...) dans le contrôleur, lors d’une erreur de création du modèle.
- [ ] Ne plus afficher les détails PDO dans le message d’échec de connexion.
- [ ] Nettoyer si utile le traitement séparé des points : la vue utilise maintenant le formulaire regroupé.
- [ ] Année universitaire automatique : tâche de notre ancienne checklist, non explicitement imposée au paragraphe 5.1.
- [ ] Modification de l’ordre : complément de notre checklist, non explicitement imposé au paragraphe 5.1.
- [ ] Mise en forme HTML des descriptions : complément à vérifier dans les autres consignes ; le code actuel affiche du texte échappé.

## Précisions du sujet

- L’évolution annuelle nécessite un nouveau modèle même pour des changements minimes. L’édition ne doit pas altérer les évaluations anciennes.
- La modification des intitulés est explicitement demandée. La nouvelle version du critère est notre choix technique pour conserver les anciennes descriptions.
- Le sujet propose trois méthodes de détection d’utilisation : rechercher dans les cinq tables d’évaluation, choisir la table selon la nature, ou employer un indicateur entretenu par trigger. Le trigger n’est pas obligatoire pour cette détection.
- Le paragraphe de modification demande aussi de vérifier les notes dans LesCriteresNotes... lorsqu’une évaluation référence le modèle. Distinguer la référence d’une évaluation des notes déjà saisies ; clarifier le verrouillage si nécessaire.
- La somme des maximums peut dépasser la note maximale de la grille : note finale = somme des notes / somme des maximums × note maximale de la grille (section 2.5.1).
- Aucun script SQL ni trigger n’est présent dans ce dépôt ; les contrôles installés dans la base ne sont pas audités.

**Sources :** sujet R3.07 2026–2027, page 12 (§5.1), page 3 (§2.5.1), règles d’évolution et d’historique, et fichiers PHP actuels.
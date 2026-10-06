<h2>
    <?php echo htmlspecialchars( $modeleSelectionne['nomModuleGrilleEvaluation'], ENT_QUOTES, 'UTF-8'); ?>
</h2>
<p>
    Nature : 
    <?php echo htmlspecialchars( $modeleSelectionne['natureGrille'], ENT_QUOTES, 'UTF-8'); ?>
</p>
<p>
    Année : 
    <?php echo (int) $modeleSelectionne['anneeDebut'] . '-' . ((int)$modeleSelectionne['anneeDebut'] + 1); ?>
</p>
<p>
    Note maximale : 
    <?php echo htmlspecialchars((string) $modeleSelectionne['noteMaxGrille'],ENT_QUOTES, 'UTF-8') ?>
</p>
<h3> Critères d'évaluation </h3>
<?php if (empty($criteresModele)) : ?>
    <p>Aucun critère d'évaluation n'est défini pour ce modèle.</p>
<?php else : ?>
    <!-- Affichage des critères d'évaluation -->
    <table>
        <thead>
            <tr>
                <th>Description courte</th>
                <th>Description longue</th>
                <th>Valeur maximale</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($criteresModele as $critere) : ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($critere['descCourte'], ENT_QUOTES, 'UTF-8'); ?>

                    </td>
                    <td>
                        <?php echo htmlspecialchars($critere['descLongue'], ENT_QUOTES, 'UTF-8'); ?>

                    </td>

                    <td>
                        <?= htmlspecialchars((string) $critere['ValeurMaxCritereEVal'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>
                        <form method="post" action="index.php" style="display: inline;">
                            <input type="hidden" name="idModeleEval" value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">
                            <input type="hidden" name="idCritere" value="<?= (int) $critere['IdCritere'] ?>">
                            <button type="submit" name="modifierCritere">Modifier</button>
                            <button type="submit" name="retirerCritere" onclick="return confirm('Êtes-vous sûr de vouloir retirer ce critère ?');">Retirer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<!-- Choisir un critère : ce formulaire reste visible pendant l'édition du modèle. -->
<form method="post" action="index.php">
    <input type="hidden" name="idModeleEval" value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">
    <label for="critere">Critère :</label>
    <select name="critere" id="critere">
        <option value="nouveau">Créer un nouveau critère</option>
        <?php foreach ($listCritere as $critere) : ?>
            <option value="<?= (int) $critere['IdCritere'] ?>">
                <?= htmlspecialchars($critere['descCourte'], ENT_QUOTES, 'UTF-8') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="selectionnerCritere">Sélectionner</button>
</form>

<!-- Saisir les informations : ce formulaire apparaît après le choix « nouveau ». -->
<?php if ($afficherFormulaireCritere) : ?>
    <h3>Créer un nouveau critère d'évaluation</h3>
    <form method="post" action="index.php">
        <input type="hidden" name="idModeleEval" value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">

        <label for="descCourteCritere">Description courte :</label>
        <input type="text" id="descCourteCritere" name="descCourteCritere" required maxlength="100">
        <br>

        <label for="descLongueCritere">Description longue :</label>
        <input type="text" id="descLongueCritere" name="descLongueCritere" maxlength="500">
        <br>

        <label for="valeurMaxCritere">Valeur maximale :</label>
        <input type="number" id="valeurMaxCritere" name="valeurMaxCritere" min="0.5" step="0.5" required>
        <br>

        <button type="submit" name="creerCritere">Créer le critère d'évaluation</button>
    </form>
<?php endif; ?>
<!-- Afficher le formualaire d'affectation d'un critère à un modèle : ce formulaire apparaît après le choix d'un critère existant. -->
<?php if ($afficherFormulaireAssociation) : ?>
    <h3>Affecter un critère existant au modèle</h3>
    <form method="post" action="index.php">
        <input type="hidden" name="idModeleEval" value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">
        <input type="hidden" name="idCritere" value="<?= (int) $idCritereSelectionne ?>">

        <label for="valeurMaxCritere">Valeur maximale :</label>
        <input type="number" id="valeurMaxCritere" name="valeurMaxCritere" min="0.5" step="0.5" required>
        <br>

        <button type="submit" name="affecterCritere">Affecter le critère au modèle</button>
    </form>
<?php endif; ?>
<!-- Ce formulaire apparaît après un clic sur Modifier dans la colonne description. -->
<?php if ($critereAModifier !== null) : ?>
    <h3>Modifier le critère</h3>
    <form method="post" action="index.php">
        <input type="hidden" name="idModeleEval" value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">
        <input type="hidden" name="idCritere" value="<?= (int) $critereAModifier['IdCritere'] ?>">

        <label for="descCourteModification">Description courte :</label>
        <input type="text" id="descCourteModification" name="descCourteCritere" value="<?= htmlspecialchars((string) $critereAModifier['descCourte'], ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
        <br>

        <label for="descLongueModification">Description longue :</label>
        <textarea id="descLongueModification" name="descLongueCritere" maxlength="500"><?= htmlspecialchars((string) ($critereAModifier['descLongue'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        <br>

        <label for="pointsModification">Valeur maximale :</label>
        <input type="number" id="pointsModification" name="valeurMaxCritere" value="<?= htmlspecialchars((string) $critereAModifier['ValeurMaxCritereEVal'], ENT_QUOTES, 'UTF-8') ?>" min="0.5" step="0.5" required>
        <br>
        <button type="submit" name="modifierDescriptionCritere">Enregistrer les modifications</button>
    </form>
<?php endif; ?>
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
            </tr>
        </thead>
        <tbody>
            <?php foreach ($criteresModele as $critere) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($critere['descCourte'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($critere['descLongue'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars((string) $critere['ValeurMaxCritereEVal'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<!-- Formulaire de proposition de création de critere -->
<form method="post" action="index.php">
    <input type="hidden" name="idModeleEval" value="<?php echo (int) $modeleSelectionne['IdModeleEval']; ?>">

    <button type="submit" name="afficherCreationCritere"> Nouveau critère d'évaluation </button>
</form>
<?php if ($afficherFormulaireCritere) : ?>
    <h3> Créer un nouveau critère d'évaluation </h3>
    <!-- Formulaire pour créer un nouveau critère -->
    <form method="post" action="index.php">
        <input type="hidden" name="idModeleEval"
            value="<?= (int) $modeleSelectionne['IdModeleEval'] ?>">

        <label for="critere">Critère :</label>
        <select name="critere" id="critere">
            <option value="nouveau">Créer un nouveau critère</option>

            <?php foreach ($listCritere as $critere) : ?>
                <option value="<?= (int) $critere['IdCritere'] ?>">
                    <?= htmlspecialchars($critere['descCourte'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="selectionnerCritere">
            Sélectionner
        </button>
    </form>
<?php endif; ?>
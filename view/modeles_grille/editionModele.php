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
        <input type="hidden" name="idModeleEval" value="<?php echo (int) $modeleSelectionne['IdModeleEval']; ?>">
    
        <!-- Description courte (100 caractères max et obligatoire) -->
        <label for="descCourteCritere">Description courte :</label>
        <input type="text" id="descCourteCritere" name="descCourteCritere" required maxlength="100">
        <br>
    
        <!-- Description longue (500 caractères max et facultative) -->
        <label for="descLongueCritere">Description longue :</label>
        <input type="text" id="descLongueCritere" name="descLongueCritere" maxlength="500">
        <br>
    
        <!-- Valeur maximale (obligatoire, min 1) -->
        <label for="valeurMaxCritere">Valeur maximale :</label>
        <input type="number" id="valeurMaxCritere" name="valeurMaxCritere" min="0.5" step="0.5" required>
        <br>

        <button type="submit" name="creerCritere"> Créer le critère d'évaluation </button>
    </form>
<?php endif; ?>
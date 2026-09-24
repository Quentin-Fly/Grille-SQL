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

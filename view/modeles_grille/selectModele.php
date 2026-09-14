<form method="post">  
    <select name="modele" id="modele">

        <option value="nouveau">
            Créer un nouveau modèle
        </option>

        <?php foreach ($listModele as $modele) : ?>

            <option value="<?= $modele['IdModeleEval'] ?>">
                <?= $modele['natureGrille'] ?>
                -
                <?= $modele['nomModuleGrilleEvaluation'] ?>
                -
                <?= $modele['anneeDebut'] ?>
            </option>

        <?php endforeach; ?>

    </select>
    <button type="submit" name="selectionnerModele" value="submit">
        Selectionner
    </button>
</form>
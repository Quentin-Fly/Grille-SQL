<!-- Formulaire de création de modèle de grille -->

<form method="post" action="">
    <fieldset>
        <legend>Point de départ</legend>
        <label>
            <input type="radio" name="pointDepart" value="vierge" checked onchange="toggleSourceModele()">
            Structure vierge
        </label>
        <label>
            <input type="radio" name="pointDepart" value="copie" onchange="toggleSourceModele()">
            Copier un modèle existant
        </label>
    </fieldset>

    <div id="blocNature">
        <label for="natureGrille">Nature de la grille :</label>
        <select name="natureGrille" id="natureGrille" required>
            <option value="">Sélectionnez une nature</option>
            <?php foreach (getNaturesGrilleValides() as $nature) : ?>
                <option value="<?= $nature ?>" <?= $valeursFormulaire['natureGrille'] === $nature ? 'selected' : '' ?>><?= ucfirst(strtolower($nature)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="blocSource" style="display:none;">
        <label for="modeleSource">Modèle à copier :</label>
        <select name="modeleSource" id="modeleSource">
            <option value="">Sélectionnez un modèle source</option>
            <?php foreach ($listModele as $modele) : ?>
                <option value="<?= htmlspecialchars($modele['IdModeleEval'], ENT_QUOTES, 'UTF-8') ?>">
                    <?= htmlspecialchars($modele['natureGrille'] . ' - ' . $modele['nomModuleGrilleEvaluation'] . ' - ' . $modele['anneeDebut'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <label for="nomModule">Nom du module :</label>
    <input type="text" name="nomModule" id="nomModule" maxlength="80" value="<?= htmlspecialchars($valeursFormulaire['nomModule'], ENT_QUOTES, 'UTF-8') ?>" required>

    <label for="noteMax">Note maximale :</label>
    <input type="number" name="noteMax" id="noteMax" step="0.5" min="0.5" value="<?= htmlspecialchars($valeursFormulaire['noteMax'], ENT_QUOTES, 'UTF-8') ?>" required>

    <label for="anneeDebut">Année de début :</label>
    <input type="number" name="anneeDebut" id="anneeDebut" value="<?= htmlspecialchars($valeursFormulaire['anneeDebut'], ENT_QUOTES, 'UTF-8') ?>" required>

    <button type="submit" name="creerModele">Créer la grille</button>
</form>

<script>
function toggleSourceModele()
{
    var copie = document.querySelector('input[name="pointDepart"]:checked').value === 'copie';

    document.getElementById('blocSource').style.display = copie ? 'block' : 'none';
    document.getElementById('modeleSource').required = copie;
    document.getElementById('blocNature').style.display = copie ? 'none' : 'block';
    document.getElementById('natureGrille').required = !copie;
}
</script>

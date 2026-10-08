<?php if (empty($anneesUniversitaires)) : ?>
    <p>Aucune année universitaire disponible.</p>
<?php else : ?>
    <form method="get" action="index.php">
        <label for="anneeDebut">Année universitaire :</label>
        <select name="anneeDebut" id="anneeDebut" required>
            <?php if ($anneeSelectionnee === null) : ?>
                <option value="" selected disabled>Choisir une année</option>
            <?php endif; ?>
            <?php foreach ($anneesUniversitaires as $annee) : ?>
                <option value="<?= (int) $annee['anneeDebut'] ?>" <?= (int) $annee['anneeDebut'] === $anneeSelectionnee ? 'selected' : '' ?>>
                    <?= (int) $annee['anneeDebut'] ?> - <?= (int) $annee['fin'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
    </form>
<?php endif; ?>
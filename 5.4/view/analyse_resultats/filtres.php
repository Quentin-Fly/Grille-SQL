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
        <?php
            $typesEvaluation = [
                'TOUS' => 'Tous les types',
                'STAGE' => 'Stage',
                'ENTREPRISE' => 'Entreprise',
                'TUTEUR' => 'Tuteur',
                'SOUTENANCE' => 'Soutenance',
                'RAPPORT' => 'Rapport',
                'PORTFOLIO' => 'Portfolio',
                'ANGLAIS' => 'Anglais'
            ];
        ?>
        <label for="typeEvaluation">Type d'évaluation :</label>
        <select name="typeEvaluation" id="typeEvaluation">
            <?php foreach ($typesEvaluation as $valeur => $libelle) : ?>
                <option value="<?= htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8') ?>" <?= $typeSelectionne === $valeur ? 'selected' : '' ?>>
                    <?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
    </form>
<?php endif; ?>
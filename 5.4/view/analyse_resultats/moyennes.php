<?php if ($moyenneStage !== null) : ?>
    <h2>Moyenne de stage</h2>

    <?php if ((int) $moyenneStage['nombreNotes'] > 0) : ?>
        <p>
            Moyenne pour l’année <?= (int) $anneeSelectionnee ?> :
            <?= number_format((float) $moyenneStage['moyenneStage'], 2, ',', ' ') ?>
            sur <?= (int) $moyenneStage['nombreNotes'] ?> notes.
        </p>
    <?php else : ?>
        <p>Aucune note disponible.</p>
    <?php endif; ?>
<?php endif; ?>
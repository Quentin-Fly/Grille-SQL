<!--  Affichage de la moyenne des stage globale -->
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
<!-- Affichage de la moyenne par type -->
<?php if (!empty($moyennesParType)) : ?>
    <h2>Moyennes par type d’évaluation</h2>

    <table>
        <thead>
            <tr>
                <th>Type</th>
                <th>Moyenne</th>
                <th>Nombre de notes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($moyennesParType as $moyenne) : ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($moyenne['typeEvaluation'], ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td>
                        <?= $moyenne['moyenne'] === null
                            ? 'Aucune note'
                            : number_format((float) $moyenne['moyenne'], 2, ',', ' ') ?>
                    </td>
                    <td><?= (int) $moyenne['nombreNotes'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php if ($anneeSelectionnee !== null) : ?>
    <h2>Moyennes de stage par enseignant tuteur</h2>

    <?php if (!empty($moyennesParEnseignant)) : ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Moyenne</th>
                    <th>Nombre de notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($moyennesParEnseignant as $enseignant) : ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($enseignant['nom'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($enseignant['prenom'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <?= $enseignant['moyenne'] === null
                                ? 'Aucune note'
                                : number_format((float) $enseignant['moyenne'], 2, ',', ' ') ?>
                        </td>
                        <td><?= (int) $enseignant['nombreNotes'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun résultat pour cette année.</p>
    <?php endif; ?>
<?php endif; ?>
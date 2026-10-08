<?php if ($anneeSelectionnee !== null) : ?>
    <h2>Alertes sur les notes manquantes</h2>

    <?php if (empty($alertes)) : ?>
        <p>Aucune alerte pour cette année.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de soutenance</th>
                    <th>Notes manquantes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alertes as $alerte) : ?>
                    <?php
                        $notesManquantes = [];

                        if ($alerte['noteRapport'] === null) {
                            $notesManquantes[] = 'Rapport';
                        }

                        if ($alerte['noteSoutenance'] === null) {
                            $notesManquantes[] = 'Soutenance';
                        }
                        if ($alerte['notePortfolio'] === null) {
                            $notesManquantes[] = 'Portfolio';
                        }
                        if ($alerte['noteSoutenanceEnseignant1'] === null) {
                            $notesManquantes[] = 'Soutenance : enseignant tuteur';
                        }
                        if ($alerte['noteSoutenanceEnseignant2'] === null) {
                            $notesManquantes[] = 'Soutenance : second enseignant';
                        }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($alerte['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($alerte['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($alerte['date_h'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?= htmlspecialchars(
                                implode(', ', $notesManquantes),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
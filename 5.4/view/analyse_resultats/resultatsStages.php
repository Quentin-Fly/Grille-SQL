<?php if ($anneeSelectionnee !== null) : ?>
    <h2>Évaluations de stage terminées</h2>
    <?php if (empty($resultats)) : ?>
        <p>Aucune évaluation de stage terminée pour cette année.</p>
    <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Note de stage</th>
                    <th>Statut</th>
                    <th>Fiche</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultats as $resultat) : ?>
                    <tr>
                        <td><?= htmlspecialchars($resultat['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($resultat['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $resultat['noteStage'] === null ? 'Non renseignée' : htmlspecialchars((string) $resultat['noteStage'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($resultat['Statut'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="index.php?anneeDebut=<?= (int) $anneeSelectionnee ?>&amp;idEtudiant=<?= (int) $resultat['IdEtudiant'] ?>">
                                Voir la fiche
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
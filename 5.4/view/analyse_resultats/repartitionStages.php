<h2>Répartition des stages par département et par année</h2>
<p>Cette comparaison porte sur toutes les années et tous les stages, indépendamment de leur statut d'évaluation.</p>
<?php if (empty($repartitionStages)) : ?>
    <p>Aucun stage disponible.</p>
<?php else : ?>
    <table>
        <thead>
            <tr>
                <th>Année universitaire</th>
                <th>Département</th>
                <th>Nombre de stages</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($repartitionStages as $repartition) : ?>
                <tr>
                    <td><?= (int) $repartition['anneeDebut'] ?> - <?= (int) $repartition['anneeDebut'] + 1 ?></td>
                    <td><?= htmlspecialchars($repartition['departement'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int) $repartition['nombreStages'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
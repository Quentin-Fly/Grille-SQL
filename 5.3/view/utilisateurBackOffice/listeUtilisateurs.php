<table>
    <thead>
        <tr>
            <th>Identifiant</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Mail</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($utilisateursBackOffice)) : ?>
            <tr><td colspan="4">Aucun utilisateur.</td></tr>
        <?php else : ?>
            <?php foreach ($utilisateursBackOffice as $utilisateur) : ?>
                <tr>
                    <td><?= (int) $utilisateur['Identifiant'] ?></td>
                    <td><?= htmlspecialchars($utilisateur['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($utilisateur['prenom'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($utilisateur['mail'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
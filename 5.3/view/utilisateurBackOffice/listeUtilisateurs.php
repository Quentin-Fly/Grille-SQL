<table>
    <thead>
        <tr>
            <th>Identifiant</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Mail</th>
            <th>Action</th>
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
                    <td>
                        <form method="post" action="index.php">
                            <input type="hidden" name="identifiant"value="<?= (int) $utilisateur['Identifiant'] ?>">
                            <button name="supprimerUtilisateur" type="submit" onclick="return confirm('Supprimer cet accès au back-office ?');" > Supprimer </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
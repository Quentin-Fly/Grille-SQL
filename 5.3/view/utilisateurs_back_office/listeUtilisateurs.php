<?php foreach ($utilisateursBackOffice as $utilisateur) : ?>
    <tr>
        <td><?= htmlspecialchars($utilisateur['id_utilisateur']) ?></td>
        <td><?= htmlspecialchars($utilisateur['nom_utilisateur']) ?></td>
        <td><?= htmlspecialchars($utilisateur['prenom_utilisateur']) ?></td>
        <td><?= htmlspecialchars($utilisateur['email_utilisateur']) ?></td>
        <td><?= htmlspecialchars($utilisateur['role_utilisateur']) ?></td>
        <td>
            <a href="modifierUtilisateur.php?id=<?= $utilisateur['id_utilisateur'] ?>" class="btn btn-primary">Modifier</a>
            <a href="supprimerUtilisateur.php?id=<?= $utilisateur['id_utilisateur'] ?>" class="btn btn-danger">Supprimer</a>
        </td>
    </tr>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statuts et droits des utilisateurs</title>
</head>
<body>
    <h1>Statuts et droits des utilisateurs</h1>
    <?php if ($erreurCreation !== null) : ?>
        <p role="alert"><?= htmlspecialchars($erreurCreation, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <?php if ($succesCreation !== null) : ?>
        <p role="status"><?= htmlspecialchars($succesCreation, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
    <form method="post" action="index.php">
        <button type="submit" name="nouvelUtilisateur">Nouvel utilisateur</button>
    </form>
    <?php require __DIR__ . '/listeUtilisateurs.php'; ?>
    <?php if ($afficherCreation) : ?>
        <?php require __DIR__ . '/formulaireCréation.php'; ?>
    <?php endif; ?>
</body>
</html>
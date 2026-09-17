
<!DOCTYPE html>
<html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion des grilles</title>
    </head>

    <body>

        <h1>Gestion des grilles</h1>
        <!-- Formulaire de création de modèle de grille -->
        <?php if ($afficherCreation) : ?>
            <?php require __DIR__ . "/formulaireCreation.php"; ?>
        <?php endif; ?>
        <!-- Messages d'erreur et de succès -->
        <?php if ($erreurCreation !== null) : ?>
            <p role="alert"><?= htmlspecialchars($erreurCreation, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <?php if ($succesCreation !== null) : ?>
            <p role="status"><?= htmlspecialchars($succesCreation, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </body>



</html>

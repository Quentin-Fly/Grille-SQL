<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Analyse des résultats</title>
        <link rel="stylesheet" href="view/analyse_resultats/style.css">
    </head>
    <body>
        <h1>Analyse des résultats</h1>
        <?php if ($erreurFiltre !== null) : ?>
            <p role="alert"><?= htmlspecialchars($erreurFiltre, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <!-- Affichage des filtre -->
        <?php require __DIR__ . '/filtres.php'; ?>
        <!-- Affichage des résultats de Stage -->
        <?php require __DIR__ . '/resultatsStages.php'; ?>
        <?php require __DIR__ . '/ficheEtudiant.php'; ?>
        <!-- Affichage des moyennes -->
        <?php require __DIR__ . '/moyennes.php'; ?>
        <!-- Affichage des alertes -->
        <?php require __DIR__ . '/alerte.php'; ?>
    </body>
</html> 
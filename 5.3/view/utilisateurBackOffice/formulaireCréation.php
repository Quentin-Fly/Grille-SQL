<form method="post" action="index.php">
    <h2>Créer un utilisateur</h2>
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" maxlength="50" value="<?= htmlspecialchars($valeursFormulaire['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
    <br>
    <label for="prenom">Prénom :</label>
    <input type="text" name="prenom" id="prenom" maxlength="50" value="<?= htmlspecialchars($valeursFormulaire['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
    <br>
    <label for="mail">Mail :</label>
    <input type="email" name="mail" id="mail" maxlength="150" value="<?= htmlspecialchars($valeursFormulaire['mail'], ENT_QUOTES, 'UTF-8') ?>" required>
    <br>
    <label for="motdepasse">Mot de passe :</label>
    <input type="password" name="motdepasse" id="motdepasse" autocomplete="new-password" required>
    <br>
    <button type="submit" name="creerUtilisateur">Créer l'utilisateur</button>
    <a href="index.php">Annuler</a>
</form>
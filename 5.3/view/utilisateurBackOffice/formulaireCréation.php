<!-- Formulaire de création d'utilisateur back-office -->
 <form method="post" action="index.php">
    <!-- NOM -->
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>
    <br>
    <!-- PRÉNOM -->
    <label for="prenom">Prénom :</label>
    <input type="text" name="prenom" id="prenom" required>
    <br>
    <!-- MAIL -->
    <label for="mail">Mail :</label>
    <input type="email" name="mail" id="mail" required>
    <br>
    <!-- MOT DE PASSE -->
    <label for="motdepasse">Mot de passe :</label>
    <input type="password" name="motdepasse" id="motdepasse" required>
    <br>
    <!-- BOUTON DE SOUMISSION -->
    <input name="creerUtilisateur" type="submit" value="Créer l'utilisateur">
 </form>
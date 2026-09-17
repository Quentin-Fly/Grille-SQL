<!-- Formulaire de création de modèle de grille -->

<form method="post" action="">

    <label for="natureGrille">Nature de la grille :</label>
    <select name="natureGrille" id="natureGrille" required>
        <option value="">Sélectionnez une nature</option>
        <?php foreach (getNaturesGrilleValides() as $nature) : ?>
        <option value="<?= $nature ?>"><?= ucfirst(strtolower($nature)) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="nomModule">Nom du module :</label>
    <!-- Champ de saisie obligatoire pour le nom du module --> 
    <input type="text" name="nomModule" id="nomModule" required>

    <label for="noteMax">Note maximale :</label>
    <!-- Champ de saisie obligatoire pour la note maximale, avec une valeur allant a un pas de 0.5 -->
    <input type="number" name="noteMax" id="noteMax" step="0.5" min="0.5" required> 
    
    <label for="anneeDebut">Année de début :</label>
    <!-- Champ de saisie obligatoire pour l'année de début -->
    <input type="number" name="anneeDebut" id="anneeDebut" required>
    <!-- Bouton de validation pour soumettre le formulaire -->
    <button type="submit" name="creerModele">
        Créer la grille
    </button>
</form>
    

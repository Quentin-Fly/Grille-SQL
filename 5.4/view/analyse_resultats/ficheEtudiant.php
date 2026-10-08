<?php if ($ficheEtudiant !== null) : ?>
    <h2>Fiche de l'étudiant</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($ficheEtudiant['nom'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Prénom :</strong> <?= htmlspecialchars($ficheEtudiant['prenom'], ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Parcours :</strong> <?= (int) $ficheEtudiant['but3sinon2'] === 1 ? 'BUT 3' : 'BUT 2' ?></p>
    <p><strong>Année universitaire :</strong> <?= (int) $ficheEtudiant['anneeDebut'] ?> - <?= (int) $ficheEtudiant['anneeDebut'] + 1 ?></p>

    <?php
        // Les libellés sont associés aux colonnes retournées par le modèle.
        $notesFiche = [
            'Stage' => 'noteStage',
            'Entreprise' => 'noteEntreprise',
            'Tuteur' => 'noteTuteur',
            'Rapport' => 'noteRapport',
            'Soutenance' => 'noteSoutenance',
            'Portfolio' => 'notePortfolio'
        ];
        if ((int) $ficheEtudiant['but3sinon2'] === 1) {
            $notesFiche['Anglais'] = 'noteAnglais';
        }
    ?>
    <table>
        <thead>
            <tr><th>Évaluation</th><th>Note</th></tr>
        </thead>
        <tbody>
            <?php foreach ($notesFiche as $libelle => $cle) : ?>
                <tr>
                    <td><?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $ficheEtudiant[$cle] === null ? 'Non renseignée' : htmlspecialchars((string) $ficheEtudiant[$cle], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
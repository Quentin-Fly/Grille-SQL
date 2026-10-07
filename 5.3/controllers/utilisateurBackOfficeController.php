<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/modeleUtilisateurBackOffice.php';

$utilisateursBackOffice = getUtilisateurBackOffice($pdo);

require __DIR__ . '/../view/utilisateurBackOffice/index.php';
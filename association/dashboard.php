<?php
require "config.php";
require "header.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Statistiques
$nb_membres = $pdo->query("SELECT COUNT(*) FROM membre")->fetchColumn();
$nb_activites = $pdo->query("SELECT COUNT(*) FROM activite")->fetchColumn();
$nb_inscriptions = $pdo->query("SELECT COUNT(*) FROM membre_activite")->fetchColumn();
?>

<h2>Dashboard Administrateur</h2>

<div class="stats">

    <div class="card">
        <h3>Membres</h3>
        <p><?= $nb_membres ?></p>
    </div>

    <div class="card">
        <h3>Activités</h3>
        <p><?= $nb_activites ?></p>
    </div>

    <div class="card">
        <h3>Inscriptions</h3>
        <p><?= $nb_inscriptions ?></p>
    </div>

</div>

<?php require "footer.php"; ?>

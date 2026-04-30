<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion Association</title>

    <!-- CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- JS -->
    <script src="/association/script.js" defer></script>
</head>

<body>

<div class="container">

<nav class="navbar">

<?php if (isset($_SESSION["role"]) && $_SESSION["role"] === "admin"): ?>
    <a href="dashboard.php">Dashboard</a>
    <a href="membre.php">Membres</a>
    <a href="activite.php">Activités</a>
<?php else: ?>
    <a href="activites_public.php">Activités</a>
    <a href="mes_inscriptions.php">Mes inscriptions</a>
<?php endif; ?>

    <div class="nav-right">
        Connecté : <?= htmlspecialchars($_SESSION["prenom"] ?? 'Utilisateur') ?>
        | <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<hr>

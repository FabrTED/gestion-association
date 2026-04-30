<?php
// ============================
// 1. DÉMARRER LA SESSION
// ============================
session_start();

// ============================
// 2. CONNEXION À LA BASE
// ============================
require "config.php"; // Doit contenir la variable $pdo


$message = "";
$erreur = "";

if (isset($_SESSION["success"])) {
    $message = $_SESSION["success"];
    unset($_SESSION["success"]);
}

// ============================


// ============================
// 4. TRAITEMENT DU FORMULAIRE
// ============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Récupération des données du formulaire
    $email = trim($_POST["email"]);
    $mot_de_passe = $_POST["password"];

    // Vérification si l'utilisateur existe
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, mot_de_passe, role
        FROM membre
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    // Vérification du mot de passe
if ($user && !empty($user["mot_de_passe"]) && password_verify($mot_de_passe, $user["mot_de_passe"])) {

    // Création des variables de session
    $_SESSION["user_id"] = $user["id"];
    $_SESSION["nom"] = $user["nom"];
    $_SESSION["prenom"] = $user["prenom"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];
    $_SESSION["admin"] = ($user["role"] === "admin");

    // Redirection
    if ($user["role"] === "admin") {
        header("Location: dashboard.php");
    } else {
        header("Location: activites_public.php");
    }
    exit;

} elseif ($user && empty($user["mot_de_passe"])) {

    $erreur = "Compte créé par admin. Veuillez vous inscrire.";

} else {

    $erreur = "Email ou mot de passe incorrect.";

}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-container">
    <h2>Connexion Association </h2>
<?php if (!empty($message)) : ?>
    <p style="color:green"><?= $message ?></p>
<?php endif; ?>
    <?php if (!empty($erreur)) : ?>
        <p class="error"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form method="post" class="login-form">
        <p>Pas de compte ? <a href="inscription.php">Créer un compte</a></p>
        <label>Email :</label>
        <input type="email" name="email" required>

        <label>Mot de passe :</label>
        <input type="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>
</div>

</body>
</html>
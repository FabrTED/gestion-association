<?php

session_start();
require "config.php";

$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$nom = trim($_POST["nom"]);
$prenom = trim($_POST["prenom"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreur = "Email invalide";
} elseif (strlen($nom) < 2 || strlen($prenom) < 2) {
    $erreur = "Nom ou prénom trop court";
} elseif (strlen($password) < 6) {
    $erreur = "Mot de passe trop court";
} elseif ($password !== $confirm_password) {
    $erreur = "Les mots de passe ne correspondent pas";
} else {

    $stmt = $pdo->prepare("SELECT * FROM membre WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {

    // ✅ compte existe
    if (empty($user["mot_de_passe"])) {

        // 👉 compte créé par admin → on active
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $update = $pdo->prepare("
            UPDATE membre
            SET mot_de_passe = ?
            WHERE email = ?
        ");

        $update->execute([$hash, $email]);

        $_SESSION["success"] = "Compte activé, vous pouvez vous connecter";
        header("Location: login.php");
        exit;

    } else {

        // ❌ vrai compte déjà existant
        $erreur = "Email déjà utilisé";

    }

} else {

    // ✅ nouveau compte normal
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO membre (nom, prenom, email, mot_de_passe, role, date_inscription)
        VALUES (?, ?, ?, ?, 'membre', CURDATE())
    ");

    $stmt->execute([$nom, $prenom, $email, $hash]);

    $_SESSION["success"] = "Compte créé avec succès";
    header("Location: login.php");
    exit;
}
}


}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<script>
function verifierFormulaire() {

    const email = document.querySelector('input[name="email"]').value;
    const mdp = document.querySelector('input[name="password"]').value;
    const confirm = document.querySelector('input[name="confirm_password"]').value;

    // Vérif email
    if (!email.includes("@")) {
        alert("Email invalide");
        return false;
    }

    // Vérif mot de passe
    if (mdp !== confirm) {
        alert("Les mots de passe ne correspondent pas");
        return false;
    }

    return true;
}
</script>

<div class="login-container">
    <h2>Créer un compte</h2>

    <?php if (!empty($message)): ?>
    <p style="color:green"><?= $message ?></p>
    <?php endif; ?>

    <?php if ($erreur): ?>
        <p style="color:red;"><?= $erreur ?></p>
    <?php endif; ?>

    <form method="post" onsubmit="return verifierFormulaire()">

<input type="text" name="nom" placeholder="Nom" required minlength="2">

<input type="text" name="prenom" placeholder="Prénom" required minlength="2">

<input type="email" name="email" placeholder="Email" required>

          <input type="password" name="password" placeholder="Mot de passe" required minlength="6">
          <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>

        <button type="submit">S'inscrire</button>

    </form>

    <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
</div>

</body>


</html>
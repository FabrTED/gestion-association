<?php
// ============================
// 🔐 SESSION + PROTECTION ADMIN
// ============================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

require "config.php";

// ============================
// 📌 VARIABLES
// ============================
$editMembre = null;
$inscrireMembre = null;
$message = null;


// ============================
// 🔴 SUPPRESSION MEMBRE
// ============================
if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    // supprimer inscriptions liées
    $pdo->prepare("DELETE FROM membre_activite WHERE id_membre = ?")->execute([$id]);

    // supprimer membre
    $pdo->prepare("DELETE FROM membre WHERE id = ?")->execute([$id]);

    header("Location: membre.php?success=delete");
    exit;
}


// ============================
// 🟠 MODE MODIFICATION
// ============================
if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare("SELECT * FROM membre WHERE id = ?");
    $stmt->execute([(int) $_GET["edit"]]);
    $editMembre = $stmt->fetch(PDO::FETCH_ASSOC);
}


// ============================
// 🔵 MODE INSCRIPTION
// ============================
if (isset($_GET["inscrire"])) {
    $stmt = $pdo->prepare("SELECT * FROM membre WHERE id = ?");
    $stmt->execute([(int) $_GET["inscrire"]]);
    $inscrireMembre = $stmt->fetch(PDO::FETCH_ASSOC);
}


// ============================
// 🟢 TRAITEMENT FORMULAIRE
// ============================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ========================
    // ➕ AJOUT MEMBRE
    // ========================
    if ($_POST["action"] === "add") {

        $email = strtolower(trim($_POST["email"]));

        // 🔴 Vérification doublon email
        $check = $pdo->prepare("SELECT COUNT(*) FROM membre WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetchColumn() > 0) {
            header("Location: membre.php?error=email");
            exit;
        }

        // ✅ insertion
        $pdo->prepare("
            INSERT INTO membre (nom, prenom, email, date_inscription)
            VALUES (?, ?, ?, CURDATE())
        ")->execute([
            $_POST["nom"],
            $_POST["prenom"],
            $email
        ]);

        header("Location: membre.php?success=add");
        exit;
    }


    // ========================
    // ✏️ MODIFICATION MEMBRE
    // ========================
    if ($_POST["action"] === "edit") {

        $email = strtolower(trim($_POST["email"]));

        // 🔴 éviter doublon (sauf lui-même)
        $check = $pdo->prepare("
            SELECT COUNT(*) FROM membre 
            WHERE email = ? AND id != ?
        ");
        $check->execute([$email, $_POST["id"]]);

        if ($check->fetchColumn() > 0) {
            header("Location: membre.php?error=email");
            exit;
        }

        $pdo->prepare("
            UPDATE membre
            SET nom = ?, prenom = ?, email = ?
            WHERE id = ?
        ")->execute([
            $_POST["nom"],
            $_POST["prenom"],
            $email,
            $_POST["id"]
        ]);

        header("Location: membre.php?success=edit");
        exit;
    }


    // ========================
    // 📌 INSCRIPTION ACTIVITÉ
    // ========================
    if ($_POST["action"] === "inscrire") {

        try {
            $pdo->prepare("
                INSERT INTO membre_activite (id_membre, id_activite)
                VALUES (?, ?)
            ")->execute([
                $_POST["id_membre"],
                $_POST["id_activite"]
            ]);

            header("Location: membre.php?success=inscription");
            exit;

        } catch (PDOException $e) {
            // 🔒 doublon bloqué par SQL UNIQUE
            header("Location: membre.php?error=deja_inscrit");
            exit;
        }
    }
}


// ============================
// 📊 RÉCUP DONNÉES
// ============================
$membres = $pdo->query("SELECT * FROM membre ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$activites = $pdo->query("SELECT * FROM activite ORDER BY titre")->fetchAll(PDO::FETCH_ASSOC);

require "header.php";
?>

<h2>Liste des membres</h2>

<input type="text" id="searchMembre" placeholder="Rechercher membre...">

<!-- ============================
MESSAGES
============================ -->

<?php if (isset($_GET["success"])): ?>
    <p style="color:green;">
        <?php
        if ($_GET["success"] === "add") echo "Membre ajouté ✔";
        if ($_GET["success"] === "edit") echo "Membre modifié ✔";
        if ($_GET["success"] === "delete") echo "Membre supprimé ✔";
        if ($_GET["success"] === "inscription") echo "Inscription réussie ✔";
        ?>
    </p>
<?php endif; ?>

<?php if (isset($_GET["error"])): ?>
    <p style="color:red;">
        <?php
        if ($_GET["error"] === "email") echo "Email déjà utilisé ❌";
        if ($_GET["error"] === "deja_inscrit") echo "Déjà inscrit à cette activité ❌";
        ?>
    </p>
<?php endif; ?>


<table class="table">
<thead>
<tr>
    <th>Prénom</th>
    <th>Nom</th>
    <th>Email</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php foreach ($membres as $m): ?>
<tr>

<td><?= htmlspecialchars($m["prenom"]) ?></td>
<td><?= htmlspecialchars($m["nom"]) ?></td>
<td><?= htmlspecialchars($m["email"]) ?></td>

<td>
    <a href="membre.php?edit=<?= $m["id"] ?>">Modifier</a> |
    <a href="membre.php?delete=<?= $m["id"] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a> |
    <a href="membre.php?inscrire=<?= $m["id"] ?>">Inscrire</a>
</td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>

<!-- ============================
FORMULAIRE AJOUT / MODIF
============================ -->

<?php if ($editMembre): ?>

<h2>Modifier un membre</h2>

<form method="post" onsubmit="return confirm('Confirmer modification ?')">
<input type="hidden" name="action" value="edit">
<input type="hidden" name="id" value="<?= $editMembre["id"] ?>">

<input type="text" name="nom" value="<?= htmlspecialchars($editMembre["nom"]) ?>" required>
<input type="text" name="prenom" value="<?= htmlspecialchars($editMembre["prenom"]) ?>" required>
<input type="email" name="email" value="<?= htmlspecialchars($editMembre["email"]) ?>" required>

<button type="submit">Modifier</button>
</form>

<?php else: ?>

<h2>Ajouter un membre</h2>

<form method="post">
<input type="hidden" name="action" value="add">

<input type="text" name="nom" placeholder="Nom" required>
<input type="text" name="prenom" placeholder="Prénom" required>
<input type="email" name="email" placeholder="Email" required>

<button type="submit">Ajouter</button>
</form>

<?php endif; ?>


<!-- ============================
INSCRIPTION ACTIVITÉ
============================ -->

<?php if ($inscrireMembre): ?>

<hr>

<h2>Inscrire <?= htmlspecialchars($inscrireMembre["prenom"]) ?></h2>

<form method="post" onsubmit="return confirm('Confirmer inscription ?')">

<input type="hidden" name="action" value="inscrire">
<input type="hidden" name="id_membre" value="<?= $inscrireMembre["id"] ?>">

<select name="id_activite" required>
<?php foreach ($activites as $a): ?>
<option value="<?= $a["id"] ?>">
<?= htmlspecialchars($a["titre"]) ?>
</option>
<?php endforeach; ?>
</select>

<button type="submit">Inscrire</button>

</form>

<?php endif; ?>

<?php require "footer.php"; ?>
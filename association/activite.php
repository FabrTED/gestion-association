<?php
// ==========================
// 🔐 SESSION + PROTECTION ADMIN
// ==========================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "config.php";

// Vérifie si admin
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// ==========================
// 📌 VARIABLE POUR MODIF
// ==========================
$editActivite = null;

// ==========================
// 🔴 SUPPRESSION ACTIVITÉ
// ==========================
if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"]; // sécurisation

    // 1. supprimer les inscriptions liées
    $stmt = $pdo->prepare("DELETE FROM membre_activite WHERE id_activite = ?");
    $stmt->execute([$id]);

    // 2. supprimer l’activité
    $stmt = $pdo->prepare("DELETE FROM activite WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: activite.php?success=1");
    exit;
}

// ==========================
// 🟠 CHARGER ACTIVITÉ À MODIFIER
// ==========================
if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare("SELECT * FROM activite WHERE id = ?");
    $stmt->execute([$id]);

    $editActivite = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ==========================
// 🟢 TRAITEMENT FORMULAIRE
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 🔵 MODIFICATION
    if (isset($_POST["modifier"])) {

        $id = (int) $_POST["edit_id"];
        $titre = trim($_POST["titre"]);
        $description = trim($_POST["description"]);
        $date = $_POST["date"];

        if ($titre !== "" && $date !== "") {

            $stmt = $pdo->prepare("
                UPDATE activite
                SET titre = ?, description = ?, date_activite = ?
                WHERE id = ?
            ");

            $stmt->execute([$titre, $description, $date, $id]);

            header("Location: activite.php?success=1");
            exit;
        }
    }

    // 🟢 AJOUT
    if (isset($_POST["ajouter"])) {

    $titre = trim($_POST["titre"]);
    $description = trim($_POST["description"]);
    $date = $_POST["date"];

    // 🔴 Vérifier doublon
    $check = $pdo->prepare("SELECT COUNT(*) FROM activite WHERE titre = ?");
    $check->execute([$titre]);

    if ($check->fetchColumn() > 0) {
        echo "<p style='color:red'>Activité déjà existante</p>";
    } else {

        $stmt = $pdo->prepare("
            INSERT INTO activite (titre, description, date_activite)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$titre, $description, $date]);

        header("Location: activite.php?success=1");
        exit;
    }
}
}

// ==========================
// 🔵 DÉSINSCRIRE UN MEMBRE
// ==========================
if (isset($_GET["desinscrire_membre"]) && isset($_GET["activite"])) {

    $idMembre = (int) $_GET["desinscrire_membre"];
    $idActivite = (int) $_GET["activite"];

    $stmt = $pdo->prepare("
        DELETE FROM membre_activite
        WHERE id_membre = ? AND id_activite = ?
    ");
    $stmt->execute([$idMembre, $idActivite]);

    header("Location: activite.php?success=1");
    exit;
}

// ==========================
// 📊 RÉCUP ACTIVITÉS
// ==========================
$stmt = $pdo->query("SELECT * FROM activite ORDER BY date_activite ASC");
$activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

require "header.php";
?>

<h2>Liste des activités</h2>
<input type="text" id="searchActivite" placeholder="Rechercher activité...">

<!-- ✅ MESSAGE SUCCESS -->
<?php if (isset($_GET["success"])): ?>
    <p style="color:green;">Action réussie ✔</p>
<?php endif; ?>

<table class="table">
<tr>
    <th>Titre</th>
    <th>Date</th>
    <th>Description</th>
    <th>Actions</th>
    <th>Membres inscrits</th>
</tr>

<tbody>

<?php foreach ($activites as $a): ?>
<tr>

    <!-- 🔹 INFOS -->
    <td><?= htmlspecialchars($a["titre"]) ?></td>
    <td><?= htmlspecialchars($a["date_activite"]) ?></td>
    <td><?= htmlspecialchars($a["description"]) ?></td>

    <!-- 🔹 ACTIONS -->
    <td>
        <a href="activite.php?edit=<?= $a["id"] ?>">Modifier</a> |
        <a href="activite.php?delete=<?= $a["id"] ?>"
           onclick="return confirm('Supprimer cette activité ?')">
           Supprimer
        </a>
    </td>

    <!-- 👥 MEMBRES -->
    <td>
    <?php
        $stmt2 = $pdo->prepare("
            SELECT membre.id, membre.prenom, membre.nom
            FROM membre
            JOIN membre_activite 
            ON membre.id = membre_activite.id_membre
            WHERE membre_activite.id_activite = ?
        ");
        $stmt2->execute([$a["id"]]);
        $membres = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($membres)) {
            echo "<ul>";
            foreach ($membres as $m) {
                echo "<li>"
                    . htmlspecialchars($m["prenom"]) . " " . htmlspecialchars($m["nom"])
                    . " - <a href='activite.php?desinscrire_membre=" . $m["id"] . "&activite=" . $a["id"] . "' 
                    onclick=\"return confirm('Désinscrire ce membre ?')\">
                    Désinscrire</a>"
                    . "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun inscrit";
        }
    ?>
    </td>

</tr>
<?php endforeach; ?>
    </tbody>

</table>

<hr>

<!-- ========================== -->
<!-- ➕ AJOUT -->
<!-- ========================== -->

<h2>Ajouter une activité</h2>

<form method="post">

    <label>Titre :</label>
    <input type="text" name="titre" required>

    <label>Description :</label>
    <input type="text" name="description">

    <label>Date :</label>
    <input type="date" name="date" required>

    <button type="submit" name="ajouter">
        Ajouter
    </button>

</form>

<!-- ========================== -->
<!-- ✏️ MODIFIER -->
<!-- ========================== -->

<?php if ($editActivite): ?>

<hr>

<h2>Modifier une activité</h2>

<form method="post">

    <input type="hidden" name="edit_id" value="<?= $editActivite["id"] ?>">

    <input type="text" name="titre" value="<?= htmlspecialchars($editActivite["titre"]) ?>" required>

    <input type="text" name="description" value="<?= htmlspecialchars($editActivite["description"]) ?>">

    <input type="date" name="date" value="<?= $editActivite["date_activite"] ?>" required>

    <button type="submit" name="modifier"
        onclick="return confirm('Confirmer modification ?')">
        Modifier
    </button>

</form>

<?php endif; ?>

<?php require "footer.php"; ?>
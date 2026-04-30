<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "config.php";

// Protection utilisateur
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// ==========================
// DESINSCRIPTION
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_activite"])) {

    $stmt = $pdo->prepare("
        DELETE FROM membre_activite
        WHERE id_membre = ? AND id_activite = ?
    ");

    $stmt->execute([
        $_SESSION["user_id"],
        $_POST["id_activite"]
    ]);

    header("Location: mes_inscriptions.php");
    exit;
}

// ==========================
// RÉCUP ACTIVITÉS INSCRITES
// ==========================
$stmt = $pdo->prepare("
    SELECT activite.id, activite.titre, activite.date_activite, activite.description
    FROM activite
    JOIN membre_activite 
        ON activite.id = membre_activite.id_activite
    WHERE membre_activite.id_membre = ?
");


$stmt->execute([$_SESSION["user_id"]]);

$activites = $stmt->fetchAll(PDO::FETCH_ASSOC);

require "header.php";
?>

<h2>Mes inscriptions</h2>

<table>
<tr>
    <th>Activité</th>
    <th>Date</th> 
    <th>Description</th>
    <th>Action</th>
   
</tr>

<?php foreach ($activites as $a): ?>
<tr>
    <td><?= htmlspecialchars($a["titre"]) ?></td>
    <td><?= htmlspecialchars($a["date_activite"]) ?></td>
    <td><?= htmlspecialchars($a["description"]) ?></td>

    <td>
    <form method="post" onsubmit="return confirmerDesinscription()">
    <input type="hidden" name="id_activite" value="<?= $a["id"] ?>">
    <button class="btn-desinscrire" onclick="return confirm('Tu veux te désinscrire ?')">
    Se désinscrire
</button>
    </td>
</tr>
<?php endforeach; ?>

</table>

<?php require "footer.php"; ?>
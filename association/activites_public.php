<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "config.php";

// ==========================
// PROTECTION : utilisateur connecté
// ==========================
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// ==========================
// TRAITEMENT POST (INSCRIPTION / DESINSCRIPTION)
// ==========================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {

    // INSCRIPTION
    if ($_POST["action"] === "subscribe") {

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO membre_activite (id_membre, id_activite)
            VALUES (?, ?)
        ");

        $stmt->execute([
            $_SESSION["user_id"],
            $_POST["id_activite"]
        ]);
    }

    // DESINSCRIPTION
    if ($_POST["action"] === "unsubscribe") {

        $stmt = $pdo->prepare("
            DELETE FROM membre_activite
            WHERE id_membre = ? AND id_activite = ?
        ");

        $stmt->execute([
            $_SESSION["user_id"],
            $_POST["id_activite"]
        ]);
    }

    header("Location: activites_public.php");
    exit;
}

// ==========================
// RÉCUP ACTIVITÉS
// ==========================
$activites = $pdo->query("
    SELECT * FROM activite ORDER BY date_activite
")->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// RÉCUP INSCRIPTIONS USER
// ==========================
$stmt = $pdo->prepare("
    SELECT id_activite 
    FROM membre_activite 
    WHERE id_membre = ?
");

$stmt->execute([$_SESSION["user_id"]]);

$inscriptions = $stmt->fetchAll(PDO::FETCH_COLUMN);

require "header.php";
?>

<h2>Activités disponibles</h2>
<input type="text" id="searchUser" placeholder="Rechercher activité...">

<table>
<tr>
    <th>Titre</th>
    
    <th>Date</th>
    <th>Description</th>
    <th>Action</th>
    
    
</tr>

<tbody>

<?php foreach ($activites as $a): ?>
<tr>
    <td><?= htmlspecialchars($a["titre"]) ?></td>
    <td><?= htmlspecialchars($a["date_activite"]) ?></td>
     <td><?= htmlspecialchars($a["description"]) ?></td>

    <td>

        <?php if (in_array($a["id"], $inscriptions)): ?>

    <button class="btn-inscrit" disabled>
        ✔ Inscrit
    </button>

<?php else: ?>

    <form method="post" onsubmit="return confirmerInscription()">
    <input type="hidden" name="action" value="subscribe">
    <input type="hidden" name="id_activite" value="<?= $a["id"] ?>">
    <button type="submit" onclick="return confirm('Tu veux t’inscrire ?')">

    

    S'inscrire
</button>
</form>

<?php endif; ?>

    </td>
</tr>
<?php endforeach; ?>
 </tbody>


</table>

<?php require "footer.php"; ?>
<?php
// -------------------------
// 🔐 PROTECTION ADMIN
// -------------------------

// ⚠️ session_start est déjà dans header.php normalement
// donc PAS besoin de le remettre ici

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


// -------------------------
// 📦 CONNEXION + HEADER
// -------------------------

require "config.php";
require "header.php";

// -------------------------
// 📊 RÉCUPÉRER LES MEMBRES
// -------------------------

$req = $pdo->query("SELECT * FROM membre");
$membres = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- ------------------------- -->
<!-- 📋 TITRE -->
<!-- ------------------------- -->

<h2>Liste des membres</h2>

<!-- ------------------------- -->
<!-- 🔍 BARRE DE RECHERCHE (JS) -->
<!-- ------------------------- -->

<input type="text" id="search" placeholder="Rechercher un membre...">

<br><br>

<!-- ------------------------- -->
<!-- 📊 TABLEAU -->
<!-- ------------------------- -->

<table>

<thead>
<tr>
    <th>Prénom</th>
    <th>Nom</th>
    <th>Email</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>

<?php foreach ($membres as $membre): ?>

<tr>

    <!-- Sécurisation XSS -->
    <td><?= htmlspecialchars($membre['prenom']) ?></td>
    <td><?= htmlspecialchars($membre['nom']) ?></td>
    <td><?= htmlspecialchars($membre['email']) ?></td>

    <td>
        <!-- Modifier -->
        <a href="membre.php?mode=edit&id=<?= $membre['id'] ?>">
            Modifier
        </a> |

        <!-- Supprimer -->
        <a href="membre.php?mode=delete&id=<?= $membre['id'] ?>"
           onclick="return confirm('Supprimer ce membre ?');">
            Supprimer
        </a> |

        <!-- Inscrire -->
        <a href="membre.php?mode=inscrire&id=<?= $membre['id'] ?>">
            Inscrire
        </a>
    </td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<?php require "footer.php"; ?>
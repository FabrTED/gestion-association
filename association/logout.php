<?php
session_start();

// Détruire toutes les variables de session
session_unset();
session_destroy();

// Redirection vers login
header("Location: login.php");
exit;

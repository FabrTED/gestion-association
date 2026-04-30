<?php
// On crée un objet PDO qui permet de se connecter à la base de données
// PDO = PHP Data Objects (méthode moderne et propre pour accéder à MySQL)

$pdo = new PDO(
    
    // Chaîne de connexion :
    // mysql → type de base
    // host → adresse du serveur (localhost = ton PC)
    // port → 3307 car on l’a changé dans XAMPP
    // dbname → nom de la base de données
    // charset → encodage pour éviter les problèmes d'accents
    
    "mysql:host=127.0.0.1;port=3307;dbname=association_db;charset=utf8",
    
    // Nom d'utilisateur MySQL
    "root",
    
    // Mot de passe (vide par défaut dans XAMPP)
    ""
);

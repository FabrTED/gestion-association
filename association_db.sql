-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 30 avr. 2026 à 23:10
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `association_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `activite`
--

CREATE TABLE `activite` (
  `id` int(11) NOT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `date_activite` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `activite`
--

INSERT INTO `activite` (`id`, `titre`, `description`, `date_activite`) VALUES
(1, 'Badmington', 'Club de Sport ', '2026-02-11'),
(2, 'Boxe', 'Club de Sport', '2026-02-16'),
(3, 'Natation', 'Club de Sport', '2026-02-16');

-- --------------------------------------------------------

--
-- Structure de la table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(3, 'admin', '$2y$10$drYD7F9HzhKX3xmPI4sARujjDR461.tGRloqx5BkDHY6ZfEjC.BCe');

-- --------------------------------------------------------

--
-- Structure de la table `membre`
--

CREATE TABLE `membre` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `date_inscription` date NOT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `role` enum('admin','membre') NOT NULL DEFAULT 'membre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membre`
--

INSERT INTO `membre` (`id`, `nom`, `prenom`, `email`, `date_inscription`, `mot_de_passe`, `role`) VALUES
(1, 'Dupont', 'Jean', 'jean@test.com', '2026-02-16', '$2y$10$QdyMg6yhkWgPtOyat0f.YuxhmJVR8VuWTrnBbGC986hwZvAXtda0y', 'membre'),
(4, 'Hedjaj', 'Faten', 'h.faten@parisnanterre.fr', '2026-03-23', '$2y$10$kmFz1jBkqube0t.z7JIrHu0PerNYluqKnt6nXEeXeuSkKPydm3G2m', 'membre'),
(9, 'Principal', 'Admin', 'admin@association.com', '2026-04-15', '$2y$10$csN8.E6kbsEN598wRi.6u./l812KvyKJLDM1.INjEwqInRY6RMBK6', 'admin'),
(24, 'Tedjiogny', 'Fabrice', 'fabrice.tedjogny@gmail.com', '2026-04-17', '$2y$10$PNQMSrS3zHA.DbixrhmzrOrd0a/OnQPTqoOFEprSkC9rjEOL5Ek8m', 'membre');

-- --------------------------------------------------------

--
-- Structure de la table `membre_activite`
--

CREATE TABLE `membre_activite` (
  `id_membre` int(11) NOT NULL,
  `id_activite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membre_activite`
--

INSERT INTO `membre_activite` (`id_membre`, `id_activite`) VALUES
(1, 1),
(4, 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activite`
--
ALTER TABLE `activite`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titre` (`titre`);

--
-- Index pour la table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `membre`
--
ALTER TABLE `membre`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `email_2` (`email`);

--
-- Index pour la table `membre_activite`
--
ALTER TABLE `membre_activite`
  ADD PRIMARY KEY (`id_membre`,`id_activite`),
  ADD UNIQUE KEY `id_membre` (`id_membre`,`id_activite`),
  ADD UNIQUE KEY `id_membre_2` (`id_membre`,`id_activite`),
  ADD KEY `id_activite` (`id_activite`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activite`
--
ALTER TABLE `activite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `membre`
--
ALTER TABLE `membre`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `membre_activite`
--
ALTER TABLE `membre_activite`
  ADD CONSTRAINT `membre_activite_ibfk_1` FOREIGN KEY (`id_membre`) REFERENCES `membre` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membre_activite_ibfk_2` FOREIGN KEY (`id_activite`) REFERENCES `activite` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

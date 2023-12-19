-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 19 déc. 2023 à 20:23
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dataware3`
--

-- --------------------------------------------------------

--
-- Structure de la table `equipe`
--

CREATE TABLE `equipe` (
  `id` int(11) NOT NULL,
  `nom` varchar(55) DEFAULT NULL,
  `date_creation` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL,
  `id_projet` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipe`
--

INSERT INTO `equipe` (`id`, `nom`, `date_creation`, `id_user`, `id_projet`) VALUES
(1, 'team1', '2023-12-15', 1, 3),
(2, 'equipe 2', '2023-12-16', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `membreequipe`
--

CREATE TABLE `membreequipe` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_equipe` int(11) DEFAULT NULL,
  `tache` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `membreequipe`
--

INSERT INTO `membreequipe` (`id`, `id_user`, `id_equipe`, `tache`) VALUES
(4, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `projet`
--

CREATE TABLE `projet` (
  `id` int(11) NOT NULL,
  `nom` varchar(55) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `date_creation` date NOT NULL DEFAULT current_timestamp(),
  `date_limite` date DEFAULT NULL,
  `statut` varchar(55) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projet`
--

INSERT INTO `projet` (`id`, `nom`, `description`, `date_creation`, `date_limite`, `statut`, `id_user`) VALUES
(3, 'projet1', 'hello word', '2023-12-15', '2024-12-02', 'hjk', 1),
(4, 'projet2', 'jjdjhhhh', '2023-12-19', '0000-00-00', 'kjkjj', 4);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `nom` varchar(25) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(60) DEFAULT NULL,
  `statut` varchar(55) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `email`, `password`, `statut`, `role`) VALUES
(1, 'yassir', 'yassir@gmail.com', '$2y$10$qQTEPczrgJTN/V8rbZSEDu5rR1gwVtPtAmXqtl5lqIXjsQyZa7szi', 'actif', 'sm'),
(2, 'hhh', 'hhh@gmail.com', '$2y$10$OyCL9VEh9W2MT8Pi6LFVC.SHHzGcwIpPkyn0kFVlr3kML/Z7hGSHa', 'actif', 'po'),
(3, 'hhhh', 'hhhh@gmail.com', '$2y$10$LhTK5F09yz7iySE2UndJ7O/PK7Kn/fUVhy01G2J.ngqGF6tEVFfy.', 'actif', 'user'),
(4, 'frend', 'xxxx@gmail.com', '$2y$10$BaTLUuiUoPgiGiKeL3ybgujp/JDT5m..3iT3O9XO15BFZ9.XpSfIG', 'actif', 'user');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_projet` (`id_projet`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `membreequipe`
--
ALTER TABLE `membreequipe`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_equipe` (`id_equipe`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `equipe`
--
ALTER TABLE `equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `membreequipe`
--
ALTER TABLE `membreequipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `equipe`
--
ALTER TABLE `equipe`
  ADD CONSTRAINT `equipe_ibfk_1` FOREIGN KEY (`id_projet`) REFERENCES `projet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `equipe_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `membreequipe`
--
ALTER TABLE `membreequipe`
  ADD CONSTRAINT `membreequipe_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `membreequipe_ibfk_2` FOREIGN KEY (`id_equipe`) REFERENCES `equipe` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `projet_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

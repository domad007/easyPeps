-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 07 mars 2020 à 11:16
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `easypeps`
--

-- --------------------------------------------------------

--
-- Structure de la table `ecole`
--

DROP TABLE IF EXISTS `ecole`;
CREATE TABLE IF NOT EXISTS `ecole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom_ecole` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ecole`
--

INSERT INTO `ecole` (`nom_ecole`) VALUES
( 'COLLEGE TECHNIQUE SAINT-JEAN'),
( 'Collège Notre-Dame'),
( 'INSTITUT PROVINCIAL D\'ENSEIGNEMENT SECONDAIRE'),
( 'INSTITUT SAINT-JEAN-BAPTISTE'),
( 'Institut de la Providence'),
( 'ECOLE INTERNATIONALE LE VERSEAU - E.L.C.E.'),
( 'ATHENEE ROYAL RIXENSART WAVRE'),
( 'COLLEGE NOTRE-DAME DES TROIS VALLEES'),
( 'Ecole plurielle'),
( 'ATHENEE ROYAL PAUL DELVAUX'),
( 'COLLEGE DU CHRIST-ROI'),
( 'Collège Da Vinci'),
( 'ATHENEE ROYAL JODOIGNE');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

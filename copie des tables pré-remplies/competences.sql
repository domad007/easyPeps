-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 21 mars 2020 à 17:46
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
-- Structure de la table `competences`
--

DROP TABLE IF EXISTS `competences`;
CREATE TABLE IF NOT EXISTS `competences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `types_id` int(11) DEFAULT NULL,
  `degre` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DB2077CE8EB23357` (`types_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `competences`
--

INSERT INTO `competences` (`id`, `nom`, `description`, `type_competence_id`, `degre`) VALUES
(1, 'cp1', 'Endurance : fournir des efforts de longue durée à une intensité moyenne', 1, 2),
(2, 'cp2', 'Souplesse : étirer les muscles des grandes articulations', 1, 2),
(3, 'cp3', 'Vélocité : exécuter des mouvements et des déplacements simples à grande vitesse', 1, 2),
(4, 'cp4', 'Force : déplacer des charges adaptées', 1, 2),
(5, 'cp5', 'Puissance alactique', 1, 2),
(6, 'hgm1', 'Adapter sa vitesse d’exécution aux nécessités du mouvement et à la durée de l’action', 2, 2),
(7, 'hgm2', 'Maintenir la régularité d’une allure ou d’un mouvement', 2, 2),
(8, 'hgm3', 'Maintenir une position', 2, 2),
(9, 'hgm4', 'Enchaîner plusieurs actions successives et/ou simultanées de façon à produire un mouvement fluide, voir harmonieux ', 2, 2),
(10, 'hgm5', 'Exécuter un mouvement en respectant le code d’exécution (conformité)', 2, 2),
(11, 'hgm6', 'Exécuter un mouvement en respectant des préceptes ergonomiques', 2, 2),
(12, 'hgm7', 'Exécuter un mouvement en respectant les règles de sécurité', 2, 2),
(13, 'hgm8', 'Adapter une technique de base, un mouvement en fonction de sa morphologie et du but poursuivi', 2, 2),
(14, 'hgm9', 'Communiquer ses émotions', 2, 2),
(15, 'hgm10', 'Gérer l’expression corporelle de ses émotions', 2, 2),
(16, 'csm1', 'Valoriser et respecter ses partenaires', 3, 2),
(17, 'csm2', 'Capter efficacement les signaux émis par ses partenaires et y réagir de manière interactive', 3, 2),
(18, 'csm3', 'Accepter les faiblesses éventuelles de ses partenaires et adapter ses comportements à la situation collective', 3, 2),
(19, 'csm4', 'Capter efficacement les signaux émis par les adversaires et y réagir de manière interactive', 3, 2),
(20, 'csm5', 'Agir avec fair-play et dans le respect de soi et de l’adversaire', 3, 2),
(21, 'csm6', 'Agir en équipe dans un but fixé', 3, 2),
(22, 'csm7', 'Assumer des rôles différents dans une équipe', 3, 2),
(23, 'csm8', 'Accepter de perdre, savoir gagner', 3, 2),
(24, 'csm9', 'Respecter des règles convenues dans l’intérêt du groupe', 3, 2),
(25, 'csm10', 'Respecter des règles de sécurité : avoir une tenue correcte et adaptée à la pratique sportive', 3, 2),
(26, 'cp1', 'Endurance : fournir des efforts de longue durée à une intensité moyenne', 1, 1),
(27, 'cp2', 'Souplesse : étirer les muscles des grandes articulations', 1, 1),
(28, 'cp3', 'Vélocité : exécuter des mouvements et des déplacements simples à grande vitesse', 1, 1),
(29, 'cp4', 'Force : déplacer des charges adaptées', 1, 1),
(30, 'cp5', 'Puissance alactique', 1, 1),
(31, 'hgm1', 'Enchainer des mouvements fondamentaux dans le but d\'une action précise en relation avec une activité codifiée et en appliquant les préceptes ergonomiques', 2, 1),
(32, 'hgm2', 'Enchainer des mouvements fondamentaux de façon à produire un mouvement fluide', 2, 1),
(33, 'hgm3', 'Utiliser ses mouvements dans des situations codifiées', 2, 1),
(34, 'hgm4', 'Utiliser des techniques d\'aide et de protection', 2, 1),
(35, 'hgm5', 'Se situer, s\'orienter, se déplacer dans un espace nouveau, le représenter', 2, 1),
(36, 'hgm6', 'Utiliser des techniques d\'équilibre dans des situations codifiées', 2, 1),
(37, 'hgm7', 'Ajuster un mouvement dans une situation codifiée', 2, 1),
(38, 'hgm8', 'Percevoir et mémomiser des structures rythmiques élaborées', 2, 1),
(39, 'hgm9', 'Modifier son rythme dans une recherche de performance et en fonction de variations extérieures', 2, 1),
(40, 'hgm10', 'Agir sur des paramètres du mouvement expressif : temps, espace, énergie', 2, 1),
(41, 'hgm11', 'Nager 25 mètre dans un style correct', 2, 1),
(42, 'csm1', 'Adapter ses comportements aux règles convenues', 3, 1),
(43, 'csm2', 'Assumer différents rôles dans une action collective', 3, 1),
(44, 'csm3', 'Utiliser des moyens techniques acquis pour participer à une action collective', 3, 1),
(45, 'csm4', 'Valoriser et respecter ses partenaires (coéquipiers et adversaires)', 3, 1),
(46, 'csm5', 'Agir avec fair-play dans la défaites et la victoire, dans le respect de soi et de ses partenaires (coéquipiers et adversaires)', 3, 1),
(47, 'csm6', 'Agir en équipe dans un but fixé', 3, 1),
(48, 'csm7', 'Assumer des rôles différents dans une équipe', 3, 1),
(49, 'csm8', 'Accepter de perdre, savoir gagner', 3, 1),
(50, 'csm9', 'Respecter des règles convenues dans l’intérêt du groupe', 3, 1),
(51, 'csm10', 'Respecter des règles de sécurité : avoir une tenue correcte et adaptée à la pratique sportive cf. règlement pour plus de détails', 3, 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `competences`
--
ALTER TABLE `competences`
  ADD CONSTRAINT `FK_DB2077CE8EB23357` FOREIGN KEY (`types_id`) REFERENCES `types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

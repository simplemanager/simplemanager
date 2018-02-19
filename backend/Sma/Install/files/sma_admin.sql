SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `sma_admin` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sma_admin`;

CREATE TABLE `account` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'type: ignored',
  `email` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'type: email\nlabel: E-mail\nplaceholder: mon@email.com\nleft:\n  icon: at',
  `password` varchar(64) CHARACTER SET utf8mb4 NOT NULL COMMENT 'type: password\nlabel: Mot de passe\nplaceholder: mot de passe\nvalidators:\n  Password:\nleft: \n  icon: key',
  `firstname` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Prénom\nfilters: \n  CleanPhrase:\n  UcPhrase:\nvalidators:\n  len: \n    min: 2\nleft: \n  icon: user\nsize: 6',
  `lastname` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Nom\nfilters: \n  CleanPhrase:\n  UcPhrase:\nvalidators:\n  len: 2\nleft: \n  icon: user\nsize: 6',
  `date_insert` datetime NOT NULL COMMENT 'type: ignored',
  `date_update` datetime NOT NULL COMMENT 'type: ignored',
  `status` enum('draft','enabled','disabled','suspended') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'enabled' COMMENT 'label: Statut\ndesc: Etat du compte\nacl: ADMIN\nleft:\n  icon: check\nsize: 6',
  `id_campaign` int(10) UNSIGNED DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COMMENT 'label: Commentaires\nacl: ADMIN',
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'type: ignored'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `account_date_insert` BEFORE INSERT ON `account` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `account_date_update` BEFORE UPDATE ON `account` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `campaign` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(10) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `provider_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_url` varchar(250) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `provider_id_company` int(10) UNSIGNED DEFAULT NULL,
  `access_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `cost` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `date_insert` int(11) NOT NULL,
  `date_update` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `campaign_insert` BEFORE INSERT ON `campaign` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `campaign_update` BEFORE UPDATE ON `campaign` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `formula` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_application` int(10) UNSIGNED NOT NULL COMMENT 'type: ignored',
  `name` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Nom',
  `description` text CHARACTER SET utf8mb4 COMMENT 'label: Description',
  `duration` smallint(5) UNSIGNED NOT NULL COMMENT 'label: Durée\nplaceholder: ''[nombre de jours]''',
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `product_date_insert` BEFORE INSERT ON `formula` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `product_date_update` BEFORE UPDATE ON `formula` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `payment` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,0) NOT NULL,
  `date_begin` date NOT NULL,
  `date_end` date NOT NULL,
  `id_product` int(10) UNSIGNED NOT NULL,
  `status` enum('topay','error','enabled','disabled','cancelled') CHARACTER SET utf8mb4 NOT NULL,
  `comment` text CHARACTER SET utf8,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `payment_date_insert` BEFORE INSERT ON `payment` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `payment_date_update` BEFORE UPDATE ON `payment` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;


ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`) USING BTREE,
  ADD KEY `id_campaign` (`id_campaign`);

ALTER TABLE `campaign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_id_company` (`provider_id_company`);

ALTER TABLE `formula`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_application` (`id_application`);

ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `payment_search` (`date_begin`,`date_end`,`status`),
  ADD KEY `id_account` (`id_account`);


ALTER TABLE `account`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'type: ignored', AUTO_INCREMENT=22;
ALTER TABLE `campaign`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `formula`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `payment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `account`
  ADD CONSTRAINT `account_ibfk_1` FOREIGN KEY (`id_campaign`) REFERENCES `campaign` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `payment`
  ADD CONSTRAINT `payment_account_fk` FOREIGN KEY (`id_account`) REFERENCES `account` (`id`),
  ADD CONSTRAINT `payment_formula_fk` FOREIGN KEY (`id_product`) REFERENCES `formula` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

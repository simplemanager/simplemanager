SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `sma_common` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `sma_common`;

CREATE TABLE `address` (
  `id` int(10) UNSIGNED NOT NULL,
  `address` varchar(250) CHARACTER SET utf8mb4 NOT NULL COMMENT 'element: textarea\nlabel: Adresse\nfilters: \n  StringTrim:\nvalidators:\n  PostalAddressBody:',
  `postal_code` varchar(8) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'label: C. Postal\nsize: 3\nfilters:\n  StringTrim:\nvalidators:\n  Alnum: 1',
  `city` varchar(80) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Ville\nsize: 9\nfilters:\n  CleanPhrase:\n  UcPhrase:',
  `country` varchar(80) CHARACTER SET utf8mb4 NOT NULL DEFAULT 'France' COMMENT 'label: Pays\nfilters:\n  CleanPhrase:\n  UcPhrase:',
  `id_account` int(10) UNSIGNED DEFAULT NULL COMMENT 'type: ignored'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `address_contact` (
`civility` varchar(5)
,`firstname` varchar(128)
,`lastname` varchar(128)
,`address` varchar(250)
,`postal_code` varchar(8)
,`city` varchar(80)
,`country` varchar(80)
,`email` varchar(255)
,`tel` varchar(32)
,`fax` varchar(32)
,`gsm` varchar(32)
,`function` varchar(80)
,`is_account` int(10) unsigned
,`id_account` int(10) unsigned
,`id_contact` int(10) unsigned
,`id_company` int(10) unsigned
);

CREATE TABLE `basket` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_invoice` int(10) UNSIGNED NOT NULL,
  `id_product` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `discount` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `company` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `hash` char(64) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `type` enum('client','prospect','mine','branch','competitor','administration','other') CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Type\nelement: select\noptions:\n  client: Client\n  prospect: Prospect\n  branch: Associé\n  competitor: Concurrent\n  administration: Administration\n  other: Autre\ndefault: client',
  `legal_status` enum('?','a','ae','ei','eurl','sarl','sa','sas','sasu','sci','scp','scm') CHARACTER SET utf8mb4 NOT NULL DEFAULT '?' COMMENT 'label: Statut Juridique\nelement: select\noptions:\n  a: Association loi 1901\n  ae: Auto Entrepreneur\n  ei: "Entreprise Individuelle / Micro Entreprise"\n  eurl: EURL (Entreprise Unipersonnelle à Responsabilité Limitée)\n  sarl: SARL (Société à Responsabilité Limitée)\n  sa: SA (Société Anonyme)\n  sas: SAS (Société par Actions Simplifiée)\n  sasu: SASU (Société par Actions Simplifiée Unipersonnelle)\n  sci: SCI (Société Civile Immobilière)\n  scp: SCP (Société Civile Professionnelle)\n  scm: SCM (Société Civile de Moyens)\ndefault: ''?''',
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Société\nelement: input\nplaceholder: Nom de la société\nleft:\n  icon: industry',
  `tel` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: tel\nelement: input\nlabel: Tel\nplaceholder: Téléphone\nleft:\n  icon: phone\nsize: 6',
  `fax` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: tel\nelement: input\nlabel: Fax\nplaceholder: Fax\nleft:\n  icon: fax\nsize: 6',
  `email` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: email\nelement: input\nlabel: Email\nplaceholder: "E-mail de contact (ex: contact@monentreprise.com)"\nleft:\n  icon: at',
  `tva_intra` varchar(30) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL COMMENT 'element: input\nlabel: TVA Intracommunautaire\nfilters: \n  CleanPhrase:\n  StringToUpper:\nvalidators:\n  TvaIntra:\nleft:\n  icon: money',
  `charge_with_tax` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COMMENT 'element: input\nlabel: Description\nplaceholder: "Description ou slogan (ex: Le numéro 1 de la gestion)"\nleft:\n  icon: bullhorn',
  `id_address` int(10) UNSIGNED DEFAULT NULL,
  `id_address_delivery` int(10) UNSIGNED DEFAULT NULL,
  `id_contact` int(10) UNSIGNED DEFAULT NULL,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `url` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: url\nelement: input\nlabel: Url\nplaceholder: "Site web (ex: http://monentreprise.com)"\nleft:\n  icon: desktop',
  `status` enum('enabled','disabled') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'enabled' COMMENT 'default: enabled',
  `id_logo` int(10) UNSIGNED DEFAULT NULL,
  `id_company` int(10) UNSIGNED DEFAULT NULL COMMENT 'Entreprise cliente',
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `company_date_insert` BEFORE INSERT ON `company` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `company_date_update` BEFORE UPDATE ON `company` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `contact` (
  `id` int(10) UNSIGNED NOT NULL,
  `civility` varchar(5) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'element: select\nleft:\n  icon: transgender\noptions:\n  M.: M.\n  Mme: Mme\n#  Mlle: Mlle\n#  Dr: Dr\n#  Me: Me\n#  Pr: Pr\nsize: 3',
  `firstname` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'label: Prénom\nfilters:\n  CleanPhrase:\n  UcPhrase:\nleft:\n  icon: user\nsize: 5',
  `lastname` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Nom\nfilters:\n  CleanPhrase:\n  UcPhrase:\nplaceholder: Nom de famille\nsize: 4',
  `function` varchar(80) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'label: Fonction\nplaceholder: Ma fonction au sein de l''entreprise\nfilters:\n  trim:\nleft:\n  icon: id-card-o',
  `email` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'label: E-mail\nplaceholder: nom@domaine.com\ntype: email',
  `tel` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: tel\nlabel: Téléphone fixe\nleft:\n  icon: phone\nsize: 4',
  `fax` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: tel\nlabel: Fax\nleft:\n  icon: fax\nsize: 4',
  `gsm` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: tel\nlabel: Mobile\nleft:\n  icon: mobile\nsize: 4',
  `id_address` int(10) UNSIGNED DEFAULT NULL COMMENT 'element: select',
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `comment` text CHARACTER SET utf8,
  `id_company` int(10) UNSIGNED DEFAULT NULL COMMENT 'type: ignored',
  `id_account` int(10) UNSIGNED NOT NULL COMMENT 'type: ignored',
  `is_account` int(10) UNSIGNED DEFAULT NULL,
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `contact_date_insert` BEFORE INSERT ON `contact` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `contact_date_update` BEFORE UPDATE ON `contact` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `document` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `id_recipient` int(10) UNSIGNED DEFAULT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `type` enum('letter','quote','order','invoice','form') CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `status` enum('created','sent','read','processed','canceled') CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'created',
  `template` varchar(128) CHARACTER SET utf8mb4 DEFAULT NULL,
  `title` varchar(256) CHARACTER SET utf8mb4 NOT NULL,
  `subject` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `document_date_insert` BEFORE INSERT ON `document` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `document_date_update` BEFORE UPDATE ON `document` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `document_event` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `id_document` int(10) UNSIGNED NOT NULL,
  `id_document_history` int(10) UNSIGNED DEFAULT NULL,
  `event` enum('creation','update','sending','read','process','delete','status_created','status_sent','status_processed','status_canceled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'creation',
  `date` datetime NOT NULL,
  `comment` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `letter_event_before_insert` BEFORE INSERT ON `document_event` FOR EACH ROW BEGIN
  SET NEW.date=NOW();
END
$$
DELIMITER ;

CREATE TABLE `document_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_document` int(10) UNSIGNED NOT NULL,
  `dump` mediumblob NOT NULL,
  `source` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `hash` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `date_insert` datetime NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `document_history_date_insert` BEFORE INSERT ON `document_history` FOR EACH ROW SET NEW.date_insert=NOW()
$$
DELIMITER ;
CREATE TABLE `document_history_current` (
`id_account` int(10) unsigned
,`id_document` int(10) unsigned
,`id_document_history` int(10) unsigned
,`id_recipient` int(10) unsigned
,`uid` int(10) unsigned
,`type` enum('letter','quote','order','invoice','form')
,`status` enum('created','sent','read','processed','canceled')
,`title` varchar(256)
,`subject` varchar(256)
,`description` text
,`template` varchar(128)
,`source` mediumtext
,`dump` mediumblob
,`hash` varchar(64)
,`date_insert` datetime
,`date_update` datetime
);

CREATE TABLE `event` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
  `description` text CHARACTER SET utf8,
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `form` (
  `id` int(10) UNSIGNED NOT NULL,
  `class` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `form_values` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `form_stats` (
  `id` int(10) UNSIGNED NOT NULL,
  `class` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_values` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `image` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('logo','unknown') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'unknown',
  `content` mediumblob NOT NULL,
  `color` varchar(6) CHARACTER SET utf8mb4 DEFAULT NULL,
  `description` varchar(250) CHARACTER SET utf8mb4 DEFAULT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoice` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `id_provider` int(10) UNSIGNED DEFAULT NULL,
  `id_recipient` int(10) UNSIGNED DEFAULT NULL,
  `id_document` int(10) UNSIGNED NOT NULL,
  `id_document_history` int(10) UNSIGNED NOT NULL,
  `code` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `product_count` smallint(10) UNSIGNED NOT NULL DEFAULT '0',
  `total_ht` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_ttc` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text CHARACTER SET utf8,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `order_date_insert` BEFORE INSERT ON `invoice` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `order_date_update` BEFORE UPDATE ON `invoice` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;
CREATE TABLE `invoice_document` (
`id_invoice` int(10) unsigned
,`id_document` int(10) unsigned
,`uid_invoice` int(10) unsigned
,`uid_document` int(10) unsigned
,`id_account` int(10) unsigned
,`id_provider` int(10) unsigned
,`id_recipient` int(10) unsigned
,`id_document_history` int(10) unsigned
,`code` varchar(32)
,`product_count` smallint(10) unsigned
,`total_ht` decimal(10,2)
,`total_ttc` decimal(10,2)
,`description` text
,`type` enum('letter','quote','order','invoice','form')
,`status` enum('created','sent','read','processed','canceled')
,`title` varchar(256)
,`subject` varchar(256)
,`document_description` text
,`date_insert` datetime
,`date_update` datetime
,`bean` text
);

CREATE TABLE `letter_template` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `title` varchar(250) CHARACTER SET utf8mb4 NOT NULL COMMENT 'element: input\nlabel: Titre du modèle\nrequired: 1',
  `description` text CHARACTER SET utf8mb4 COMMENT 'element: textarea\nlabel: Description succincte de ce modèle',
  `data_type` enum('recipient','invoices','invoice','order','quote') CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `data_type_filters` set('status_created','status_sent','status_read','status_processed','status_canceled','overdue','on_time') CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `target_type` enum('email','letter','both') CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT 'both',
  `search_data` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE TRIGGER `letter_template_date_insert` BEFORE INSERT ON `letter_template` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `letter_template_date_update` BEFORE UPDATE ON `letter_template` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `log` (
  `id` int(10) UNSIGNED NOT NULL,
  `level` enum('error','warning','info') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'info',
  `message` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `id_account` int(10) UNSIGNED DEFAULT NULL,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL,
  `category` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `page_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `dump` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `log_date_update` BEFORE UPDATE ON `log` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_date_update_insert` BEFORE INSERT ON `log` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `date_insert` datetime NOT NULL,
  `date_end` date DEFAULT NULL COMMENT 'type: date\nlabel: Fin de validité\nsize: 4',
  `icon` varchar(16) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL COMMENT 'element: select\nplaceholder: Icone\ncreate: 1\noptions:\n  info: info\n  warning: warning\n  bomb: bomb (error)\n  check: check (success)\n  bar-chart: bar-chart (resultats)\n  bell-o: bell (notification)\n  book: book (doc)\n  bookmark: bookmark\n  bug: bug\n  calendar: calendar\n  check-square-o: check-square (form/edit)\n  circle-o: circle-o\n  circle: circle\n  coffee: coffee\n  cog: cog\n  comment: comment\n  credit-card: credit-card\n  envelope: envelope\n  file-o: file-o (devis)\n  file-text-o: file-text-o (commande)\n  file-text: file-text (facture)\n  film: film (vidéo)\n  heart: heart (love)\n  legal: legal (infos légales)\n  link: link\n  question: question (?)\n  refresh: refresh\n  thumbs-down: pouce bas (j''aime pas)\n  thumbs-up: pouce haut (j''aime)\n  paper-plane-o: send / sent\n  user: user\nsize: 4',
  `color` varchar(16) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL COMMENT 'element: select\nplaceholder: Couleur\noptions:\n  blue: Bleu\n  orange: Orange\n  red: Rouge\n  green: Vert\n  fuchsia: Fuchsia\n  purple: Violet\n  aqua: Aqua\n  black: Noir\nsize: 4',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'element: input\ntype: text\nplaceholder: Contenu\nfilters:\n  StringTrim:',
  `link` varchar(128) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL COMMENT 'element: input\ntype: text\nplaceholder: Lien\nfilters:\n  StringTrim:'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `notification_before_insert` BEFORE INSERT ON `notification` FOR EACH ROW SET NEW.date_insert=NOW()
$$
DELIMITER ;

CREATE TABLE `product` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `title` varchar(128) CHARACTER SET utf8mb4 NOT NULL COMMENT 'label: Nom\nfilters:\n  trim:\nattrs:\n  autofocus:\nsize: 6',
  `price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT 'label: Prix\ntype: price\nvalidators:\n  Currency:\nfilters:\n  Currency:\n  RemoveSpaces:\nleft:\n  icon: eur\nsize: 3\ntooltip: Prix Unitaire',
  `code` varchar(16) CHARACTER SET utf8mb4 NOT NULL COMMENT 'Label: Code\nfilters:\n  trim:\ntooltip: "Code produit : laisser vide pour générer automatiquement"\nsize: 3',
  `price_type` enum('ht','ttc') CHARACTER SET utf8mb4 NOT NULL DEFAULT 'ht' COMMENT 'label: Prix mentionné\nelement: select\noptions:\n  ht: HT (sans taxe)\n  ttc: TTC (avec taxe)\nleft:\n  icon: balance-scale\ndefault: ht\nsize: 6\nrelevance: low\ntooltip: Le prix mentionné est...',
  `tax` decimal(3,1) UNSIGNED DEFAULT NULL COMMENT 'label: TVA\nelement: select\noptions:\n  20: "20% (normal)"\n  10: "10% (réduit)"\n  5.5: "5.5% (réduit)"\n  2.1: "2.1% (réduit)"\n  0: "Aucune"\nleft:\n  icon: institution\ndefault: 20\nsize: 6\nrelevance: low\ntooltip: TVA',
  `unit` varchar(3) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'element: select\nlabel: Unité\noptions:\n  h: heure\n  d: jour\n  l: litre\n  g: g\n  kg: kg\n  t: tonne\nsize: 4\ntooltip: Unité de tarif\nrelevance: low',
  `discount` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'label: Remise\ntype: number\nvalidators:\n  Percentage:\nfilters:\n  Percentage:\n  RemoveSpaces:\nleft:\n  icon: star-o\nright:\n  icon: percent\nsize: 4\nrelevance: low\nattrs:\n  step: 1\ntooltip: Remise (pourcentage)',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'element: select\nlabel: Etat\noptions:\n  1: Activé\n  0: Désactivé\nleft:\n  icon: power-off\nsize: 4\ndefault: 1\nrelevance: low\ntooltip: Désactiver un produit le rend indisponible',
  `description` text CHARACTER SET utf8mb4 COMMENT 'label: Description\nplaceholder: "Description succincte"\nfilters:\n  trim:\nrelevance: low',
  `bean_type` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT 'type: ignored',
  `bean` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `product_date_insert` BEFORE INSERT ON `product` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `product_date_update` BEFORE UPDATE ON `product` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `search` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT 'Titre original',
  `doc` text CHARACTER SET utf8mb4 COMMENT 'Document original',
  `doc_uid` int(10) UNSIGNED DEFAULT NULL,
  `search_content` text CHARACTER SET ascii COLLATE ascii_bin NOT NULL COMMENT 'contenu utilise pour la recherche',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '3' COMMENT 'Priorité 0 = faible, 5 = fort',
  `url` varchar(255) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `params` text CHARACTER SET utf8mb4 NOT NULL,
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `search_tag` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_search` int(10) UNSIGNED NOT NULL,
  `tag` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT 'filtre de recherche. Ex: c12 (contact 12) ou d45 (document 45)',
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sequence` (
  `id_account` int(10) UNSIGNED NOT NULL,
  `name` varchar(20) COLLATE ascii_bin NOT NULL,
  `value` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_bin;

CREATE TABLE `ticket` (
  `id` int(10) UNSIGNED NOT NULL,
  `category` enum('bug','doc','improvement','feature') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bug' COMMENT 'label: Type de demande\nelement: select\noptions:\n  bug: Disfonctionnement (bug)\n  doc: Problème de compréhension (demande de documentation)\n  improvement: Demande d''amélioration d''une fonctionnalité existante\n  feature: Demande de nouvelle fonctionnalité\ndefault: bug\ndesc: Le type de demande détermine sa priorité. Veillez à faire un choix pertinent.',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'element: input\nlabel: Titre\nplaceholder: Proposez un titre court et explicite\nrequired: 1\nvalidators: \n  len: 6\nfilters:\n  StringTrim:',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'element: textarea\nlabel: Description\nplaceholder: Décrivez ici votre demande avec un maximum de précision et de clarté.\nrequired: 1\nvalidators: \n  len: 10\nfilters:\n  StringTrim:',
  `response` text COLLATE utf8mb4_unicode_ci COMMENT 'element: textarea\nlabel: Réponse\nrequired: 0\nfilters:\n  StringTrim:',
  `status` enum('draft','under_study','accepted','in_progress','test','beta','published','closed','refused','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'label: Etat (status)\nelement: select\noptions:\n  draft: Brouillon\n  under_study: En cours d''étude\n  accepted: Accepté\n  in_progress: En travaux\n  test: En test\n  beta: Version beta\n  published: Publié (réalisé)\n  closed: Fermé\n  refused: Refusé (annulé)\n  deleted: Supprimé\ndefault: draft',
  `visibility` enum('public','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'label: Visibilité\nelement: select\noptions:\n  public: Publique (tous les utilisateurs)\n  admin: Administrateurs uniquement\ndefault: public',
  `id_account` int(10) UNSIGNED NOT NULL,
  `date_insert` datetime NOT NULL,
  `date_update` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `ticket_date_insert` BEFORE INSERT ON `ticket` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `ticket_date_update` BEFORE UPDATE ON `ticket` FOR EACH ROW SET NEW.date_update=NOW()
$$
DELIMITER ;

CREATE TABLE `ticket_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_ticket` int(10) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'element: input\nlabel: Commentaire\nrequired: 1\nacl: ADMIN',
  `date_insert` datetime NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DELIMITER $$
CREATE TRIGGER `ticket_log_date_insert` BEFORE INSERT ON `ticket_log` FOR EACH ROW BEGIN
  SET NEW.date_insert=NOW();
END
$$
DELIMITER ;

CREATE TABLE `ticket_poll` (
  `id_ticket` int(10) UNSIGNED NOT NULL,
  `id_account` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `ticket_with_polls` (
`id` int(10) unsigned
,`category` enum('bug','doc','improvement','feature')
,`title` varchar(255)
,`content` text
,`response` text
,`status` enum('draft','under_study','accepted','in_progress','test','beta','published','closed','refused','deleted')
,`visibility` enum('public','admin')
,`id_account` int(10) unsigned
,`date_insert` datetime
,`date_update` datetime
,`poll_count` bigint(21)
);
DROP TABLE IF EXISTS `address_contact`;

CREATE VIEW `address_contact`  AS  select `contact`.`civility` AS `civility`,`contact`.`firstname` AS `firstname`,`contact`.`lastname` AS `lastname`,`address`.`address` AS `address`,`address`.`postal_code` AS `postal_code`,`address`.`city` AS `city`,`address`.`country` AS `country`,`contact`.`email` AS `email`,`contact`.`tel` AS `tel`,`contact`.`fax` AS `fax`,`contact`.`gsm` AS `gsm`,`contact`.`function` AS `function`,`contact`.`is_account` AS `is_account`,`contact`.`id_account` AS `id_account`,`contact`.`id` AS `id_contact`,`contact`.`id_company` AS `id_company` from (`contact` left join `address` on((`contact`.`id_address` = `address`.`id`))) where (`contact`.`is_account` is not null) ;
DROP TABLE IF EXISTS `document_history_current`;

CREATE VIEW `document_history_current`  AS  select `document_history`.`id_account` AS `id_account`,`document`.`id` AS `id_document`,`document_history`.`id` AS `id_document_history`,`document`.`id_recipient` AS `id_recipient`,`document`.`uid` AS `uid`,`document`.`type` AS `type`,`document`.`status` AS `status`,`document`.`title` AS `title`,`document`.`subject` AS `subject`,`document`.`description` AS `description`,`document`.`template` AS `template`,`document_history`.`source` AS `source`,`document_history`.`dump` AS `dump`,`document_history`.`hash` AS `hash`,`document`.`date_insert` AS `date_insert`,`document`.`date_update` AS `date_update` from (`document_history` join `document`) where ((`document`.`id` = `document_history`.`id_document`) and `document_history`.`id` in (select max(`document_history`.`id`) from (`document_history` join `document`) where (`document`.`id` = `document_history`.`id_document`) group by `document`.`id`)) ;
DROP TABLE IF EXISTS `invoice_document`;

CREATE VIEW `invoice_document`  AS  select `invoice`.`id` AS `id_invoice`,`document`.`id` AS `id_document`,`invoice`.`uid` AS `uid_invoice`,`document`.`uid` AS `uid_document`,`invoice`.`id_account` AS `id_account`,`invoice`.`id_provider` AS `id_provider`,`invoice`.`id_recipient` AS `id_recipient`,`invoice`.`id_document_history` AS `id_document_history`,`invoice`.`code` AS `code`,`invoice`.`product_count` AS `product_count`,`invoice`.`total_ht` AS `total_ht`,`invoice`.`total_ttc` AS `total_ttc`,`invoice`.`description` AS `description`,`document`.`type` AS `type`,`document`.`status` AS `status`,`document`.`title` AS `title`,`document`.`subject` AS `subject`,`document`.`description` AS `document_description`,`invoice`.`date_insert` AS `date_insert`,`invoice`.`date_update` AS `date_update`,`invoice`.`bean` AS `bean` from (`invoice` join `document`) where (`invoice`.`id_document` = `document`.`id`) ;
DROP TABLE IF EXISTS `ticket_with_polls`;

CREATE VIEW `ticket_with_polls`  AS  select `ticket`.`id` AS `id`,`ticket`.`category` AS `category`,`ticket`.`title` AS `title`,`ticket`.`content` AS `content`,`ticket`.`response` AS `response`,`ticket`.`status` AS `status`,`ticket`.`visibility` AS `visibility`,`ticket`.`id_account` AS `id_account`,`ticket`.`date_insert` AS `date_insert`,`ticket`.`date_update` AS `date_update`,count(`ticket_poll`.`id_account`) AS `poll_count` from (`ticket` left join `ticket_poll` on(((`ticket`.`id` = `ticket_poll`.`id_ticket`) and `ticket_poll`.`id_account` in (select `sma_admin`.`account`.`id` from `sma_admin`.`account` where ((`sma_admin`.`account`.`id` = `ticket_poll`.`id_account`) and (`sma_admin`.`account`.`status` = 'enabled')))))) group by `ticket`.`id` ;


ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `basket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_order` (`id_invoice`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `id_address` (`id_address`),
  ADD KEY `id_contact` (`id_contact`),
  ADD KEY `id_company` (`id_company`),
  ADD KEY `id_logo` (`id_logo`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `uid` (`uid`),
  ADD KEY `id_address_delivery` (`id_address_delivery`);

ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `is_account` (`is_account`),
  ADD KEY `id_address` (`id_address`),
  ADD KEY `id_company` (`id_company`),
  ADD KEY `id_account` (`id_account`) USING BTREE;

ALTER TABLE `document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `uid` (`uid`),
  ADD KEY `type` (`type`),
  ADD KEY `id_recipient` (`id_recipient`);

ALTER TABLE `document_event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `id_document` (`id_document`),
  ADD KEY `date` (`date`),
  ADD KEY `id_document_history` (`id_document_history`);

ALTER TABLE `document_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_document` (`id_document`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `hash` (`hash`);

ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `form`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`class`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `form_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`class`);

ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `image_account` (`id_account`);

ALTER TABLE `invoice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_document` (`id_document`),
  ADD UNIQUE KEY `id_document_history` (`id_document_history`),
  ADD KEY `id_provider` (`id_provider`),
  ADD KEY `id_client` (`id_recipient`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `uid` (`uid`),
  ADD KEY `code` (`code`) USING BTREE;

ALTER TABLE `letter_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `category` (`category`),
  ADD KEY `title` (`title`);
ALTER TABLE `letter_template` ADD FULLTEXT KEY `search_data` (`search_data`);

ALTER TABLE `log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date_insert` (`date_insert`);

ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `date_end` (`date_end`);

ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`,`id_account`),
  ADD KEY `id_account` (`id_account`),
  ADD KEY `uid` (`uid`);

ALTER TABLE `search`
  ADD PRIMARY KEY (`id`),
  ADD KEY `i_level` (`level`),
  ADD KEY `id_account` (`id_account`);
ALTER TABLE `search` ADD FULLTEXT KEY `ift_doc` (`search_content`);

ALTER TABLE `search_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `i_tag` (`tag`),
  ADD KEY `id_search` (`id_search`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `sequence`
  ADD PRIMARY KEY (`name`,`id_account`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

ALTER TABLE `ticket_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_account` (`id_account`);

ALTER TABLE `ticket_poll`
  ADD PRIMARY KEY (`id_account`,`id_ticket`),
  ADD KEY `id_ticket` (`id_ticket`);


ALTER TABLE `address`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
ALTER TABLE `basket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `company`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;
ALTER TABLE `contact`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;
ALTER TABLE `document`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=169;
ALTER TABLE `document_event`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=927;
ALTER TABLE `document_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;
ALTER TABLE `event`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `form`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `form_stats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `image`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=175;
ALTER TABLE `invoice`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
ALTER TABLE `letter_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37392;
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
ALTER TABLE `product`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
ALTER TABLE `search`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19410;
ALTER TABLE `search_tag`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22014;
ALTER TABLE `ticket`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `ticket_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_3` FOREIGN KEY (`id_invoice`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basket_ibfk_4` FOREIGN KEY (`id_product`) REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basket_ibfk_5` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `company`
  ADD CONSTRAINT `company_ibfk_1` FOREIGN KEY (`id_address`) REFERENCES `address` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_ibfk_4` FOREIGN KEY (`id_logo`) REFERENCES `image` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `company_ibfk_5` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `company_ibfk_6` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `company_ibfk_7` FOREIGN KEY (`id_address_delivery`) REFERENCES `address` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `contact`
  ADD CONSTRAINT `contact_account_fk` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`),
  ADD CONSTRAINT `contact_address_fk` FOREIGN KEY (`id_address`) REFERENCES `address` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`is_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `document`
  ADD CONSTRAINT `document_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `document_ibfk_2` FOREIGN KEY (`id_recipient`) REFERENCES `contact` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `document_event`
  ADD CONSTRAINT `document_event_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `document_event_ibfk_2` FOREIGN KEY (`id_document`) REFERENCES `document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_event_ibfk_3` FOREIGN KEY (`id_document_history`) REFERENCES `document_history` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `document_history`
  ADD CONSTRAINT `document_history_ibfk_1` FOREIGN KEY (`id_document`) REFERENCES `document` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `document_history_ibfk_3` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `event`
  ADD CONSTRAINT `event_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `form`
  ADD CONSTRAINT `form_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`id_provider`) REFERENCES `company` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`id_recipient`) REFERENCES `contact` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_4` FOREIGN KEY (`id_document`) REFERENCES `document` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_5` FOREIGN KEY (`id_document_history`) REFERENCES `document_history` (`id`) ON UPDATE CASCADE;

ALTER TABLE `letter_template`
  ADD CONSTRAINT `letter_template_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `product`
  ADD CONSTRAINT `product_account_fk` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `sequence`
  ADD CONSTRAINT `sequence_ibfk_1` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE `ticket_log`
  ADD CONSTRAINT `ticket_log_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ticket_poll`
  ADD CONSTRAINT `ticket_poll_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `ticket` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ticket_poll_ibfk_2` FOREIGN KEY (`id_account`) REFERENCES `sma_admin`.`account` (`id`) ON UPDATE CASCADE;

USE `sma_admin`;
ALTER TABLE `campaign`
  ADD CONSTRAINT `campaign_ibfk_1` FOREIGN KEY (`provider_id_company`) REFERENCES `sma_common`.`company` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

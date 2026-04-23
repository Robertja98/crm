-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
-- Database: `crmdb`

-- Host: localhost
-- Generation Time: Feb 19, 2026 at 08:37 PM
-- Server version: 9.6.0
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS=0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crmdb`
--

-- --------------------------------------------------------

--
DROP TABLE IF EXISTS `audit_log`;
DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE `audit_log` (
  `id` int NOT NULL,
  `timestamp` datetime DEFAULT NULL,
  `user_id` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` varchar(100) DEFAULT NULL,
  `changes` text,
  `summary` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `error_msg` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `timestamp`, `user_id`, `ip_address`, `action`, `entity_type`, `entity_id`, `changes`, `summary`, `status`, `error_msg`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, '2026-02-19 03:42:03', '1', '::1', 'create', 'contact', '6996868bc73ee', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:42:03\"}}', 'Created new contact', 'failed', 'Data truncated for column \'id\' at row 1'),
(3, '2026-02-19 03:42:49', '1', '::1', 'create', 'contact', '699686b9cb8ce', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:42:49\"}}', 'Created new contact', 'failed', 'Data truncated for column \'id\' at row 1'),
(4, '2026-02-19 03:45:02', '1', '::1', 'create', 'contact', '6996873e8e0ac', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:45:02\"}}', 'Created new contact', 'failed', 'Out of range value for column \'id\' at row 1'),
(5, '2026-02-19 03:45:36', '1', '::1', 'create', 'contact', '699687603141e', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:45:36\"}}', 'Created new contact', 'failed', 'Out of range value for column \'id\' at row 1'),
(6, '2026-02-19 03:48:55', '1', '::1', 'create', 'contact', '6996882799e58', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:48:55\"}}', 'Created new contact', 'failed', 'Out of range value for column \'id\' at row 1'),
(7, '2026-02-19 03:50:04', '1', '::1', 'create', 'contact', '6996886c8d732', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:50:04\"}}', 'Created new contact', 'failed', 'Data truncated for column \'id\' at row 1'),
(8, '2026-02-19 03:51:12', 1, '::1', 'create', 'contact', NULL, '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:51:12\"}}', 'Created new contact', 'failed', 'Incorrect datetime value: NULL for column last_modified at row 1'),
(9, '2026-02-19 03:51:53', 1, '::1', 'create', 'contact', NULL, '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:51:53\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 03:51:53\"}}', 'Created new contact', 'failed', 'Incorrect integer value: NULL for column is_customer at row 1'),
(10, '2026-02-19 03:52:29', 1, '::1', 'create', 'contact', NULL, '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 03:52:29\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 03:52:29\"}}', 'Created new contact', 'failed', 'Incorrect date value: NULL for column delivery_date at row 1'),
(11, '2026-02-19 04:00:58', '1', '::1', 'create', 'contact', '', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 04:00:58\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 04:00:58\"}}', 'Created new contact', 'failed', 'Incorrect date value: \'\' for column \'delivery_date\' at row 1'),
(12, '2026-02-19 04:04:19', '1', '::1', 'create', 'contact', '', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 04:04:19\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 04:04:19\"}}', 'Created new contact', 'failed', 'Incorrect date value: \'\' for column \'delivery_date\' at row 1'),
(13, '2026-02-19 04:05:12', '1', '::1', 'create', 'contact', '', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 04:05:12\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 04:05:12\"}}', 'Created new contact', 'failed', 'Incorrect date value: \'\' for column \'delivery_date\' at row 1'),
(14, '2026-02-19 04:07:02', '1', '::1', 'create', 'contact', '', '{\"first_name\":{\"old\":null,\"new\":\"d\"},\"last_name\":{\"old\":null,\"new\":\"d\"},\"company\":{\"old\":null,\"new\":\"df\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 04:07:01\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 04:07:01\"}}', 'Created new contact', 'success', ''),
(15, '2026-02-19 04:19:39', '1', '::1', 'create', 'contact', '', '{\"first_name\":{\"old\":null,\"new\":\"Robert\"},\"last_name\":{\"old\":null,\"new\":\"Lee\"},\"company\":{\"old\":null,\"new\":\"a\"},\"email\":{\"old\":null,\"new\":\"robertja98@gmail.com\"},\"phone\":{\"old\":null,\"new\":\"6473550944\"},\"city\":{\"old\":null,\"new\":\"Toronto\"},\"province\":{\"old\":null,\"new\":\"Ontario\"},\"postal_code\":{\"old\":null,\"new\":\"M6B 4E4\"},\"country\":{\"old\":null,\"new\":\"Canada\"},\"created_at\":{\"old\":null,\"new\":\"2026-02-19 04:19:38\"},\"last_modified\":{\"old\":null,\"new\":\"2026-02-19 04:19:38\"}}', 'Created new contact', 'success', ''),
(16, '2026-02-19 07:49:05', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(17, '2026-02-19 07:50:54', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(18, '2026-02-19 07:51:53', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(19, '2026-02-19 07:52:54', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(20, '2026-02-19 07:53:50', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(21, '2026-02-19 07:54:45', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(22, '2026-02-19 07:58:43', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(23, '2026-02-19 08:00:43', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(24, '2026-02-19 08:01:33', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(25, '2026-02-19 08:01:51', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(26, '2026-02-19 08:02:53', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(27, '2026-02-19 08:04:30', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', ''),
(28, '2026-02-19 08:05:24', '1', '::1', 'export', 'contact', 'bulk', '{\"export_count\":{\"old\":0,\"new\":853},\"filters\":{\"old\":null,\"new\":\"[]\"}}', 'Exported 853 contacts', 'success', '');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
  `contact_id` int NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_at` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `is_customer` tinyint(1) DEFAULT NULL,
  `tank_number` varchar(50) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `first_name`, `last_name`, `company`, `email`, `phone`, `address`, `city`, `province`, `postal_code`, `country`, `notes`, `created_at`, `last_modified`, `is_customer`, `tank_number`, `delivery_date`, `tags`) VALUES
(1, 'Wayne', 'Arsenault', 'AIRBORN FLEXIBLE CIR', 'arsenaultw@airbornflex.com', '(416) 752-2224', '', 'East York', 'ON', 'L3R 4C1d', '', '', NULL, NULL, 0, '', NULL, '\r'),
(2, '', '', 'LEYSONS PRODUCTS', '', '905) 648-7832', '', 'Ancaster', '', 'M9W 4Y1', '', '', NULL, NULL, 0, '', NULL, '\r'),
(3, '', '', 'Activation Labratories', 'ancaster@actlabs.com', '905) 648-9611', '', 'Ancaster', '', 'M9L 2X6', '', '', NULL, NULL, 0, '', NULL, '\r'),
(4, '', '', 'ACTIVATION LABORATOR', '', '', '', 'Ancaster', '', 'M3J 2K8', '', '', NULL, NULL, 0, '', NULL, '\r'),
(5, '', '', 'ACTIVATION LABORATOR', '', '', '', '', '', 'M1N 1V4', '', '', NULL, NULL, 0, '', NULL, '\r'),
(6, '', '', 'JANA LABORATORIES', '', '905 726 8550', '', 'Aurora', 'ON', 'L7A 1A7', '', '', NULL, NULL, 0, '', NULL, '\r'),
(7, '', '', 'O I CANADA', '', '905) 457-2423', '', 'Brampton', '', '', '', '', NULL, NULL, 0, '', NULL, '\r'),
(8, 'Hamid', 'Moazami', 'A Berger Precision Limited', '', '905 840 4207', '', 'Brampton', 'ON', 'l7A 1A7', '', '', NULL, NULL, 0, '', NULL, '\r'),
(9, '', '', 'NEWTRON GROUP', '', '(905) 458-1400', '', 'Brampton', '', 'L9G 4V5', '', '', NULL, NULL, 0, '', NULL, '\r'),
(10, '', '', 'GRAFF DIAMOND PRODUC', 'juan@graffdiamond.com', '1 800-465-9021', '', 'Brampton', '', '', '', '', NULL, NULL, 0, '', NULL, '\r'),
(11, 'Richardo', '', 'Asahi Refining', '', '(905) 453-6120', '', 'Brampton', '', 'L4W 2S6', '', '', NULL, NULL, 0, '', NULL, '\r'),
(12, 'Joe', '', 'INOX INDUSTRIES', 'joe@inoxindustries.com', '(905) 799-9996', '', 'Brampton', '', 'M1R 3C3', '', '', NULL, NULL, 0, '', NULL, '\r'),
(13, 'Woods', '', 'CHROMOFLO TECHNOLOGI', '', '(905) 451-3810', '', 'Brampton', '', 'L5T 1H5', '', '', NULL, NULL, 0, '', NULL, '\r'),
(14, 'Vince', 'Pietrantonio', 'Embassy Ingredients', '', '905) 789-3200', '', 'Brampton', '', 'L5T 1Z7', '', '', NULL, NULL, 0, '', NULL, '\r'),
(15, '', '', 'CHROMAFLOW TECHNOLOG', '', '(905) 451-3810', '', 'Brampton', '', '', '', '', NULL, NULL, 0, '', NULL, '\r'),
(16, '', '', 'Newtron Group 1192901 ONTARIO LTD', '', '905.458.1400', '', 'Brampton', '', 'L6T 3Y3', '', '', NULL, NULL, 0, '', NULL, '\r'),
(17, 'Bob', '', 'Syncoat Chemcials Ltd', '', '(905) 270-2391', '', 'Brantford', 'ON', 'L5N 6B9', '', '', NULL, NULL, 0, '', NULL, '\r'),
(18, 'Tammy', '', 'HAWCO PRODUCTS LTD', 'tammy@hawcoproducts.com', '(519) 759-2443', '', 'Brantford', '', 'N3T 5M1', '', '', NULL, NULL, 0, '', NULL, '\r'),
(19, 'Ext 209', '', 'JOHNSEN ULTRAVAC', '', '', '', 'Burlington', 'ON', 'L7M 1A8', '', '', NULL, NULL, 0, '', NULL, '\r'),
(20, 'Enzo', '', 'Canada Brick', '', '(905) 633-7384', '', 'Burlington', '', 'M1V 4A9', '', '', NULL, NULL, 0, '', NULL, '\r'),
(21, 'Diane', 'Iles', 'ASK COSMETICS INC', '', '(905) 634-2454', '', 'Burlington', '', 'N5A 6T3', '', '', NULL, NULL, 0, '', NULL, '\r'),
(22, '', '', 'OC TANNER MFG LTD', 'service@octanner.com', '905 632 7255', '', 'Burlington', '', 'L5E 1E4', '', '', NULL, NULL, 0, '', NULL, '\r'),
(23, '', '', 'CAPO INDUSTRIES LTD', 'purchasing@capoindustries.com', '905 332 6626', '', 'Burlington', '', 'L7L 5R6', '', '', NULL, NULL, 0, '', NULL, '\r'),
(24, '', '', 'Rampf', '', '905) 331-8042', '', 'Burlington', '', 'L7L 6A6', '', '', NULL, NULL, 0, '', NULL, '\r'),
(25, 'Janet', 'Augland', 'ATOTECH CANADA LTD', '', '(905) 332-0111', '', 'Burlington', '', 'L7L 5R6', '', '', NULL, NULL, 0, '', NULL, '\r'),
(26, '', '', 'WILLARD MFG INC', '', '905) 633-6905', '', 'Brampton', '', 'L6T 5G5', '', '', NULL, NULL, 0, '', NULL, '\r'),
(27, 'Ohmar', 'Chijaini', 'ARZON LTD', 'ochijani@arzonlimited.com', '905) 332-5600', '', 'Burlington', '', 'L7L 7P3', '', '', NULL, NULL, 0, '', NULL, '\r'),
(28, '', '', 'CANADA MALTING CO LT', '', '', '', 'Calgary', '', '', '', '', NULL, NULL, 0, '', NULL, '\r'),
(29, 'Bryan', '', 'Cambridge Customer Chrome', 'ccc@cambridgecustomchrome.com', '226 988 3449', '', 'Cambridge', '', 'N3C 3W2', '', '', NULL, NULL, 0, '', NULL, '\r'),
(30, 'Robert', 'Krebsz', 'INDUSTRIAL PROCESSIN', 'rkrebsz@industrialprocessing.ca', '(519) 621-2571', '', 'Cambridge', '', 'N1R 7Z1', '', '', NULL, NULL, 0, '', NULL, '\r'),
(31, '', '', 'INDUSTRIAL PROCESSIN', '', '(519) 621-2571', '', 'Cambridge', '', 'N1R 7Z1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(32, '', '', 'Innovative Steam Technologies', '', '(519) 740-0036', '', 'Cambridge', '', 'N1R 7P4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(33, '', '', 'Honeywell Aerospace', '', '15196222300', '', 'Cambridge', '', 'N1R 7H6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(34, '', '', 'CANADIAN GEN TOWERS', '', '(519) 623-1630', '', 'Cambridge', '', 'N1R 5T6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(35, '', '', 'DAVID W WILSON MFG L', '', '', '', 'Cathcart', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(36, '', '', 'LAKE SUPERIOR POWER', '', '(905) 984-8383', '', 'Catherines', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(37, '', '', 'AB SCIEX LP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(38, '', '', 'MAGNA EXTERIORS AND', '', '(905) 669-2888', '', 'Concord', '', 'L4K 4J5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(39, '', '', 'CO EX TEC', '', '(905) 738-8710', '', 'Concord', '', 'L4K 2X3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(40, '', '', 'DCL INTERNATIONAL', '', '905 660 6450', '', 'Concord', '', 'L4K 4T5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(41, '', '', 'K-G PACKAGING INC', '', 'v', '', 'Concord', '', 'L4K 1Y8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(42, '', '', 'ALLBRAND ELECTRONICS', '', '905 479 4141', '', 'Concord', '', 'L4K 2Y7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(43, 'Mark', 'Henry', 'The Plating House', '', '(416) 661-3964', 'C1-116 Viceroy Rd', 'Concord', 'ON', 'L4K 2L8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(44, '', '', 'MULTIMATIC MANUFACTU', '', '(905) 879-0200', '', 'Concord', '', 'L4K 4V6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(45, 'Johnathan', 'Lake', 'ANTON MANUFACTURING', '', '905 879 0500', '', 'Concord', '', 'L4K 2N2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(46, 'Corey ', 'Multin', 'TRACKLESS VEHICLES L', '', '(519) 688-0370', '', 'Courtland', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(47, 'Myles', 'Melcher', 'CARMEUSE LIME', 'myles.melchers@carmuse.com', '1 866-780-0974', '', 'Dundas', '', 'L9H 5E2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(48, '', '', 'Schaeffler Canada', '', '(905) 829-2750', '', 'E Unit 101', '', 'Rd E Unit 101,', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(49, '', '', 'AIRBORN FLEXIBLE CIR', '', '(416) 752-2224', '', 'East York', '', 'M4B 1Y9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(50, '', '', 'AIRBORN FLEXIBLE CIR', '', '', '', 'East York', '', 'M4B 1Y9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(51, '', '', 'Sulco Chemcials Ltd', '', '(519) 669-5166', '', 'Elmira', '', 'N3B 2Z5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(52, '', '', 'CSA INTERNATIONAL', '', '(416) 747-4000', '', 'Etobicoke', '', 'M9W 1R3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(53, '', '', 'National Silicates Ltd', '', '(416) 255-7771', '', 'Etobicoke', '', 'M8Z 5C7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(54, '', '', 'Kinectrics Inc', '', '416) 207-6000', '', 'Etobicoke', '', 'M8Z 5G5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(55, '', '', 'MMG CANADA LTD TT EL', 'melissa.marques@mmgca.com', '(416) 251-2831', '', 'Etobicoke', '', 'M8Z 5J4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(56, 'Roger', 'Dolicker', 'Acadian Platers Company', '', '(416) 743-7130', '', 'Etobicoke', '', 'M9W 1R8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(57, '', '', 'Ultraform', '', '(416) 749-9323', '', 'Etobicoke', '', 'M9V 3Y8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(58, 'Chris', 'Goodwin', 'Nanowave Inc', 'cgoodwin@nanowavetech.com', '416 252 5602', '', 'Etobicoke', '', 'M8W 4W3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(59, '', '', 'ESSILOR CANADA', '', '416) 252-5458', '', 'Etobicoke', '', 'M8Z 1K2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(60, '', '', 'ESSILOR CANADA', '', '(416) 252-5458', '', 'Etobicoke', '', 'M8Z 1K2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(61, '', '', 'BASF CANADA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(62, '', '', 'Continuous Colour Coat Ltd', '', '416 743 7980', '', 'Rexdale', 'ON', 'M9W 4Y1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(63, 'Dave', 'Damen', 'STAINLESS PIPING SYS', '', '(416) 679-2937', '', 'Etobicoke', '', 'M9W 6N4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(64, '', '', 'KIK CORP', '', '(416) 740-7400', '', 'Etobicoke', '', 'M9W 1G1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(65, '', '', 'CALEDON LABORATORY C', '', '(905) 877-0101', '', 'Georgetown', '', 'L7G 4R9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(66, '', '', 'COMMUNICATIONS & POW', '', '(905) 877-0161', '', 'Georgetown', '', 'L7G 2J4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(67, '', '', 'INTEL VAC', '', '1 800-959-5517', '', 'Georgetown', '', 'L7G 4X6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(68, '', '', 'HPG INC', '', '', '', 'Georgetown', '', 'L7G 0C6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(69, '', '', 'SIFTO CANADA INC', '', '', '', 'Goderich', '', 'N7A 2L9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(70, '', '', 'SEMEX - GUELPH LABOR', '', '(519) 821-5060', '', 'Guelph', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(71, '', '', 'CANADIAN SOLAR MANUF', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(72, '', '', 'NESTLE WATERS CANADA', '', '(519) 763-9462', '', 'Guelph', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(73, '', '', 'GUELPH UTILITY POLE', '', '(519) 822-3901', '', 'Guelph', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(74, '', '', 'MOLD MASTERS LTD', '', '(905) 877-0185', '', 'Halton Hills', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(75, '', '', 'DALPRO TECHNOLOGIES', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(76, '', '', 'A RAYMOND TINNERMAN', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(77, '', '', 'HAMILTON PAPER BOX C', '', '(905) 548-0999', '', 'Hamilton', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(78, '', '', 'PIONEER BALLOON CANA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(79, '', '', 'CFF SPECIALTY', '', '905) 549-2603', '', 'Hamilton', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(80, '', '', 'A RAYMOND TINNERMAN', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(81, '', '', 'A RAYMOND TINNERMAN', '', '(905) 549-4661', '', 'Hamilton', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(82, '', '', 'SAF T CAB', '', '', '', 'Huron Park', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(83, '', '', 'TETRA CHEM INDUSTRIE', '', '(519) 485-4370', '', 'Ingersoll', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(84, '', '', 'SKYWORKS SOLUTIONS I', '', '', '', 'Kanata', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(85, '', '', 'SAVARIN SPRINGS INC', '', '', '', 'Kitchener', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(86, '', '', 'SCI LAB MATERIAL TES', '', '519) 895-0500', '', 'Kitchener', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(87, '', '', 'KUNTZ ELECTROPLATING', '', '(519) 893-7680', '', 'Kitchener', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(88, '', '', 'ULTRATEC SPECIAL EFF', '', '519) 659-7972', '', 'London', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(89, '', '', 'SCANDIA METAL FINISH', '', '(519) 451-4365', '', 'London', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(90, '', '', 'ZUCORA INC', '', '1 800-388-2640', '', 'London', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(91, '', '', 'STRATHCRAFT TROPHIES', '', '1 877-661-1103', '', 'London', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(92, '', '', 'TROJAN TECHNOLOGIES', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(93, '', '', 'CREATION TECHNOLOGIE', '', '905.754.0055', '', 'Markham', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(94, '', '', 'DATA CIRCUITS', '', '(905) 477-4400', '', 'Markham', '', 'L3R 2Z3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(95, '', '', 'UMICORE PRECIOUS MET', '', '(905) 475-9566', '', 'Markham', '', 'L3R 1B7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(96, '', '', 'DATA CIRCUITS', '', '905) 477-4400', '', 'Markham', '', 'L3R 2Z3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(97, '', '', 'STENTECH', '', '905) 472-7773', '', 'Markham', '', 'L6E 1A4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(98, '', '', 'SIBER CIRCUITS INC', '', '905) 470-0515', '', 'Markham', '', 'L3R 5J6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(99, '', '', 'PANCAP INC', '', '', '', '', 'On', 'L3R 6E9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(100, '', '', 'ESTEE LAUDER', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(101, '', '', 'SILLIKER LABORATORIE', '', '905) 479-5255', '', 'Markham', '', 'L3R 5V5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(102, '', '', 'ARAMARK', '', '(905) 472-8065', '', 'Markham', '', 'L3S 3L5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(103, '', '', 'RAYTHEON ELCAN OPTIC', '', '(705) 526-5401', '', 'Midland', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(104, '', '', 'RECOCHEM INC', '', '(905) 878-5544', '', 'Milton', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(105, '', '', 'KARMAX HEAVY STAMPIN', '', '905) 878-5571', '', 'Milton', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(106, 'Darshan', '', 'BRYCE INDUSTRIES INC', '', '(905) 678-1548', '', 'Mississauga', '', 'L5S 1B1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(107, '', '', 'CAMBRIDGE MATERIAL T', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(108, '', '', 'EXOVA CANADA INC', '', '(905) 822-4111', '', 'Mississauga', '', 'L5K 1B3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(109, '', '', 'MERSEN CANADA TORONT', '', '(416) 252-9371', '', 'Mississauga', '', 'L5T 1Z1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(110, '', '', 'HOPE AERO PROPELLER', '', '', '', 'Mississauga', '', 'L4T 3T1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(111, '', '', 'PVD ADVANCED TECHNOL', '', '(905) 564-1859', '', 'Mississauga', '', 'L5T 1L9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(112, '', '', 'GENTEK BUILDING PROD', '', '(416) 745-6133', '', 'Mississauga', '', 'L5T 1Z4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(113, '', '', 'AXIOMATIC TECHNOLOGI', '', '(905) 602-9270', '', 'Mississauga', '', 'L4Z 1Z8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(114, '', '', 'COVALON TECHNOLOGIES', '', '(905) 568-8400', '', 'Mississauga', '', 'L4W 5S7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(115, '', '', 'ROTOMETRICS CANADA I', '', '905) 858-3800', '', 'Mississauga', '', 'L5N 6P9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(116, '', '', 'ROLLSTAR METAL FORMI', '', '', '', 'Mississauga', '', 'L4V 1L1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(117, '', '', 'MGA RESEARCH CORPORA', '', '905) 670-3330', '', 'Mississauga', '', 'L4W 4T2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(118, '', '', 'XEROX RESEARCH CENTR', '', '(905) 823-7091', '', 'Mississauga', '', 'L5K 2L1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(119, '', '', 'DAHL BROTHERS LTD', '', '905) 822-2330', '', 'Mississauga', '', 'L5J 2M4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(120, '', '', 'HENKEL CANADA CORPRO', '', '', '', 'Mississauga', '', 'L5N 6C3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(121, '', '', 'PEARLON PRODUCTS LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(122, '', '', 'PRATT & WHITNEY', '', '905) 564-7500', '', 'Mississauga', '', 'L5T 1J3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(123, 'Michael', 'V', 'BASIC PACKAGING IND', 'michaelv@basicpackaging.com', '905) 890-0922', '', 'Mississauga', '', 'L4Z 1P1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(124, '', '', 'TRILLIUM MEDITEC INC', '', '416) 840-3428', '', 'Mississauga', '', 'L4V 1T4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(125, 'Michael', 'Cheek', 'ALPHACHEM LTD', 'mrcheek@alphachem.ca', '(905) 821-2995', '', 'Mississauga', '', 'L5N 5Z6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(126, '', '', 'ARMSTRONG MFG INC', '', '(905) 566-1395', '', 'Mississauga', '', 'L4Y 1Y7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(127, '', '', 'NATIONAL BAIT CO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(128, '', '', 'QSDM', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(129, '', '', 'DANTE GROUP', '', '905) 678-9916', '', 'Mississauga', '', 'L5S 1N6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(130, '', '', 'PROTEC FINISHERS', '', '(905) 564-5338', '', 'Mississauga', '', 'L5T 1C4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(131, '', '', 'BURCKHARDT COMPRESSI', '', '844 307 0222', '', 'Mississauga', 'ON', 'L4W 4M2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(132, '', '', 'HYDROGENICS CORP', '', '905) 361-3660', '', 'Mississauga', '', 'L5T 2N6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(133, '', '', 'INFASCO', '', '(905) 677-8920', '', 'Mississauga', '', 'L4V 1P8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(134, '', '', 'LABSTAT', '', '', '', 'N', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(135, '', '', 'MARITIME HOUSE METAL', '', '\'+1 (613) 354 3808', '', 'Napanee', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(136, '', '', 'SAF DRIVES INC', '', '(519) 662-6489', '', 'New Hamburg', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(137, '', '', 'CLOSURES TECHNICAL C', '', '(905) 898-2665', '', 'Newmarket', '', 'L3Y 4X7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(138, '', '', 'SAINT GOBAIN CERAMIC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(139, '', '', 'CYTEC CANADA INC', '', '(905) 356-9000', '', 'Niagara Falls', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(140, '', '', 'ISOMERS LABORATORIES', '', '(416) 787-2465', '', 'North York', '', 'M6B 1W3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(141, '', '', 'SECURITY MIRROR IND', '', '416) 244-3393', '', 'North York', '', 'M6M 2P5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(142, '', '', 'DE HAVILLAND INC', '', '', '', 'North York', '', 'M3K 1Y5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(143, '', '', 'TORONTO RESEARCH CHE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(144, '', '', 'FOUR STAR PLATING', '', '416) 745-1742', '', 'North York', '', 'M9L 1X5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(145, '', '', 'ABC GROUP TECH CENTR', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(146, '', '', 'SOUTHERN GRAPHICS', '', '(416) 512-6692', '', 'North York', '', 'M2N 6S6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(147, '', '', 'SANOFI PASTEUR LTD', '', '416) 667-2700', '', 'North York', '', 'M2R 3T4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(148, 'Mohamed', 'Sadeghian', 'CDN CUSTOM PACKAGING', 'msadeghian@cdncustompackaging.com', '', '', 'North York', 'ON', 'M3J 3J9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(149, '', '', 'THOMSON RESEARCH', '', '416) 955-1881', '', 'North York', '', 'M3C 1Y9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(150, '', '', 'DANA CANADA CORP', '', '905) 849-1200', '', 'Oakville', '', 'L6K 3E4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(151, '', '', 'DANA CANADA CORP', '', '905) 849-1200', '', 'Oakville', '', 'L6K 3E4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(152, '', '', 'VIROX TECHNOLOGIES I', '', '(905) 813-0110', '', 'Oakville', '', 'L6H 6R1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(153, '', '', 'MANCOR', '', '905) 844-0581', '', 'Oakville', '', 'L6J 7X6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(154, '', '', 'ALLCOLOUR PAINT LTD', '', '(905) 827-4173', '', 'Oakville', '', 'L6L 2X5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(155, '', '', 'ATOTECH CANADA LTD', '', '1 905 332 0111', '', 'Ontario', '', 'L7L 5R6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(156, '', '', 'EXOVA CANADA INC', '', '15195509021', '', 'Mississauga', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(157, '', '', 'GAMMA CLEAN', '', '(905) 576-4450', '', 'Oshawa', '', 'L1J 8M8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(158, '', '', 'TECHFORM PRODUCTS LT', '', '(705) 549-7406', '', 'Penetanguishene', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(159, '', '', 'REENA ENTERPRISES LT', '', '', '', 'Scarborough', '', 'M1X 1E7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(160, 'Andrew', 'Douglas', 'CAMECO FUEL MFG INC', '', '905 885 1129', '', 'Port Hope', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(161, '', '', 'NOVX SYSTEMS INC', '', '(905) 474-5051', '', 'Richmond Hill', '', 'L4B 4N1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(162, '', '', 'SILCHEM INC', '', '905) 709-5867', '', 'Richmond Hill', '', 'L4B 1H7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(163, '', '', 'ARTAFLEX INC', '', '(905) 470-0109', '', 'Richmond Hill', '', 'L4B 1B4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(164, '', '', 'POLYBRITE', '', '', '', 'Richmond Hill', '', 'L4C 1A8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(165, 'Ramana', 'Wignarajah', 'WGI MFG INC', 'Ramana.Wignarajah@ia.ca', '416) 412-2970', '3 Pullman Crt', 'Scarborough', '', 'M1X 1E4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(166, '', '', 'AQUABOND INC', '', '(416) 754-7211', '440 Passmore Ave', 'Scarborough', 'ON', 'M1V 5J8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(167, '', '', 'UNDERWRITERS LABORAT', '', '', '', 'Scarborough', '', 'M1R 5P8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(168, '', '', 'PFC CIRCUITS', '', '416 750 8433', '11 Canadian Road', 'Scarborough', 'ON', 'M1R 5G1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(169, '', '', 'ACT COSMETICS CANADA', '', '(416) 285-6228', '1-11 Canadian Road', 'Scarborough', 'ON', 'M1R 5G1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(170, '', '', 'QUALITY PLATING LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(171, '', '', 'SAE POWER', '', '416) 298-0560', '1810 Birchmount Road', 'Scarborough', 'ON', 'M1P 2H7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(172, '', '', 'HEROUX DEVTEK', '', '', '', 'Scarborough', '', 'M1P 2E3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(173, '', '', 'THE SOLAR GROUP INC', 'admin@solarwindowcleaning.com', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(174, '', '', 'ELIZABETH GRANT', '', '1 877-751-1999', '381 Kennedy Rd', 'Scarborough', 'ON', 'M1K 2A1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(175, '', '', 'BESTWIND INDUSTRIES', '', '(416) 289-3993', '705 Progress Ave', 'Scarborough', 'ON', 'M1H 2X1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(176, '', '', 'CHEMTURA', '', '(416) 284-1662', '10 Chemical Crt', 'Scarborough', 'ON', 'M1E 3X7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(177, '', '', 'AMPHENOL CANADA CORP', '', '(416) 291-4401', '5950 14h Ave', 'Markham', 'ON', 'L3S 4M4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(178, '', '', 'TTM Technologies', '', '(416) 208-2100', '8150 Sheppard Ave E', 'Scarborough', 'ON', 'M1B 5K2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(179, '', '', 'PURDUE PHARMA', '', '\'+1 800-387-5349', '', 'Pickering', '', 'L1W 3W8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(180, '', '', 'Safran Landing Systems', '', '905) 683-3100', '', 'Ajax', '', 'L1S 2G8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(181, '', '', 'AEROSPACE METAL FINI', '', '905) 939-8830', '', 'Schomberg', '', 'L0G 1T0', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(182, '', '', 'DYNAMIC AND PROTO CI', '', '(905) 643-9900', '', 'Stoney Creek', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(183, '', '', 'Stratford Agri Analysis Inc', '', '(519) 273-4411', '1131 Erie Street', 'Stratford', 'ON', 'N5A 6S4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(184, '', '', 'CLEMMER TECHOLOGIES', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(185, '', '', 'INTERNATIONAL GRAPHI', '', '1 800-565-5345', '505 Douro St', 'Stratford', 'ON', 'N5A 3S9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(186, '', '', 'EASTERN POWER LTD', '', '416) 234-1301', '2275 Lake Shore Blvd W', 'Toronto', 'ON', 'M3V 3Y3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(187, '', '', 'BABCOCK & WILCOX', '', '1 855-696-9588', '75 Savage Dr', 'Cambridge', 'ON', 'N1T 1S5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(188, '', '', 'LEASIDE CURLING CLUB', '', '(647) 748-2875', '', 'Toronto', '', 'M4G 1X6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(189, '', '', 'MONDELEZ CANADA INC', '', '(647) 243-5400', '5 Bermondsey Road', 'Toronto', 'ON', 'M6J 3L9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(190, '', '', 'HOUSE OF HORVATH INC', '', '', '', 'Toronto', '', 'M6J 2Z2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(191, '', '', 'COMMERCIAL ALCOHOLS', '', '(416) 304-1700', '1400-20 Toronto St', 'Toronto', 'ON', 'M5C 2B8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(192, '', '', 'CANADA COLOUR & CHEM', '', '(416) 443-5500', '175 Bloor St E', 'Toronto', 'ON', 'M4W 3R8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(193, '', '', 'BRENNTAG CANADA INC', '', '(416) 259-8231', '133 The West Mall', 'Etobicoke', 'ON', 'M9C 1C2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(194, '', '', 'MORGAN SOLAR INC', '', '(416) 203-1655', '100 Symes Road', 'Toronto', 'ON', 'M6N 0A8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(195, '', '', 'RELECTRONICS SERVICE', '', '519-884-8665', '785 Bridge Street W', 'Waterloo', 'ON', 'N2V 2K1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(196, '', '', 'BEN MACHINE PRODUCTS', '', '(905) 856-7707', '1-8065 Huntington Road', 'Woodbridge', 'ON', 'L4H 3T9', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(197, '', '', 'FISCHER CANADA', '', '519) 746-0088', '190 Frobisher Dr', 'Waterloo', 'ON', 'N2V 2A2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(198, '', '', 'WESTEX COATINGS', '', '519) 884-2260', '639 Colby Dr', 'Waterloo', 'ON', 'N2V 1B4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(199, '', '', 'ACCU-AUTOMATION', '', '(519) 725-9090', '141 Dearborn Pl', 'Waterloo', 'ON', 'N2J 4N5', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(200, '', '', 'VALLEY BLADES LTD', '', '(519) 885-5500', '425 Phillip St', 'Waterloo', 'ON', 'N2L 3X2', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(201, '', '', 'HAUSER INDUSTRIES IN', '', '1 800-268-7328', '330 Weber St N', 'Waterloo', 'ON', 'N2J 3H6', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(202, 'Rochan', '', 'CROVEN CRYSTALS', '', '905) 668-3324', '500 Beech St', 'Whitby', 'ON', 'L1N 7T8', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(203, '', '', 'ACTIVE BURGESS MOULD', '', '519) 737-1341', '', 'Windsor', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(204, '', '', 'WESCAST CASTING WING', '', '(519) 357-3450', '', 'Wingham', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(205, '', '', 'P3T LAB', '', '905) 851-9237', '', 'Woodbridge', '', 'L4L 2A4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(206, '', '', 'AD FIRE SYSTEMS', '', '(905) 660-4077', '', 'Woodbridge', '', 'L4L 5Y3', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(207, '', '', 'RAYTHEON', '', '905) 265-1723', '', 'Woodbridge', '', 'L4L 8V1', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(208, 'Rosen', '', 'SPUTTEK INC', 'info@sputtek.com', '(416) 213-9833', '110 Sharer Rd', 'Woodbridge', 'ON', 'L4L 8P4', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(209, '', '', 'FIRESTONE TEXTILES', '', '(519) 537-6231', '', 'Woodstock', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 'yes', '0000-00-00', '\r'),
(210, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(211, '', '', 'TRANSCANADA ENERGY L', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(212, '', '', 'NATIONAL RESEARCH CO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(213, '', '', 'LONDON HEALTH SCIENC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(214, '', '', 'SULCO CHEMICALS LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(215, '', '', 'APOTEX PHARMACHEM IN', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(216, '', '', 'HOME HARDWARE STORES', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(217, '', '', 'APOTEX INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(218, '', '', 'PATHEON INC WHITBY O', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(219, '', '', 'HONDA CANADA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(220, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(221, '', '', 'BELVEDERE INTL 26741', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(222, '', '', 'ELCAN OPTICAL TECH R', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(223, '', '', 'CML HEALTHCARE INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(224, '', '', 'NIAGARA HEALTH SYSTE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(225, '', '', 'TARO PHARMACEUTICALS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(226, '', '', 'LAKERIDGE HEALTH OSH', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(227, '', '', 'CDN CUSTOM PACKAGING', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(228, '', '', 'MILTON HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(229, 'Daniel', 'Vanderstern', 'Toronto Zoo', 'purchasing@torontozoo.ca', '416 392 5916', '2000 Meadowvale Road', 'Scarborough', 'ON', 'M1B 5K7', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(230, '', '', 'TORONTO GEN HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(231, '', '', 'BIO AGRI MIX LP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(232, '', '', 'NIAGARA HEALTH SYSTE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(233, '', '', 'GENERAL MOTORS COMPA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(234, '', '', 'WELLSPRING PHARMACEU', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(235, '', '', 'EPM', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(236, '', '', 'APOTEX PHARMACHEM IN', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(237, '', '', 'BIO CLEAN SERVICES I', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(238, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(239, '', '', 'THE WILLIAM OSLER HE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(240, '', '', 'ROYAL VICTORIA HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(241, '', '', 'PURDUE PHARMA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(242, '', '', 'SPRAY PAK INDUSTRIES', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(243, '', '', 'TORONTO GEN HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(244, '', '', 'PATHEON INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(245, '', '', 'SGS CANADA INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(246, '', '', 'PATHEON', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(247, '', '', 'MARKHAM STOUFFVILLE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(248, '', '', 'GEORGETOWN HOSP CAMP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(249, '', '', 'GO TRANSIT', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(250, '', '', 'ATLANTIC POWER - NIP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(251, '', '', 'COMPOUND METAL COATI', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(252, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(253, '', '', 'PATHEON WHITBY INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(254, '', '', 'CITY OF TORONTO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(255, '', '', 'PILLAR5 PHARMA INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(256, '', '', 'APOTEX INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(257, '', '', 'PANCAP INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(258, '', '', 'PATHEON', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(259, '', '', 'HYDRO ONE NETWORKS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(260, '', '', 'HYDRO ONE NETWORKS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(261, '', '', 'REMBRANDT CHARMS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(262, '', '', 'COOPER STANDARD AUTO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(263, '', '', 'APOTEX PHARMACHEM IN', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(264, '', '', 'HAMILTON CIVIC HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(265, '', '', 'COLMAR CORP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(266, '', '', 'TARO PHARMACEUTICALS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(267, '', '', 'SAINT JOSEPHS HOSP', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(268, '', '', 'HYDRO ONE NETWORKS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(269, '', '', 'UNIVERSITY OF WATERL', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(270, '', '', 'CARILLION ELLISDON S', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(271, '', '', 'FORD WINDSOR ENGINE', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(272, '', '', 'SUNNYBROOK HEALTH SC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(273, '', '', 'EAST WINDSOR COGENER', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(274, '', '', 'VALE CANADA LIMITED', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(275, '', '', 'PARMALAT CANADA INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(276, '', '', 'MRHH MACKENZIE RICHM', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(277, '', '', 'MULTI CHAIR', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(278, '', '', 'BABCOCK & WILCOX', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(279, '', '', 'CHALMERS REGIONAL HO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(280, '', '', 'CHARLSTON INTERNATIO', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(281, '', '', 'ACUREN GROUP INC', '', '19058390015', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(282, '', '', 'UNIVERSITY OF WATERL', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(283, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(284, '', '', 'BABCOCK & WILCOX', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(285, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(286, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(287, '', '', 'PIONEER HI BRED', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(288, '', '', 'L 3 COMMUNICATIONS C', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(289, '', '', 'APOTEX INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(290, '', '', 'APOTEX INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(291, '', '', 'PATHEON WHITBY INC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(292, '', '', 'PATHEON', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(293, '', '', 'APOTEX', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(294, '', '', 'UNIVERSITY OF WATERL', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(295, '', '', 'PATHEON YM', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(296, '', '', 'CINRAM LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(297, '', '', 'LHSC UNIVERSITY', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(298, '', '', 'COMMUNICATIONS AND P', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(299, '', '', 'US STEEL CANADA', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(300, '', '', 'ST JOSEPH HAMILTON', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(301, '', '', 'HOLLYS ANODIZING SER', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(302, '', '', 'ARCELORMITTAL DOFASC', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(303, '', '', 'SANOFI PASTEUR LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(304, '', '', 'MEDICAL LAB OF WINDS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(305, '', '', 'MULTI CHAIR', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(306, '', '', 'DOMINION DIAMOND MAR', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(307, '', '', 'DIKSON SERVICE CANAD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(308, '', '', 'BABCOCK & WILCOX LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(309, '', '', 'SYNFINE RESEARCH LTD', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(310, '', '', 'HOUSE OF HORVATH CIG', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(311, '', '', 'HYDRO ONE NETWORKS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r'),
(312, '', '', 'HYDRO ONE NETWORKS', '', '', '', '', '', '', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '', '0000-00-00', '\r');

-- --------------------------------------------------------

--
-- Table structure for table `contact_field_visibility`
--

DROP TABLE IF EXISTS `contact_field_visibility`;
CREATE TABLE `contact_field_visibility` (
  `id` int NOT NULL,
  `field_name` varchar(64) NOT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
CREATE TABLE `contracts` (
  `contract_id` varchar(32) NOT NULL,
  `contact_id` int DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `contract_type` varchar(50) DEFAULT NULL,
  `contract_status` varchar(50) DEFAULT NULL,
  `equipment_type` varchar(100) DEFAULT NULL,
  `monthly_fee` decimal(10,2) DEFAULT NULL,
  `annual_value` decimal(12,2) DEFAULT NULL,
  `payment_frequency` varchar(20) DEFAULT NULL,
  `contract_term` int DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `renewal_date` date DEFAULT NULL,
  `auto_renew` varchar(10) DEFAULT NULL,
  `notice_period` int DEFAULT NULL,
  `evoqua_account` varchar(50) DEFAULT NULL,
  `evoqua_contract` varchar(50) DEFAULT NULL,
  `equipment_ids` varchar(255) DEFAULT NULL,
  `service_frequency` varchar(50) DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `notes` text,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contracts`
--

INSERT INTO `contracts` (`contract_id`, `contact_id`, `customer_id`, `contract_type`, `contract_status`, `equipment_type`, `monthly_fee`, `annual_value`, `payment_frequency`, `contract_term`, `start_date`, `end_date`, `renewal_date`, `auto_renew`, `notice_period`, `evoqua_account`, `evoqua_contract`, `equipment_ids`, `service_frequency`, `last_service_date`, `next_service_date`, `notes`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
('CNT-20260219-0001', 1, NULL, 'New', 'Active', 'Softener', 333.00, 3996.00, 'Monthly', 12, '2026-02-19', '2027-02-19', '2027-01-20', 'Yes', 30, '', '', '', 'Weekly', NULL, NULL, '', '2026-02-19 04:25:49', '1', '2026-02-19 06:46:36', '1'),
('CNT-20260219-0002', 17, NULL, 'New', 'Active', 'Softener', 2.00, 24.00, 'Monthly', 12, '2026-02-19', '2027-02-19', '2027-01-20', 'Yes', 30, '', '', '', 'Weekly', NULL, NULL, '', '2026-02-19 04:27:58', '1', '2026-02-19 04:27:58', '1');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customer_id` varchar(32) NOT NULL,
  `contact_id` int DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `tank_count` int DEFAULT NULL,
  `last_delivery` date DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `contact_id`, `address`, `tank_count`, `last_delivery`, `last_modified`) VALUES
('1', 1, 'c', NULL, NULL, NULL),
('2', 2, 'c', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

DROP TABLE IF EXISTS `discussions`;
CREATE TABLE `discussions` (
  `id` int NOT NULL,
  `contact_id` int DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  `entry_text` text,
  `linked_opportunity_id` int DEFAULT NULL,
  `visibility` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discussion_log`
--

DROP TABLE IF EXISTS `discussion_log`;
CREATE TABLE `discussion_log` (
  `id` int NOT NULL,
  `contact_id` varchar(32) DEFAULT NULL,
  `author` varchar(128) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `entry_text` text NOT NULL,
  `linked_opportunity_id` varchar(32) DEFAULT NULL,
  `visibility` varchar(32) DEFAULT 'private'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discussion_log`
--

INSERT INTO `discussion_log` (`id`, `contact_id`, `author`, `timestamp`, `entry_text`, `linked_opportunity_id`, `visibility`) VALUES
(1, '1', 'testadmin', '2026-02-19 07:10:19', 'test', '', 'public'),
(2, '68dab0ad04660', 'Robert Lee', '2025-09-29 13:30:00', 'Discussed filtration upgrade and next steps.', '101', 'public'),
(3, '68dab0ad04660', 'Robert Lee', '2025-09-29 13:30:00', 'Discussed filtration upgrade and next steps.', '101', 'public'),
(4, '68dab0ad04660', 'Rob', '2025-09-29 21:21:00', 'talked to the person', '', 'public'),
(5, '68db56a0a1681', 'Admin', '2025-09-30 20:50:00', 'I have updated this', '', 'internal'),
(6, '68db56a0a1681', 'Robert Lee', '2025-09-30 22:19:00', 'This is a new one', '', 'public'),
(7, '68db56a0a16a0', 'Robert Lee', '2025-09-30 23:39:00', 'Left message in February', '', 'public'),
(8, '68db56a0a16af', 'Robert Lee', '2025-09-30 23:41:00', 'joe is president bernie, Sheri and Shawn', '', 'public'),
(9, '68db56a0a16af', 'Robert Lee', '2025-09-30 23:41:00', 'emailed in September', '', 'public'),
(10, '68db56a0a16b2', 'Robert Lee', '2025-09-30 23:42:00', 'Left message with woods in February', '', 'public'),
(11, '68db56a0a17cc', 'Robert Lee', '2025-10-01 16:03:00', 'Spoke, not required right now but will be put in the books as a back up ISO controls\r\n', '', 'public'),
(12, '68db56a0a1936', 'Robert Lee', '2025-10-01 16:05:00', 'spoke in september, They are relictant to switch as have been with Evoqual for 20 years', '', 'public'),
(13, '68db56a0a18a2', 'Robert Lee', '2025-10-01 16:09:00', 'emailed Feb 12 2025', '', 'public'),
(14, '68db56a0a1847', 'Robert Lee', '2025-10-01 16:23:00', 'Emailed March 4', '', 'public'),
(15, '68db56a0a1819', 'Robert Lee', '2025-10-01 16:26:00', 'Left Message', '', 'public'),
(16, '68db56a0a1819', 'Robert Lee', '2025-10-01 16:26:00', 'Left Message', '', 'public'),
(17, '68db56a0a17fe', 'Robert Lee', '2025-10-01 16:27:00', 'Emailed March 4', '', 'public'),
(18, '68db56a0a1805', 'Robert Lee', '2025-10-01 16:29:00', 'email March 4', '', 'public'),
(19, '68db56a0a1746', 'Robert Lee', '2025-10-01 16:33:00', '1800361 6924 x 137', '', 'public'),
(20, '68db56a0a1681', 'Robert Lee', '2025-10-01 17:39:00', 'called talked to receptionist. put me through to accts payable.', '', 'public'),
(21, '68db56a0a1681', 'Robert Lee', '2025-10-01 17:52:00', 'spoke with wayne. they have deionized water. Would like us to email and follow in mid november', '', 'public'),
(22, '68db56a0a168a', 'Robert Lee', '2025-10-01 17:57:00', 'Left message with George.', '', 'public'),
(23, '68db56a0a168e', 'Robert Lee', '2025-10-01 18:00:00', 'send an email to confirmw who would be best to discuss', '', 'public'),
(24, '68db56a0a168e', 'Robert Lee', '2025-10-02 20:23:00', 'email bounced back will need to verify email address and resent\r\n', '', 'public'),
(25, '68db56a0a168e', 'Robert Lee', '2025-10-02 20:26:00', 'found issue sent email', '', 'public'),
(26, '68db56a0a1681', 'Robert Lee', '2025-10-02 21:41:00', 'email forwarded with brochure\r\n', '', 'public'),
(27, '68db56a0a1698', 'Robert Lee', '2025-10-02 21:45:00', 'phone number no longer works', '', 'public'),
(28, '68db56a0a169a', 'Robert Lee', '2025-10-02 22:00:00', 'jimmy Giancoulas is the supervisor but did nto know who was in charge fo the water system', '', 'public'),
(29, '68db56a0a169e', 'Robert Lee', '2025-10-02 22:05:00', 'Left message', '', 'public'),
(30, '68db56a0a16a3', 'Robert Lee', '2025-10-02 22:30:00', 'called and Juan has left for the day, ', '', 'public'),
(31, '68db56a0a16a3', 'Robert Lee', '2025-10-02 22:33:00', 'sent an email to juan', '', 'public'),
(32, '68db56a0a16b5', 'Robert Lee', '2025-10-03 16:47:00', 'Left message', '', 'public'),
(33, '68db56a0a16a9', 'Robert Lee', '2025-10-03 17:04:00', 'Left Message', '', 'public'),
(34, '68db56a0a16bd', 'Robert Lee', '2025-10-03 17:13:00', 'Bob is with technical support - left message regarding ultraform', '', 'public'),
(35, '68db56a0a16c0', 'Robert Lee', '2025-10-03 17:15:00', 'called receptionist recommended I send email', '', 'public'),
(36, '68db56a0a16c0', 'Robert Lee', '2025-10-03 17:17:00', 'sent introductory email with brochure', '', 'public'),
(37, '68db56a0a16c3', 'Robert Lee', '2025-10-03 17:43:00', 'left message at that extension\r\n', '', 'public'),
(38, '68db56a0a16c6', 'Robert Lee', '2025-10-03 17:45:00', 'left message with plant manager', '', 'public'),
(39, '68db56a0a16c9', 'Robert Lee', '2025-10-03 18:03:00', 'no answer on the phone. will try to friend Diane on linkedin\r\n', '', 'public'),
(40, '68db56a0a16cc', 'Robert Lee', '2025-10-06 15:34:00', 'sent email ', '', 'public'),
(41, '68db56a0a16cf', 'Robert Lee', '2025-10-06 15:44:00', 'sent email. Is off today. will follow up t', '', 'public'),
(42, '68db56a0a16d2', 'Robert Lee', '2025-10-06 15:46:00', 'Left message. Purchasing voice mail was in german...', '', 'public'),
(43, '68db56a0a16d4', 'Robert Lee', '2025-10-06 15:50:00', 'Left message', '', 'public'),
(44, '68db56a0a16de', 'Robert Lee', '2025-10-06 15:58:00', 'left message and follow up email.', '', 'public'),
(45, '68db56a0a16e3', 'Robert Lee', '2025-10-06 16:42:00', 'talked to service tech. will need to talk with owner.', '', 'public'),
(46, '68db56a0a16e3', 'Robert Lee', '2025-10-06 16:53:00', 'talked with Brian. He is looking for an alternative supplier as the costs with Evoqua have risen substantially. Also may need some assitance installing his water softener. Has it but was never installed.', '', 'public'),
(47, '68db56a0a16e6', 'Robert Lee', '2025-10-06 16:59:00', 'do have evoqua as a supplier. best to email owner', '', 'public'),
(48, '68db56a0a16e6', 'Robert Lee', '2025-10-06 17:02:00', 'sent email', '', 'public'),
(49, '68db56a0a16f2', 'Robert Lee', '2025-10-06 17:08:00', 'have to contact via their website. Have reached out\r\n', '', 'public'),
(50, '68db56a0a16e6', 'Robert Lee', '2025-10-06 17:30:00', 'Robert Responded that he is undercontract with Evoqua until May 2026 will be happy to talke after that.', '', 'public'),
(51, '68db56a0a16e6', 'Robert Lee', '2025-10-06 17:33:00', 'send follow up email', '', 'public'),
(52, '68db56a0a1703', 'Robert Lee', '2025-10-06 17:39:00', 'called number not in service', '', 'public'),
(53, '68db56a0a1706', 'Robert Lee', '2025-10-06 17:42:00', 'left messaage', '', 'public'),
(54, '68db56a0a170c', 'Robert Lee', '2025-10-10 15:51:00', 'number no longer in service', '', 'public'),
(55, '68db56a0a170f', 'Robert Lee', '2025-10-10 15:54:00', 'cell phone 4162196156', '', 'public'),
(56, '68db56a0a171a', 'Robert Lee', '2025-10-10 15:59:00', 'Left Message', '', 'public'),
(57, '68db56a0a171d', 'Robert Lee', '2025-10-10 16:05:00', 'Contacted Chris the environmental manager. He had no need but provided Myles\' email\r\n', '', 'public'),
(58, '68db56a0a171d', 'Robert Lee', '2025-10-10 16:08:00', 'Send follow up email to Myles', '', 'public'),
(59, '68db56a0a1720', 'Robert Lee', '2025-10-10 16:10:00', 'Just a sales Office', '', 'public'),
(60, '68db56a0a172b', 'Robert Lee', '2025-10-10 16:13:00', 'They say they regenerate their own DI system', '', 'public'),
(61, '68db56a0a1731', 'Robert Lee', '2025-10-10 16:16:00', 'it is out of quebec.', '', 'public'),
(62, '68db56a0a1734', 'Robert Lee', '2025-10-10 16:17:00', 'The answering system does not understand and the mailbox is full', '', 'public'),
(63, '68db56a0a1737', 'Robert Lee', '2025-10-10 16:20:00', 'melissa.marques@mmgca.com', '', 'public'),
(64, '68db56a0a1737', 'Robert Lee', '2025-10-10 16:23:00', 'sent email', '', 'public'),
(65, '68db56a0a173a', 'Robert Lee', '2025-10-10 16:25:00', 'Left message', '', 'public'),
(66, '68db56a0a1740', 'Robert Lee', '2025-10-10 16:33:00', 'Sent email as a follow up to earlier conversations', '', 'public'),
(67, '68db56a0a1886', 'Robert Lee', '2025-10-16 18:42:00', 'Alan has a new boss Ramara, I have emailed Allen to forward to provide an introduction', '', 'public'),
(68, '68db56a0a1886', 'Robert Lee', '2025-10-16 19:23:00', 'Updated to  new operations manager Ramana', '', 'public'),
(69, '68db56a0a1886', 'Robert Lee', '2025-10-16 19:32:00', 'sent Ramana an estimate', '', 'public'),
(70, '68db56a0a174b', 'Robert Lee', '2025-10-16 20:25:00', 'called and left message with Diane AP and AR', '', 'public'),
(71, '68db56a0a174e', 'Robert Lee', '2025-10-16 20:34:00', 'Left message', '', 'public'),
(72, '68db56a0a1979', 'Robert Lee', '2025-10-20 19:32:00', 'Left message and email', '', 'public'),
(73, '68db56a0a1c56', 'Robert Lee', '2025-10-31 20:32:00', 'Ilya Called and will complete site visit on Monday at 1:30', '', 'public'),
(74, '690507e9c30e8', 'Robert Lee', '2025-10-31 20:47:00', 'Attended virtual meeting', '3', 'public'),
(75, '68db56a0a1c56', 'Robert Lee', '2025-11-03 21:20:00', 'dropped by - has a 2 cuft tank only 1 and the light was red', '', 'public'),
(76, '68db56a0a1c56', 'Robert Lee', '2025-11-03 21:24:00', '1 cuft tank not 2', '', 'public'),
(77, '68db56a0a16e3', 'Robert Lee', '2025-11-17 19:23:00', 'Follow up with Brian regarding status of Tanks...', '', 'public'),
(78, '68db56a0a1baf', 'Robert Lee', '2025-11-17 19:36:00', 'Emailed as a follow up. Potentially 4 tanks with a recirculation pump', '', 'public'),
(79, '68db56a0a186b', 'Robert Lee', '2025-11-17 19:53:00', 'called and left Andrew a message', '', 'public'),
(80, '68db56a0a16e3', 'Robert Lee', '2025-11-24 22:06:00', 'sent the sdi_cancel email to brian', '', 'public'),
(81, '68db56a0a1886', 'Robert Lee', '2026-01-08 19:26:00', 'sent folllow up email', '', 'public'),
(82, '68db56a0a1c56', 'Robert Lee', '2026-01-08 19:28:00', 'sent follow up email', '', 'public'),
(83, 'CNT_20251003023317_ecf31f', 'Robert Lee', '2026-01-08 20:00:00', 'forwarded Email to Erin', '', 'public'),
(84, '68db56a0a193d', 'Robert Lee', '2026-01-08 20:02:00', 'They had a fire and are currently not operational', '', 'public'),
(85, '68db56a0a1939', 'Robert Lee', '2026-01-08 20:06:00', 'Number not in service', '', 'public'),
(86, '68db56a0a1917', 'Robert Lee', '2026-01-13 20:24:00', 'Left Message', '', 'public'),
(87, '68db56a0a18f1', 'Robert Lee', '2026-01-13 20:29:00', 'Left Message', '', 'public'),
(88, '68db56a0a18fa', 'Robert Lee', '2026-01-13 20:33:00', 'only needed it for one job.. not longer has it. left number for anytime in the future', '', 'public'),
(89, '68db56a0a1907', 'Robert Lee', '2026-01-13 20:37:00', 'lm', '', 'public'),
(90, '68db56a0a18e9', 'Robert Lee', '2026-01-13 20:46:00', 'lm', '', 'public'),
(91, '68db56a0a18d1', 'Robert Lee', '2026-01-13 20:57:00', 'lm', '', 'public'),
(92, '68db56a0a1c56', 'Robert Lee', '2026-01-13 21:25:00', 'met with Ilya, He has 1 cuft tank that needs exhcange 1 per year. Will go back next week to test. Send quote', '', 'public'),
(93, '68db56a0a1b1d', 'Robert Lee', '2026-02-05 20:24:00', 'site visit with John Litwin. 3 x 1 cuft tanks', '', 'public'),
(94, '68db56a0a1c56', 'Robert Lee', '2026-02-05 21:43:00', 'Site visit. Discussed 1 cuft and  will be issuing PO for an exchange in March 2026', '', 'public');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

DROP TABLE IF EXISTS `equipment`;
CREATE TABLE `equipment` (
  `equipment_id` varchar(32) NOT NULL,
  `equipment_type` varchar(100) DEFAULT NULL,
  `manufacturer` varchar(100) DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `ownership` varchar(50) DEFAULT NULL,
  `customer_id` int DEFAULT NULL,
  `contact_id` int DEFAULT NULL,
  `contract_id` int DEFAULT NULL,
  `install_date` date DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_value` decimal(12,2) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `tank_size` varchar(50) DEFAULT NULL,
  `resin_type` varchar(100) DEFAULT NULL,
  `regeneration_id` varchar(50) DEFAULT NULL,
  `service_frequency` varchar(50) DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `purchase_order` varchar(100) DEFAULT NULL,
  `notes` text,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `equipment_type`, `manufacturer`, `model_number`, `serial_number`, `ownership`, `customer_id`, `contact_id`, `contract_id`, `install_date`, `purchase_date`, `purchase_value`, `location`, `tank_size`, `resin_type`, `regeneration_id`, `service_frequency`, `last_service_date`, `next_service_date`, `warranty_expiry`, `status`, `purchase_order`, `notes`, `created_date`, `modified_date`) VALUES
('EQ001', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '', NULL, '', NULL, NULL, NULL, '', '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `item_id` varchar(32) NOT NULL,
  `item_name` varchar(150) DEFAULT NULL,
  `description` text,
  `category` varchar(100) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `barcode` varchar(100) DEFAULT NULL,
  `rfid_tag` varchar(100) DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `supplier_name` varchar(150) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT NULL,
  `margin` decimal(5,2) DEFAULT NULL,
  `selling_price` decimal(12,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `quantity_in_stock` int DEFAULT NULL,
  `reorder_level` int DEFAULT NULL,
  `reorder_quantity` int DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `warehouse` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `updated_by` varchar(100) DEFAULT NULL,
  `notes` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`item_id`, `item_name`, `description`, `category`, `brand`, `model`, `serial_number`, `barcode`, `rfid_tag`, `supplier_id`, `supplier_name`, `purchase_date`, `cost_price`, `margin`, `selling_price`, `currency`, `quantity_in_stock`, `reorder_level`, `reorder_quantity`, `unit`, `warehouse`, `location`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`, `notes`) VALUES
('CAP-MA', 'Female Union- Cap', 'Female Union- Cap', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2.20, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('HB-100', 'Male Union X 1\" Hose Barb', 'Male Union X 1\" Hose Barb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.43, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('HB-75', 'Male Union X 3/4\" Hose Barb', 'Male Union X 3/4\" Hose Barb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.43, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('ITM_69969c3a1c9bc', '', '', '', '', '', '', '', '', NULL, '', NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, '', '', '', '', '2026-02-19 05:14:34', '2026-02-19 05:14:34', NULL, NULL, ''),
('MA-100', 'Male Union X 1\" NPT', 'Male Union X 1\" NPT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.43, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('MA-75', 'Male Union X 3/4\" NPT', 'Male Union X 3/4\" NPT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.43, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('PLUG-UN', 'Male Union- Plug', 'Male Union- Plug', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2.57, NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RT1235-45', '12\" X 36\" Tank, 4.5\" top opening', '12\" X 36\" Tank, 4.5\" top opening', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 190.00, NULL, NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RT1447-45-RB', '14\" X 47\" Tank, 4.5\" top opening', '14\" X 47\" Tank, 4.5\" top opening', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 238.15, NULL, NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RT844', '8\" X 44\" Tank, 2.5\" top opening', '8\" X 44\" Tank, 2.5\" top opening', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 69.50, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RTA-DS190-81', 'Bottom basket, 13/16\" riser pilot', 'Bottom basket, 13/16\" riser pilot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4.37, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('RTA-DS4', 'Bottom basket, 1\" riser pilot', 'Bottom basket, 1\" riser pilot', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9.75, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('TF-PVC-100-4', 'Riser tube, 1\", 4 Feet', 'Riser tube, 1\", 4 Feet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 8.50, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('TF-PVC-81-4', 'Riser tube, 13/16\", 4 Feet long', 'Riser tube, 13/16\", 4 Feet long', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3.35, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('TH-XPI-25-3-81-SS-IS-OR', 'Flow Manifold, 2.5\" thread, 13/16\" Riser Pilot, 3/4\" Female NPT inlet/outlet/fill port', 'Flow Manifold, 2.5\" thread, 13/16\" Riser Pilot, 3/4\" Female NPT inlet/outlet/fill port', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35.26, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UHB-100', 'Female Union X 1\" Hose Barb', 'Female Union X 1\" Hose Barb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7.10, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UHB-75', 'Female Union X 3/4\" Hose Barb', 'Female Union X 3/4\" Hose Barb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7.10, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UMA-100', 'Female Union X 1\" NPT', 'Female Union X 1\" NPT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7.10, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('UMA-75', 'Female Union X 3/4\" NPT', 'Female Union X 3/4\" NPT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 7.10, NULL, NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('XTA-45-3-1-IS-SS-FC-OR', 'Flow Manifold, 4.5\" thread, 1\" Female NPT inlet/outlet/fill port', 'Flow Manifold, 4.5\" thread, 1\" Female NPT inlet/outlet/fill port', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.72, NULL, NULL, NULL, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_ledger`
--

DROP TABLE IF EXISTS `inventory_ledger`;
CREATE TABLE `inventory_ledger` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `status` varchar(64) NOT NULL,
  `quantity` float NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_serials`
--

DROP TABLE IF EXISTS `inventory_serials`;
CREATE TABLE `inventory_serials` (
  `id` int NOT NULL,
  `item_id` int NOT NULL,
  `status` varchar(64) NOT NULL,
  `serial_number` varchar(128) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_status_options`
--

DROP TABLE IF EXISTS `inventory_status_options`;
CREATE TABLE `inventory_status_options` (
  `id` int NOT NULL,
  `status` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `opportunities`
--

DROP TABLE IF EXISTS `opportunities`;
CREATE TABLE `opportunities` (
  `opportunity_id` int NOT NULL,
  `contact_id` int DEFAULT NULL,
  `value` decimal(12,2) DEFAULT NULL,
  `stage` varchar(100) DEFAULT NULL,
  `probability` int DEFAULT NULL,
  `expected_close` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opportunities`
--

INSERT INTO `opportunities` (`opportunity_id`, `contact_id`, `value`, `stage`, `probability`, `expected_close`) VALUES
(1, 1, 1000000.00, 'Prospecting', 10, '2026-02-14');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `po_number` varchar(64) NOT NULL,
  `date` date DEFAULT NULL,
  `status` varchar(32) DEFAULT NULL,
  `supplier_id` varchar(64) DEFAULT NULL,
  `supplier_name` varchar(128) DEFAULT NULL,
  `supplier_contact` varchar(128) DEFAULT NULL,
  `supplier_address` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `item_id` varchar(64) DEFAULT NULL,
  `item_name` varchar(128) DEFAULT NULL,
  `quantity` decimal(12,2) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(12,2) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `total_discount` decimal(12,2) DEFAULT NULL,
  `total_tax` decimal(12,2) DEFAULT NULL,
  `shipping_cost` decimal(12,2) DEFAULT NULL,
  `other_fees` decimal(12,2) DEFAULT NULL,
  `grand_total` decimal(12,2) DEFAULT NULL,
  `currency` varchar(8) DEFAULT NULL,
  `expected_delivery` date DEFAULT NULL,
  `payment_terms` varchar(128) DEFAULT NULL,
  `notes` text,
  `created_by` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`po_number`, `date`, `status`, `supplier_id`, `supplier_name`, `supplier_contact`, `supplier_address`, `billing_address`, `shipping_address`, `item_id`, `item_name`, `quantity`, `unit`, `unit_price`, `discount`, `tax_rate`, `tax_amount`, `total`, `subtotal`, `total_discount`, `total_tax`, `shipping_cost`, `other_fees`, `grand_total`, `currency`, `expected_delivery`, `payment_terms`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
('EWTPO20260219001', '2026-02-19', '', '', '', '', '', '', '', '', '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', '2026-02-19 05:27:23', '2026-02-19 05:27:23'),
('EWTPO20260219002', '2026-02-19', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', '2026-02-19 05:40:23', '2026-02-19 05:40:23'),
('EWTPO20260219003', '2026-02-19', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', '2026-02-19 05:41:39', '2026-02-19 05:41:39'),
('EWTPO20260219004', '2026-02-19', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, '', '', '', '2026-02-19 05:41:43', '2026-02-19 05:41:43');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items` (
  `id` int NOT NULL,
  `po_number` varchar(64) DEFAULT NULL,
  `item_id` varchar(64) DEFAULT NULL,
  `item_name` varchar(128) DEFAULT NULL,
  `quantity` decimal(12,2) DEFAULT NULL,
  `unit` varchar(32) DEFAULT NULL,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(12,2) DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `po_number`, `item_id`, `item_name`, `quantity`, `unit`, `unit_price`, `discount`, `tax_rate`, `tax_amount`, `total`) VALUES
(3, 'EWTPO20260219002', '', '', NULL, '', NULL, NULL, NULL, NULL, NULL),
(4, 'EWTPO20260219003', '', '', NULL, '', NULL, NULL, NULL, NULL, NULL),
(5, 'EWTPO20260219004', '', '', NULL, '', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `user_id` int NOT NULL,
  `session_token` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `last_activity` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`user_id`, `session_token`, `ip_address`, `user_agent`, `expires_at`, `last_activity`) VALUES
(1, '5f4f30f21307fc13fa5a3145126d14ea97670a250493d41d6217aba6c7bd97ba', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-02-20 09:14:42', '2026-02-19 04:14:42');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `priority` varchar(50) DEFAULT NULL,
  `assigned_to` varchar(100) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_field_visibility`
--
ALTER TABLE `contact_field_visibility`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`contract_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD CONSTRAINT `fk_customers_contact_id` FOREIGN KEY (`contact_id`) REFERENCES `contacts`(`contact_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discussion_log`
--
ALTER TABLE `discussion_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `linked_opportunity_id` (`linked_opportunity_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `inventory_ledger`
--
ALTER TABLE `inventory_ledger`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_serials`
--
ALTER TABLE `inventory_serials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_status_options`
--
ALTER TABLE `inventory_status_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status` (`status`);

--
-- Indexes for table `opportunities`
--
ALTER TABLE `opportunities`
  ADD PRIMARY KEY (`opportunity_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`po_number`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_number` (`po_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_token`),
  ADD KEY `user_id_idx` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `contact_field_visibility`
--
ALTER TABLE `contact_field_visibility`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discussion_log`
--
ALTER TABLE `discussion_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `inventory_ledger`
--
ALTER TABLE `inventory_ledger`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_serials`
--
ALTER TABLE `inventory_serials`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_status_options`
--
ALTER TABLE `inventory_status_options`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opportunities`
--
ALTER TABLE `opportunities`
  MODIFY `opportunity_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`po_number`) REFERENCES `purchase_orders` (`po_number`) ON DELETE CASCADE;
COMMIT;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


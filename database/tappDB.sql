-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Creato il: Feb 03, 2026 alle 18:49
-- Versione del server: 8.4.7
-- Versione PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tappdb`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `automezzo`
--

DROP TABLE IF EXISTS `automezzo`;
CREATE TABLE IF NOT EXISTS `automezzo` (
  `targa` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codiceMezzo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`targa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `luogo`
--

DROP TABLE IF EXISTS `luogo`;
CREATE TABLE IF NOT EXISTS `luogo` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `denominazione` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `civico` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citta` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  `attivo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `missione`
--

DROP TABLE IF EXISTS `missione`;
CREATE TABLE IF NOT EXISTS `missione` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `statoCompilazione` enum('INSERITA','ASSEGNATA','COMPLETATA','ANNULLATA') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `partenza` datetime DEFAULT NULL,
  `annotazioni` longtext COLLATE utf8mb4_unicode_ci,
  `id_obiettivo` int DEFAULT NULL,
  `id_destinazione` int DEFAULT NULL,
  `id_turno` int DEFAULT NULL,
  `id_utente` int DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `id_obiettivo` (`id_obiettivo`),
  KEY `id_destinazione` (`id_destinazione`),
  KEY `id_utente` (`id_utente`),
  KEY `id_turno` (`id_turno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `operatore`
--

DROP TABLE IF EXISTS `operatore`;
CREATE TABLE IF NOT EXISTS `operatore` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cognome` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataNascita` date DEFAULT NULL,
  `sesso` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` bigint DEFAULT NULL,
  `username` longtext COLLATE utf8mb4_unicode_ci,
  `password` longtext COLLATE utf8mb4_unicode_ci,
  `utente` enum('AUTISTA','AMMINISTRATIVO') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attivo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `turno`
--

DROP TABLE IF EXISTS `turno`;
CREATE TABLE IF NOT EXISTS `turno` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `dataInizio` datetime DEFAULT NULL,
  `dataFine` datetime DEFAULT NULL,
  `id_operatore` int DEFAULT NULL,
  `automezzo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`ID`),
  KEY `id_operatore` (`id_operatore`),
  KEY `automezzo` (`automezzo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

DROP TABLE IF EXISTS `utente`;
CREATE TABLE IF NOT EXISTS `utente` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `CF` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cognome` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dataNascita` date DEFAULT NULL,
  `luogoNascita` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sesso` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `indirizzo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `civico` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citta` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` bigint DEFAULT NULL,
  `noteUtente` longtext COLLATE utf8mb4_unicode_ci,
  `username` longtext COLLATE utf8mb4_unicode_ci,
  `password` longtext COLLATE utf8mb4_unicode_ci,
  `attivo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

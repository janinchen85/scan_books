-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Mai 2018 um 07:43
-- Server-Version: 10.1.30-MariaDB
-- PHP-Version: 7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `wordpress`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_books`
--

CREATE TABLE `wp_books` (
  `book_id` int(11) NOT NULL,
  `book_series_id` int(11) NOT NULL,
  `book_publisher_id` int(11) NOT NULL,
  `book_volume` int(11) NOT NULL,
  `book_published` int(11) NOT NULL,
  `book_synopsis` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `wp_books`
--

INSERT INTO `wp_books` (`book_id`, `book_series_id`, `book_publisher_id`, `book_volume`, `book_published`, `book_synopsis`) VALUES
(1, 1, 5, 1, 1998, 'By day, Kusakabe Maron is an ordinary high school girl with more then her share of problems. But by night, she is Phantom Thief Jeanne. As the reincarnation of Joan of Arc, her mission is to steal demon-possessed paintings and neutralize their evil. Jeanne\\\'s only ally is the angel-in-training Finn. Together, they must fight evil by night while surviving high school by day.'),
(2, 1, 5, 2, 1999, 'The battle continues against demon-possessed paintingsas the competition between Kaito Jeanne and Sinbad heats up! Meanwhile, life at school grows complicated for Maron, as she must deal with her growing feelings for Chiaki and her guilt over being kissed by Sinbadnot realizing theyre one and the same! And Chiaki hides a dark secret that could shatter everything.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_book_publishers`
--

CREATE TABLE `wp_book_publishers` (
  `book_publisher_id` int(11) NOT NULL,
  `book_publisher_name` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `wp_book_publishers`
--

INSERT INTO `wp_book_publishers` (`book_publisher_id`, `book_publisher_name`) VALUES
(1, 'TOKYOPOP'),
(2, 'Kazé'),
(3, 'Manga Cult'),
(4, 'Carlsen Manga'),
(5, 'Egmont Manga'),
(6, 'altraverse');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wp_book_series`
--

CREATE TABLE `wp_book_series` (
  `book_series_id` int(11) NOT NULL,
  `book_series_title` varchar(55) NOT NULL,
  `book_series_autor` varchar(55) NOT NULL,
  `book_series_volumes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `wp_book_series`
--

INSERT INTO `wp_book_series` (`book_series_id`, `book_series_title`, `book_series_autor`, `book_series_volumes`) VALUES
(1, 'Kamikaze Kaito Jeanne', 'Arina Tanemura', 7),
(2, 'Blood Lad', 'Yuuki Kodama', 17),
(3, 'Another', 'Yukito Ayatsuji', 4),
(4, 'Blue Excorcist', 'Kazue Kato', 21),
(5, 'Highschool of the Dead', 'Daisuke Sato', 7),
(6, 'Prinzessin Sakura', 'Arina Tanemura', 12);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `wp_books`
--
ALTER TABLE `wp_books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `book_series_id` (`book_series_id`),
  ADD KEY `book_publisher_id` (`book_publisher_id`);

--
-- Indizes für die Tabelle `wp_book_publishers`
--
ALTER TABLE `wp_book_publishers`
  ADD PRIMARY KEY (`book_publisher_id`);

--
-- Indizes für die Tabelle `wp_book_series`
--
ALTER TABLE `wp_book_series`
  ADD PRIMARY KEY (`book_series_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `wp_books`
--
ALTER TABLE `wp_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT für Tabelle `wp_book_publishers`
--
ALTER TABLE `wp_book_publishers`
  MODIFY `book_publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `wp_book_series`
--
ALTER TABLE `wp_book_series`
  MODIFY `book_series_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `wp_books`
--
ALTER TABLE `wp_books`
  ADD CONSTRAINT `wp_books_ibfk_1` FOREIGN KEY (`book_series_id`) REFERENCES `wp_book_series` (`book_series_id`),
  ADD CONSTRAINT `wp_books_ibfk_2` FOREIGN KEY (`book_publisher_id`) REFERENCES `wp_book_publishers` (`book_publisher_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

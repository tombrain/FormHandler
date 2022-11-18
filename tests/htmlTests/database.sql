SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Datenbank: `formhandler`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `loadfromtable`
--

CREATE TABLE `loadfromtable` (
  `keyField` int(11) NOT NULL,
  `valueField` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `loadfromtable`
--

INSERT INTO `loadfromtable` (`keyField`, `valueField`) VALUES
(1, 'foo'),
(2, 'bar');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `text1` varchar(255) DEFAULT NULL,
  `text2` varchar(255) DEFAULT NULL,
  `textarea1` text DEFAULT NULL,
  `textarea2` text NOT NULL,
  `textarea3` text DEFAULT NULL,
  `date1` date DEFAULT NULL,
  `date2` date NOT NULL,
  `date3` date DEFAULT NULL,
  `date4` date DEFAULT NULL,
  `date5` date NOT NULL,
  `date6` date DEFAULT NULL,
  `digit1` int(11) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `ok` varchar(255) DEFAULT NULL,
  `animal` int(11) DEFAULT NULL,
  `gender` char(1) NOT NULL,
  `products` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `birthdate2` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `color2` varchar(255) DEFAULT NULL,
  `saveInField` int(11) DEFAULT NULL,
  `saveInField2` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `loadfromtable`
--
ALTER TABLE `loadfromtable`
  ADD PRIMARY KEY (`keyField`);

--
-- Indizes für die Tabelle `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `saveInField` (`saveInField`);


COMMIT;
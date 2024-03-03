-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2024 at 08:52 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `salon_lepote`
--

-- --------------------------------------------------------

--
-- Table structure for table `administratori`
--

CREATE TABLE `administratori` (
  `id` int(11) NOT NULL,
  `korisnicko_ime` varchar(255) NOT NULL,
  `lozinka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administratori`
--

INSERT INTO `administratori` (`id`, `korisnicko_ime`, `lozinka`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

-- --------------------------------------------------------

--
-- Table structure for table `kategorije`
--

CREATE TABLE `kategorije` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `slika` varchar(255) NOT NULL DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorije`
--

INSERT INTO `kategorije` (`id`, `naziv`, `slika`) VALUES
(1, 'Šišanje', 'sisanje.png'),
(2, 'Manikir', 'manikir.png'),
(3, 'Pravljenje frizure', 'frizura.png'),
(4, 'Šminkanje', 'sminka.png'),
(5, 'Pedikir', 'pedikir.png'),
(6, 'Tretman lica', 'ciscenjelica.png'),
(7, 'Depilacija', 'depilacija.png'),
(8, 'Sredjivanje obrva', 'obrve.png');

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lozinka` varchar(255) NOT NULL,
  `ime` varchar(255) NOT NULL,
  `broj_telefona` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`id`, `email`, `lozinka`, `ime`, `broj_telefona`) VALUES
(1, '1231231@gmail.com', 'd1c07866d71dc3a09b3b692d0a2086b4', 'Ime i prezime213', '1231231'),
(2, 'test@gmail.com', '202cb962ac59075b964b07152d234b70', 'Ime Prezime', '06412345678'),
(3, 'test1@gmail.com', '202cb962ac59075b964b07152d234b70', 'Test', '123');

-- --------------------------------------------------------

--
-- Table structure for table `potkategorije`
--

CREATE TABLE `potkategorije` (
  `id` int(11) NOT NULL,
  `naziv` varchar(255) NOT NULL,
  `cena` int(11) NOT NULL,
  `kategorije_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `potkategorije`
--

INSERT INTO `potkategorije` (`id`, `naziv`, `cena`, `kategorije_id`) VALUES
(1, 'Šišanje', 800, 1),
(2, 'Pranje kose', 300, 1),
(3, 'Tretman zanoktica', 1000, 2),
(4, 'Feniranje', 500, 3),
(5, 'Nadogradnja', 1000, 3),
(6, 'Ruke', 1000, 7),
(7, 'Prepone', 1400, 7),
(8, 'Noge', 1200, 7),
(9, 'Farbanje', 500, 1),
(10, 'Gel lak sa crtanjem', 1000, 2),
(11, 'Sa trepavicama', 700, 4),
(12, 'Higijenski tretman', 1700, 5),
(13, 'Medicinski tretman', 1700, 5),
(14, 'Ultrazvučni tretman', 2000, 6),
(15, 'Dubinsko čišćenje', 2000, 6),
(16, 'Nega obrva', 1000, 8),
(17, 'Crtanje obrva', 1000, 8),
(18, 'Sa neonskim bojama', 1000, 4);

-- --------------------------------------------------------

--
-- Table structure for table `transakcije`
--

CREATE TABLE `transakcije` (
  `id` int(11) NOT NULL,
  `korisnik_id` int(11) NOT NULL,
  `kategorije_id` int(11) NOT NULL,
  `termin` datetime NOT NULL,
  `aktivno` tinyint(1) DEFAULT 1,
  `ukupno` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transakcije`
--

INSERT INTO `transakcije` (`id`, `korisnik_id`, `kategorije_id`, `termin`, `aktivno`, `ukupno`) VALUES
(2, 2, 3, '2024-01-18 12:00:00', 0, 1500),
(3, 2, 3, '2024-01-18 09:00:00', 0, 1500),
(4, 2, 3, '2024-01-26 09:00:00', 0, 1500),
(5, 2, 1, '2024-01-29 09:00:00', 0, 1300),
(6, 2, 1, '2024-02-25 09:00:00', 0, 1300),
(7, 2, 7, '2024-02-25 12:00:00', 1, 3600);

-- --------------------------------------------------------

--
-- Table structure for table `transakcije_potkategorije`
--

CREATE TABLE `transakcije_potkategorije` (
  `id` int(11) NOT NULL,
  `transakcije_id` int(11) NOT NULL,
  `potkategorije_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transakcije_potkategorije`
--

INSERT INTO `transakcije_potkategorije` (`id`, `transakcije_id`, `potkategorije_id`) VALUES
(1, 2, 4),
(2, 3, 4),
(3, 4, 4),
(4, 5, 1),
(5, 5, 2),
(6, 6, 1),
(7, 6, 2),
(8, 7, 6),
(9, 7, 7),
(10, 7, 8);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administratori`
--
ALTER TABLE `administratori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `kategorije`
--
ALTER TABLE `kategorije`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `potkategorije`
--
ALTER TABLE `potkategorije`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_potkategorije_kategorije` (`kategorije_id`);

--
-- Indexes for table `transakcije`
--
ALTER TABLE `transakcije`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_transakcije_korisnici` (`korisnik_id`),
  ADD KEY `FK_transakcije_kategorije` (`kategorije_id`);

--
-- Indexes for table `transakcije_potkategorije`
--
ALTER TABLE `transakcije_potkategorije`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_t_p` (`transakcije_id`),
  ADD KEY `FK_p_t` (`potkategorije_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administratori`
--
ALTER TABLE `administratori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kategorije`
--
ALTER TABLE `kategorije`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `potkategorije`
--
ALTER TABLE `potkategorije`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transakcije`
--
ALTER TABLE `transakcije`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transakcije_potkategorije`
--
ALTER TABLE `transakcije_potkategorije`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `potkategorije`
--
ALTER TABLE `potkategorije`
  ADD CONSTRAINT `FK_potkategorije_kategorije` FOREIGN KEY (`kategorije_id`) REFERENCES `kategorije` (`id`);

--
-- Constraints for table `transakcije`
--
ALTER TABLE `transakcije`
  ADD CONSTRAINT `FK_transakcije_kategorije` FOREIGN KEY (`kategorije_id`) REFERENCES `kategorije` (`id`),
  ADD CONSTRAINT `FK_transakcije_korisnici` FOREIGN KEY (`korisnik_id`) REFERENCES `korisnici` (`id`);

--
-- Constraints for table `transakcije_potkategorije`
--
ALTER TABLE `transakcije_potkategorije`
  ADD CONSTRAINT `FK_p_t` FOREIGN KEY (`potkategorije_id`) REFERENCES `potkategorije` (`id`),
  ADD CONSTRAINT `FK_t_p` FOREIGN KEY (`transakcije_id`) REFERENCES `transakcije` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

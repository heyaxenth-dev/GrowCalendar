-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2026 at 05:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `growcalendar_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `location_soil_types`
--

CREATE TABLE `location_soil_types` (
  `location` varchar(150) NOT NULL,
  `soil_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `location_soil_types`
--

INSERT INTO `location_soil_types` (`location`, `soil_type_id`) VALUES
('Baghari, Barbaza, Antique', 16),
('Baghari, Barbaza, Antique', 19),
('Baghari, Barbaza, Antique', 26),
('Bahuyan, Barbaza, Antique', 25),
('Bahuyan, Barbaza, Antique', 26),
('Bahuyan, Barbaza, Antique', 28),
('Beri, Barbaza, Antique', 22),
('Beri, Barbaza, Antique', 27),
('Beri, Barbaza, Antique', 29),
('Biga-a, Barbaza, Antique', 16),
('Biga-a, Barbaza, Antique', 19),
('Biga-a, Barbaza, Antique', 25),
('Binangbang Centro, Barbaza, Antique', 19),
('Binangbang Centro, Barbaza, Antique', 20),
('Binangbang Centro, Barbaza, Antique', 24),
('Binangbang, Barbaza, Antique', 16),
('Binangbang, Barbaza, Antique', 19),
('Binangbang, Barbaza, Antique', 29),
('Binanu-an, Barbaza, Antique', 18),
('Binanu-an, Barbaza, Antique', 21),
('Binanu-an, Barbaza, Antique', 25),
('Cadiao, Barbaza, Antique', 22),
('Cadiao, Barbaza, Antique', 27),
('Cadiao, Barbaza, Antique', 30),
('Calapadan, Barbaza, Antique', 16),
('Calapadan, Barbaza, Antique', 19),
('Calapadan, Barbaza, Antique', 25),
('Capoyuan, Barbaza, Antique', 18),
('Capoyuan, Barbaza, Antique', 21),
('Capoyuan, Barbaza, Antique', 25),
('Cubay, Barbaza, Antique', 20),
('Cubay, Barbaza, Antique', 28),
('Cubay, Barbaza, Antique', 29),
('Embrangga-an, Barbaza, Antique', 22),
('Embrangga-an, Barbaza, Antique', 24),
('Embrangga-an, Barbaza, Antique', 27),
('Esparar, Barbaza, Antique', 20),
('Esparar, Barbaza, Antique', 28),
('Esparar, Barbaza, Antique', 29),
('Gua, Barbaza, Antique', 18),
('Gua, Barbaza, Antique', 21),
('Gua, Barbaza, Antique', 25),
('Idao, Barbaza, Antique', 16),
('Idao, Barbaza, Antique', 19),
('Idao, Barbaza, Antique', 25),
('Igpalge, Barbaza, Antique', 20),
('Igpalge, Barbaza, Antique', 28),
('Igpalge, Barbaza, Antique', 29),
('Igtunarum, Barbaza, Antique', 22),
('Igtunarum, Barbaza, Antique', 27),
('Igtunarum, Barbaza, Antique', 29),
('Integasan, Barbaza, Antique', 22),
('Integasan, Barbaza, Antique', 27),
('Integasan, Barbaza, Antique', 30),
('Ipil, Barbaza, Antique', 18),
('Ipil, Barbaza, Antique', 21),
('Ipil, Barbaza, Antique', 25),
('Jinalinan, Barbaza, Antique', 18),
('Jinalinan, Barbaza, Antique', 21),
('Jinalinan, Barbaza, Antique', 25),
('Lanas, Barbaza, Antique', 22),
('Lanas, Barbaza, Antique', 27),
('Lanas, Barbaza, Antique', 29),
('Langcaon (Evelio Javier), Barbaza, Antique', 22),
('Langcaon (Evelio Javier), Barbaza, Antique', 27),
('Langcaon (Evelio Javier), Barbaza, Antique', 28),
('Lisub, Barbaza, Antique', 20),
('Lisub, Barbaza, Antique', 22),
('Lisub, Barbaza, Antique', 27),
('Lombuyan, Barbaza, Antique', 22),
('Lombuyan, Barbaza, Antique', 27),
('Lombuyan, Barbaza, Antique', 29),
('Mablad, Barbaza, Antique', 20),
('Mablad, Barbaza, Antique', 24),
('Mablad, Barbaza, Antique', 28),
('Magtulis, Barbaza, Antique', 22),
('Magtulis, Barbaza, Antique', 27),
('Magtulis, Barbaza, Antique', 29),
('Marigne, Barbaza, Antique', 20),
('Marigne, Barbaza, Antique', 22),
('Marigne, Barbaza, Antique', 27),
('Mayabay, Barbaza, Antique', 20),
('Mayabay, Barbaza, Antique', 28),
('Mayabay, Barbaza, Antique', 29),
('Mayos, Barbaza, Antique', 20),
('Mayos, Barbaza, Antique', 22),
('Mayos, Barbaza, Antique', 27),
('Nalusdan, Barbaza, Antique', 22),
('Nalusdan, Barbaza, Antique', 27),
('Nalusdan, Barbaza, Antique', 29),
('Narirong, Barbaza, Antique', 17),
('Narirong, Barbaza, Antique', 20),
('Narirong, Barbaza, Antique', 24),
('Palma, Barbaza, Antique', 21),
('Palma, Barbaza, Antique', 24),
('Palma, Barbaza, Antique', 25),
('Poblacion, Barbaza, Antique', 20),
('Poblacion, Barbaza, Antique', 28),
('Poblacion, Barbaza, Antique', 29),
('San Antonio, Barbaza, Antique', 22),
('San Antonio, Barbaza, Antique', 27),
('San Antonio, Barbaza, Antique', 29),
('San Ramon, Barbaza, Antique', 18),
('San Ramon, Barbaza, Antique', 21),
('San Ramon, Barbaza, Antique', 25),
('Soligao, Barbaza, Antique', 16),
('Soligao, Barbaza, Antique', 19),
('Soligao, Barbaza, Antique', 20),
('Tabongtabong, Barbaza, Antique', 16),
('Tabongtabong, Barbaza, Antique', 19),
('Tabongtabong, Barbaza, Antique', 24),
('Tig-Alaran, Barbaza, Antique', 20),
('Tig-Alaran, Barbaza, Antique', 22),
('Tig-Alaran, Barbaza, Antique', 27),
('Yapo, Barbaza, Antique', 22),
('Yapo, Barbaza, Antique', 27),
('Yapo, Barbaza, Antique', 29);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `location_soil_types`
--
ALTER TABLE `location_soil_types`
  ADD PRIMARY KEY (`location`,`soil_type_id`),
  ADD KEY `soil_type_id` (`soil_type_id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `location_soil_types`
--
ALTER TABLE `location_soil_types`
  ADD CONSTRAINT `location_soil_types_ibfk_1` FOREIGN KEY (`soil_type_id`) REFERENCES `soil_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

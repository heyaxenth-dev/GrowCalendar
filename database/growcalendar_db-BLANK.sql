-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 12:35 PM
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
-- Table structure for table `crops`
--

CREATE TABLE `crops` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `scientific_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `planting_season` varchar(50) DEFAULT NULL,
  `harvest_days` int(11) DEFAULT NULL,
  `water_requirements` varchar(50) DEFAULT NULL,
  `temperature_min` decimal(4,1) DEFAULT NULL,
  `temperature_max` decimal(4,1) DEFAULT NULL,
  `humidity_min` decimal(4,1) DEFAULT NULL,
  `humidity_max` decimal(4,1) DEFAULT NULL,
  `rainfall_min` decimal(6,2) DEFAULT NULL,
  `rainfall_max` decimal(6,2) DEFAULT NULL,
  `ph_min` decimal(3,1) DEFAULT NULL,
  `ph_max` decimal(3,1) DEFAULT NULL,
  `marketability` text DEFAULT NULL,
  `soil_type_preference` varchar(255) DEFAULT NULL,
  `weather_conditions` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crops`
--

INSERT INTO `crops` (`id`, `name`, `scientific_name`, `description`, `planting_season`, `harvest_days`, `water_requirements`, `temperature_min`, `temperature_max`, `humidity_min`, `humidity_max`, `rainfall_min`, `rainfall_max`, `ph_min`, `ph_max`, `marketability`, `soil_type_preference`, `weather_conditions`, `image_url`, `created_at`) VALUES
(51, 'Rice (Palay)', 'Oryza sativa', 'Staple grain crop requiring flooded conditions', 'Wet Season', 120, 'High', 20.0, 35.0, 70.0, 90.0, 1000.00, 2000.00, 6.0, 7.5, 'Local & Provincial (staple); high demand', 'Alluvial clay loam, Sta. Rita clay', 'Tropical, wet season May-Nov, dry Feb-Apr', NULL, '2025-10-26 01:02:31'),
(52, 'Corn (Maize)', 'Zea mays', 'Versatile grain crop for food and feed', 'Dry Season', 90, 'Medium', 15.0, 30.0, 50.0, 80.0, 500.00, 1000.00, 6.0, 7.5, 'Local & Provincial', 'Loam to sandy loam', 'Tropical warm; tolerant of variable rainfall', NULL, '2025-10-26 01:02:31'),
(53, 'Coconut', 'Cocos nucifera', 'Tropical palm tree for oil and copra', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 60.0, 90.0, 800.00, 1500.00, 5.5, 7.0, 'National & Export (copra, oil)', 'Sandy loam, well-drained coastals', 'Tropical humid; prefers coastal areas', NULL, '2025-10-26 01:02:31'),
(54, 'Sugarcane', 'Saccharum officinarum', 'Industrial crop for sugar production', 'Wet Season', 365, 'High', 20.0, 35.0, 60.0, 85.0, 1200.00, 2000.00, 6.0, 7.5, 'Provincial & National (sugar industry)', 'Loam, clay loam, alluvial soils', 'Warm, high rainfall during growing season', NULL, '2025-10-26 01:02:31'),
(55, 'Banana - Saba', 'Musa acuminata', 'Cooking banana variety', 'Year-round', 300, 'High', 20.0, 35.0, 70.0, 90.0, 1000.00, 1500.00, 6.0, 7.0, 'Local & Provincial (cooking banana)', 'Loam to sandy loam', 'Tropical humid', NULL, '2025-10-26 01:02:31'),
(56, 'Banana - Cavendish', 'Musa acuminata', 'Export banana variety', 'Year-round', 300, 'High', 20.0, 35.0, 70.0, 90.0, 1000.00, 1500.00, 6.0, 7.0, 'National & Export potential', 'Well-drained loam', 'Tropical humid; needs regular rainfall', NULL, '2025-10-26 01:02:31'),
(57, 'Sweet potato (Kamote)', 'Ipomoea batatas', 'Nutritious root crop', 'Wet Season', 120, 'Low to Medium', 20.0, 30.0, 60.0, 80.0, 600.00, 1200.00, 5.5, 6.5, 'Local markets, processed products', 'Sandy loam to loam', 'Tropical; tolerates drought', NULL, '2025-10-26 01:02:31'),
(58, 'Cassava (Kamoteng kahoy)', 'Manihot esculenta', 'Drought-tolerant root crop', 'Dry Season', 300, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 300.00, 800.00, 5.0, 7.0, 'Local & Industrial (starch)', 'Sandy loam to clay loam', 'Tropical; tolerant of poor soils', NULL, '2025-10-26 01:02:31'),
(59, 'Taro (Gabi)', 'Colocasia esculenta', 'Wetland root crop', 'Wet Season', 180, 'High', 20.0, 30.0, 70.0, 90.0, 1000.00, 1500.00, 5.5, 7.0, 'Local markets, specialty', 'Clay to silty clay (moist soils)', 'Warm, humid; suits flooded/irrigated fields', NULL, '2025-10-26 01:02:31'),
(60, 'Purple yam (Ube)', 'Dioscorea alata', 'High-value root crop', 'Wet Season', 240, 'Medium', 20.0, 30.0, 60.0, 80.0, 800.00, 1200.00, 6.0, 7.0, 'High local demand (value-added product)', 'Loam to sandy loam', 'Tropical; warm and humid', NULL, '2025-10-26 01:02:31'),
(61, 'Peanut (Groundnut)', 'Arachis hypogaea', 'Oilseed legume', 'Dry Season', 120, 'Low to Medium', 20.0, 30.0, 50.0, 70.0, 400.00, 800.00, 6.0, 7.0, 'Local & Regional', 'Sandy loam, well-drained', 'Warm; prefers dryer spells for harvesting', NULL, '2025-10-26 01:02:31'),
(62, 'Mungbean (Munggo)', 'Vigna radiata', 'Protein-rich legume', 'Dry Season', 60, 'Low to Medium', 20.0, 30.0, 50.0, 70.0, 300.00, 600.00, 6.0, 7.0, 'Local staple; good demand', 'Loam to sandy loam', 'Warmer, drier windows best for harvest', NULL, '2025-10-26 01:02:31'),
(63, 'Soybean', 'Glycine max', 'Industrial legume crop', 'Dry Season', 90, 'Medium', 18.0, 30.0, 50.0, 80.0, 500.00, 1000.00, 6.0, 7.0, 'Local & industrial uses', 'Loam; well-drained', 'Warm season crop', NULL, '2025-10-26 01:02:31'),
(64, 'Eggplant (Talong)', 'Solanum melongena', 'Tropical vegetable crop', 'Dry Season', 100, 'Medium', 20.0, 30.0, 65.0, 85.0, 500.00, 900.00, 5.5, 7.0, 'Local markets', 'Loam, fertile garden soil', 'Warm tropical; needs moisture', NULL, '2025-10-26 01:02:31'),
(65, 'Tomato', 'Solanum lycopersicum', 'Popular vegetable crop', 'Dry Season', 75, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local & Regional markets', 'Loam, well-drained', 'Warm; prefers dryer spells for harvesting', NULL, '2025-10-26 01:02:31'),
(66, 'Chili pepper (Siling labuyo)', 'Capsicum frutescens', 'Spicy condiment crop', 'Dry Season', 90, 'Low to Medium', 20.0, 35.0, 50.0, 70.0, 300.00, 600.00, 6.0, 7.5, 'High local demand; value product', 'Loam to sandy loam', 'Warm; tolerates high temperatures', NULL, '2025-10-26 01:02:31'),
(67, 'Okra', 'Abelmoschus esculentus', 'Heat-tolerant vegetable', 'Dry Season', 60, 'Low to Medium', 25.0, 35.0, 50.0, 70.0, 300.00, 600.00, 6.0, 7.5, 'Local markets', 'Loam, sandy loam', 'Tropical warm; drought-tolerant variety available', NULL, '2025-10-26 01:02:31'),
(68, 'Squash (Kalabasa)', 'Cucurbita maxima', 'Versatile gourd family crop', 'Dry Season', 80, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local & Provincial markets', 'Loam, fertile garden soil', 'Warm; needs regular moisture', NULL, '2025-10-26 01:02:31'),
(69, 'Watermelon', 'Citrullus lanatus', 'Summer fruit crop', 'Dry Season', 90, 'Medium to High', 20.0, 30.0, 50.0, 70.0, 400.00, 800.00, 6.0, 7.0, 'Local seasonal demand', 'Sandy loam, well-drained', 'Warm; with dry spells preferred at harvest', NULL, '2025-10-26 01:02:31'),
(70, 'Cucumber', 'Cucumis sativus', 'Cool-season vegetable', 'Dry Season', 60, 'Medium', 18.0, 28.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local markets', 'Loam, well-drained', 'Warm; needs regular water', NULL, '2025-10-26 01:02:31'),
(71, 'Cabbage', 'Brassica oleracea', 'Cool-season leafy vegetable', 'Dry Season', 90, 'Medium', 15.0, 25.0, 60.0, 80.0, 500.00, 1000.00, 6.0, 7.0, 'Local & Regional', 'Loam, fertile soil (often higher elevation)', 'Cooler sites preferred (upland)', NULL, '2025-10-26 01:02:31'),
(72, 'Onion (bulb/green)', 'Allium cepa', 'Bulb vegetable crop', 'Dry Season', 120, 'Low to Medium', 15.0, 25.0, 50.0, 70.0, 300.00, 600.00, 6.0, 7.0, 'Regional demand', 'Loam, sandy loam', 'Upland/cooler areas better for bulb onions', NULL, '2025-10-26 01:02:31'),
(73, 'Garlic', 'Allium sativum', 'Bulb spice crop', 'Dry Season', 150, 'Low to Medium', 15.0, 25.0, 50.0, 70.0, 300.00, 600.00, 6.0, 7.0, 'Local & regional', 'Loam, well-drained', 'Coolish period for proper bulb formation', NULL, '2025-10-26 01:02:31'),
(74, 'Bell pepper (Capsicum)', 'Capsicum annuum', 'Sweet pepper variety', 'Dry Season', 90, 'Medium to High', 18.0, 28.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'High local/retail demand', 'Loam, fertile, well-drained', 'Warm; controlled moisture important', NULL, '2025-10-26 01:02:31'),
(75, 'Bitter gourd (Ampalaya)', 'Momordica charantia', 'Medicinal vegetable', 'Dry Season', 90, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local markets; medicinal demand', 'Loam to sandy loam', 'Tropical; warm', NULL, '2025-10-26 01:02:31'),
(76, 'Yardlong bean (Sitaw)', 'Vigna unguiculata', 'Climbing legume vegetable', 'Dry Season', 60, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local & market vegetable demand', 'Loam, fertile garden soil', 'Tropical; warm', NULL, '2025-10-26 01:02:31'),
(77, 'Winged bean (Sigarilyas)', 'Psophocarpus tetragonolobus', 'Protein-rich climbing bean', 'Dry Season', 90, 'Medium', 20.0, 30.0, 60.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local markets; protein-rich legume', 'Loam, well-drained', 'Tropical warm; climbs supports', NULL, '2025-10-26 01:02:31'),
(78, 'Ginger', 'Zingiber officinale', 'Rhizome spice crop', 'Year-round', 240, 'Medium to High', 20.0, 30.0, 70.0, 90.0, 800.00, 1200.00, 6.0, 7.0, 'High value, local & export processing', 'Loam, well-drained, rich organic matter', 'Warm, humid; shade tolerant', NULL, '2025-10-26 01:02:31'),
(79, 'Turmeric', 'Curcuma longa', 'Rhizome spice crop', 'Year-round', 240, 'Medium', 20.0, 30.0, 70.0, 90.0, 800.00, 1200.00, 6.0, 7.0, 'Growing demand for spice and health products', 'Loam, rich organic soil', 'Warm, humid; similar to ginger', NULL, '2025-10-26 01:02:31'),
(80, 'Lemongrass (Tanglad)', 'Cymbopogon citratus', 'Aromatic grass', 'Year-round', 90, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 400.00, 800.00, 6.0, 7.0, 'Local, essential oil potential', 'Sandy loam to loam', 'Tropical; hardy and drought-tolerant', NULL, '2025-10-26 01:02:31'),
(81, 'Calamansi', 'Citrus microcarpa', 'Small citrus fruit', 'Year-round', 365, 'Medium', 20.0, 30.0, 60.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'High local demand; juice & condiments', 'Loam, sandy loam, well-drained', 'Tropical humid; fruiting year-round', NULL, '2025-10-26 01:02:31'),
(82, 'Mango', 'Mangifera indica', 'Tropical fruit tree', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'Local, provincial, export potential for certain varieties', 'Well-drained loam; sandy loam', 'Tropical; distinct dry season aids flowering', NULL, '2025-10-26 01:02:31'),
(83, 'Pineapple', 'Ananas comosus', 'Tropical fruit crop', 'Year-round', 365, 'Low to Medium', 20.0, 35.0, 50.0, 80.0, 600.00, 1200.00, 4.5, 6.5, 'High as fruit & processing', 'Sandy loam, well-drained acidic soil', 'Tropical; drought-tolerant once established', NULL, '2025-10-26 01:02:31'),
(84, 'Papaya', 'Carica papaya', 'Fast-growing fruit tree', 'Year-round', 365, 'Medium to High', 20.0, 35.0, 60.0, 80.0, 800.00, 1200.00, 6.0, 7.0, 'Local & regional', 'Loam, well-drained', 'Warm, humid; continuous fruiting possible', NULL, '2025-10-26 01:02:31'),
(85, 'Jackfruit', 'Artocarpus heterophyllus', 'Large tropical fruit', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.00, 1200.00, 6.0, 7.0, 'Local markets; value-added products', 'Loam to sandy loam, deep soils', 'Tropical humid', NULL, '2025-10-26 01:02:31'),
(86, 'Rambutan', 'Nephelium lappaceum', 'Tropical fruit tree', 'Year-round', 365, 'High', 20.0, 35.0, 70.0, 90.0, 1000.00, 1500.00, 6.0, 7.0, 'Local & provincial seasonal fruit', 'Deep loam, well-drained', 'Humid tropical; wet season growth', NULL, '2025-10-26 01:02:31'),
(87, 'Lanzones', 'Lansium parasiticum', 'Tropical fruit tree', 'Year-round', 365, 'Medium to High', 20.0, 30.0, 70.0, 90.0, 800.00, 1200.00, 6.0, 7.0, 'Local specialty fruit', 'Loam, well-drained', 'Tropical; humid; specific microclimates favorable', NULL, '2025-10-26 01:02:31'),
(88, 'Sugar apple (Atis)', 'Annona squamosa', 'Tropical fruit tree', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 600.00, 1000.00, 6.0, 7.0, 'Local fruit markets', 'Loam, well-drained', 'Tropical; warm humid', NULL, '2025-10-26 01:02:31'),
(89, 'Citrus (Orange/Mandarin)', 'Citrus spp.', 'Citrus fruit trees', 'Year-round', 365, 'Medium', 15.0, 30.0, 50.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'Local & regional', 'Loam with good drainage', 'Tropical to subtropical microclimates (upland)', NULL, '2025-10-26 01:02:31'),
(90, 'Avocado', 'Persea americana', 'Tropical fruit tree', 'Year-round', 365, 'Medium', 15.0, 30.0, 50.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'Growing market; higher value', 'Deep loam, well-drained', 'Upland cooler pockets preferred for some varieties', NULL, '2025-10-26 01:02:31'),
(91, 'Coffee (Robusta)', 'Coffea canephora', 'Coffee plantation crop', 'Year-round', 365, 'Medium', 15.0, 30.0, 60.0, 80.0, 800.00, 1500.00, 6.0, 7.0, 'Regional & specialty markets', 'Loam, well-drained upland soils', 'Upland cooler sites preferred for quality; robust', NULL, '2025-10-26 01:02:31'),
(92, 'Cacao (Cocoa)', 'Theobroma cacao', 'Chocolate tree crop', 'Year-round', 365, 'High', 20.0, 30.0, 70.0, 90.0, 1000.00, 1500.00, 6.0, 7.0, 'High-value (chocolate supply chain)', 'Loam with good organic matter, shade', 'Humid tropical; shade-grown systems work best', NULL, '2025-10-26 01:02:31'),
(93, 'Abaca', 'Musa textilis', 'Fiber crop', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.00, 1200.00, 6.0, 7.0, 'Fiber industry (national & export)', 'Loam, well-drained slopes', 'Tropical humid; thrives in upland/gullied areas', NULL, '2025-10-26 01:02:31'),
(94, 'Rubber', 'Hevea brasiliensis', 'Latex-producing tree', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 800.00, 1200.00, 6.0, 7.0, 'Industrial, regional demand', 'Deep loam to clay loam with good drainage', 'Tropical humid; long-term plantation crop', NULL, '2025-10-26 01:02:31'),
(95, 'Bamboo (shoots)', 'Bambusa spp.', 'Fast-growing grass', 'Year-round', 90, 'Medium', 15.0, 35.0, 50.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'Local construction, crafts, shoots for food', 'Wide range; loam preferred', 'Tropical; tolerant and fast-growing', NULL, '2025-10-26 01:02:31'),
(96, 'Strawberry (small-scale)', 'Fragaria × ananassa', 'Cool-season fruit', 'Dry Season', 90, 'High', 10.0, 25.0, 60.0, 80.0, 600.00, 1000.00, 6.0, 7.0, 'Niche/local high-value market', 'Sandy loam, well-drained (usually high organic matter)', 'Cooler highland microclimates required', NULL, '2025-10-26 01:02:31'),
(97, 'Mushroom (cultivated)', 'Pleurotus spp.', 'Indoor cultivated crop', 'Year-round', 30, 'High', 15.0, 25.0, 80.0, 95.0, 0.00, 0.00, 6.0, 7.0, 'High local demand; value-added', 'Not soil-grown (substrate)', 'Indoor-controlled; any climate if facility provides', NULL, '2025-10-26 01:02:31'),
(98, 'Areca nut (Betel nut)', 'Areca catechu', 'Cultural palm crop', 'Year-round', 365, 'Medium', 20.0, 35.0, 60.0, 80.0, 600.00, 1200.00, 6.0, 7.0, 'Local cultural demand', 'Loam, well-drained; coastal to inland', 'Tropical humid', NULL, '2025-10-26 01:02:31'),
(99, 'Sesame (Til)', 'Sesamum indicum', 'Oilseed crop', 'Dry Season', 90, 'Low', 20.0, 35.0, 40.0, 70.0, 200.00, 600.00, 6.0, 7.0, 'Niche/local markets', 'Sandy loam, well-drained', 'Hot, dry spells favorable at harvest', NULL, '2025-10-26 01:02:31'),
(100, 'Melon (Cantaloupe)', 'Cucumis melo', 'Summer fruit crop', 'Dry Season', 90, 'Medium', 20.0, 30.0, 50.0, 70.0, 400.00, 800.00, 6.0, 7.0, 'Local seasonal demand', 'Sandy loam, well-drained', 'Warm; dry period near harvest preferred', NULL, '2025-10-26 01:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `crop_feedback`
--

CREATE TABLE `crop_feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `crop_schedule_id` int(11) DEFAULT NULL,
  `recommendation_id` int(11) DEFAULT NULL,
  `crop_condition` enum('success','partial','failure') NOT NULL,
  `challenges_encountered` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`challenges_encountered`)),
  `remarks` text DEFAULT NULL,
  `photos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`photos`)),
  `feedback_score` int(11) DEFAULT NULL CHECK (`feedback_score` >= 1 and `feedback_score` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crop_recommendations`
--

CREATE TABLE `crop_recommendations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `crop_id` int(11) DEFAULT NULL,
  `soil_type_id` int(11) DEFAULT NULL,
  `weather_data_id` int(11) DEFAULT NULL,
  `recommendation_score` decimal(3,2) DEFAULT NULL,
  `reasons` text DEFAULT NULL,
  `planting_tips` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `crop_schedules`
--

CREATE TABLE `crop_schedules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `crop_id` int(11) DEFAULT NULL,
  `recommendation_id` int(11) DEFAULT NULL,
  `planting_date` date DEFAULT NULL,
  `expected_harvest_date` date DEFAULT NULL,
  `actual_harvest_date` date DEFAULT NULL,
  `status` enum('planting','vegetative','reproductive','harvest','completed') DEFAULT 'planting',
  `progress_percentage` decimal(5,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crop_soil_compatibility`
--

CREATE TABLE `crop_soil_compatibility` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) DEFAULT NULL,
  `soil_type_id` int(11) DEFAULT NULL,
  `compatibility_score` decimal(3,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recommendations`
--

CREATE TABLE `recommendations` (
  `id` int(11) NOT NULL,
  `recom_id` varchar(255) NOT NULL,
  `crop_name` varchar(255) NOT NULL,
  `soil_type` varchar(255) NOT NULL,
  `water_avail` varchar(255) NOT NULL,
  `weather_condition` varchar(255) NOT NULL,
  `area` double NOT NULL,
  `marketability` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soil_types`
--

CREATE TABLE `soil_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ph_min` decimal(3,1) DEFAULT NULL,
  `ph_max` decimal(3,1) DEFAULT NULL,
  `drainage` varchar(50) DEFAULT NULL,
  `fertility_level` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soil_types`
--

INSERT INTO `soil_types` (`id`, `name`, `description`, `ph_min`, `ph_max`, `drainage`, `fertility_level`, `created_at`) VALUES
(16, 'Alluvial clay loam', 'Rich alluvial soil with good water retention', 6.0, 7.5, 'Moderate', 'High', '2025-10-26 01:02:31'),
(17, 'Loam to sandy loam', 'Balanced soil with good drainage', 6.0, 7.0, 'Good', 'Medium', '2025-10-26 01:02:31'),
(18, 'Sandy loam, well-drained coastals', 'Light soil with excellent drainage', 5.5, 7.0, 'Excellent', 'Low', '2025-10-26 01:02:31'),
(19, 'Loam, clay loam, alluvial soils', 'Heavy soil with good fertility', 6.0, 7.5, 'Moderate', 'High', '2025-10-26 01:02:31'),
(20, 'Well-drained loam', 'Ideal soil with balanced properties', 6.0, 7.0, 'Good', 'High', '2025-10-26 01:02:31'),
(21, 'Sandy loam to loam', 'Light to medium soil', 5.5, 7.0, 'Good', 'Medium', '2025-10-26 01:02:31'),
(22, 'Clay to silty clay (moist soils)', 'Heavy soil with high water retention', 6.0, 7.5, 'Poor', 'High', '2025-10-26 01:02:31'),
(23, 'Sandy loam, well-drained', 'Light soil with good drainage', 5.5, 7.0, 'Excellent', 'Low', '2025-10-26 01:02:31'),
(24, 'Loam, fertile garden soil', 'Rich soil ideal for vegetables', 6.0, 7.0, 'Good', 'High', '2025-10-26 01:02:31'),
(25, 'Loam, well-drained', 'Balanced soil with good drainage', 6.0, 7.0, 'Good', 'Medium', '2025-10-26 01:02:31'),
(26, 'Deep loam, well-drained', 'Deep soil with excellent properties', 6.0, 7.0, 'Good', 'High', '2025-10-26 01:02:31'),
(27, 'Sandy loam, well-drained acidic soil', 'Acidic soil suitable for specific crops', 4.5, 6.5, 'Excellent', 'Low', '2025-10-26 01:02:31'),
(28, 'Loam with good organic matter', 'Rich organic soil', 6.0, 7.0, 'Good', 'Very High', '2025-10-26 01:02:31'),
(29, 'Deep loam to clay loam', 'Deep heavy soil', 6.0, 7.5, 'Moderate', 'High', '2025-10-26 01:02:31'),
(30, 'Wide range; loam preferred', 'Versatile soil type', 5.5, 7.0, 'Good', 'Medium', '2025-10-26 01:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `last_login` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `password_token` varchar(255) NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'Active',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `user_soil_preferences`
--

CREATE TABLE `user_soil_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `soil_type_id` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weather_data`
--

CREATE TABLE `weather_data` (
  `id` int(11) NOT NULL,
  `location` varchar(100) NOT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `humidity` decimal(4,1) DEFAULT NULL,
  `rainfall` decimal(6,2) DEFAULT NULL,
  `wind_speed` decimal(4,1) DEFAULT NULL,
  `weather_condition` varchar(50) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `api_source` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `weather_data`
--

INSERT INTO `weather_data` (`id`, `location`, `temperature`, `humidity`, `rainfall`, `wind_speed`, `weather_condition`, `recorded_at`, `api_source`) VALUES
(1, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:44:17', 'OpenWeatherMap'),
(2, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:44:17', 'OpenWeatherMap'),
(3, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:51:38', 'OpenWeatherMap'),
(4, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:51:38', 'OpenWeatherMap'),
(5, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:51:49', 'OpenWeatherMap'),
(6, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:51:49', 'OpenWeatherMap'),
(7, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:52:07', 'OpenWeatherMap'),
(8, 'Barbaza, PH', 28.4, 80.0, 0.94, 4.0, 'Rain', '2025-10-27 05:52:07', 'OpenWeatherMap'),
(9, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:54:28', 'OpenWeatherMap'),
(10, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:54:28', 'OpenWeatherMap'),
(11, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:57:56', 'OpenWeatherMap'),
(12, 'Barbaza, PH', 28.4, 80.0, 0.00, 4.0, 'Clouds', '2025-10-27 05:57:56', 'OpenWeatherMap');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crops`
--
ALTER TABLE `crops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_feedback`
--
ALTER TABLE `crop_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `crop_schedule_id` (`crop_schedule_id`),
  ADD KEY `recommendation_id` (`recommendation_id`);

--
-- Indexes for table `crop_recommendations`
--
ALTER TABLE `crop_recommendations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `crop_id` (`crop_id`),
  ADD KEY `soil_type_id` (`soil_type_id`),
  ADD KEY `weather_data_id` (`weather_data_id`);

--
-- Indexes for table `crop_schedules`
--
ALTER TABLE `crop_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `crop_id` (`crop_id`),
  ADD KEY `recommendation_id` (`recommendation_id`);

--
-- Indexes for table `crop_soil_compatibility`
--
ALTER TABLE `crop_soil_compatibility`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_crop_soil` (`crop_id`,`soil_type_id`),
  ADD KEY `soil_type_id` (`soil_type_id`);

--
-- Indexes for table `recommendations`
--
ALTER TABLE `recommendations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `soil_types`
--
ALTER TABLE `soil_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_soil_preferences`
--
ALTER TABLE `user_soil_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `soil_type_id` (`soil_type_id`);

--
-- Indexes for table `weather_data`
--
ALTER TABLE `weather_data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crops`
--
ALTER TABLE `crops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `crop_feedback`
--
ALTER TABLE `crop_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crop_recommendations`
--
ALTER TABLE `crop_recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crop_schedules`
--
ALTER TABLE `crop_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crop_soil_compatibility`
--
ALTER TABLE `crop_soil_compatibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recommendations`
--
ALTER TABLE `recommendations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `soil_types`
--
ALTER TABLE `soil_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_soil_preferences`
--
ALTER TABLE `user_soil_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weather_data`
--
ALTER TABLE `weather_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crop_feedback`
--
ALTER TABLE `crop_feedback`
  ADD CONSTRAINT `crop_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_feedback_ibfk_2` FOREIGN KEY (`crop_schedule_id`) REFERENCES `crop_schedules` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_feedback_ibfk_3` FOREIGN KEY (`recommendation_id`) REFERENCES `crop_recommendations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crop_recommendations`
--
ALTER TABLE `crop_recommendations`
  ADD CONSTRAINT `crop_recommendations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_recommendations_ibfk_2` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_recommendations_ibfk_3` FOREIGN KEY (`soil_type_id`) REFERENCES `soil_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_recommendations_ibfk_4` FOREIGN KEY (`weather_data_id`) REFERENCES `weather_data` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crop_schedules`
--
ALTER TABLE `crop_schedules`
  ADD CONSTRAINT `crop_schedules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_schedules_ibfk_2` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_schedules_ibfk_3` FOREIGN KEY (`recommendation_id`) REFERENCES `crop_recommendations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `crop_soil_compatibility`
--
ALTER TABLE `crop_soil_compatibility`
  ADD CONSTRAINT `crop_soil_compatibility_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crops` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `crop_soil_compatibility_ibfk_2` FOREIGN KEY (`soil_type_id`) REFERENCES `soil_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_soil_preferences`
--
ALTER TABLE `user_soil_preferences`
  ADD CONSTRAINT `user_soil_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_soil_preferences_ibfk_2` FOREIGN KEY (`soil_type_id`) REFERENCES `soil_types` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

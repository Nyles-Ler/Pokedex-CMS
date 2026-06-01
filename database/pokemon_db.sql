-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2026 at 03:21 PM
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
-- Database: `serverside`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `pokemon_id` int(10) UNSIGNED NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `pokemon_id`, `user_name`, `comment`, `created_at`, `is_hidden`) VALUES
(1, 7, 'Jordan', 'TEST!', '2026-04-19 02:23:32', 0),
(4, 7, 'Jordan', 'TEST #2', '2026-04-21 12:32:02', 0),
(9, 7, 'admin', 'TEST#4', '2026-04-21 16:08:23', 0),
(15, 7, 'test', 'test', '2026-04-21 16:45:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(10) UNSIGNED NOT NULL,
  `pokemon_id` int(10) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `pokemon_id`, `file_name`, `created_at`) VALUES
(2, 3, '69d3e1bdc116b-004.png', '2026-04-06 16:39:25'),
(3, 4, '69d3e1c53e130-007.png', '2026-04-06 16:39:33'),
(4, 5, '69d3e1cfd90c5-025.png', '2026-04-06 16:39:43'),
(5, 6, '69d3e1d622bc2-054.png', '2026-04-06 16:39:50'),
(6, 7, '69d3e1dd403be-063.png', '2026-04-06 16:39:57'),
(7, 8, '69d3e1e475e80-079.png', '2026-04-06 16:40:04'),
(8, 9, '69d3e1eb3ce13-133.png', '2026-04-06 16:40:11'),
(9, 10, '69d3e1f1ab634-150.png', '2026-04-06 16:40:17'),
(10, 11, '69d3e1fac202c-151.png', '2026-04-06 16:40:26'),
(11, 1, '69d3e200aa84f-384.png', '2026-04-06 16:40:32'),
(12, 12, '69d3e3645806f-001.png', '2026-04-06 16:46:28'),
(14, 19, '69d3e439c1ead-144.png', '2026-04-06 16:50:01'),
(15, 20, '69d3e58904b3b-010.png', '2026-04-06 16:55:37'),
(16, 21, '69d3e629ef201-197.png', '2026-04-06 16:58:18'),
(17, 22, '69d3e6e999ab3-493.png', '2026-04-06 17:01:29'),
(18, 23, '69d3e75d36568-035.png', '2026-04-06 17:03:25'),
(19, 24, '69d3e7fe88f83-068.png', '2026-04-06 17:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `pokemon`
--

CREATE TABLE `pokemon` (
  `id` int(10) UNSIGNED NOT NULL,
  `pokedex_number` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type1` varchar(50) NOT NULL,
  `type2` varchar(50) DEFAULT NULL,
  `hp` int(10) UNSIGNED NOT NULL,
  `attack_stat` int(10) UNSIGNED NOT NULL,
  `defense_stat` int(10) UNSIGNED NOT NULL,
  `special_attack` int(10) UNSIGNED NOT NULL,
  `special_defense` int(10) UNSIGNED NOT NULL,
  `speed_stat` int(10) UNSIGNED NOT NULL,
  `height` decimal(4,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pokemon`
--

INSERT INTO `pokemon` (`id`, `pokedex_number`, `name`, `type1`, `type2`, `hp`, `attack_stat`, `defense_stat`, `special_attack`, `special_defense`, `speed_stat`, `height`, `weight`, `description`, `image`, `created_at`, `updated_at`) VALUES
(1, 384, 'Rayquaza', 'Dragon', 'Flying', 105, 150, 90, 150, 90, 95, 23.00, 455.00, 'Rayquaza is said to have lived for hundreds of millions of years. Legends remain of how it put to rest the clash between Kyogre and Groudon', 'images/384.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(3, 4, 'Charmander', 'Fire', NULL, 39, 52, 43, 60, 50, 65, 0.60, 8.50, 'The flame on its tail shows the strength of its life-force. If Charmander is weak, the flame also burns weakly.', 'images/004.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(4, 7, 'Squirtle', 'Water', NULL, 44, 48, 65, 50, 64, 43, 0.50, 9.00, 'After birth, its back swells and hardens into a shell. It sprays a potent foam from its mouth.\r\nJust a Turtle can spray water.', 'images/007.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(5, 25, 'Pikachu', 'Electric', NULL, 35, 55, 40, 50, 50, 90, 0.40, 6.00, 'When it is angered, it immediately discharges the energy stored in the pouches in its cheeks.', 'images/025.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(6, 54, 'Psyduck', 'Water', NULL, 50, 52, 48, 65, 50, 55, 0.80, 19.60, 'It is constantly wracked by a headache. When the headache turns intense, it begins using mysterious powers.', 'images/054.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(7, 63, 'Abra', 'Psychic', NULL, 50, 52, 48, 65, 50, 55, 0.90, 19.50, 'This Pokemon uses its psychic powers while it sleeps. The contents of Abras dreams affect the powers that the Pokemon wields.', 'images/063.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(8, 79, 'Slowpoke', 'Water', 'Psychic', 90, 65, 65, 40, 40, 15, 1.20, 36.00, 'It is incredibly slow and dopey. It takes five seconds for it to feel pain when under attack.', 'images/079.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(9, 133, 'Eevee', 'Normal', NULL, 55, 55, 50, 45, 65, 55, 0.30, 6.50, 'Its ability to evolve into many forms allows it to adapt smoothly and perfectly to any environment.', 'images/133.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(10, 150, 'Mewtwo', 'Psychic', NULL, 106, 110, 90, 154, 90, 130, 2.00, 122.00, 'Its DNA is almost the same as Mews. However, its size and disposition are vastly different.', 'images/150.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(11, 151, 'Mew', 'Psychic', NULL, 100, 100, 100, 100, 100, 100, 0.40, 4.00, 'When viewed through a microscope, this Pokemons short, fine, delicate hair can be seen.', 'images/151.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(12, 1, 'Bulbasaur', 'Grass', 'Poison', 45, 49, 49, 65, 65, 45, 0.70, 6.90, 'For some time after its birth, it uses the nutrients that are packed into the seed on its back in order to grow.', 'images/001.png', '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(19, 144, 'Articuno', 'Ice', 'Flying', 90, 85, 100, 95, 125, 85, 1.70, 55.40, 'This legendary bird Pok&eacute;mon can create blizzards by freezing moisture in the air.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(20, 10, 'Caterpie', 'Bug', NULL, 45, 30, 35, 20, 20, 45, 0.30, 2.90, 'For protection, it releases a horrible stench from the antenna on its head to drive away enemies.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(21, 197, 'Umbreon', 'Dark', NULL, 95, 65, 110, 60, 130, 65, 1.00, 27.00, 'When exposed to the moon&#039;s aura, the rings on its body glow faintly and it gains a mysterious power.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(22, 493, 'Arceus', 'Normal', NULL, 120, 120, 120, 120, 120, 120, 3.20, 320.00, 'According to the legends of Sinnoh, this Pok&eacute;mon emerged from an egg and shaped all there is in this world.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(23, 35, 'Clefairy', 'Fairy', NULL, 70, 45, 48, 60, 65, 35, 0.60, 7.50, 'On nights with a full moon, Clefairy gather from all over and dance. Bathing in moonlight makes them float.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31'),
(24, 68, 'Machamp', 'Fighting', NULL, 90, 130, 80, 65, 85, 55, 1.60, 130.00, 'It punches with its four arms at blinding speed. It can launch 1,000 punches in two seconds.', NULL, '2026-04-14 12:28:31', '2026-04-14 12:28:31');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `color` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `strengths` varchar(255) DEFAULT NULL,
  `weaknesses` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`, `color`, `description`, `strengths`, `weaknesses`) VALUES
(1, 'Normal', '#A8A77A', 'Basic and balanced type with no special elemental traits', 'None', 'Fighting'),
(2, 'Fire', '#EE8130', 'Hot and aggressive type that burns opponents', 'Grass, Ice, Bug, Steel', 'Water, Rock, Ground'),
(3, 'Water', '#6390F0', 'Fluid and adaptable type with strong defensive traits', 'Fire, Rock, Ground', 'Electric, Grass'),
(4, 'Electric', '#F7D02C', 'Fast and energetic type that uses electricity', 'Water, Flying', 'Ground'),
(5, 'Grass', '#7AC74C', 'Nature-based type focused on growth and recovery', 'Water, Rock, Ground', 'Fire, Ice, Poison, Flying, Bug'),
(6, 'Ice', '#96D9D6', 'Cold and fragile type with strong offensive power', 'Grass, Ground, Flying, Dragon', 'Fire, Fighting, Rock, Steel'),
(7, 'Fighting', '#C22E28', 'Physical combat type focused on strength and skill', 'Normal, Ice, Rock, Dark, Steel', 'Flying, Psychic, Fairy'),
(8, 'Poison', '#A33EA1', 'Toxic type that weakens opponents over time', 'Grass, Fairy', 'Ground, Psychic'),
(9, 'Ground', '#E2BF65', 'Earth-based type with strong physical attacks', 'Fire, Electric, Poison, Rock, Steel', 'Water, Grass, Ice'),
(10, 'Flying', '#A98FF3', 'Air-based type known for speed and mobility', 'Grass, Fighting, Bug', 'Electric, Ice, Rock'),
(11, 'Psychic', '#F95587', 'Mind-based type with strong special attacks', 'Fighting, Poison', 'Bug, Ghost, Dark'),
(12, 'Bug', '#A6B91A', 'Fast and adaptable type often with status effects', 'Grass, Psychic, Dark', 'Fire, Flying, Rock'),
(13, 'Rock', '#B6A136', 'Tough and defensive type with high durability', 'Fire, Ice, Flying, Bug', 'Water, Grass, Fighting, Ground, Steel'),
(14, 'Ghost', '#735797', 'Mysterious type that can bypass normal defenses', 'Psychic, Ghost', 'Ghost, Dark'),
(15, 'Dragon', '#6F35FC', 'Powerful and rare type with strong attacks', 'Dragon', 'Ice, Dragon, Fairy'),
(16, 'Dark', '#705746', 'Sneaky type that uses underhanded tactics', 'Psychic, Ghost', 'Fighting, Bug, Fairy'),
(17, 'Steel', '#B7B7CE', 'Highly defensive type with strong resistances', 'Ice, Rock, Fairy', 'Fire, Fighting, Ground'),
(18, 'Fairy', '#D685AD', 'Magical type that counters powerful dragons', 'Fighting, Dragon, Dark', 'Poison, Steel');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(45) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$5.gn/IG6Svwm1aCNLyhMQebJ5W3laFcRCGrN5jk93i8rTiEK/ddnm', NULL, 'admin', '2026-03-25 14:34:38'),
(2, 'jordan', '$2y$10$5jmYlkHuNXRNIgP93.O9f.Ctwk/Htc/RA2dBxAbpG3jHSR8wDw9Gi', NULL, 'user', '2026-03-26 01:03:13'),
(5, 'bob', '$2y$10$WJ28ktVOZvNe7qoVNu4.QOvDr1xRF0A3PBIm6C.H3ihGX8oZioySu', 'bob@mail.com', 'user', '2026-04-21 15:44:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pokemon_id` (`pokemon_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_images_pokemon` (`pokemon_id`);

--
-- Indexes for table `pokemon`
--
ALTER TABLE `pokemon`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pokedex_number` (`pokedex_number`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `pokemon`
--
ALTER TABLE `pokemon`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`pokemon_id`) REFERENCES `pokemon` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `fk_images_pokemon` FOREIGN KEY (`pokemon_id`) REFERENCES `pokemon` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

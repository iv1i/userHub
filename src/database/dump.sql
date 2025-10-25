/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: userHub
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `admins` VALUES
(1,'admin','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2025-10-22 19:18:32');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `birthdate` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'ivi','$2y$10$xt5lML5uwVATLUZxziAj7.JoCePZ42Z0zM8ALfFXBqemBVAagkSYu','ilya','ilinyx','male','2004-04-26','2025-10-23 11:21:50','2025-10-23 16:35:04'),
(7,'ivanov_ii','$2y$10$p2HnIJX2NtFzU4pvPAmfae6lCCxDi.BbgEyIgMAm0LTUoKMKvZ9Ly','Иван','Иванов','male','2025-10-10','2025-10-23 16:47:51','2025-10-24 04:01:14'),
(9,'petrova_ap','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Анна','Петрова','female','2025-10-03','2025-10-23 17:04:19','2025-10-24 04:01:44'),
(10,'sidorov_sm','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Сергей','Сидоров','male','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:03:25'),
(11,'kuznetsova_ek','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Екатерина','Кузнецова','female','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:04:40'),
(12,'smirnov_av','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Алексей','Смирнов','male','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:05:21'),
(13,'vorobey_so','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Ольга','Воробей','female','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:06:33'),
(14,'fedorov_nd','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Николай','Федоров','male','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:06:19'),
(15,'morozova_ma','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Мария','Морозова','female','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:06:51'),
(16,'pavlenko_dv','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Дмитрий','Пввленко','male','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:07:24'),
(17,'zhuk_tn','$2y$10$5qs59DTboYnoXH.O3i8jCeS6FjqOH5z22LXmaZZCGz/6b3AFjBdJO','Татьяна','Жук','female','2025-10-03','2025-10-23 17:07:27','2025-10-24 04:07:51'),
(18,'JohD','$2y$10$awVtkPB1lta9qeGwt2GCr.UN/VaxljU5sO7oxiEMZPgF05GwyfYFa','John','Doe','other','2025-10-05','2025-10-23 17:10:01','2025-10-24 04:08:31');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-10-24 11:25:18

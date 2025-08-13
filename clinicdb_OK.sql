-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-08-2025 a las 20:33:56
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clinicdb`
--

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `actives_patients`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `actives_patients` (
`id_patient` int(11)
,`id_user` int(11)
,`full_names` varchar(255)
,`address` varchar(255)
,`gender` varchar(255)
,`age` varchar(255)
,`birthdate` date
,`register_observations` varchar(255)
,`referred` varchar(255)
,`phone_patient` varchar(255)
,`manager` varchar(255)
,`diseases` varchar(255)
,`register_date` date
,`update_date` date
,`id_status` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `appointments`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `appointments` (
`id_appointment` int(11)
,`id_patient` int(11)
,`motive` varchar(255)
,`descritption` varchar(255)
,`historial` varchar(255)
,`appointment_date` date
,`nex_appointment_date` date
,`register_date` datetime
,`update_date` datetime
,`id_user` int(11)
,`id_doc` int(11)
,`id_status_appointment` int(11)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_new`
--

CREATE TABLE `appointment_new` (
  `id_appointment` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `motive` varchar(255) NOT NULL,
  `descritption` varchar(255) NOT NULL,
  `historial` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `nex_appointment_date` date NOT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_doc` int(11) NOT NULL,
  `id_status_appointment` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointment_new`
--

INSERT INTO `appointment_new` (`id_appointment`, `id_patient`, `motive`, `descritption`, `historial`, `appointment_date`, `nex_appointment_date`, `register_date`, `update_date`, `id_user`, `id_doc`, `id_status_appointment`) VALUES
(1, 2, 'Dolor de Cabeza ', 'Fiebre', 'Con historial de diabetes', '2023-03-31', '0000-00-00', '2023-02-28 21:48:07', '0000-00-00 00:00:00', 1, 1, 5),
(2, 1, 'Prueba', 'Test QA', 'Test QA', '2023-04-30', '0000-00-00', '2023-02-28 21:58:09', '0000-00-00 00:00:00', 1, 1, 5),
(3, 3, 'Diabetes Revisión', 'Observation of patients glucosa levels', 'hiperglusemia', '2025-09-30', '0000-00-00', '2025-08-07 22:23:08', '0000-00-00 00:00:00', 1, 1, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_status`
--

CREATE TABLE `appointment_status` (
  `id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointment_status`
--

INSERT INTO `appointment_status` (`id`, `status`) VALUES
(1, 'Completed'),
(2, 'Incompleted'),
(3, 'Cancelled by patients'),
(4, 'Cancelled'),
(5, 'Current'),
(6, 'Closed');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `app_logs`
--

CREATE TABLE `app_logs` (
  `log_id` int(11) NOT NULL,
  `Timestamp` varchar(255) DEFAULT NULL,
  `Action` varchar(255) DEFAULT NULL,
  `TableName` varchar(255) DEFAULT NULL,
  `RecordID` varchar(255) DEFAULT NULL,
  `SqlQuery` varchar(255) DEFAULT NULL,
  `UserID` varchar(255) DEFAULT NULL,
  `ServerIP` varchar(255) DEFAULT NULL,
  `RequestUrl` text DEFAULT NULL,
  `RequestData` text DEFAULT NULL,
  `RequestCompleted` varchar(255) DEFAULT NULL,
  `RequestMsg` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `app_logs`
--

INSERT INTO `app_logs` (`log_id`, `Timestamp`, `Action`, `TableName`, `RecordID`, `SqlQuery`, `UserID`, `ServerIP`, `RequestUrl`, `RequestData`, `RequestCompleted`, `RequestMsg`) VALUES
(1, '2021-04-29 16:00:47', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(2, '2021-04-29 16:00:47', 'list', 'users', NULL, 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 20', '1', '::1', 'users', '[]', 'true', NULL),
(3, '2021-04-29 16:01:52', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(4, '2021-04-29 16:01:52', 'list', 'users', NULL, 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 20', '1', '::1', 'users', '[]', 'true', NULL),
(5, '2021-04-29 16:10:18', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(6, '2021-04-29 16:10:18', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(7, '2021-04-29 16:10:29', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(8, '2021-04-29 16:10:30', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(9, '2021-04-29 16:10:45', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(10, '2021-04-29 16:10:50', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(11, '2021-04-29 16:11:09', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(12, '2021-04-29 16:11:22', 'list', 'appointment_new', '2,1', 'SELECT SQL_CALC_FOUND_ROWS  appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.id_doc, doc.full_names AS doc_full_names, appointment_new.motive, appointment_new.descritptio', '1', '::1', 'appointment_new', '[]', 'true', NULL),
(13, '2021-04-29 16:12:36', 'list', 'appointment_new', '2,1', 'SELECT SQL_CALC_FOUND_ROWS  appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.id_doc, doc.full_names AS doc_full_names, appointment_new.motive, appointment_new.descritptio', '1', '::1', 'appointment_new', '[]', 'true', NULL),
(14, '2021-04-29 16:12:37', 'list', 'appointment_new', '2,1', 'SELECT SQL_CALC_FOUND_ROWS  appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.id_doc, doc.full_names AS doc_full_names, appointment_new.motive, appointment_new.descritptio', '1', '::1', 'appointment_new', '[]', 'true', NULL),
(15, '2021-04-29 16:12:46', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(16, '2021-04-29 16:13:18', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(17, '2021-04-29 16:13:19', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(18, '2021-04-29 16:13:20', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(19, '2021-04-29 16:13:21', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(20, '2021-04-29 16:14:51', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(21, '2021-04-29 16:14:52', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(22, '2021-04-29 16:15:10', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(23, '2021-04-29 16:15:13', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(24, '2021-04-29 16:15:14', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(25, '2021-04-29 16:15:17', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(26, '2021-04-29 16:15:21', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(27, '2021-04-29 16:15:24', 'list', 'doc', '1', 'SELECT SQL_CALC_FOUND_ROWS  doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user O', '1', '::1', 'doc/', '[]', 'true', NULL),
(28, '2021-04-29 16:18:09', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(29, '2021-04-29 16:18:09', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(30, '2021-04-29 16:18:09', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(31, '2021-04-29 16:23:00', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(32, '2021-04-29 16:23:06', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(33, '2021-04-29 16:23:15', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(34, '2021-04-29 16:23:21', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(35, '2021-04-29 16:23:36', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(36, '2021-04-29 16:23:45', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(37, '2021-04-29 16:24:01', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(38, '2021-04-29 16:24:02', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(39, '2021-04-29 16:24:03', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(40, '2021-04-29 16:24:03', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(41, '2021-04-29 16:24:04', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(42, '2021-04-29 16:24:04', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(43, '2021-04-29 16:24:04', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(44, '2021-04-29 16:24:07', 'list', 'clinic_prescription', '1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_prescription.id_prescription, clinic_prescription.id_appointment, appointment_new.appointment_date AS appointment_new_appointment_date, clinic_prescription.id_patient, clinic_patients.full_names AS clinic_patients_full_n', '1', '::1', 'clinic_prescription', '[]', 'true', NULL),
(45, '2021-04-29 16:24:07', 'list', 'clinic_prescription', '1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_prescription.id_prescription, clinic_prescription.id_appointment, appointment_new.appointment_date AS appointment_new_appointment_date, clinic_prescription.id_patient, clinic_patients.full_names AS clinic_patients_full_n', '1', '::1', 'clinic_prescription', '[]', 'true', NULL),
(46, '2021-04-29 16:36:46', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(47, '2021-04-29 16:38:03', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(48, '2021-04-29 16:38:04', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(49, '2021-04-29 16:38:05', 'list', 'clinic_patients', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.phone_patient, clinic_patients.register_date, clinic_patie', '1', '::1', 'clinic_patients', '[]', 'true', NULL),
(50, '2021-04-29 16:50:49', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(51, '2021-04-29 16:50:52', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(52, '2021-04-29 16:52:27', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(53, '2021-04-29 16:52:31', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '2', '::1', 'index/login/', '{\"username\":\"rikicastro10\",\"password\":\"$2y$10$\\/ldLSHnmOgwQYh3b9TBWx.a6QP13NoglynmhMk\\/Z1enpe8D295kRm\"}', 'true', NULL),
(54, '2021-04-29 16:52:32', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '2', '::1', 'index/login/', '{\"username\":\"rikicastro10\",\"password\":\"$2y$10$\\/ldLSHnmOgwQYh3b9TBWx.a6QP13NoglynmhMk\\/Z1enpe8D295kRm\"}', 'true', NULL),
(55, '2021-04-29 16:53:29', 'userlogout', 'users', NULL, NULL, '2', '::1', 'index/logout', '[]', 'true', NULL),
(56, '2021-04-29 16:53:34', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(57, '2021-04-29 16:54:33', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(58, '2021-04-29 16:57:57', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(59, '2021-04-29 18:50:52', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(60, '2021-04-29 18:53:36', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(61, '2021-04-29 20:09:28', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(62, '2021-04-29 20:10:13', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(63, '2021-04-29 22:43:49', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', NULL, '::1', 'index/login/', '{\"username\":\"luiscaflores85\",\"password\":\"12345678a\"}', 'false', 'Username or password not correct'),
(64, '2021-04-29 22:43:54', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(65, '2021-04-29 23:48:01', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(66, '2021-04-29 23:49:52', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(67, '2021-04-30 08:54:36', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(68, '2021-04-30 08:54:36', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(69, '2021-04-30 09:14:21', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(70, '2021-04-30 09:16:32', 'list', 'users', '1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users WHERE  id_user = ?  ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users/list/id_user/1', '[]', 'true', NULL),
(71, '2021-04-30 09:16:32', 'view', 'users', '1', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/1', '[]', 'true', NULL),
(72, '2021-04-30 09:28:30', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(73, '2021-04-30 10:57:22', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(74, '2021-04-30 11:13:53', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(75, '2021-04-30 11:13:57', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(76, '2021-04-30 11:13:59', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(77, '2021-04-30 11:13:59', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(78, '2021-04-30 11:14:06', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(79, '2021-04-30 11:14:06', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(80, '2021-04-30 11:14:06', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(81, '2021-04-30 11:14:35', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(82, '2021-04-30 11:14:52', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(83, '2021-04-30 11:16:46', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(84, '2021-04-30 11:16:46', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(85, '2021-04-30 11:16:49', 'view', 'users', '1', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/1', '[]', 'true', NULL),
(86, '2021-04-30 11:17:03', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(87, '2021-04-30 11:17:04', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(88, '2021-04-30 11:17:12', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(89, '2021-04-30 11:17:14', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(90, '2021-04-30 11:18:16', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(91, '2021-04-30 11:18:22', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(92, '2021-04-30 11:18:26', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(93, '2021-04-30 11:19:24', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(94, '2021-04-30 11:19:38', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(95, '2021-04-30 11:19:41', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(96, '2021-04-30 11:20:18', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.referred, clinic_patients.phone_patient, clinic_patients.manager, clinic_pati', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(97, '2021-04-30 11:20:21', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.referred, clinic_patients.phone_patient, clinic_patients.manager, clinic_pati', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(98, '2021-04-30 11:20:22', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.referred, clinic_patients.phone_patient, clinic_patients.manager, clinic_pati', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(99, '2021-04-30 11:21:32', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(100, '2021-04-30 11:22:20', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(101, '2021-04-30 11:22:26', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(102, '2021-04-30 11:22:27', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(103, '2021-04-30 11:22:34', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(104, '2021-04-30 11:22:34', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(105, '2021-04-30 11:22:34', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(106, '2021-04-30 11:22:42', 'view', 'appointment_new', '1', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/1', '[]', 'true', NULL),
(107, '2021-04-30 11:23:30', 'view', 'appointment_new', '1', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/1', '[]', 'true', NULL),
(108, '2021-04-30 11:23:34', 'view', 'appointment_new', '1', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/1', '[]', 'true', NULL),
(109, '2021-04-30 11:23:44', 'view', 'clinic_prescription', '1', 'SELECT   clinic_prescription.id_prescription, clinic_prescription.id_appointment, appointment_new.appointment_date AS appointment_new_appointment_date, clinic_prescription.id_patient, clinic_patients.full_names AS clinic_patients_full_names, clinic_prescr', '1', '::1', 'clinic_prescription/view/1', '[]', 'true', NULL),
(110, '2021-04-30 11:24:29', 'view', 'clinic_prescription', '1', 'SELECT   clinic_prescription.id_prescription, clinic_prescription.id_appointment, appointment_new.appointment_date AS appointment_new_appointment_date, clinic_prescription.id_patient, clinic_patients.full_names AS clinic_patients_full_names, clinic_prescr', '1', '::1', 'clinic_prescription/view/1', '[]', 'true', NULL),
(111, '2021-04-30 11:24:49', 'view', 'clinic_prescription', '1', 'SELECT   clinic_prescription.id_prescription, clinic_prescription.id_appointment, appointment_new.appointment_date AS appointment_new_appointment_date, clinic_prescription.id_patient, clinic_patients.full_names AS clinic_patients_full_names, clinic_prescr', '1', '::1', 'clinic_prescription/view/1', '[]', 'true', NULL),
(112, '2021-04-30 11:45:24', 'view', 'users', '0', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/0', '[]', 'false', 'No record found'),
(113, '2021-04-30 15:52:49', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(114, '2021-04-30 15:52:55', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', NULL, '::1', 'index/login/', '{\"username\":\"luiscaflores85\",\"password\":\"12345678a\"}', 'false', 'Username or password not correct'),
(115, '2021-04-30 15:53:02', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '2', '::1', 'index/login/', '{\"username\":\"rikicastro10\",\"password\":\"$2y$10$\\/ldLSHnmOgwQYh3b9TBWx.a6QP13NoglynmhMk\\/Z1enpe8D295kRm\"}', 'true', NULL),
(116, '2021-04-30 16:03:09', 'userlogout', 'users', NULL, NULL, '2', '::1', 'index/logout', '[]', 'true', NULL),
(117, '2021-04-30 16:03:17', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '3', '::1', 'index/login/', '{\"username\":\"margomes\",\"password\":\"$2y$10$C9TA.jSae4I6V.LLGUzEzukX0XpX3DhhZmD1EaV3QmFS9SViwckTi\"}', 'true', NULL),
(118, '2021-04-30 16:12:11', 'userlogout', 'users', NULL, NULL, '3', '::1', 'index/logout', '[]', 'true', NULL),
(119, '2021-04-30 16:12:20', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(120, '2021-04-30 16:17:34', 'edit', 'clinic_patients', '3', 'UPDATE clinic_patients SET `address` = ? WHERE  clinic_patients.id_patient = ? ', '1', '::1', 'clinic_patients/editfield/3', '{\"address\":\"San Miguel\"}', 'true', NULL),
(121, '2021-04-30 17:01:04', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(122, '2021-04-30 17:01:08', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '3', '::1', 'index/login/', '{\"username\":\"margomes\",\"password\":\"$2y$10$C9TA.jSae4I6V.LLGUzEzukX0XpX3DhhZmD1EaV3QmFS9SViwckTi\"}', 'true', NULL),
(123, '2021-04-30 17:07:14', 'userlogout', 'users', NULL, NULL, '3', '::1', 'index/logout', '[]', 'true', NULL),
(124, '2021-04-30 17:07:19', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(125, '2021-04-30 17:25:57', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(126, '2021-04-30 17:37:58', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '3', '::1', 'index/login/', '{\"username\":\"margomes\",\"password\":\"$2y$10$C9TA.jSae4I6V.LLGUzEzukX0XpX3DhhZmD1EaV3QmFS9SViwckTi\"}', 'true', NULL),
(127, '2021-04-30 17:40:09', 'userlogout', 'users', NULL, NULL, '3', '::1', 'index/logout', '[]', 'true', NULL),
(128, '2021-04-30 17:40:13', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(129, '2021-04-30 17:49:44', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(130, '2021-04-30 17:49:44', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(131, '2021-04-30 17:49:49', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(132, '2021-04-30 17:49:50', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(133, '2021-04-30 17:50:11', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(134, '2021-04-30 17:50:12', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(135, '2021-04-30 17:50:13', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(136, '2021-04-30 17:50:14', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(137, '2021-04-30 17:50:28', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(138, '2021-04-30 17:50:29', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(139, '2021-04-30 17:50:30', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(140, '2021-04-30 17:50:52', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(141, '2021-04-30 17:50:55', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(142, '2021-04-30 17:50:56', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(143, '2021-04-30 17:50:56', 'view', 'clinic_patients', '3', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/3', '[]', 'true', NULL),
(144, '2021-04-30 17:51:00', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(145, '2021-04-30 17:51:00', 'view', 'doc', '1', 'SELECT   doc.id, doc.full_names, doc.address, doc.birthdate, doc.gender, doc.age, doc.Speciality, doc.register_date, doc.update_date, doc.id_user, users.full_names AS users_full_names, doc.photo FROM doc INNER JOIN users ON doc.id_user = users.id_user WHE', '1', '::1', 'doc/view/1', '[]', 'true', NULL),
(146, '2021-04-30 17:51:08', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(147, '2021-04-30 17:51:09', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(148, '2021-04-30 17:51:13', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(149, '2021-04-30 17:51:13', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(150, '2021-04-30 17:51:19', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(151, '2021-04-30 17:51:19', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(152, '2021-04-30 17:51:22', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(153, '2021-04-30 17:51:22', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(154, '2021-04-30 17:51:23', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL);
INSERT INTO `app_logs` (`log_id`, `Timestamp`, `Action`, `TableName`, `RecordID`, `SqlQuery`, `UserID`, `ServerIP`, `RequestUrl`, `RequestData`, `RequestCompleted`, `RequestMsg`) VALUES
(155, '2021-04-30 17:51:24', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(156, '2021-04-30 17:51:24', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(157, '2021-04-30 17:51:24', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(158, '2021-04-30 17:51:24', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(159, '2021-04-30 17:51:25', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(160, '2021-04-30 18:11:49', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(161, '2021-04-30 18:11:49', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(162, '2021-04-30 18:12:00', 'view', 'users', '3', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/3', '[]', 'true', NULL),
(163, '2021-04-30 18:13:02', 'view', 'users', '3', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/3', '[]', 'true', NULL),
(164, '2021-04-30 18:13:27', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(165, '2021-04-30 18:13:29', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(166, '2021-04-30 18:13:31', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(167, '2021-04-30 18:13:31', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(168, '2021-04-30 18:13:32', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(169, '2021-04-30 18:13:33', 'view', 'appointment_new', '2', 'SELECT   appointment_new.id_appointment, appointment_new.id_patient, clinic_patients.full_names AS clinic_patients_full_names, appointment_new.motive, appointment_new.descritption, appointment_new.historial, appointment_new.appointment_date, appointment_n', '1', '::1', 'appointment_new/view/2', '[]', 'true', NULL),
(170, '2021-04-30 18:29:27', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users/', '[]', 'true', NULL),
(171, '2021-04-30 18:36:56', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users/', '[]', 'true', NULL),
(172, '2021-04-30 18:37:08', 'view', 'users', '3', 'SELECT   id_user, full_names, rol, user_name, email, register_date, update_date FROM users WHERE  users.id_user = ?  LIMIT 1', '1', '::1', 'users/view/3', '[]', 'true', NULL),
(173, '2021-04-30 18:45:02', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(174, '2021-04-30 18:45:04', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(175, '2021-04-30 18:45:06', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(176, '2021-04-30 18:45:07', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(177, '2021-04-30 18:45:09', 'view', 'clinic_patients', '1', 'SELECT   clinic_patients.id_patient, clinic_patients.full_names, clinic_patients.birthdate, clinic_patients.address, clinic_patients.gender, clinic_patients.age, clinic_patients.register_observations, clinic_patients.referred, clinic_patients.phone_patien', '1', '::1', 'clinic_patients/view/1', '[]', 'true', NULL),
(178, '2021-04-30 18:45:51', 'view', 'patients_status', '1', 'SELECT   id, status FROM patients_status WHERE  patients_status.id = ?  LIMIT 1', '1', '::1', 'patients_status/view/1', '[]', 'true', NULL),
(179, '2021-04-30 18:47:52', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(180, '2021-04-30 18:47:53', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(181, '2021-04-30 18:47:54', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(182, '2021-04-30 18:48:01', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(183, '2021-04-30 18:48:01', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(184, '2021-04-30 18:48:07', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(185, '2021-04-30 18:49:08', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(186, '2021-04-30 18:49:08', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(187, '2021-04-30 18:49:12', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(188, '2021-04-30 18:49:39', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(189, '2021-04-30 18:49:39', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(190, '2021-04-30 18:49:42', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(191, '2021-04-30 18:49:42', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(192, '2021-04-30 18:49:53', 'view', 'invoices', '1', 'SELECT   invoices.id_invoice, invoices.invoice_num, invoices.id_concept, invoices_concepts.concept AS invoices_concepts_concept, invoices.id_patient, clinic_patients.full_names AS clinic_patients_full_names, invoices.quantity, invoices.price, invoices.tot', '1', '::1', 'invoices/view/1', '[]', 'true', NULL),
(193, '2021-04-30 19:09:30', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(194, '2021-04-30 19:09:51', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(195, '2021-04-30 19:09:51', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(196, '2021-04-30 19:12:14', 'list', 'users', '3,2,1', 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 0', '1', '::1', 'users', '[]', 'true', NULL),
(197, '2021-04-30 19:12:51', 'list', 'users', NULL, 'SELECT SQL_CALC_FOUND_ROWS  id_user, full_names, rol, user_name, email, photo, register_date, update_date FROM users ORDER BY users.id_user DESC  LIMIT 20 OFFSET 20', '1', '::1', 'users', '[]', 'true', NULL),
(198, '2023-02-28 15:25:32', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(199, '2023-02-28 15:26:19', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL),
(200, '2023-02-28 15:37:12', 'userlogout', 'users', NULL, NULL, '1', '::1', 'index/logout', '[]', 'true', NULL),
(201, '2023-02-28 16:03:28', 'userlogin', 'users', NULL, 'SELECT   * FROM users WHERE  user_name = ?  OR email = ?  LIMIT 1', '1', '::1', 'index/login/', '{\"username\":\"juanp10\",\"password\":\"$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW\"}', 'true', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `blood_type_catalog`
--

CREATE TABLE `blood_type_catalog` (
  `id` int(11) NOT NULL,
  `type` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `blood_type_catalog`
--

INSERT INTO `blood_type_catalog` (`id`, `type`) VALUES
(1, 'A+'),
(2, 'A-'),
(3, 'B+'),
(4, 'B-'),
(5, 'AB+'),
(6, 'AB-'),
(7, 'O+'),
(8, 'O-'),
(9, 'Other');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clinic_patients`
--

CREATE TABLE `clinic_patients` (
  `id_patient` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `full_names` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `gender` varchar(255) NOT NULL,
  `age` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `register_observations` varchar(255) NOT NULL,
  `referred` varchar(255) NOT NULL,
  `phone_patient` varchar(255) NOT NULL,
  `manager` varchar(255) NOT NULL,
  `diseases` varchar(255) NOT NULL,
  `register_date` date NOT NULL,
  `update_date` date NOT NULL,
  `id_status` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `id_document_type` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `id_blood_type` int(11) NOT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clinic_patients`
--

INSERT INTO `clinic_patients` (`id_patient`, `id_user`, `full_names`, `address`, `gender`, `age`, `birthdate`, `register_observations`, `referred`, `phone_patient`, `manager`, `diseases`, `register_date`, `update_date`, `id_status`, `photo`, `email`, `id_document_type`, `document_number`, `occupation`, `allergies`, `id_blood_type`, `emergency_contact_phone`) VALUES
(1, 1, 'Juan Alberto  Flores', 'Col.El Rosario #56644', 'Male', '38', '1985-01-03', 'the patient has fever and cough', 'hospital x', '75760539', 'Juan Flores', 'the patient has fever and cough', '2023-02-28', '2023-02-28', 1, 'http://localhost/clinic/uploads/files/satmd1i3yrbqgc8.png', 'test35@gmail.com', 1, '41225222222', 'bakery', 'aspririnas', 3, '75053691'),
(2, 1, 'Marvin Gomez', 'Col.El Rosario #56644', 'Male', '25', '2015-08-11', 'Diabetes', 'Doctor merengues', '75053691', 'Juan Perez', 'Diabetes complications', '2025-08-08', '2025-08-08', 2, '			Error 500 		Server Error											Exception Traces                This will only be displayed in DEVELOPMENT_MODE.															Error Message					syntax error, unexpected token &#34;private&#34;													File					C:\\x\\htdocs\\public_clinic_app\\libs', 'luiscalero345@gmail.com', 1, '41225222222', 'bakery', 'aspririnas', 4, '75053691'),
(3, 1, 'Luis Calero', 'Col.Jucuapa#2 Pol Casa 4 San Miguel', 'Male', '40', '1985-01-03', 'El paciente se queja de dolor abdominal fuerte ', 'NA', '73170759', 'Juan Perez', 'Sin enfermedades crónicas', '2025-08-07', '2025-08-07', 1, '			Error 500 		Server Error											Exception Traces                This will only be displayed in DEVELOPMENT_MODE.															Error Message					syntax error, unexpected token &#34;private&#34;													File					C:\\x\\htdocs\\public_clinic_app\\libs', 'admin@tienda.com', 1, '41225222222', 'bakery', 'aspririnas', 5, '75053691'),
(4, 1, 'Maria de Jesus Flores Reyes ', 'ColJucuapa#2Pol L casa 4 San Miguel', 'Female', '65', '1959-12-24', 'Diabetes Complications', 'NA', '75053691', 'Luis Calero', 'Diabetes Mellitos ', '2025-08-08', '2025-08-08', 2, NULL, 'admin@tienda.com', 1, '41225222222', 'bakery', 'aspririnas', 1, '75053691'),
(5, 1, 'Denis humberto Mondragón ', 'Col Jucuapa#2', 'Male', '45', '1979-09-29', 'Others', 'NA', '75053691', 'Luis Calero', 'stomach pain', '2025-08-08', '2025-08-08', 1, NULL, 'admin@tienda.com', 1, '41225222222', 'bakery', 'aspririnas', 1, '75053691'),
(6, 1, 'Blanca de Mondragón ', 'Los Laureles San Miguel', 'Female', '45', '1979-09-15', 'Fever', 'NA', '75053691', 'Juan Perez', 'fever', '2025-08-08', '2025-08-08', 2, NULL, 'admin@tienda.com', 1, '41225222222', 'bakery', 'aspririnas', 2, '75053691'),
(7, 1, 'Josefina Castro', 'San Miguel', 'Female', '40', '1985-08-22', 'Prueba QA', 'NA', '75053691', 'Juan Perez', 'Diabetes', '2025-08-08', '2025-08-08', 1, NULL, 'calflores45@gmail.com', 1, '41225222222', 'bakery', 'aspririnas', 1, '75053691'),
(8, 1, 'Francisco Gomez', 'San Lorenzo', 'Male', '40', '1985-08-21', 'strong pain', 'NA', '75053691', 'Juan Perez', 'diabetes', '2025-08-08', '2025-08-08', 1, NULL, 'calflores45@gmail.com', 5, '41225222222', 'bakery', 'penicilina', 5, '75053691'),
(10, 1, 'Elodie Larriete Mondragón Umanzor', 'Colonia Jucuapa#2 San Miguel', 'Female', NULL, '2024-10-14', 'baby with stomach complications ', 'NA', '75053691', 'Rocio Umanzor', 'NA', '2025-08-08', '2025-08-08', 1, NULL, 'admin@tienda.com', 1, '41225222222', 'NA', 'Nothing', 5, '75053691');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clinic_prescription`
--

CREATE TABLE `clinic_prescription` (
  `id_prescription` int(11) NOT NULL,
  `id_appointment` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `id_doctor` int(11) NOT NULL,
  `description_prescription` varchar(255) NOT NULL,
  `additional_comments` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clinic_prescription`
--

INSERT INTO `clinic_prescription` (`id_prescription`, `id_appointment`, `id_patient`, `id_doctor`, `description_prescription`, `additional_comments`, `id_user`, `register_date`, `update_date`) VALUES
(1, 3, 3, 1, 'the patients must inject insuline  15 units in the morning and come back to check whith doctor', 'Patients rebels with hiperglusemia ', 1, '2025-08-07 22:29:56', '2025-08-07 22:29:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doc`
--

CREATE TABLE `doc` (
  `id` int(11) NOT NULL,
  `full_names` varchar(255) NOT NULL,
  `dni` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `birthdate` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `age` varchar(255) DEFAULT NULL,
  `Speciality` varchar(255) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `license_issuer` varchar(100) DEFAULT NULL,
  `license_issue_date` date DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `university` varchar(150) DEFAULT NULL,
  `years_experience` int(3) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `work_email` varchar(150) DEFAULT NULL,
  `working_hours` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive','On Vacation','Retired') DEFAULT 'Active',
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `doc`
--

INSERT INTO `doc` (`id`, `full_names`, `dni`, `address`, `birthdate`, `gender`, `age`, `Speciality`, `license_number`, `license_issuer`, `license_issue_date`, `license_expiry_date`, `university`, `years_experience`, `office_phone`, `work_email`, `working_hours`, `status`, `register_date`, `update_date`, `id_user`, `photo`) VALUES
(1, 'Juan Jose Gomez', '4555555555555555555', 'San Miguel', '1985-01-03 12:00:00', 'Male', '38', 'Cirujano', '45555555555', NULL, NULL, NULL, NULL, NULL, '	75053691', NULL, NULL, '', '2023-02-28 21:46:32', '2023-02-28 21:46:32', 1, 'http://localhost/clinic/uploads/files/a4ynmqexiurv01s.png'),
(2, 'Marla Gomez', '4555555555555555555', 'San Juan ', '1998-10-07 12:00:00', 'Female', '25', 'oncology', '45555555555', NULL, NULL, NULL, NULL, NULL, '	75053691', NULL, NULL, '', '2025-08-07 22:39:17', '2025-08-07 22:39:17', 1, '			Error 500 		Server Error											Exception Traces                This will only be displayed in DEVELOPMENT_MODE.															Error Message					syntax error, unexpected token &#34;private&#34;													File					C:\\x\\htdocs\\public_clinic_app\\libs'),
(3, 'Glenda Yamilet Flores', '4555555555555555555', 'Colonia Jucuapa #2', '1978-10-17 12:00:00', 'Female', NULL, 'cirujan Medic', '45555555555', 'State', '2003-02-12', '2029-07-11', 'Ucla ', 18, '75053691', 'glenda@gmail.com', 'Weeks', 'Active', '2025-08-09 09:28:23', '2025-08-09 09:28:23', 2, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `document_type_catalog`
--

CREATE TABLE `document_type_catalog` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `document_type_catalog`
--

INSERT INTO `document_type_catalog` (`id`, `type`) VALUES
(1, 'DUI'),
(2, 'NIT'),
(3, 'Passport'),
(4, 'Foreigner ID'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `inactives_patients`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `inactives_patients` (
`id_patient` int(11)
,`id_user` int(11)
,`full_names` varchar(255)
,`address` varchar(255)
,`gender` varchar(255)
,`age` varchar(255)
,`birthdate` date
,`register_observations` varchar(255)
,`referred` varchar(255)
,`phone_patient` varchar(255)
,`manager` varchar(255)
,`diseases` varchar(255)
,`register_date` date
,`update_date` date
,`id_status` int(11)
,`photo` varchar(255)
,`email` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insurance_type_catalog`
--

CREATE TABLE `insurance_type_catalog` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `insurance_type_catalog`
--

INSERT INTO `insurance_type_catalog` (`id`, `type`) VALUES
(1, 'Public'),
(2, 'Private'),
(3, 'None');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices`
--

CREATE TABLE `invoices` (
  `id_invoice` int(11) NOT NULL,
  `id_concept` int(11) NOT NULL,
  `id_patient` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `total_invoice` decimal(10,2) NOT NULL,
  `date_invoice` date NOT NULL,
  `id_invoice_status` int(11) NOT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `comments` varchar(255) NOT NULL,
  `invoice_num` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoices_concepts`
--

CREATE TABLE `invoices_concepts` (
  `id` int(11) NOT NULL,
  `concept` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `invoices_concepts`
--

INSERT INTO `invoices_concepts` (`id`, `concept`) VALUES
(1, 'Payment of medical date'),
(2, 'Medical exams\r\n');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `invoice_cancelled`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `invoice_cancelled` (
`id_invoice` int(11)
,`id_concept` int(11)
,`id_patient` int(11)
,`quantity` int(11)
,`price` decimal(11,2)
,`total_invoice` decimal(10,2)
,`date_invoice` date
,`id_invoice_status` int(11)
,`register_date` datetime
,`update_date` datetime
,`comments` varchar(255)
,`invoice_num` varchar(255)
,`id_user` int(11)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `invoice_debt`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `invoice_debt` (
`id_invoice` int(11)
,`id_concept` int(11)
,`id_patient` int(11)
,`quantity` int(11)
,`price` decimal(11,2)
,`total_invoice` decimal(10,2)
,`date_invoice` date
,`id_invoice_status` int(11)
,`register_date` datetime
,`update_date` datetime
,`comments` varchar(255)
,`invoice_num` varchar(255)
,`id_user` int(11)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `invoice_status`
--

CREATE TABLE `invoice_status` (
  `id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `invoice_status`
--

INSERT INTO `invoice_status` (`id`, `status`) VALUES
(1, 'Cancelled'),
(2, 'Debt');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marital_status_catalog`
--

CREATE TABLE `marital_status_catalog` (
  `id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `marital_status_catalog`
--

INSERT INTO `marital_status_catalog` (`id`, `status`) VALUES
(1, 'Single'),
(2, 'Married'),
(3, 'Divorced'),
(4, 'Widowed'),
(5, 'Separated'),
(6, 'Cohabitating');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patients_status`
--

CREATE TABLE `patients_status` (
  `id` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `patients_status`
--

INSERT INTO `patients_status` (`id`, `status`) VALUES
(1, 'Active'),
(2, 'Inactive');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `full_names` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `login_session_key` varchar(255) DEFAULT NULL,
  `email_status` varchar(255) DEFAULT NULL,
  `password_expire_date` datetime DEFAULT '2021-07-27 00:00:00',
  `password_reset_key` varchar(255) DEFAULT NULL,
  `cel` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `full_names`, `rol`, `user_name`, `password`, `email`, `photo`, `register_date`, `update_date`, `login_session_key`, `email_status`, `password_expire_date`, `password_reset_key`, `cel`) VALUES
(1, 'Juan Perez', 'Admin', 'juanp10', '$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW', 'juanp10@gmail.com', 'qmpc9b6wx4hgkf5.png', '2025-08-06 22:53:22', '2025-08-06 22:53:22', 'c7f5d13d3cc0e804384b0514d632b86a', NULL, '2021-07-27 00:00:00', NULL, ''),
(2, 'Ricardo Castro', 'Assistant', 'rikicastro10', '$2y$10$/ldLSHnmOgwQYh3b9TBWx.a6QP13NoglynmhMk/Z1enpe8D295kRm', 'rikicastro@gmail.com', '3nly5foeuxvtjgh.png', '2021-04-27 17:52:59', '2021-04-27 17:52:59', NULL, NULL, '2021-07-27 00:00:00', NULL, ''),
(3, 'Marlon Gomes', 'Doctor', 'margomes', '$2y$10$C9TA.jSae4I6V.LLGUzEzukX0XpX3DhhZmD1EaV3QmFS9SViwckTi', 'margomez@gmail.com', '3nly5foeuxvtjgh.png', '2023-02-28 21:20:52', '2023-02-28 21:20:52', NULL, NULL, '2021-07-27 00:00:00', NULL, ''),
(4, 'Marvin Gomez', 'Patients', 'marvgo', '$2y$10$5aM92DfGbs8QK3Y8BxnShuD9m.g9j7xL39CMFGcTLD71YTo074cO2', 'luiscalero345@gmail.com', 'tgil61anwbx5sfo.png', '2023-02-28 21:08:37', '2023-02-28 21:08:37', NULL, NULL, '2021-07-27 00:00:00', NULL, '75053691');

-- --------------------------------------------------------

--
-- Estructura para la vista `actives_patients`
--
DROP TABLE IF EXISTS `actives_patients`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `actives_patients`  AS SELECT `cp`.`id_patient` AS `id_patient`, `cp`.`id_user` AS `id_user`, `cp`.`full_names` AS `full_names`, `cp`.`address` AS `address`, `cp`.`gender` AS `gender`, `cp`.`age` AS `age`, `cp`.`birthdate` AS `birthdate`, `cp`.`register_observations` AS `register_observations`, `cp`.`referred` AS `referred`, `cp`.`phone_patient` AS `phone_patient`, `cp`.`manager` AS `manager`, `cp`.`diseases` AS `diseases`, `cp`.`register_date` AS `register_date`, `cp`.`update_date` AS `update_date`, `cp`.`id_status` AS `id_status` FROM `clinic_patients` AS `cp` WHERE `cp`.`id_status` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `appointments`
--
DROP TABLE IF EXISTS `appointments`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointments`  AS SELECT `an`.`id_appointment` AS `id_appointment`, `an`.`id_patient` AS `id_patient`, `an`.`motive` AS `motive`, `an`.`descritption` AS `descritption`, `an`.`historial` AS `historial`, `an`.`appointment_date` AS `appointment_date`, `an`.`nex_appointment_date` AS `nex_appointment_date`, `an`.`register_date` AS `register_date`, `an`.`update_date` AS `update_date`, `an`.`id_user` AS `id_user`, `an`.`id_doc` AS `id_doc`, `an`.`id_status_appointment` AS `id_status_appointment` FROM `appointment_new` AS `an` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `inactives_patients`
--
DROP TABLE IF EXISTS `inactives_patients`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `inactives_patients`  AS SELECT `cp`.`id_patient` AS `id_patient`, `cp`.`id_user` AS `id_user`, `cp`.`full_names` AS `full_names`, `cp`.`address` AS `address`, `cp`.`gender` AS `gender`, `cp`.`age` AS `age`, `cp`.`birthdate` AS `birthdate`, `cp`.`register_observations` AS `register_observations`, `cp`.`referred` AS `referred`, `cp`.`phone_patient` AS `phone_patient`, `cp`.`manager` AS `manager`, `cp`.`diseases` AS `diseases`, `cp`.`register_date` AS `register_date`, `cp`.`update_date` AS `update_date`, `cp`.`id_status` AS `id_status`, `cp`.`photo` AS `photo`, `cp`.`email` AS `email` FROM `clinic_patients` AS `cp` WHERE `cp`.`id_status` = 2 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `invoice_cancelled`
--
DROP TABLE IF EXISTS `invoice_cancelled`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `invoice_cancelled`  AS SELECT `i`.`id_invoice` AS `id_invoice`, `i`.`id_concept` AS `id_concept`, `i`.`id_patient` AS `id_patient`, `i`.`quantity` AS `quantity`, `i`.`price` AS `price`, `i`.`total_invoice` AS `total_invoice`, `i`.`date_invoice` AS `date_invoice`, `i`.`id_invoice_status` AS `id_invoice_status`, `i`.`register_date` AS `register_date`, `i`.`update_date` AS `update_date`, `i`.`comments` AS `comments`, `i`.`invoice_num` AS `invoice_num`, `i`.`id_user` AS `id_user` FROM `invoices` AS `i` WHERE `i`.`id_invoice_status` = 1 ;

-- --------------------------------------------------------

--
-- Estructura para la vista `invoice_debt`
--
DROP TABLE IF EXISTS `invoice_debt`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `invoice_debt`  AS SELECT `i`.`id_invoice` AS `id_invoice`, `i`.`id_concept` AS `id_concept`, `i`.`id_patient` AS `id_patient`, `i`.`quantity` AS `quantity`, `i`.`price` AS `price`, `i`.`total_invoice` AS `total_invoice`, `i`.`date_invoice` AS `date_invoice`, `i`.`id_invoice_status` AS `id_invoice_status`, `i`.`register_date` AS `register_date`, `i`.`update_date` AS `update_date`, `i`.`comments` AS `comments`, `i`.`invoice_num` AS `invoice_num`, `i`.`id_user` AS `id_user` FROM `invoices` AS `i` WHERE `i`.`id_invoice_status` = 2 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `appointment_new`
--
ALTER TABLE `appointment_new`
  ADD PRIMARY KEY (`id_appointment`);

--
-- Indices de la tabla `appointment_status`
--
ALTER TABLE `appointment_status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `app_logs`
--
ALTER TABLE `app_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indices de la tabla `blood_type_catalog`
--
ALTER TABLE `blood_type_catalog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clinic_patients`
--
ALTER TABLE `clinic_patients`
  ADD PRIMARY KEY (`id_patient`);

--
-- Indices de la tabla `clinic_prescription`
--
ALTER TABLE `clinic_prescription`
  ADD PRIMARY KEY (`id_prescription`);

--
-- Indices de la tabla `doc`
--
ALTER TABLE `doc`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `document_type_catalog`
--
ALTER TABLE `document_type_catalog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `insurance_type_catalog`
--
ALTER TABLE `insurance_type_catalog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id_invoice`);

--
-- Indices de la tabla `invoices_concepts`
--
ALTER TABLE `invoices_concepts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `invoice_status`
--
ALTER TABLE `invoice_status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `marital_status_catalog`
--
ALTER TABLE `marital_status_catalog`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `patients_status`
--
ALTER TABLE `patients_status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `appointment_new`
--
ALTER TABLE `appointment_new`
  MODIFY `id_appointment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `appointment_status`
--
ALTER TABLE `appointment_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `app_logs`
--
ALTER TABLE `app_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT de la tabla `blood_type_catalog`
--
ALTER TABLE `blood_type_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clinic_patients`
--
ALTER TABLE `clinic_patients`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `clinic_prescription`
--
ALTER TABLE `clinic_prescription`
  MODIFY `id_prescription` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `doc`
--
ALTER TABLE `doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `document_type_catalog`
--
ALTER TABLE `document_type_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `insurance_type_catalog`
--
ALTER TABLE `insurance_type_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id_invoice` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `invoices_concepts`
--
ALTER TABLE `invoices_concepts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `invoice_status`
--
ALTER TABLE `invoice_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `marital_status_catalog`
--
ALTER TABLE `marital_status_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `patients_status`
--
ALTER TABLE `patients_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

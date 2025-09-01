-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-09-2025 a las 19:15:05
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `notify_event` (IN `p_event_name` VARCHAR(100), IN `p_json_data` JSON)   BEGIN
    DECLARE v_id_event INT;
    DECLARE v_title VARCHAR(150) DEFAULT '';
    DECLARE v_message TEXT DEFAULT '';

    -- Buscar plantilla del evento
    SELECT id_event, title_template, message_template
    INTO v_id_event, v_title, v_message
    FROM notification_events
    WHERE event_name = p_event_name
    LIMIT 1;

    -- Si no encuentra plantilla, poner mensaje genérico
    SET v_message = IFNULL(v_message, CONCAT('No template found for event: ', p_event_name));

    -- Reemplazos dinámicos
    SET v_message = REPLACE(v_message, '{patient_name}', 
        IFNULL(JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.patient_name')), ''));
    SET v_message = REPLACE(v_message, '{doctor_name}', 
        IFNULL(JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.doctor_name')), ''));
    SET v_message = REPLACE(v_message, '{doctor_specialty}', 
        IFNULL(JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.doctor_specialty')), ''));
    SET v_message = REPLACE(v_message, '{appointment_date}', 
        IFNULL(JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.appointment_date')), ''));

    -- 🔹 Caso: registro de pacientes (self)
    IF p_event_name = 'patient_registered_self' THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_user')) IS NOT NULL THEN
            INSERT INTO notifications (id_event, id_user, title, message)
            VALUES (
                v_id_event,
                JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_user')),
                v_title,
                v_message
            );
        END IF;
    END IF;

    -- 🔹 Caso: registro de doctor → notificación a Admin y Asistente
    IF p_event_name = 'doctor_registered' THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role IN (1,2);
    END IF;

    -- 🔹 Caso: bienvenida al doctor → notificación solo al nuevo doctor
    IF p_event_name = 'doctor_welcome' THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_user')) IS NOT NULL THEN
            INSERT INTO notifications (id_event, id_user, title, message)
            VALUES (
                v_id_event,
                JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_user')),
                v_title,
                v_message
            );
        END IF;
    END IF;

    -- 🔹 Caso: cita creada por admin → notificación a Admin y Asistente
    IF p_event_name = 'appointment_request_admin' THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role IN (1,2);
    END IF;

    -- 🔹 Caso: cita creada → notificación al paciente
    IF p_event_name = 'appointment_request_patient' THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')) IS NOT NULL THEN
            INSERT INTO notifications (id_event, id_user, title, message)
            VALUES (
                v_id_event,
                JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')),
                v_title,
                v_message
            );
        END IF;
    END IF;

    -- 🔹 Caso: cita aprobada → paciente
    IF p_event_name = 'appointment_approved_patient' THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')) IS NOT NULL THEN
            INSERT INTO notifications (id_event, id_user, title, message)
            VALUES (
                v_id_event,
                JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')),
                v_title,
                v_message
            );
        END IF;
    END IF;

    -- 🔹 Caso: cita aprobada → admin y asistente
    IF p_event_name = 'appointment_approved_admin' THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role IN (1,2);
    END IF;

    -- 🔹 Caso: cita aprobada → doctor
    IF p_event_name = 'appointment_approved_doctor' THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM doc d
        INNER JOIN users u ON u.id_user = d.id_user
        WHERE d.id = JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_doc'));
    END IF;

    -- 🔹 Caso: cita denegada → paciente
    IF p_event_name = 'appointment_denied_patient' THEN
        IF JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')) IS NOT NULL THEN
            INSERT INTO notifications (id_event, id_user, title, message)
            VALUES (
                v_id_event,
                JSON_UNQUOTE(JSON_EXTRACT(p_json_data, '$.id_patient_user')),
                v_title,
                v_message
            );
        END IF;
    END IF;

    -- 🔹 Caso: cita denegada → admin y asistente
    IF p_event_name = 'appointment_denied_admin' THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role IN (1,2);
    END IF;

    -- 🔹 Otros eventos (ejemplo para citas, etc.)
    IF p_event_name IN ('appointment_request_created', 'appointment_cancelled', 'appointment_rescheduled') THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role = 2;
    END IF;

    -- 🔹 Siempre: Admin recibe TODO (pero evitar duplicados si ya tiene notificación dedicada)
    IF p_event_name NOT IN (
        'doctor_welcome',
        'appointment_denied_patient',
        'appointment_approved_patient',
        'appointment_request_patient',
        'appointment_request_admin',
        'appointment_approved_admin',
        'appointment_denied_admin',
        'appointment_approved_doctor'
    ) THEN
        INSERT INTO notifications (id_event, id_user, title, message)
        SELECT v_id_event, u.id_user, v_title, v_message
        FROM users u
        WHERE u.id_role = 1;
    END IF;

END$$

DELIMITER ;

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
-- Estructura de tabla para la tabla `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `level` enum('info','warning','error') NOT NULL DEFAULT 'info',
  `type` varchar(50) NOT NULL,
  `action` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `appointments`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `appointments` (
`id_appointment` int(11)
,`id_patient` int(11)
,`motive` varchar(255)
,`description` varchar(255)
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
  `requested_date` datetime DEFAULT NULL,
  `approved_date` datetime DEFAULT NULL,
  `admin_response` text DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `historial` varchar(255) NOT NULL,
  `symptoms` text DEFAULT NULL,
  `preliminary_diagnosis` text DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `payment_method` enum('Cash','Card','Insurance','Other') DEFAULT 'Cash',
  `insurance_provider` varchar(100) DEFAULT NULL,
  `policy_number` varchar(50) DEFAULT NULL,
  `reminder_preference` enum('Email','SMS','Phone') DEFAULT 'Email',
  `follow_up_required` tinyint(1) DEFAULT 0,
  `follow_up_date` date DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `nex_appointment_date` date NOT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_doc` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'Paciente que solicita la cita',
  `updated_by` int(11) DEFAULT NULL COMMENT 'Admin que atiende la solicitud',
  `id_status_appointment` int(11) NOT NULL,
  `id_appointment_type` int(11) DEFAULT NULL,
  `priority` enum('Low','Medium','High') DEFAULT 'Low',
  `duration_minutes` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointment_new`
--

INSERT INTO `appointment_new` (`id_appointment`, `id_patient`, `motive`, `requested_date`, `approved_date`, `admin_response`, `description`, `historial`, `symptoms`, `preliminary_diagnosis`, `doctor_notes`, `payment_method`, `insurance_provider`, `policy_number`, `reminder_preference`, `follow_up_required`, `follow_up_date`, `appointment_date`, `nex_appointment_date`, `register_date`, `update_date`, `id_user`, `id_doc`, `created_by`, `updated_by`, `id_status_appointment`, `id_appointment_type`, `priority`, `duration_minutes`) VALUES
(1, 9, 'General Check-up', '2025-09-30 12:00:00', '2025-08-28 20:24:57', 'Aprove', 'Headache Consultation', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-09-30', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(2, 9, 'Headache Consultation', '2025-08-30 12:00:00', '2025-08-28 20:35:44', 'Approve Appointment', 'I have been experiencing frequent headaches for the past two weeks. I would like to see a doctor for further evaluation.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-08-30', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 00:00:00', 0, 6, 11, 1, 1, NULL, 'Low', NULL),
(3, 9, 'Follow-up Appointment', '2025-11-19 14:00:00', '2025-08-28 20:50:06', 'Approve', 'Follow-up visit after the treatment prescribed last month. I need to discuss lab results and next steps.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-11-19', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(4, 9, 'Pediatric Appointment', '2025-11-28 12:10:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Appointment request for my 6-year-old child due to recurring cough and fever symptoms.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 05:30:43', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(5, 9, 'Headache Consultation', '2025-10-31 08:00:00', '2025-08-28 21:47:24', 'Approve', 'Headache Consultation', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-10-31', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(6, 9, 'General Check-up', '2025-10-01 09:00:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'I would like to schedule a routine health check-up to review my blood pressure and cholesterol levels.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 20:41:40', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(7, 9, 'Follow-up Consultation', '2025-11-12 17:00:00', '2025-08-29 12:42:42', 'Approve', 'Follow-up appointment after last month’s treatment to review recovery progress.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-11-12', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(8, 9, 'Follow-up Consultation', '2025-11-18 16:35:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Follow-up appointment after last month’s treatment to review recovery progress.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 20:43:05', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(9, 9, 'Follow-up Consultation', '2025-10-29 15:40:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Follow-up appointment after last month’s treatment to review recovery progress.', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-29 00:00:00', '2025-08-29 20:43:23', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(10, 9, 'Grado pain', '2025-09-30 12:47:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-29 00:00:00', '2025-08-30 20:08:21', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(11, 9, 'Test', '2025-09-23 19:42:00', '2025-08-30 09:25:14', 'QA', 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-09-23', '0000-00-00', '2025-08-30 00:00:00', '2025-08-30 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(12, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Phone', 1, NULL, '2025-12-25', '0000-00-00', '2025-08-30 20:01:44', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 2, 'Low', NULL),
(13, 9, 'Heart pain', '2025-10-31 13:01:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-30 00:00:00', '2025-08-31 04:08:33', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(14, 9, 'Headache', '2025-09-30 20:55:00', '2025-08-30 21:03:00', 'Test', 'A lot of pain in the front\'s head part', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2025-09-30', '0000-00-00', '2025-08-31 00:00:00', '2025-08-31 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(15, 9, 'Test', '2026-02-10 04:00:00', '2025-08-30 21:17:16', 'Approve', 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '2026-02-10', '0000-00-00', '2025-08-31 00:00:00', '2025-08-31 00:00:00', 0, 12, 11, 1, 1, NULL, 'Low', NULL),
(16, 9, 'Headache', '2025-09-02 21:20:00', NULL, 'For capacity reasons, your appointment request has been denied by the administration.', 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-31 00:00:00', '2025-08-31 05:21:59', 0, NULL, 11, 1, 7, NULL, 'Low', NULL),
(17, 9, 'Headache', '2025-11-03 21:25:00', NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-31 00:00:00', '2025-08-31 00:00:00', 0, NULL, 11, NULL, 2, NULL, 'Low', NULL),
(18, 9, 'Headache', '2027-09-02 10:04:00', NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 0, NULL, '0000-00-00', '0000-00-00', '2025-08-31 00:00:00', '2025-08-31 00:00:00', 0, NULL, 11, NULL, 2, NULL, 'Low', NULL),
(19, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 1, NULL, '2025-09-10', '0000-00-00', '2025-08-31 18:28:16', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 1, 'Medium', NULL),
(20, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Phone', 1, NULL, '2025-10-15', '0000-00-00', '2025-08-31 18:38:50', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 2, 'Medium', NULL),
(21, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Phone', 1, NULL, '2025-08-23', '0000-00-00', '2025-08-31 18:48:27', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 4, 'Medium', NULL),
(22, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2026-01-24', '0000-00-00', '2025-08-31 18:49:57', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 4, 'Medium', NULL),
(23, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2026-01-24', '0000-00-00', '2025-08-31 18:56:00', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 4, 'Medium', NULL),
(24, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2025-12-12', '0000-00-00', '2025-08-31 19:17:37', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 1, 'Medium', NULL),
(25, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2025-08-30', '0000-00-00', '2025-08-31 19:24:14', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 1, 'Medium', NULL),
(26, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2025-10-17', '0000-00-00', '2025-08-31 19:32:26', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 1, 'Medium', NULL),
(27, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2025-08-15', '0000-00-00', '2025-08-31 19:36:05', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 6, 'High', NULL),
(28, 9, 'QA', NULL, NULL, NULL, 'QA', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'SMS', 1, NULL, '2025-10-16', '0000-00-00', '2025-08-31 19:43:52', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 1, 'Medium', NULL),
(29, 9, 'Test', NULL, NULL, NULL, 'Test', '', NULL, NULL, NULL, 'Cash', NULL, NULL, 'Email', 1, NULL, '2025-08-23', '0000-00-00', '2025-09-01 04:34:24', '0000-00-00 00:00:00', 1, 12, NULL, NULL, 1, 3, 'Low', NULL);

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
(1, 'Scheduled'),
(2, 'Pending Confirmation'),
(3, 'Awaiting Payment'),
(4, 'Current'),
(5, 'Rescheduled'),
(6, 'Completed'),
(7, 'Cancelled'),
(8, 'Cancelled by patients'),
(9, 'Incompleted'),
(10, 'Closed');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_types`
--

CREATE TABLE `appointment_types` (
  `id_appointment_type` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointment_types`
--

INSERT INTO `appointment_types` (`id_appointment_type`, `name`, `description`, `status`) VALUES
(1, 'General Consultation', 'Routine medical appointment for general health concerns', 1),
(2, 'Follow-up Visit', 'Follow-up appointment to review test results or treatment progress', 1),
(3, 'Emergency', 'Urgent medical care needed immediately', 1),
(4, 'Preventive Check-up', 'Regular health check-up or screening', 1),
(5, 'Telemedicine', 'Online or remote consultation via video call', 1),
(6, 'Specialist Consultation', 'Appointment with a specific medical specialist', 1);

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
  `id_marital_status` int(11) DEFAULT NULL,
  `photo` longblob DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `id_document_type` int(11) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `workplace` varchar(255) DEFAULT NULL,
  `id_blood_type` int(11) NOT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `photo` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
,`photo` longblob
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
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id_notification` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification_events`
--

CREATE TABLE `notification_events` (
  `id_event` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `title_template` varchar(150) NOT NULL,
  `message_template` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notification_events`
--

INSERT INTO `notification_events` (`id_event`, `event_name`, `title_template`, `message_template`) VALUES
(1, 'patient_registered_self', 'You registered in My ClinicSystem', 'Welcome {patient_name}, you can now access your account.'),
(2, 'patient_registered_admin', 'New patient registered', 'A new patient {patient_name} has been added by Admin/Assistant.'),
(3, 'doctor_registered', 'New doctor registered', 'A new doctor {doctor_name} has been registered in the system.'),
(4, 'doctor_welcome', 'Welcome to My ClinicSystem', 'Welcome Dr. {doctor_name}, your profile is now active.'),
(5, 'appointment_request_created', 'New appointment request', 'Patient {patient_name} has requested an appointment for {appointment_date}.'),
(6, 'appointment_approved', 'Appointment approved', 'Your appointment for {appointment_date} with Dr. {doctor_name} has been approved.'),
(7, 'appointment_denied', 'Appointment denied', 'Your appointment request for {appointment_date} has been denied.'),
(8, 'appointment_rescheduled', 'Appointment rescheduled', 'Appointment for patient {patient_name} was rescheduled to {appointment_date}.'),
(9, 'appointment_cancelled', 'Appointment cancelled', 'Appointment for patient {patient_name} has been cancelled.'),
(10, 'prescription_created', 'New prescription', 'A prescription has been created for patient {patient_name}.'),
(11, 'appointment_reminder', 'Appointment reminder', 'Your appointment scheduled on {appointment_date} with Dr. {doctor_name} is coming soon. If you cannot attend, please contact the clinic before the appointment date.'),
(12, 'appointment_request_patient', 'New appointment scheduled', 'You have an appointment scheduled on {appointment_date} with Dr. {doctor_name}, specialty {doctor_specialty}, assigned by Admin.'),
(13, 'appointment_request_admin', 'New appointment created', 'You have created a new appointment for patient {patient_name} with Dr. {doctor_name} on {appointment_date}.'),
(14, 'appointment_approved_patient', 'Your appointment has been approved', 'Dear {patient_name}, your appointment request has been approved for {appointment_date} with Dr. {doctor_name} ({doctor_specialty}). We look forward to seeing you soon.'),
(15, 'appointment_approved_admin', 'Appointment approved', 'You approved the appointment request of {patient_name} for {appointment_date}.'),
(16, 'appointment_approved_doctor', 'New appointment assigned', 'You have a new appointment created by Admin with patient {patient_name} on {appointment_date}.'),
(17, 'appointment_denied_patient', 'Your appointment request has been denied', 'Dear {patient_name}, your appointment request for {appointment_date} has been denied by the administration. {admin_response}'),
(18, 'appointment_denied_admin', 'Appointment denied', 'You denied the appointment request of {patient_name} scheduled for {appointment_date}.'),
(19, 'appointment_created_admin', 'New appointment created', 'You have created a new appointment for patient {patient_name} with Dr. {doctor_name} on {appointment_date}.'),
(20, 'appointment_created_patient', 'New appointment scheduled', 'You have an appointment scheduled on {appointment_date} with Dr. {doctor_name}, specialty: {doctor_specialty}, assigned by Admin.'),
(21, 'appointment_created_doctor', 'New appointment assigned', 'You have a new appointment created by Admin with patient {patient_name} on {appointment_date}.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification_event_roles`
--

CREATE TABLE `notification_event_roles` (
  `id_event_role` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notification_event_roles`
--

INSERT INTO `notification_event_roles` (`id_event_role`, `id_event`, `id_role`) VALUES
(1, 19, 1),
(2, 19, 2),
(4, 20, 4),
(5, 21, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notification_roles`
--

CREATE TABLE `notification_roles` (
  `id_event` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notification_roles`
--

INSERT INTO `notification_roles` (`id_event`, `id_role`) VALUES
(1, 1),
(1, 2),
(1, 4),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 3),
(5, 1),
(5, 2),
(5, 4),
(6, 1),
(6, 3),
(6, 4),
(7, 1),
(7, 4),
(8, 1),
(8, 3),
(8, 4),
(9, 1),
(9, 3),
(9, 4),
(10, 1),
(10, 3),
(10, 4),
(11, 1),
(11, 4),
(12, 4),
(13, 1),
(13, 2),
(14, 4),
(15, 1),
(15, 2),
(16, 3),
(17, 4),
(18, 1),
(18, 2);

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
(3, 'Deleted'),
(2, 'Inactive');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_role` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_role`, `role_name`) VALUES
(1, 'admin'),
(2, 'assistant'),
(3, 'doctor'),
(4, 'Patients');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `full_names` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `id_role` int(11) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `photo` longblob DEFAULT NULL,
  `register_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `login_session_key` varchar(255) DEFAULT NULL,
  `email_status` varchar(255) DEFAULT NULL,
  `password_expire_date` datetime DEFAULT '2021-07-27 00:00:00',
  `password_reset_key` varchar(255) DEFAULT NULL,
  `cel` varchar(255) NOT NULL,
  `created_by` int(11) DEFAULT NULL COMMENT 'ID del usuario que creó el registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id_user`, `full_names`, `rol`, `id_role`, `user_name`, `password`, `email`, `photo`, `register_date`, `update_date`, `login_session_key`, `email_status`, `password_expire_date`, `password_reset_key`, `cel`, `created_by`) VALUES
(1, 'Juan Perez', 'Admin', 1, 'juanp10', '$2y$10$lsH0i8jgF5Vauv27XhKgR.FpCkjreUdxzqg2qAbrGOUbX2Izh.fLW', 'mc2056951@gmail.com', 0x89504e470d0a1a0a0000000d494844520000012c0000012c0806000000797d8e750000200049444154789cecbd7bb4a5575527fa9b7bef73ea54a552a9542a21094599048c6548420821c40819189a484745f46a7c205c69915b47e1dec66e0d7627c3cbc8b191eed1ede881dd61d82a6dd38ea1f41db7fbb6cabdb71d72bb6d0544b4056909d83c82010324a42acf7a9c7de6fd63ad39e76fae6f7dfb9cbc1f9c95d4d97b7fdf7acc35d75cbff99b6badfd6dc176da4e35cdd7566700f62970b640cf82ca5e08f6a8ea1e113903c07e55ec17c13e55dd2b8215852c0bb00c6005e5d5fe01c009404f007202d063aa7242444fa8e298881c01f0350077017a17207703b8b7fe3b02c55720b85381afcd6eba75fd89d7c6767a2a2679b205d84e4f5c9aafad4ea0d803e83e88ec077000c07900ce87ea050a1c1091dd007601ba026045556622980180421126a3281f05a2744f00a842208028546b09fba30a48989d426b8d0200eb00d6a13806c131051e14e07e55bd43443e0be073003e0fe00e0077a100debdd39b6edd78dc94b69d9e52691bb09e8169beb60a04d3d90be821402e85e2050a3d00c8b9021c80603700300ea9bdafa0538008900a327ebb6616915aa68294e551400dbf6aa952ad9589b2865f090e33ae25d944f57e88dca1aa5f12913b00fd18201f07701b80230a9c10e0c4f4a65b1f339d6ea7a746da06ac6748aa20b55715970b702980174170a52a2e80600604a054b471d65300a62284362caa64a900420c49a9f10a705e17a80a2854a501a59acb90ad5e4995d6320c762c13e16a792db2ad5726f61185fe29201f17e0cf001cd906af6746da06aca7695a5f5b9d097010c02100df02e855805ca4d03d80ec82eac4984c4902c61b68030612ec078097730371c46168c9409459199996c01a4406c30618a93ec7349297f95d405be281f5bd40041b503c08c1bd00fe52553f2c820f29e43601be30dd5e177b5aa66dc07a9aa4f9daea4ca17b047236806b157885400f41719e0a566c3d29814665428135c1a202980006020fd750423abf5e11a284794d7858f3c300b261620e285c9f07960184a50ba54c69a782a3d6552e625c247c84a59cc7ca404b5da5c2630a7c1ed0dba0f2fb807e00c09d2272ef36803d3dd236603dc5d3fadaea2e815eabc02b05729502970a74c5422a4d6b474a919e81071c171cc82cbc0301008398018adfaa4c4cda36ca9fc2ccea7b0af72cbfaf67058ec4823d01108779069edc2fcb9fa3d65ee8683da070d311cd5e04008e01f838800f03f83d053e30bbe9d6071fe5906da7c7316d03d6532cd5b5a8b3015c0de0bb005c0fe83e05663edb69f29768cb188cb19758db69c32aa5956c5102095b20afe0a3105fd70ad2a491d9e4802d5f05440059000fe2a8b1149ea27d6f0059d6bf40b225e045dda174fa671d8b360695b7e16aacb1adabead720783f20bf2dd00f0272e7f6dad7532b6d03d65320cdd75627aa7ab6885c06e07b14b85a80f314ba4b2cd4a314f34f63311ccdbc2cb763fd08b00fbcd6dd1c3b884269517b6cae0f05ca9861acad59ae6a81d619a1956bcb7b3395c94505deffd2278a0b1bd932e76aeab6b0589c8d3d08e0f322f82020ff5e813f87e2ced9cddbc7279eecb40d584f525a5f3b0c01660a5c28c06b01b95e151703983118c50cb6f52058c4e5e79f7c715d24f049da095f81c8cf48d9554de0e50ca6397e405574a95184912d6a36b829f94e0add88090ef259df21745c825be0c638242dc9cf8a0931becad05a5d85540a81ac03f884aabe1f82df10e0d30aaccf6e7af7f8e06ea7c72d6d03d69390e66bab6743719d425f0be05a1199d9ccd21aeb89c4ac75f0e8ac373123024d7f78f8045f4052ad935514a2c24425800f8850107d030986c6eb4fbcb645c055f3a933453e28da324463648e9c293c74c0ada81de09be508cc2376563594efc5b155d0bbd44e761aeb0a7c4080df00f09fa637dd7ae7f8286fa7c7236d03d61394e66babbb005c0ce07500ae07702e54577cd74db5ae2f99e73708029d7f824ff0bc982d4621a0ccb260f723547260ab00a2046c8a0c28015eb5132de3abd7fc9041bd67519bb31c674e153861d9634732011f5510b82c04e6c0908ea9afab0750f3dd10487c4732fa69f5e5c0343ac54c0d22c7007c49a1ef17c87b017c62babd58ff84a46dc07a1c535d40df03e02a003fae8a9743743f0394872420a2a0c18222744302a200114d470d2cb7ef1d12739358594f219d831c4047144c8e3a89ed7d9acf0c1af00572585f2ca58e110f6c42cefc351de27704e662a1636a5b067d0a76877c0d86f25694db19e66716671c8d55a8aa7741e43f03f857027c58817b67db0bf58f5bda06acc729adafadee16e0350afd31815c03601264c563a738a98d76770e341725e65f05280f0d893914d625f9e800866193310f93c3d79e506592606fb12664c01213de77fe6ac866b253f4e749f94c580a4135d890105c595468cc12c1c88242595e6262c8075745430cf186429e5e3dbc762855ff96494279a1e7727303c01f08f0abaaf80fb39b6fbd7fc434b6d3a348db80f518a6f9daea44818302bd01901f03709e2a960d2c020462bd080e32360beac4a4f355cc0ed25e179105d85d261c80b3afb817e193e39e8324afdd889f9d0210339f26faf07c169d9a77497bfdaa99eaf5d407da593450abd90240823fc6897c893a8720181b021a8849b8c7e5e8c844a32706efa42b041353e80911f982aafe2b88bc4fa05f98def4eeedddc5c7286d03d663906ae8771005a8dea0aa8704980070c3e689ed4c41108bdfb666d5008c222fc0a72304c4c462b11a14e2d9846b01b0deab804427c17db217369717e63916caa7db0108970bb614172262cb2159cdd40957a349eaa7e9a6011eb0ae2ab070f42a24bf87b930b02e19029c18f1639c03ef83c5fac9b3da6789107b03e5cbd8ef01f47d807c61fb4cd7a34fdb80f528d2fa2dab90f2c483d703780ba0170232494f27006ae866b61f0722a5a28587833c6961f3a5feadf725cd7c621b0424d646849cfcd518c076219501a0868d31b9797d29efbd3943e4572b5f515094ce9937f37f7036abf6844199f311b4e47e1386c7b18f26b7ebd30019e003b14053a632c760ad06f88870d74ad83581cb96d7e460c0f56900ef52e0df08f4fee9f69188479cb601eb11a4f9da6100b25f811f81ea5b20b820267e22158948d8a2757b8ea8376fdba714e4efe845dec020a319b49e842c83b51567b602d44cb032c90db802ac8476f73a0804838838820197c743e2a82df543e9f370c714542fa04d6f3c6c4b9b082163ac93d12badd7f9697a984edac1b06a9de2a524e91d1f640dd969343fabd07709e4df02b86b9b713dfcb40d580f33adafadee15e05580dea82a178960b99d98697d88d66472480500748f92873cbd9d2a0fff808c7eea21494547d8244ce79b683297d49f81b16c66348419953127939d585477272ef76ed4ec1a1d11a1331172718ef57897d0c3b41119da58312f04766518e88bea1b9cb05890147a42207f09e09d00fe9fe94db71e595c623b71da06ac2da6f9daea4c55af00e44601ae8360178081916a0318051f72b890e696571115a539c921460f30ec3a22a4e1d42e500f4fbe07307948c4e1171dbf48eb60de2eaf73e94086b6af4936def1a3be39b36a40b25ccc32b4f539bbd3a8c381bf5eb731c9402eb9df8df0dad4d7ea8ac3630f4f13deb1ee012d5ffff94f00de09918f6e3f067a6b691bb0b690e66bab17a0ac511d06b08276d27268449383e1c78e1cb8f74fb8310481b42bc7b52506604081115488c394329890bcf99f8a580113887a612cd23aa09e25d7afc37ed58cbc9b676b55265f1222adf6337be36cc4ecaa325a60e0b62d84f4feb4f9da4d8c4160cef2218f7787e0f9b70b6a3f9c8c663b3906c5bb21faaed94deffe2cb6d3c2b40d582369beb60aa8ee579137025815e08042274561cd81c791f75b49252ab145713aa459eff7eab22957d67b82f9f0d30c06654698ce98bcbdef12deb7aef8f4031bb86faeb8fda10d7cecde0d2c4d801d13c1ca04589908f62e010757269809301360e754b06f49f09c15c16cc2e16da337063b07347a4ba1b563c5089ce43ec03733e20c9b41171d445da49b147993be927c9951e53e0d6dc4d6cbeadb0d85de01955b45f02bd85edf1a4ddb80d549f35b569721b8128ab743708d96b90700e42537012aad13aa02489e14993185fd9799315c0fb1f08d439ef1502b644895f770a0b7ab8575551c39a978680edc7552f1fb77ade3af8f298eae17a07a600e6c1058f079246e7326c0ca04386d26387b87e0b425c10b4f9de25b4e9f625980d396043ba7329025eb95990bf78798ec88ae000b2bcb1d26c283ba7b63d228ba0334031d0e14dcea78f1fb75007f00e0e7007c647ad3ad27b09d52da06ac26cdd756f7027aa32ade2422fbba7193cd123a01cdeb4a8389e0ac2003434caebc26d536e6d040a1c6a01c2fd0536cc20be169c1de6b8efbc73780ffefee393e7a748eff76ef1c5f3aae58d738fe40c8126b55b4189dcf471925b1be5ba3a5dc9e1970f1a9133c6fd714dfb66f8ae79f3a6df43bce0adbb1707d8e7dcdc6c51e679396f206493356cdfa5f7e3be2848851f6f2a63a1c7df135057e198a77ce6e7ef7f6a23ca56dc0aa69beb6ba0ce875aaf8a722f23cd4839ffe1402a941180103875f61f2150478b5849e8a99ce3d01a3b352c9cdf39109072a89c93078ea28406de6508627a002b8f3b8e2bf1dddc007beb68e8f1c99e3d806f56524244be15c67727bc8c69b0106ced455c3972980d39680579fb584579c31c5379d32453c103095aeb2884ffcac7f8d2f82bbae86471b388c4c7a453b21ccd9f43633588f0c5ec3faf266499555725be9d80a0011d900f03f00fc3d9427436cb32d6c031600607ecbea0510fc34801f04742fab459b49d14e7ed83502aaee3660cdd88bd26c672c030ab5ad762ea9097f7aec2fdd6a33283614f8d271c5a7eedfc07ffcca3a6e7f68037f73bc5c6f7701c9e3536d2de3e2d5a75857b38ed9a9fbfe73b586f29fb124b86ccf043f70ce125e70ea049366e13e478711fef57e866cd06ed2447b1095eb8e10773094893df519e08261c9328ed4d530b42302f94d88fe93e9f6a2fcd73760d55f3abe1ad07701b8189049fb45da9294fe0efd7302ac4a2f7c7233d732d71a946de86973098a449aafc3b844eeb3d11eb0e4c7076f2870d709c5fbee5cc707ee5ec71dc734dad384a9d0f4173e7b7892b587305d46e29a8999a29de00a7efc7130b4f277c714f8de672de175cf9ee18c2581ed50b6c72a7c4488d179688c4626897ef1370c8cf22885f9ee8ea85fa50fa16f5e6c140a9363541a46ad4d3985fd3c59bd9cfb464e7103c02704780b800f7e3dff60c6d72d60cdd756f729f4ad80fc94a09ca9e210a605124ef93c95c548f5a6cf4c809f6ae0dfd5a3782bd636b221f38e5399f4049069517ed8760ed5cafdc2a64ee23f7c791d27d4c21cf8f6babd37b161d5dbe976956602260600038c4dd38210134ae84dc876c692e0f0c125bce6594b5de693bea799267e6a2ceade8a98a6070257d773c32aa34cbb514079a83f5bdf45a6a0d38beb83aaf2cf44f08bd39b6efddad67af3cc4a5f7780355f3b3c81ca1510bc13c0d5505d6eacb324ad66d933fedee22b82adf416bf4beac44123b1407ba8318996c2abbe3c73557cfcde0dbcefce93f893a3731c5de7f21970da50b49771188ef5e5191c90b45eb7d79941b4a023f6959b32e157a6c00de72ce18d0796b0733aec2beb6af06cb0e40c307a7dc8183b53633056807921de84502a3fdc41dc4a4c99af3373047002d00f6a39c0fcd1e94d5f5fcf99ffba02acfad4cf1b00dca2d0034efe2d8c721bee851dc642b4836f8d01126be028d00904ca1f37425e702566e6557ae6682bae332b13cc55f1d90737f0de2f9dc47fb97b8e0737186c11a1ab95b3a0a596a725643825645603c0bfe40c25b12da4b2794513b686734919ae70696432c5671d4e44f003e72ce1270e56d0aae0e40cd45923d278b6679f402ccd1d0953461f9fd2597e248fe5e33098485773afe30488c26acdcf2ba1bc1cc1b2279544187c0704372bf0beafa79f26fbba01acf9daea3e00b700782380e572b50cfea8718cd465e7a56cc286b135bb51da844a0660804f82bc031875952c568fdd29e5c6645355fcf25f9fc4bfbbf3248e9e549a87d2ed90811eef82f9ce63883368abcba82c6a3234aea57cae374f50cd6d6902b9aae41c78d5a23ffaec25ac1e5c62c641119bfa7b7e6c4eec0c8e31c6dc7e725296b7e730046819d9e08804b56ead86fee873ed60013225a034b0457c532206eb840a7e45546e9edefcf511223ee3016bbeb63a41f939f7f700b8028a4978b961d83604afe14c1f0b1fd22e99a3ccd03b1b898ae0c56617d17f662229a4342122f498abe243f7ccf1cb7f7d12b73db0613dc933cd0080d81ecd10a709b658ef67bd2c2b873dcc0a00fad18ad88240179c406d65f58e83304921e520eacf5cb08cef3c7396416424bcca0cb56feea3eb4a2d63eedd1b9489bc4386c517c65d621302f6728018e206201f85e20d10dcf64c0f119fd18055ce56e1550a7d2720872212199e5f32031cec34f9ea37d28f20f81777cb9d081f006447c82045a7a578b709b4cbe5e1cd709daab7f3f595138a5fbbe3247eefae75dcb70ea216dc4fc4fa7cc38e384c6a4328678ac454e25a00737eb24491414c6c62481411e529d71207eb46bd1875956312e7ec10fce6653b71ca042e7f5e4392b68590db349efc88d5114c28ae033c4eeed87cf4f9c06c804dfce51d5df8b2028f794827499fdc463840ef416b1bb789c88d284f8078c69ed97a4602d67c6d150a9d09e4a700dc08605fb13b66377036c48423250a4b12a718f57cf090a8fff50b9e94439636004136602b4580f9e1a373fce2e74ee0730fe960ad68f05039c4b46bdbb3891eac0b834918a0d251123af9ec0e816e0207b110073e55039403e41848e219f3c0cf9cbf8cef3f7b86c8187ae7c3a0aef704b7d941f06b721ede87500fc360ecac6a90559268d80ed98f328cc1fbef5a65e74a6575d002b783af49d94cfa6750ac4f6f7ee67d1f71f2640bf038a5fd527e7ee91da86025a893a57aec74c0d1fe419349b8132bc52042568b9a9fde9b2dc6fa449ee01c4ed9a41411f7ead686d591eaf2c9a458df00def3c593f8fb9f3c8ecf3da45eb6d42e6ef84ae52092eab4751b2711c68e8c5179b31a3212e3e08913eb6c940f590fde18907f7d47bc9a04743c0665dc6c0ccbebff71e7497ce584529fa85f5e711c1131dd7937087ce33530c3db946a2db571f1c6684c09cced16af0b72fd74b1e6a13ad9f4b8ac4659418ca365a42af7a1d8fc7b21d88f67607ac601d6fc96c31701f875056e8062526661fda70dc09023f7d8c49fb2e917bdbcaa947f202a2f4acc282c4e95bca8c25fb5ba62adccc15909a48689c10ad4e58d36ef3e01fca3cf1ec7affcf5499c303c82781bd6170765035aea4a301f0935b8e8e27950f3f9d793a0484f0f25066a951b938d7ec4441425b553fc5780461c381389b3f209c580cf3fa4b8eb04ebdb589054bd454865729a8ee3ba8d1ddc27d867ad6deb402fb54cebb42ae0ab79371a77346dd97b034e12a1e6d12c9bab37981c45f3ee686a9909143728f4d7d7d7562fc2332cc9e6599e1ea93e5ffd7285de2a902bdbfb3185eae73ae2064f32a60a2f68064f0bcb1cee88a41f2cb5b286831235446b0af83393ac3d12239a2ed3edf6638a777ee6383e7a743e08b772193adc49e1d730a02071edbee5b5230a5667ea937a9932813a2127d7ef0aa051905639dc87d8d1e330aaaaabe651fce4372ce30d0796d1834d216576c33b627fc3d167c08df60729199552546d8d14198adc8adc481c7e8d766aa54e6f9133803f475ed7cb50c48f005855e89fcd9e21cf917f4630acf5b5c313115c0be0b70b58657e049031d4ab2261a4cd99e55c79f3c03a0feb2a55e7838e29a6a845c4270f1d5d30d6676c45b40044a63ab5e902381f39ba81b7fcf763f8e8d10d586862ded59889872cde8cc9163183c05893e980d919514e632b52af935a2cc4b2aeb4e18ab745f2a4f539279fe297d4189f8f0be9dbc2a2681600f091a3731fb2185f036c62765687ddf5f0b7f2506633bdd403ab04ff61531c9f817a9259970c2cd4b39b5d81ea32e04ab61568979d45eac795007e5b806bd7cb6ef9d33e3ded3bb1beb6ba2c2a7f07c06f01381780875d4cdb9dba23330dcec11c88f941ac6cd92e58353ea185e9fab7ad3b602af25938e680937e91d3673320e53b801fbc678e9ffdd431dc79a2d6ef0bf564faca329b7de789c6ac252ed6f231bf9a32042a0d6bb07614419ed2ee2675c93704240a1a49a895a7fc037412a00d918ecde1e331d463e4337ea500ee5d57fccdf10d7ceea10ddcfed006ee5fcf27d3834947ff2c59886eaff6c89c003f501df62ec23b6376365622717fa82fe43ad8aef25037729a5e3dc3b980fc96007fa7ee9a3fad53cf753c6dd27cedf0b22ade2c22372b74af6fbd4b4c200700220ff64ea139fc71634278e1dea35bca079865f1978c396fdeaca67212a7cb93508dcc1b00fee4e81cfffb5f1dc7dd27d5c3b6389241e06c32dbfdd40f3a8e80a69fd46c8813473ef864bffd34999f72a77c1cc10c76c51072356a482e227635e163e8f706212170c9a913fcda252bc988e700ee3ea178600efc8f0737f02747e7f8ea09c5f10dc55c81631bc04373c5b10d602ae5e1823b27a5bd5d53c165a74e71f5e953ac4c803396053b266c4b20721de7d55c2e190ce440cf29752fc68dc19881f5097a522d6067578ca43b76c7af281d11e016407f697ad3bb9fb6c71e9eb680b57ecbeab2087e02e5d74796d3f10100d0ece97b87030706009e2c31e900daf67623930046b3e26a29b14ec2edb4edb20434e909003e72741d3ff7572770d7491d941c549aea0f00f68cf6626b70c686c823b3fedcdef93a03515556ef37f9b80dabd0fb48bfc5c8f04e5cd1f3d9223afd0a6a82bd8b774ff06b97ae4020b8edfe39fef09e39be725cf1c747e7b8f3b862ce2aaa9398011a5c63a00e00e0f425e08a3d53bc72ff0c2f3fa31c9f609d00a0f1af009316a41a27d77ceb21eb383087bc062165e352ea7a69b0dc22489c458b216fe43e51cf6afdcba7eb59ada72560adaf1dde2dc0cf02f2360526656c0960981df85ccdcca77c2054c1e8a541b270c0eae5f599f891d4b0b77652b692f08ea3d5ffe12373dcf4e9e3b8770e4360671a59961160b68d00ba0e2eef13b80208cd9ef43d388e8d9c51881fec1c9e3763502dca642c48876333a6d5b6111af2b1b036f92c1770ee8ae0a5a74ff1bb5f5dc743f3f20c16d6036080178da8b31019b77eea8200387787e0b5e72ee1e5fba6d8bf3c09d6dd86c8acbb44133388c70e6180581b62f2f9b0ac771d7c8732795a83481f36f29ec5ce3620f80585be6376d3bbef1fd1c053363ded006bbeb6ba47a16f17e0305456b2570a86d3a720c468fc763e38c85fb371af56e9be37d5b20ed4b0b21a716263eeea94ea60266660055f24ffd8bd73fcf46dc770e4245c96649c6d7f3aec3164837bde1eb8014310cc981340145deaef4cf1f18e041456293d29d47646f9c771b44ebc41d92e5d0de6c2e4232360f379c0606043168c110d13aa37a7009ebb6b82377fc3125eb2778aa9834f51447aa698d05110c90eadaa8fb442c09a44d7e691445a9d5f13ce0b956ee4f7ef20ba03f083b5c700bc5b809f9bdef4ee7bf1344a4f2bc0aa4f5b782780370158569f049a062559e360de683c344d84a38d30941eb5ef805a54399cf8ed7c69eba805f27d00b73fb481bf7fdb317cfe41f53e0cc4d13edbea3246ebde087374006e98c8669631f67db7b61daecad7b46cc11de63e904fbd1338a7e1b432c4ba9af8ce8920d85f90de46854394ab82f18bdfdb3d037eecc032beefec19764e692c29b576208bf485beda479d89df932a1faff10d4192711e19c04e00f86500374e9f464f7b78da00d67c6d750f809f07f4cdc678380d1675b5c34aaa11c42f240f81278543a9feceb51166634db573c3de0d169febdfbb4f286efcd4317cecbe8d6e3dceeceb87b49e8466728c802b7be11e88f500ae37d96239af69832b0048f71910d3992f629f446839e24a0c49a99e41c4d36617507806e4df7b0c066575451c6fa1bdbb3158f80c01be75ef147feffc653c67e764d489e575cf0a30d98315f0ae68288d4cde0f0ef79440cc9c2c298e6d220135e08ebad95cfa2500ff707ad3ad4f0ba6f5b438d6b0be767837a06f07f026d5623c36784ca9d9d7756144905915008612f3dc194e827e83af0fc2c75a8f1b82e5ab75ab0693f19a23649babe2976e3f81bfb86f23661e3419b8877555209f77aa49ee38c954b7fb5de40a1212ef930e346cdfdbb5f78c9aac90968218a08a5841f01a99e38188873cee586c5c84dba5eb0899d9079968dca4bf6a99a8c1d404ac543ba5164b3c12baf596e86c582dfac12373bced53c770e7f18d0c34c4b8025c2464b27c55e002f899d5db19413f001bc2a5f0dcebac7665ce763036cc68857a55debf09c0dbe76babbbf134484f79c05abf657559809f55c86100cb3198b62352bf8a81b06dd0ab0d1ac38eda80d76b067ae56b1b6570e36b14b93ee57f669f032b897337fe0d1d61a8a21755ccb53c6bfd77bfba8e0d98330e191d744c5ebaa19531f1f10603d072b6ca1133afbbd1a4680e5eb83ea2bf8d47b7de4ae8cfc2110754bf6a2d640db26e9c51b118a254425d0eaedfbe77a906c649ea7897f49edc5ad46d6d5a96e81f49adf16943159f7e40f10b9f3981bb4f6cd43292fbc075693ea157c6adeacc6c8efeb9be1d03ebb89badc570e7fac9e920e984b69d8cd9954fcbaa380ce0679f0ee7b49ed280355fb3a30bf23651acd804254707ff42ab7d06c09e18e9badd238f080ad1ea0e9268ec56b147aa85ace1f0e803eb112f9b26af538622b7ddfdc89139fee5ed271288f2ef6155a75f27766cc3db4972bb0f9755dc608d41447d741add252df7f9cc4f7435cef6304bc8e56b9d4275ab73170f535c36631cd4b7aa7e67458004d65656cc7d54abc76564cf444724aae2f804be67735d297c174fe10bd954a17febc0fa661df8a32373fcc6974efae538865141b88292efd6997e455c6fa215b228960b07abc41a4359366eaa64575aec8396b8ea38c66e3921b8b346115d81e26d0afcc47cedf0531ab49eb280b5beb6baacaa6f4659649f9875e713c99526671a95af0d9883b8e10b0108876be0eb947c2113741cc12856b9e0edb5eb4a92ea815ff9f209c5bfb8fd64f93dc064480614d9eb1766a2090482b41168560357af83f4a09a9caf3354165a038cbc4ef7f44dbbcc1ed8b32b68c928ea89b9af540729895f355f0bc2481d307ca730d77515b99ca1783607610aa7d01979024e2f46e6f5be3bd7f17fdfb51e7552cc53d3fa00002000494441540df9cc1b8bacd16f6bd541dcc02d00d79991f5c38a905e7d3e08d5c9af2e78bb5920007402e09d80bcf9a9ccb49e9280b5be767822aaaf17919b511f67cc743c3ee7752b3670a57c51940cc98d80726b184366d4d14a6218d5c863ed40780e86486e7f31d3554b58f1eb5f3c89bf7a70c39bf16fe90b60a19e870fb5add8da36598921580bd617add02751b7319b81eab8effe5fe8405b7d4994893952c3379a2b0e1e69b0d428496200de7f428f888035d6c2ec8fb11a807415e36326e1e3242ceb5057494293596c0c9bbed67a8fcd815fbbe3041e5857eacf90c959ff48e3e59fad65593f988911d8189071686f4ed3fb474b1931c66acd78cb36dea10b01a0cbaa7a3380d7cf6f39fc94c486a79c50ebb7ac42202f57c13b14bad7260fc02062ee387badb4904c5ed1cdd9c75e9df6dba357dc98d3a2a4e58eba345d3123292514c94a228fc9d118f31f1fddc0ef7c65dd279f859866a809589a456f6705565fdbacf543ec2d85a8bef0036fc7c3200334cb9f66b7b87c41b86221987562d5d9758a5a82b955244b3a87787f78c986ebedf5397425e99a8d7be21a035d919eb84e091070992bce9a2d5abb5f7848f11fbfbaeeb239d8fb615093999d2c2f3944480e1f5bd41e04a8315962b9c519598cbb22db5bee73b4edb3ab9c0ddc0be01d1079f97c6d7558ee494e4f39c012c1e500de2b90fdb40ae246e7c70e7c8295729a5d4a6790c2558bfbbbf2c97f55b9e113a5de6cdf41e5d55db0cb6646e74ca5f5a631e1ee9d03fff4b3c7eb177835c4535a5c15f394529f289049802f9e06d972e95bb6e0ecd1f233c8f87ba72c5624bc38d5431a0ddd98dc54b8948dd9edcf3f34f1aa1cb18900a8048b338724ce14032423aca5310b3f466d7067ec6db3ada08026ead6aad006226e261cd772cafedfdc7112f79cac7d35f027c769ce00a4536649f659d48026da3206c6e1787c0e3876bd28d964d52bebc95865d80eed84aaee57e0bd805e8ea7587a4a01d6fa2d872f82eaada84f5db014343f76033d99d77626050f972c94ca73970e211880a558457db0958cc277de14100db6627539c61818d805b345ab43047300bffad72771fb31ad8f95c9de342d7a4bb411e090114abc0ead93a132c7c0f378e5b094ae4b5e40abbf7213173c04950c9c36367e5ac026823345380d612d339bc99b00955d815906b1315019ff2276e8ca188ff85878c5d19e4d4e022a213d0384735aef99c05275a5e41febeb5d27157f7864dd6f88f59b426de8d0991af38206bbf471aff694802f1536008e335f2236ba0816c66087bad8c08e2a796681943978eb53ed21801daef8c4a74a3df703f87500d703e45150584d84fec66324099f81ccd6a4ca80b4072c01f360fcb59c1868e9a825ad4b284286b64e9619d9c0ac8ebfbc7f8eb77ef238ee3eb1313440ab5fa2cfbd93cb511f40d20cf271bfc7c2835607fdfb36b94d51b92ed7bf06e82aa188e40aeb21466a2fa159ad470c74ea923509189c958a533886ce577fb850d81501d648ff5b3d0efa6cf7015c75fa14ffe49b7660c75406fa0823ebdb06cc7ad220934db6e368ba5134368cce773dc90e80f45d476b39ea4f36f37e88fccf02dc35bde9c97f46fc538261297406e05d005e65d7827ddbe0c6e74a5b9d7501ce0300573f6df793ef1c1876bd988d31668fb196c402cc287a0068f77d7d400706fa6fbf78125f3bb9e146c6611783108c6554c3eb4ea6fa18df2ec84207c0d7742faef7266b2a57c11fb6b6d6b0040df616f724984e6e10e4ee89e6c11ff5e3f3c75991b1a63866914ca2b65b2663e88a480312dba6f0d5a56d05254632f08e6876fdea87bfb86f8e4f3db0318c02dc30c236da7100cb4765a59795f2f3ba95f52705bd4a2a37fdf1fa15cd85b06bdfc8799500ef4299a34f7a7ad2016bbeb6ba2c909f52e80d2c4f32070a41787084d74c60869bb76c63713dbe02c15e3abf311f974cd187924386b45363f9981939b00ab142e08fee59c7078fc4834f4a34c0c7306a599a231eaea1355c632feab396015a900d39eb8480d2e881cb5d5fd3046e16ba1d606b7b8151215da04cad3a06b23814aa9f9f8d3fc0de3858d976c6003e35df91a554e09d0475b83a15ae33ca9b4db50064559a0c351b1e98039f7a60c36d964929035d92d31d121daf50b6b7a22bd6411c96cef384d15f328295be4aca0c5bdaa0152cd20f00c144556f00e4a7d69f02c71d9e54c0aa3f72fa2a55dc289009c08010afbe6b460b0d6620683c3bb3a6d8d6b74aeb1fcf226c4934992259fcef431a56ea6dc60e0d104f8bb01db5c8bba18af7dc71120fcc919a54028cb49324748fbbe06dd32426dac7dbe15a0f84a665ba21c50a1b35d657cb4611ab8fe586f7d565cfea89352862a08117957114da517456413498a735e6d5d0fb58884e9b033c4604a83e96243783b0f28f4e383a541b626f6548a4c45e5d66e0fd5f5d878298a00f94e95a43cf548733201bc7e44caadc8d6f688f36a8c6aea6d9bf105039fb6a4c20a70c8222320170a300af9aaf3db9c71d9e6c867548a1ef94f2f34431c835990f89c157272d25493d445972fbd542bfaa91c68e6078b676f1be0ea37bf966a9de3d6a1c8d489ebe71f1e95c519d1d0ae04347e6f8f3fbe6399ff5b50526076c19da1687a7cd5d6dde1b1e1779420f56b7af7b11eb72d6475f8f31b0a0682f87c4bc0be5edaab3b414b6f08c6bfaac12e1bc817e021bea002f50fb7108907d981d38785b9b426056cadaa6813b9946ce221b79b45a8f3b54090775f7c9f24453b75ed36f2058ada67224a1f5c3da27b31b66491eaa81d82ee511845ce03ea8d46f6f58c81c3a6076482a8975b26a3faaba0fe510f7213c89e94903acf9daea3e05de239043ee7cea2bd3dd58c8ce031d14b6b1aeea15630e56b351862073d500ff0041187d84681c5ef6aee57bb1a610eb05a5cc8373e0dfddb9def7d6893d266ad2795fff34c8a4151c12769b8715d295163633e883cd455e5f5162950c56b52eaec1818f70d7c6d0bb407d1e1c78f4fcf00a842a6a094abb7dcfc31fd849c72aa8229e985e375df4d0d591d7da218618589c582714387252f1e12373274679bd4fa25002fd3cdeacc3f630eca02ebb2e45b0c0d93837a8b5b6b4b8eeb9ac0aa5ab66cd3c7038a4c07be66babfbf024a52705b0ca73adf41601aeb045e99202a08a31c76e888156842b61fd79c1ba028e08fcb7ee80e289a85c3adc97921f607099fc8ee6128c39e55e782d8ed200e053f76fe04f8ecc838cb967b626b47ad63835af34cba31fa62e9ae21237dd5bfa676dc049a2ed460e035935141032d7a2bca41fbb34049bd0b18338abd2260c894ecedc65e63e93a6890122be406d6139d76f202b5c91f5ad4e7001b10f1a7389ecde3283a3b56d127b6c081cdf00be7c5c1df8b2b3b4be974adc394b381115713b700b4e8e93fa876827dc261c9805a62b5a8bad9d698f02a9e6138ad1776671720514b7d467d33de1e90907ac1a03dfa08a37427502c4e4b1814332329450cdbc837b09dbbe4e59f3c00ab334f5412a6df1a274cc4e66ef29548256d09300139e2408e38095ad2d6ca8e2b7fee6244e92770f876ee5c3a09cfd39db0bbc7040cf3391805da90f31b903041a98b6c9a4de3a9b3d797a8d0d0c84f7e668d8e3cf86635945ecab1da01c7c4987b53f3c31c139dcf16bb30349d2d609cc8732eda7b1781d48ad132cbe8954f559871ed2502ad3832f9893ae8eaed318da3d037c6250514eddaec87fc1ce662596c6cfd6f25896fb1d971934c9b4bccf1073f451de1ec963eb83c6dc6ae1098037027ac393b19ef5c4332c952b54f5161159862ba56501312dc29e8379c4451b2c9b3c12cf3eb2e9af044869d06da0e85aca126e5a6b18d51084f05a1521783d803de6c7eedbc09fde3b0faf966c4c9af964c619fa5033642140f76e90de845885bdd138a315e11e858475026b651c7c2421bbdaf86c4c971d451c6b48576302547089ef700a6283228f89fa82b6b10f840df884334557a7d1844ad28ca5b55fbb5f9b1337a78225c6b2d41fc8d020b28f07cf7e0e094b5d828fddb78175e552241b5340ba1e60a981e66c57a46cd5704056c66cddf6b91dfcc260eb0b87f3d607f1bcbe285fed4dfce914c608755915b70072059ee0f48402d67c6d751f04ef84e0403afd6bc6c503a83110be705e9992828ccf8c1a3661ec5fe62d9c0cdc127e913c4cd31d40faeebe24b18562a95e9f425b94df153cba5e268836465e8b872eec9ec62e92e9a0d7347fa11b40e413dbed23b071f06e9551efd9841cb4234645225f9eb3b96dd83dfb5a89b5ab799b1f5147dba68189f03d0606eb9bc6a235ef126ad35f67a50e9a54b761bbf14b03f1d4176bd8fe8573ac3df3ba4580cf3db4e13f8a3174c8c473555df6f015e179dce1523e5549e31887982d9f98d26160ed636c634476289eb52ece23e6460cbbf5c1e6040e40f59df3b5c34fe87ad6130658f3b5d59942df0ae06ac050decea43082d7c1276f0032247f354f226e2e74dffd4662550c6e9c7cad0c187cedc68c4bec7d6d2fedcca8ff496c0f02acabe2fd5f5dafb708485946b87d86c8628624a48b66e642bd0ca387e7330f593ae6a524b95da5b695268e3ace850831dbcdc867d3099666332c2dcf309b4d319b4d309d4c309904d033698b93dc3653c87948b4655f4af7f252fa20f64a3a36f985ea158efbac7cd5838738359f4747ecc40cdf46654075243156dcced193b1de1ae7ecbc21922bface76e8ce81418c746579cb3ca29527bbef32f1615fee0fd9a198752398a525df51af73ce6d50a082ab0179eb7c6df5093b54fa449e5ebd5aca01d1655b54e71da9ec79090c9a77a89ecc1d489ba35aafd510510aaf5fd58983683f0ca67af75a97b513fe3126b84140c86ceb4b51f79f1c99e39e939acb39b030188528514ff42dd6aea85f0860e5fc66ad863161dca1429e08ae3b7f4fc05adfeedcb103e71f7c365e76e565d8b9b20302603a9d607969092b2b3b202298afcf31df98633edfc0fa7c8ef5f5394eaeafe3b6cfdc8e4f7ff60bf8eaddf7e0a1e32772e818caa4591f6b3106c6e68c0a23607bc8760048a3abd41d7e13ba8a662b0092434ac722c2b622248ec56a9663dd0d467d3c9875b76cd736954c27d9a9840db8434484fa66e41e79b88ce15cc0f9b9fbe6ecbc8c245db4613bdbb0882c03f829a8fe1e803fc013909e10c09adfb27a01ca576f76d99a15d0ceafaa743296fe7b71af01905e9351869184c1a5a93da81f60230019540636f7eacde0070b8b419dabe28fee99e3a446ed0090d61034c050584c5710cb4f89f30d7863ac6794794b7212f188350ea92a2b7d3a73dfe9b8fc9243386bdf5e5c7ec9215c7ec921cca6534c2713cc66d33426d61f4eedb8cd3736b031dfc0468dab8e1d3f81fffa913fc7273ef519dcf7c043d8986f606979868964c26f5d3c7adffdf8c217efc4ddf71cc143c74e60beb111bd4efae3d08858abeb39769dfd7b8aacec16d4c24b25566280cf1610e16953d441b09627a13c6416dec85033712f67433464f566e61287f81b2fce63ef000bba6ece8229be720501866effe4345475178077cdd756bf677ad3ad9fc5e39c64f32c8f2ecdd75697a1f8e70abc49a486a0ca3493e8121b15c0b8d3ff3c921210b54046a068a0530c9fbc14d313006d8b792270a895f3dd7352f1ba8f3d842f9fe05dce4696a178cdfd388726eefdc8e8e19c240e04769582ece529db6c36c573ce390be71d3807af7ee53538f8ecb371e0ecb3309d4e067d0a1d0cc16ab089d1b9e6baafaff3f91c1baa984ea6984c866da91680bbe7e8bd78f0c16338b1be8e8d0dc591fbeec7efffd73fc6676eff22bef8e5afe2e87df71740348661139a55e24cba24f765b466591b8d894c9740b641c424d5cb4ce80f5eb20bbb9a9f02e3cd187716301978ed88ca34f9185c9df5353681642fe18cf2dcf01821fa81a6ef0960591e1f20406403c02f43f1bf4d6f7e7c7f51fa7167580a5c27821f9466bd2c8c88260f7b04a7df3c71ed7e4cd3c6524a19a193c3c9a3245ae275c5366ebd96e85af825078ee4c54afee162b6e2d30f6ce0cb27d4eff3640d4c644363df677fe3355495bd663a954cdae10fb1a61121d3d26c86ab5ef87c5c7fedd578f1a517e1f4bd7b9a2e28da646df1c40b5534136224719ec9648269875173dab9b2033b57ce4c328908bef545974055f1f14ffe156effe29df8f09f7d021ffad38fe3fe878ec397b595f4652f7502c35892d919035a33b1f9e88bb11066e0c6f662c22bee39a9d8356decd1cb11b014fad6303ab3c3103c3357635dd56e24db0420fe457297ddc6881d35a1b97f66a096e87f02aadcef09801f84e07701fcce60001fc3b41959795469beb6ba17c01f03b890b590262e626c823223e5350fc067b1e2e7e003f6a5537f9e44daad17401a4410a0e476a4fe1f548f99530c6149ffe053c7f17b77cfe35e629670ab73866713a8e3dd42ac30ac6ec8e3757b579bb517c5d26c8aebaeb90a3ff4eaeb70d185e7279d8da576cc38e5b1dc4a58bf38f558d9c3a9fbdfbfff03f8eddfff437cfcb6cf64dfe3f85577725be793a500106ba01297322397b049a2338000fffa92153c7ff7141ee6914771c693c453ffa27bba1f7f1ac64328a8ed1ce15ee477ed5b58689ae603d97202524461d7a9cd597c5a202f99de74eb9111a53eeaf4b801d6fc96d56588be1d909f0130e92df8e5947841baee60d6c24399b9e3357680aa7f1fe0b1b175af867d7b194b2e930e27d3e71fdcc0dffde4317cf1b82d202b962682f50d069c165049a670f888c5fd1e85cafd4cba52245678daa9bb71d50b9f8f1f7ecdb7e3c2f30f627979693c5c1901a1a1b391811cedbdb67f597f430733189bcee7de757e7fef7df7e3637ff95778effff97efcf74f7f0ec78e9f846daad4d6c11a36244a26450487b1015485b6b5d1d8fce6652b78eeae2931bd703448ed6410920a0acec43b76cece6aa8ab22c870819df30d8c886460f9d8c9b318595935d78642ff31547e6ef63885868f0b60d507f2bd5455ff2f11d9178461f1da52fa64ec431b23734733dc4909766679d9ca7817a631ca540f1d7df001092b358654aa6e16305d0cc5878eccf1773f791c1b35fc9a4d8043a74cf017f7cdbd71de68000268a21e5b5ba801717ddf8605dc0da537a69fe964824bbee9b9f8c91ffd7e5c72e8b9589acd16b2965e7ab46b599ba531e6b6599b9bb5f5e043c7f05f3ef451fcea6ffd363e77c79d1cc894ba902c28838edb20dc71314071195039fbfc3b2fda85b377f09a29629340b2e5a426db06860a20a607508892f2f82f3d03434319761670bba6be0f34150c7228b840a15f03e4bb45f187d39b1ffb07fe3d3e6b58aafb21f27688ece3934fbc4b125bb248ca748a2d1852f64a3fe3f9500c6816ced1b8c59bfab7320f63010dfbf25c6d45ee4c8cb968eda690ad04f88808feecde393648feef386b860d053e717ffc0cbd8fba20e922d689a21f81c3a10f167fd8e5f266cfee53f0dad75c87d77fdf7760699687db779d529d5b078931b0180be77a9fb9cc5643c156de4500b66be70afef6b52fc58b2fbb18bff04bff1afff9237f0e25f69adca4eff2651863491350d93865bae3edef9aba900444c67c3cb88ff6a3535615000bd76cfec4fde4d4196cc2aba397c247337c872dfaee25386ca5b924ec601a77add807e0ed10fc0080bbba023c8af4b81c1c5591372a704d59d7ac4109f34c0e61acf34c7f4bc601738154a32466d22e287a46f5b3c445a68e82ede069397aa7459e2a130383194b7441620282980d62a03f78cfdc41e8a5a74ff15d67cef047f7ccd133a1002bcd134eabde1461cf1a76681227ddd3fb6f3cef00defd8eb7e10d37bc1a4bb399cbdd5b8f1a7bef32529f7bc03196b85cfb596bffdaf618801601a3951dcbc7f5eedfb717ffe86d3f891b0fbf1667ecdd337c0aea00f50d61e835b7ee6091f253fb4bcde5725a9c802d8162b1630ed9a4eac01c9685885acbbbe3afed470c217edcc1cae7b55e54db2247ad019f658ed1fceb186dc8667628b5af0240af51c51b87a51e7d7acc016bbe76f802015601cc68c33f1b1c018b7f53de7423593fe97d32e4ecb51d9eac6ec915753dbad75f414f85be181b08c176cc245e04be586efd81088e9e048e96c3ed78f68ae0ade72de34bc7155f3ba96e78a52feacd28195bd293c45dadfa8942ea2f653294ecb3e9142f79c145f8c59f7b2b2e3cff39984e9bf34d9d30706c5deae1a61e008da51e98f5da6fc1b5cdbf88b5f1fda5a519fea7ebff167efea70fe3cc7da791ccc33e984d28c43fdb905543ab2c0575f293c50436d0f885dd56e146b462632bc914e0c0539bb431d47cdf0411d5642ba6c3e4b81beae8a068dd82d9c6503c07693560759541446610acaeafad5e30d2c9479c1e53c02a47f4e52d000e04521335e8a6a09cf6eaca22b07096c34589b41993cb63dc6711eade244353eb21dde774bdbb783df9bae28f8fce71e4a4e2d429f0f317eec0c19d13fcbff5eb390e1466cd8eaf422109d9853d62596aa8da18104f02bb7cfdb557e31d6ffb499c73d6fe51f0190b035947edb5ada41684b6cae2366b63d11adb66eb6f9c2613c18b2f7b3efef13f783396a625661b8567674762842a652e8041f691ecd133c1be589cfadf61ff4aac876e06d386e323c91d8c0d88b53263656def223250175995f3c59c303211f5b7fa2185f0fd42010f00fa96f5c7f86b3b8f2960a9ea15501cd6f2b3d720fa00569cd29b38db46ee4bd595cd03551b89c16666e3afd9100c34dbb5b42c4d80aaebdd1edfabe16938b931b4ac40045f3daed831016e7ade0e5cb47b8207e7855d197d576a92bdb2392d9b281e029855b9cbb68b2c4f51e48f7eff77e0670ebf0ea7edd9ed3a184bec7139b46ad3d03b8fd7d7d6dd8249db66d2dd0219375b0b6b59e2668bf8977cf337e25facfd344edfb31bf6cb3351ae3a5a0627ca939c8c3b0b622c0acc884919d9e70d273f4253f394660dd800a34e3e751a9b8d276c5493b188a13ab48163d7ecf09d6523fbe378ca6cf45fad2d204ed423e66c84c55689029089008705fa983ed1e13103acf5b5d5bd10dc08d115a3cf056851954ab4d98cd015620bf0a6d0a09b49691adece0db9b61ff50683820f8a71d5c8ab2e5ae5661a3b712c5b7cb59f4348045635937443159f7968033ff2ec25bcecf4f235964fdebf813b8e6df866819095905d7bbdd6fd0891a2fdf68bcd9666b3297ee8d5d7e18d3ff8ddd8b9b2234de831963936d9c7f22e5a47b2b65c3d920f97dae7f6df66693386d802e35858d903c9cb2f39847ff8e61fc5a9a7eca2f95681a179867dcbc65b384cc448809569265e8165ea2003b2f644badd31d9f851791b776ecf80a716f6ba35ca7416ed068143e568ce2a19a093836ffacae5fd6229bc02c88deb6b87f70e1a7f84e93101acf9da6140f555505cc741990305e0231a76618611c6e08622963fd88487e808e31f2a52529b691785698de750b748675d6a300637021bef6c23743298d62814c08b4f9be287cf59c2ac5ac45d2714f7c70fe55403eb31017adf99a7498344cd04c0cbaeb814877fe47bb0736587f7c7eaef81430b2e2da86d054cc680b077affdd7aba765613d96b49510b3c71cdb3aecfa4bafbc0cdf7dedd571b31aa5b927662b64cd149a69fe5c9de6a95302381e5635071d763fd63f039d2c7f0522a59926544ec34e724d01403c0b3213a33a95961e944bb0dde439cedfcda4cdb0eb44e555eb6b87077d7d24e9316258b25f446e8460574c7ac36b1bd03af8a694202e34076922d7fc1509a229a35ae4b76c2dc142281f5431b0e375b258a8748fc403426b42a0496f87ffc26025ef68d63a6622b87eff0c2b53f1fe7eecbe6677d03d683b99b217877f1a5e89852bc1c5179e8f9f597d3d4ed9b5136d6ac3a4dee7cdd2a209df634b8b18cf58c8d963675ca657be6dbf65738bdab7d7a5a525acbee10771e8fc03c464cc2eb293f560c9874313103073be60d704d39a997fce8c9ff1de1955f8d11bcd2de70aac9d2843e650af29bd373940366cf333520167a542adce39a22933caa2c132f7684e8643dea5d01b05b21f8f417ad480b57ecb2a00fc08808b1cf9990ba35cb36d4f477569bde8f050a9d6c156646f6b4652bc5acdccec45780003620ae8c49a167bc881d737b9c970a83b45bee43ac5ef155388b0f423471bc0f2277ed2da10bde627f10a52bca1d98ccf39eb0cfcfcdb7e1267edef3f47adc766164d7ebe3eb626d402c4607d8f809eebe1d731f97ad71731b95e3b63692cdf8ee525fcaf3f7a034eddb5e24ecfd782069e46936d9579ddf61fb8f4d409a6626d216cb8da9c8d75f90407478ba6c86cdd47bb0f06fd23a769f5f2b11fab7fe828b25d057cc53cad92924d5383f6904ad75500bcdf8f762f52e047ea81f247951e35608960b742df02d565f6474e09a59e09f19fdda2b22045dafa95201f1ed5380f125e3dda01791713c8754a8068f4d5bca0d55fca07cd3330132b64658540d19ba2381fea03e86d555676ef3a839086974de15a9c99e92536be7a013b9666f85f7ef83538e7ccfda4f39c9fc1a5c7845a3db5a905b1cdc2b93614ed95e9d5dfbbdeae4f8d01649bc664ecc966e9ca175d8aef7dd535b010df5e934c68380933756e07c0ca249c9c88ad941a20348c3241100295fc4a3dc6437214c7c5bbca34dec47caa60f14a8f52556ad358185add2abced3c0f11118b4b6ae53571084096a1fa1685eec6a34c8f0ab02a62be5e800b9ca60a198c390d0d0f628be0168e15f605a34b0002a044c36b707d162eaa86c2cb001964f2e0458ab34a3defad2e9b794150fd9cdad501164e539dc5a04e6e142584c709c36c1c64d61b393b0f6bdd6a05af7cd94bf09d7feb65984c866ca7b766d5cbc340b69535a916487a21590b3263215dbbaeb559b8da0b41db7c2ccb56999795f9ce57bc0ce7ecdf47a0948f99680c0d9390540f507608f72e99fdb1fd861d67b9b3d3b418809b702b55667fd4a7462076ea397009a7d80be339fccb79d801bad8e4d8830c1894b12c22720120af7fb42ceb510196020701bc25872c7c94c0f4583be3b38f0731f65f9cb118ad95ac54d4c1ca91583b28ea2c4bd5375eebcd969d69ae277957772b64fc957f0995f5beaabb3e974581db1fdac0dca9a30e0d8ba4f043b346c1957256ef67799f7bf05c1c7eed6b160205bf665d0d8f29f426742f7c5cb4eed50bf9c6008aef2fca3fd69f3116b59534c6fc2e38ef39f8b6ab2ef3effb1559bd0572c67600b857bb62f74c7068f7146679d11ec846a89f603bad6dd21c8a5fadc9e0031e93b84ab58533b7c216328625c5daaf1331e8a06f69dad1194426221112f26ebd7a05027d0b0a663ce2f48801abfcccbcde00d50bb9fbfefc7bc44234b8b3e9193dec21881d1125061ca107de357bb888dd0765d0cb6fde21f96e4d2600002000494441547f6e96213d6cf57a94a931c2f5c2f10d6604228adbeedf283fef05773e894237d32ebe1cab2149807eb1aa8908def0fddf89739e756637d4eba5b175a6b1bc9b85735b5d2fea859bed9a56db5ecbfa7ae0b4680d6d8c65725d63b27fef77bc02a7ec5c76c75b4b362ea6596c8e160008764e80832b862c598fc13ce2d5ec269d5aaf08510e43e7b53211895d6b9b4b3667b42e3fa8d0311a81ff8063cd17cb6e51bf90230efdaa8b6407978b0d5b806b9109d9b546081b6504805c08e086f55b561f31ee3ce2820a1c14c81b203271126821952503257347e2106019c28bb1e740f3598d60a87f466d939907d7cc4c4cfd7378b0f022fc950b060933eec6e0846446d9d53403b2bac2ab0abe7ab2302ca90309c0db35b7162c4a608c5de236dd2ff55e71e937e3a5575ee6f2987c3da6d20bc516e5e73ef6406b111bdaacdd5e68d886933df05d24e758fe45e1e4a23c07ce79165e71f515910f0129fc1b20ee33531de5fd8e0930b168a1cde73feb660e5be2bdc46692cb170425605389e537d14a6166f00714d65c7edb5ec35db21c452e738c1ca62619299a70471af121e0f55bfb0ed313006f80e82366598f18b044f506553d042022c2408b7851c4c4b790809124054564c888c1d31a131796638aae6d481d4865df1547195c90186bc4f66b78169530181bc81e8b30faecc0dbb2bd2aa3357e62c3cad581540a196b7f0d84c9b1c2c0dc30bfa8aab0ab37fdd0ab71eaee53f2788c308ecd42bdb16b3d4059546faffd1e3beac9c2ed6d767dec5a5bb6177ef6646a59d7d2d212ae7ff955589a59489799ae48031260eb2d759cbb3209d04982876d5ab970c2b6806dcb19ded960d8a180a8102db0b1a329ad086c8e8aafb917271b84c275507b0daec3eaaeb2f81122fb3688958946fd2b4b3a885bf490406ec0234c8f08b0d6d7567743e4c745a43c98cf14533be78be6c29e0dae8498b00a8323338ee81742911594b21107b01995a5354d6fc707d07666881e8b191eb1b14087fec4b636139da3561d7e6bdd73675cc30928202c15cbc7f207185aed2f7bf10b70d9c587923c2dfb6b19cb40fe916b5b5998de4a1d2dd82daa7bd17ad856f2f5ee77970f16f4a3657d975f763176ef5c818381e5f5bfbdeb51df95a74dc129c2c1b69c2d35209ca0145b61b107a6d6f99c999cba231ee4976c576d7fc28d5b7d5437151133547b31cfea755bf491e5006402e0c7d7d7561fd18ee1c306acf9da2a04780d80832904741680a44d673d695231a0592e5b2332a4575744253f8b8db68e10cf8b3c792c9b6dc9964fc310c53ce338e3d006185372b65cee6e187dd28e529a3a9a73c78deb0676ee58c66bbefd9a2e30f4e4341df06bfb7e33101a746f84b18dd5b19575aeada6450c6fabf7167db6d7e9748a6fbbf2057633327ad79a81695ecfdb99ed702b7acda9935fb32859b6f646daa3dba29eb5fb76f462278f0eaa70d7dbe4d383507dcd23d9317c240c6b0fa03fa68a650b67b2704da860af95b6f2429ea15b2a6171118266f2770e2dd9dbd8fe0dc04914b591c9df25f723297f8eed738891c27d6ac97be51d2e80b261fdf43eb0d4d10caf9725b647753fefbc03f8d617c7dad5660bc85e4b277cebe569c3a3de22f5d8bad65818d80355cbd7ab6b91ecbdd094eb1a6395fcb9076c3d7d5cf9a24b319d887fe937b717a1523cdfaabcce44b024e28c7aac6b698d55cb9fd6d926abadcccbeca70a6e3d244b69dae8f44f53d6988bbe1cc236a9bd9af3fcf269d1b0c7e887ed28badccb22f26300f2af9e6c213d12c0ba0a906bfceb06843d1caae52d55cb53a8d2c040da792ab64214206871b7f258791cdd849328f1798ee54384728d0c5f1983c4d711d843783859b1a70c84ede1f4683b2fce377d15968375070f81a393e5e3742278fdf77d0726934977d2baf81d70e2bac7d670c616a55b36b70830acdc585ac4c416a55e5f376b6711a31c93953f5f76d137e239e79c85d6ccd5eb401d372b5cee9db124387d899da195cbf341c8494a8d0e8aaf0e901952803e632957da19406d27f569bac70c4c243ec797ac3d16c9ed094b97f7d69994947ec46ebb37ad7a0da0570d3ab2497a5880355f5bdda5d01f57d58921b28550ed64b0484f2a97f5c1618f5dffb57e021e0b475d560fbba5f2788d32e3c587acc088122218d069159807c83c8a0f6a7d9f8dc7e48c5d4a0f1b9386cc1bd5fc6cd0517d29551564c0ebcd274388f44de73f07975c7881e71903137e6dd366eb455b0d1bdb75a996c1f4c0ad65415b015c06d2b6fdb1bc8bfad9638163207ee699fbb1efb43d6e4761cf541f861fcedf35c1c19d6119e4f2fc3383562ebf60cdb466612e35704eb9aa06de98bec5675be84f736d20439d77aa9d3e0f8981475339239553a8c844811f9fafadee1a7478417ab80ceb62a8bc3c2bbf41f6146a0179d6f2440dc6c22a656613acb5e435d4377643fccb156f90e50bf9de3481606ddbf21552c372738faa994874c99f554427f183ac3965c2d02f591d848e4569a92fd6a6314b4071c9a10b70e6fe7d83c9ca9ff33adb9069f1fb76a17e2cf4e334b67e35064abd45ef8793360b195b36d8cab1a8de45eb80960e3e6bdf600469ae67f6554df9d93b044b82184ff79b655c3bcffb2bef9b7990faadb15bc9761822771c0d453be9d79baba0ae523335d00168161c0e57e6c593cc297a11b62169de73462321f2720017e361a4870b58af03747fe9843a3bb15302923ad4f822cde01686ceccc826730d534c773e4875ebd728390812fce0e9904af32078c4c5cc8ec6960d4c843d25b1087b636b0a244f78b6a29fbc5f6485f300126f2ced90cdd88f5bfcc0abbf3df27758154fc0de846d81686ca1be07766d9b9c7a21566fada9d7ee666b516dd99e4c9bc9b7a8ee315dd9e76fbff69a286ff7fc8f1b123920e035cf9a21d6766d648b93e4000108fb73306b851e30307b4b077b0c108ceda0f1b72e4176f1c682ac0f0390615dd5473db9ae621283ccd5cbbb7e80087d9583569f21fb15785ddbed4569cb80355f5b3d1bc0f5429df7813684b658ca52cc6cea529e1cc6872ae3ac280e1f742b9d17d7f3225ea9aba27b521fff576b6a260ed76386c37d139e78f5dc89ff6a4f955338e475f32d6d4e273d78227dd9270773f50226c3739f730ece3eeb8cc8dbb02a5e87f2569a9067b349df03b031e6c4f9c6c2c845f739d4eb81ec5642dc3199dabe2cbac675f5d6e6f6ee3d154b13b71c2e51cbf19df248ecfdf408803447944eac3775f13a8fd7a81adf1411b660db4d77781ad6d85c8adaddc2bc215f2d165a8bb5f73e1e238ccef239d3a43960518382fad538e3f2e8e8ebd70bb66c296d09b0eac3b7ae0370ae752ef35963436264aab2a9f84f3cfc2bc206fd14a3123ddd27100903ce036f216236a4168c2a7b239a5d2466a6658046d4984140e25fbd99f2141568fd7a4de9db920dbadf1f9abea4ebd56ca88d575ef312ec585e8ebe76c230bed70b797a7a1d5bc779b86b60bdf0721168f4c2d31eb82d62659b81dc58eab1acb150fa94951d38ebf4d3a8b0f306d818f1e7abf6ce70ea8c8f3464460189258ae054ce3642ff4518b7452b5bfa6772c38d474144616c8c580718d755be6432b5cca8017193815a723ee5e157cc0fa87a54a3aae70a70dd568f386c09b00498a9ea6b01ac38e838b9927a023684a884cb3d47e83c616d6ac0de38c3f5896719f2992a2506626ddaf027e287308cd8a2662618eb5336364a1d6c9c28f9b4860e3b9289ffc49242b132610f53062b195d130a285f51c5ce95655cfc8de7a709dafe6baf6f356db648dde61bbbd703017e3fb6b0dde6e985928b00a857770bde3df937ebaf95db77fa6978ee05f62d925a1f4d60d1bce9f2dc5d82e549e5321462055b3687aeb0effaa9da32854d9c38804d02a15a8f0520e572058a589427d030e260c5ad0eaadb221c2f0364c0f39d760e7ad5ed369ab2e59cc83b68d38b04452877654581d742b7f61ba95b022c052e84e0dad89a7555f8000a8381332e039e7add9d4015951e645733c73d334697823c110cb4aa376748a0767dbd4b434d22b463685a730762dea18498ea006513bb993c46db2323ccff98219dbd43306557e4208c3054cdbd945014f69e7a2a5efcc24b16867c63eb555e673389c7d66db6b25634f67911a3ebd5ddfb9c43fc3e98b64caa056e9667915e5a36c9edd9fb9595159c7efade0874ac9d865903e5fb83dfb277969cb5b7450fa18cf0aaec649b53ae565feb853391709831a70a20c2a794f701046076d2bcd19b3d972e302483964b6d365ddfc7db68a554488049f225fdfbb73c22835a9da503d7427021b6903605acf9daea4420af8562d6c6d1aaa468b7858ab0ec754deb6a6b4f76d91058bd3c3fae02c654d85b36e16002360e2d240610622c9053eb959088978162add68155a99f0e52d5e28a5d89eb44b53c2a7746e06a7d2b2a9144e7931fab79ce3bf74ccc66d3b8d699609cc600ac07065cbe07662d08f1fdadac1d8d8578edbdb68eb65c0b2e63752d626a63edb479db724b9309cfdb52b6a94b019c3205be79f7c43f1301a9b661a06a3548ae89e78fe5713a65ec3dfa2ba2015cad0384cda3663cfc7a08c6e0a7ccc068bef05c0e04b00a335895cb8a34d9e8c765b402afdb54a96d06e0b5f3b5c39be2d1a61954f56c855ecf486ebd1c78d3962998a7a18132f485dfb702b6f858bb21d4c1948fda734727ae04f7ae9032381afe80bd8d5059903c31306ea6d10f93284612fc3523b7331b1e295f849d7a5b36408990519718480051c5df7ec5cb48e6cd43be4500d65be7195bcf69eb6b41a217f6b5edda6b0fe4b61a96f564e9857d2dd08ce96151bdbd1072f7ca72e4f3bae2556b4471d9a9f5cbd2c6b83bf59b6f5293c72b69d95e4716c9f5a90686893f488df30cdf179bd3689be0c7ed525a274ed1522d1200a769813f60a9e40e126133d9194e8c57e8ea7a40365d7cdf14b044e432282e86d07a9456418d75d8ac4ba842d06fb748d0602cb155ea0600da09a95aca0f0a6b260729324f24bbd166ae06e2ad85dd882019a4c918ed0820166e465dd6ebdebb89db6b8541f62ef4de71b096dcb9b28cb3f79f3eca14babad8027369dff36bafae36e46b41620c90360bcbdaeb3d197b6b64dc66ef1fcbd0d3c3987e7aba39f7ccd399fc24e65d4c5a3011c1b567ccdc118a08ad2fb9d431d9598ecafea5ce9fbca259ca71fb2470d02b813bfc5e4a2a37ff2c34dfa4671f705b353b15b6506b8fcb34ec508014d930cbece8ea620097753b40692160cdd75601d5ef016496f0c77a5c27ef60b3d680c281c18cac0e944f605272553a9763266cc81f030b0f17bd5912230cc38c8340273a920c6ae8cd621d2c81897d7dc7058b3dcf2114023b26fd894bcdd003d96c3b5bf19c739f8583cf3e7bc0007a4cc0ebec801197095d650730c6ba386f1b22dab5def55e5d8b185e0f947af9b9ae3176f570aff5dab0cf67eedb9bc609a98ef2ba77069cb3c3962668b22bd5454fe31cda4bbc0eec4ad38c88febb7e6c7d8cc6b346015ae535a250da89952dfbeb4b14dec522993b71b36a9f9654878f43053e63516afdb6bac4eb3406d9e86a06e07be69bfc1cd8660ceb6c08ae6e15a69526165962a7237a6d23158a89708fe042296f6555650cc4faef03583a160c2fb0b3b233d0a034a39b96ee89f2668765038e54b7a4c1cd654cc65229c5f5c4105581b3974d831af21bad37f906735d70d619fbb0ff8c7d8951b0ac63ef7b137d513839c64c3663288bd80c03510fe02c4ffbbed787b13075acce45ed8e812dcbcdd7cfd8777ab90ee445728b10a038b032c1a1dd1367156608a92fee0bab1c6edf30aa93edb1140c2fac349e5a988bdb1a01a4d56dc55da660091e51406223ca5ebd0e291b4f892098ae2476328d38a4b9c01c44887520ba33a2abab551787859b01d6d5809c57e89fc36da57a557009bd3a1078c73258b40f3ecb03c23969f41cd4c22b4508c93b2f6140ae18afb0651a997af3ee670049f680564b615cd406d7a29aafd5ee5db3af9e7757f29e952df2a36abd915ac9e9bb7775275af463f8b90de3c642b81ecbe9dd1b4bbdba16b1a99ebc3dc01b6391e3e1ce50d6adcacf89f5cc7d7ad6d96795fb208769a14dbdf39c9df9eb380110dc176338f057e73c02d8cfd9a54ca570d8b819b544ed3ee9336a61a02e9f7bea6daa063b8a3eb3d5d3f89060f6304a43beca5b88c501ae2a20fa67dd63c241ba52d5f34440bf6a3b4c9b01d6774175973129d682b8c4965c9a86e1882b9d2484c5bfce1a0df68333964e305a17b41a00898150022953a491576b87648d61094328cad464597962f2d109347d6579e2fe393b26c11e411d663a972cb97cb8f2b2e77b7dbd89bfd9c41c9bf49bb19736941c03a43130e901d7229634061e9bf56d8c598dd5dbca3506760c5e7bf6d013506cd8bc8e327cd79fb9d454e2ad70af867df4eb0687247b646cfad434148865ee1c6daf8a4d93ae3c9cf33fc1b4a49dd7dc6e95d16f87e3663cf0dbde5e69b33ba6ac2b915d00be6b9829d22860adafadee52d5eb1d2d45639adbc4569b5f21ac2d203a761800c1c6bb504455f60406380edb15981cdd9ac5bba1b2bd29d421331663e0e2aca6b6c158e1943c4f42ab2b4dcedaa25363f35c3c8ea69fdaf8ae29b03cf1ae340309f2d6704006804b9eff4d88cb8b27fc66e0d19bd063755bfeadb2a0b1fab6ca7e1e0e7bb2eb8b006d2b20d9cb3706e0eea0c8c7985fd935019eb51c8c4655cb5c3127572a21879dd94cf7bd9dc122260254e74b17f8bdd9707cb63104128855e107ba659b54843c749beddcd76825d795aaed38618e425a5dd56bd7cfd70e8f3ec16114b004b85604fb6c1d47a8c7b6ccae1e03c73d574d05226352564fd04ed75c50ddda3b57921a36c35f05f03aacede04b06742482030ded7431b34103182409f783aa7290f1ae4a01616386017c651de0c0ca04672cd930330084f147b851aeadcca6589a4dd324ea3111979f592cfa2c859922d7d903b345eb53bdeb3d39c7580c97e9f5ab27635ba6274bcb06db728b0070d17beba6a4f7c5215dba675abf3f488e4e49ff1aeb445187b8fdc47bdae9aced24bd1b783128b18c8336c8e90a950dfe10045f63d6d9434d544396818ea3f120786a3ae1fcf16a733c24eceb0ac03ea85c3b1ca592ba80355f5b9d29f04a94035d358211ff82ae01417eb8993a78a84f41ef495550ecb889f59450a0eec93967f2afb854f6e3e043b4d5ebb4b83af481ec9f62e1102096e4ca0d0fc3c5c839781d153593f7f58122f03353fa869d8233ab517bd56d4300ecc75621827da7edc6d26c36008edec4e47b9c5ae6d5ae876db63e34965a46c2af76bff7be2733976de51dab6f0c08dbbe8d81ee22e06fd306e7d5fc465570fe4ec1295396271081d9b34ff2e0f5fe93788569581e05db85928129cd01777de1e7723be4b5dda11b70b44c52c232872bb74d7210b4f91661a2363a3659b412139bef8b7405911904af5c5f5bed7e55a70b580add23c055c9e55705d7b9ea8d3159f15839814199a801563a301e0f0f29ae8a0176e2e2f9b3fe0244ac5de379adfd092952354f3ccfa3f998068789de1baa3cb34384d7b1f6507ef269e7b411a6912b06bfa4f30e9c8b9d2b3bbacca0073a6dda2c6cda0a0bdaacfc6661596f0d6d0c487a40d40bd5b6c2d816c9b459bfdafcf7dd7b5fd41f77009430f0457ba6e17cebbdc4f08b60012ee4fccc353bdb2a8dbaa303cf156ddd6f75d2544ee266f6859ae582481798ccacb5feaea0b65558b53ae8619aafa87d724ec2c42495ebeb0ac05532f2f8e42e6009e46c855e9af0cad4abce5522bc4b7dd740704719e74381e31af96cd2ab227e81c7e5b7385c1decd4da8481648091b7ab94a733382c9e0fb56afe8234bd3a3b20c64816d078c2f02ed6df832bd1a0ad058c27c579e71dc4cace95b842e11c7fe6fbb97fc33091f3b6e03056f7d86bafddcdd68c7a6db5758f01578f95f5e469db18ebffd8f5f6fedd5fbd3b855d6eefd546bff5f4a9dba08daf3dadc32c26814e1836b79c43297e17deb5d1191cec946c3df84af489db8f1b92ec3e8a795c94e6aaf74f6deeb32a786928e69a332b9f4aba555d5d0a68f778c3d81ad6b502aca4ad4a63268e0e55413641c55054d2ef14dae4b42137fd43c4c1414d51ee19c2cb1403266f6b154bb46fd72d44530341cbd3b03c4f34325e2edd2c66690cd0aefb90101b64a615df77f4831878f9be596265645249f1967f79698a492774ebb18431e069d9ccd8441d0b155b2069dbed7dde2c8d8152dbee66ec6db3f67b7d6fdbe7eb6d197bfdda3df7443d0d937adeae897f8b21b11a31f75e27773440e464c8385ab76af32d0e4ff31284e6eb542631975486299eb383812d1a218839c87d2cf3cae66a6c70715f82e2185ed89cdfa2ae5680fe3ad600b0e625767c4569457218a736c72584b77615f1c0b1248d84469c1a1701c52716b2223d1f9d4d415307320b70882330b57b35b70facb334c0bf494f7b92084e56eb6de644853faf232dd96b35301fa052f8f425c9cad6be815afe95a559ed0f815cc31a7a6c6311f3e8856363cc6d6c2d6851ea3133bec7fd69df8fc9b119f36bfb3756574f96b1fb2cd7d1fbeea78296a1bcf9f633a7c47200f2ccd58c6de2e6146b3a8dbbd26a571a76552c484045aaed30f8582334a7340a84f3042ce44c755620f32d33918c411a8e549d71a48613cb6375099d4b6252b1a9ae14af9877d6b17a0ceb20a0878c72c23a67b51930f17d6333e270563b91f3b8fa2bad4c2369faf7c7b11af2d264042f3ae7c2f1d5840cf841875be331de656b67b93e697230e85a7dccc7789b971bb28d89e509b0671603eb0450a35a83b789084e3b65677742f5d849cb1ada89dbd6c1e5da3adb7a16ad6f3d5c8634c6f0b86cef73af5f5b59bbebe9a4d74e4f5f96ee7fe021022a2f852501cede31a17acd34d4f3e6a6c2a1daaf227395eedf745030d527f65e91ec8f6814a05a8f01194a94428d693a30198930f1db7cbc1126759eb7c3e8ebcf8ca10c001ab8b0255d090ea962f093f63dc03a04c87971f63e339f4abcfccb9dee2786542198874d5e1394c32b37124343d214d555004d1da545c8f878e657e5c45b625e5c2579a056fb6eb46a00136b0c3e51fd5c5a11405a8f17b70000672e0b5eb0675ac19d47b5823cb91611c19e53766d3ad95a8049cd4a8479ed047f38eca9c7c27af56c26cfa2f65a797afdddaacc63bada4a9e5edd0f3c742cdb634d672e0b5ebc879fd8af3e99475aadd9d4c73fabd3e64a3af8c277fcbedd52ba27c4e65300e4b65666ab8fa134f502107f72603bce54876a22055c5fc857ffa53c79526f4157e789e8a1f6ce00b054f55b5062489336289c186689634479952c0f41a9d15f0bb2c4808e3a6ab175ac03d104762711de8017d16d7b973d9d7b19a5c994a83688f1b1e282714446c49a1c08bc3400b9d45bc13c859661942b53c18115f65d6c48597922827d7bc77f6372336068538f912d0aa9389f5defadfb8cb1a55e9e45e1da22d9c60073acedcd42c55e1b8bea3976e204b51fcce08c25c1e9cbb4b4a0fea758e740476d7bb68c51eddd6c22f9dd1a9e2bdb6f009f3496137382c1d09893c473a872068332f81ca7eb56877ff249184b076effd4b6fa33b0eabc163c3c5da9aea8cab7344acb80355f5b85885cc5b4d014aa40ed7054ce6010a329de990025dfc08d3cd651565e05465556563809cb6e79ca3b1be8d879d4ea02426579d791bd995fab72f19a976aac2198c1b257d02a873147739e3620be1256eb79f169d3e8a6f58df465f72613c199679eb1e9c41a5b9bb1f78bd6a71681dd226058b41ec4c0b868ed6d91bc5c96f38c0166fb6fabcc6a0cc45b7d1d3f3927c0b6e1177cebe9d3b00d9a9480d42729002d48a96afad2b2e5f75c6ce756873941c72a9ae8a972ea2704fc74dcd28c19aa011493b5baecadf5bd1f97c82820067a8a98510d08daf24a5147b0be87adab72fbaaf5e6e90d2dc3da0bd58b7ca299c69c5248454e4d88595808edfa89094d6b502688087d9bbc0257f9e0f57139af3f0d0875aa5e51aa2f2ec7b92c632e6e246634dab02daad969368333016ef42c06a6f4db0093c23228aeda3b25af98776f7c3201984c2638e79c67752796e5ef4dce45214e1b227a4f3bef7b21d3a290b05d375a143a2e0af97af26d16eef63ef7fad64bbdf6dbfa8e9d5c1f949b0078e19ef2c0bed210dc9ed42773fc739e6236c3e183ffad80927443f90c6c946db9a1152df2a5bb316fbdafc23914fc08e3f6b739c5af8738243c40f5b523a6f4f761eaea2251d9cb75b580753944f6c404363e63e868d208fc21fa50d8a3550481fdf6ded817df0b7c56d22d9d784656b9111b1f70d28f220f4e66cd1182b64904695130c6de7a9b81cf95280634eade2226469588360034d50a3c6f97ad7b10449a6727a0dcb163c700407a13792b2c85590e7fce7ad341be1e6b625061b6b6996cad7cbdb448be36cf98ccbd7cfc793319dafa8ecfe754aee439b052bfb950edc0431e624596d9671219b5b1fc10c3368d94fec19d2d039aed4e1b6b0fce104b2a065ede4fc221bb9ee6971baad7ee7d8b922685530f3377e27c31ee1e4283e57c98ba52ec81e0721e1f07acf2b03e5c0a60570a9b42cb0d165a63016c2c5c28bb415e0214abdfbc8975c2ebb0c1630323e5169d8983941b3c988114708d41b3505403a5dc98d14dc9c8c9c89c4653b8c0d50ebec3355bc22b0feef5f20eacc4463d0ced308631b6b1e87e7baf076863f530935bc4dedaf23de01963578b18d7237dbf48be5e5f5866cebfbebe8ee3c78e97cfb089067ce345df8c67fff09b8017be1438e5b47a9fca960aa0640c3151833db118421466a00f2b57df0be5491edc3367e70eb1395ad87f71d6d1ef8802ccaa251a6bdc7d38d9c8672e9e731978fa75628c5bd51544772970293fd48f19d6328017e9ffcfdc9b865b7656e5a2ef586b7755b5ab4d55a552554925958674a42f2090844e1044e05e45b9e81179944be373bc7a4f7c1ed4131f8f1a3ce71efb8b1ace41bdd820a82822100e02a14f07e9bb22a19a5425d5a5fabe76edbdc6fd31bf31c63bbe35d7da85429209a9bdd69c5f33da77bcdf37e75a4bb5c302d93e0d8a42869a300cf0af4a2d422b09ece85bf88886a17969a5546dd40d6e06358711f3b26bd6cfbff5b42cc734c0c364f487608b615c2ea36f5e51a886b42477df4f8d0b55b4ba2d23f5e442745ef316acb8fe0730d2a14a8418cbe0d6f51dc250da58c2b03d9c61c0c36333c3a819d62059eaf3c3e46bbb5e9f6ffbaf1e7fd0fbd9ce0f6a5b03baaae2c08183d8b2e5e9e63de5f09a55a763e4e2ab203ff413901f7927b07cb5fbde420cfe3ed8b3b30a50dd1b24abe6380cbf099d8f7696970688aac89fed43e42f174bb07f939ded95e4f31cd3c41433c34692e7071eef0000200049444154a31a3acb38c456807404b81a10ff62fd0c588217395b28ccc63e2a63eca9610ce2c2c607a07949530c490c236f049ab5ca1c1007befa5794b992880f1e87948a631c8f6decfb451a462ff8e715880b93f8788dfc5625cca0e4aee61cb34e510887a1869638ed74c85bde055c753dd6bde82a2c2b3fcee93280ec0d03dcc1fb57f677d89e520d0cc3d857ddb7adfd77335e1b6baa59179f3f1536340820875daf659e6d89cab288080e1f3986ede5a3396516882adef8eaeba2df99e742def22ec8aa731a7960b9ace9bdc7329df770f582e982783cb681694daa6c3fdd28867a1c118858fe0adf8a2221b8ca57ed8ca159eee4f674332210d18baecb988a70b6cd2cb67a9136640a4006ac4500d6fa6cdebb389140c62a8d90064e4113c058e0b1881cf0854d14eaa8505f1e65a34a4c440a03925ad03f4435e16c94a9afd72faa64dc3f559e220b03adf2c08801941c0600e88e405efd23c0aa7320225876c60a2c5e381f6d8788a4aa1876ea7f3d8c4d31d8d58ca98dadfc5b966d8340601023e36bb32de5daf4ae65abed5003fc3036d9366e2d97aa626a7a1a878f9d089d215838318645a79f9e6db3e834c82bff376064d443d5d98567b34fda1f23f45ad52a5dbfbd93ed839ac4d649792f65ceac0f678d63229182c8cdbcfde1599cc0ca9b08e114318084053158f82504186a2b006ba1ea1bef04587a21e8d757db9ef288e4f6ddaa4876a39ac92c3e33bdcbd54420f133452afe1d5866943e4828a86efaf977ca97b1a85e90ce953cc97bec285e2a2a179ca4aff2306a0e66b7aacb840b2e03ceb92805f8d5179fe7d73df1196831985180fa0c7bcde76ae069633bc380aceda8991ef719748ec79f6d6958cbd106b86df30f02b4d99688751b11c14c4f7172a6d78c83c6372f7be1f998333e96650380d5e7002fb8c2771672d2533bd695e224c595c59ada72abc476895b2b6c09f734139c2443696511ec3669de452a780e7b9794ab9c0bc44bc27ed112b6c230999239ca3cb6b93fcc56aa3a22107f8094004b2ef3ef7d2eb3d8361cb2de6e21db6f726f7ae206f54bd5c09909fcba9bac02d762856cbba29ca6a7f095ae6bfc6da3d15e84f86e1fbba81fa61db42c584a0f5f225285e0ea2810c8c828e4e2ab816e3725dc0dd7bd04dd8e2d49a59221cbc83af0b5d95eb731199bbf7e5d5faf8f618c29db733028d4f30d1aaf4d36be3668293c8c6dfe5b0e55c5cc4caf8f999c77e105181d1beb639dd21d815c70196464044d5c94a4f6efff8f726ad78c90088d9f968d5e4c3537b23ffc6b1315b8d8e051970d60c9264e086c0e12c6575291cb0c5291254d1e5a9e5bb6a5af3617facf4f46719fcd562ab8cca4ea00cdaf3b037ab9b29128a183438aa330b1bf4003a7a776c7cbe8a9e9c6f4bb6134de960ced37739d7da8332a5b9e1ae50c3a5b0c555754b14d7d0d39bdca80fa218d15afc5e7ed0b0a3afac116c0c45ce0ccf368bce6efeae54b70fe9967e4b12ac6c100d79674a7b2346b6b67af6b70e0736d73d5e3b7b1a4b625573dee207d86b1b1d99676f5186def870157cd38edf5430f3d0aff65190073c64671f5b9ab13334e639c791e303e2707037fe6357589f8623c7259490e41f6a517de6aec94933443bc43e507031e713903032cff2d4e626ea325a0ebb1d443213bf541d224609ddd56025c6ebf0a6dbfadbd0090d5f5f79f67edf3ad4706077f4f132921b233bef46b3352d01b891aaa22bed9d401102d06b0b1959099c52d20840289614b720a5507a7e7019226bb2735b4b95b44e89adad7422e5a0a993bd927f3b2654b71c1dab3c2eb9a7dc8c720e65283c43010e0b1dac63c1586d4c7582b8661d718a46a596a401cd47ed85278d8d2b54daf531967d09278fd864d60a74ece9dc045975e3c98cd4d2e00e6e58f54a58520eb089241ec3d5adbf64f54c6a0cff8a16122315b65c27a8b5d28765d06356e127201016ed6dfea36f7277e100407d13f63f500ff0eb01580d50a5900f8925097a862a5868419f5ebb593a1a8556896c5796358cf2a46ec649540276ec5dd0afd2a4bbc8a81a91934aa505bc23746e680a6e4a231ec8a33b6c4d69ac1c30e28b497821da46769e436995c10c0cbcbaf4e07afbefa9298a70a266393838e3656d5b6c7d3b624ecb7d16046d5b617d526c7b0738398de30c06bbb36688c416d669373d812d2f4deb6e740d3bf8c71feb285e8ce9d37583f1160ee3ce4e2c749c3a914db0af13e491df126515089112022aef95710c5de64e6f99abf1cacbcb74ca20adb2c488af1159fafc89f004d8b8e92f58edd616a57aecf662b55ac146009608025b254a0ab1d3460ec06913d5aa9672024e2a8ecdb78896c28295d077c338e3129a7a22d6c454a076503962b1984d89826aaa4316299aa1e682a34beb52d0fd935cf5e65eaab644d480057543000f3e6b726a5aae2b26baec2dcd12e55b3bca7357df2e4d0641f747ed8d2e75497566d73f0f9614b406e331b409eea7e591b080e03cf1a64db98d72060b563eac4148e1c9f6aaea109b91f7ee5b503dbfb3131af84bb44213470515b86a599e14d580fafa4523e5162735a922ae14bdfa081179e760618040a1a675908fb0a70cf1af7770c296976c6058acb329fad761811290567b59500ab015d0ac4a6fb6a884cbad10ca04c3f55fa927cf8634d86e241fffa9f870a6c8d6cf64d3ca6c505709aadfc062562495860c4b4740bc752cf99597052343f14d9f4f6f91dbc685d4e22db93fb74ef84e48eaa150111d5a7092e4a885e8ff4b5e68d5e7327c6f1f26b5e182068d3aba237d3c38eedbb06263d277ebd1f55cf37e85c0da0265b3d979d4f0cb14af841e0316889360cf0062d25dbe6193617f76d638b6da0caefbffd9d8dd8b56bb7fb7eac2398b7ecf4bef9fb8e99998416394f6d9ba2c999087db65189b3145748ab285e3144aef16ac76254285df82e5eb02528dfa80a2184d999548f02995ca93a673864b535254c759c8aad04930056030158672b096e4cc365722b1aa0c0ff2b901a46a0e4f6735e1510c859a4336344e29a89d58dd2fc557794208386eb58da811c07c4c3abac93ba1c316ec3f6ccbde25fede3c9e0d5832a4ec44b18de9c7ff470b9d69f84a3a3a3b8f4e217f8d7209307d1ebf5b06bd7ee94c0831842dbb26fd09e521bb8b5c957f7ade79c8d9df0b9614b4e9eeb548f36f01c346e5bdb4140cfef0160e79efd3870e4a8d7abb16e171363ad3fe4928f23074b6c075d895ca8f6823c7e250198b30f2072b25e72c40c957d2aa0a8e769e9efbeee1bc8e1afe4508022933d1bac9ed78cc70599395decdbcd62abc63667030158e758ee1968304d7367d29cc6b80c3c3c7bc502831ba70eb0559dd6e8e6ed4ddf786c203111f24a241102501cf88c49990f9ac90bf1f37326aff5708094306d4a8c6219ae0866785edae2f0c1ca812487085e79ed55983f6f8e03a8d9b3a78a3dfbf6f7f719f29ecfd72cac4e5cee3f0cacb86fdbeb41f3cfb6ecabaf9d2a18d6e03b68ac618cac1eb7d6c76c77ecc449ccf4ac7801e7ac5c8617ac5995e6eb63c0aac09143281b1c8d3fe943cda14b2c93085ed1cf519ab7fd1be41174e97e9cd8aac11e1308b192ad10d74a8225f45323099c43dc871abadfe8fbe139e7d20ab4281c5f872ca76c2b113907884df7b5962c364973474c8d22e52599a8ff1c9616be2ae0c704c2340e206ec0660c665e39f8c5e7a1956d8be1958217696e2d8d1da00ce8284238088c98f1046ac08d407def1f948dc42eae2ab603001cdc0b3d713c5b841273f98ad371feaae5390e4a321e387c34b56f3b4e85a53078d54936e8ef6cd7be1b1986011bf7990d40eb7e6d00de0676a70a9e76ddfa7cee0b5f49d7962d5b8ac9a54b078e0d00ba7f0f3075dc42bc891da10296e6aa989415db3edea45eb87d6e63299247151096a19f51c579038be867398c92c792a0909e4f64e262604773798aa7a94d56c94a930eb3d90aaa6b01a03373f37b4754b1da11bfc0ab8345415fbf9b67c3bb44c6926c22ae54802dc5ac8d1adb0a41d23059d07c47cece19dc2716975b3908f915694c2e223467e0bb39c3d8223b25b10d529b83231540ab30278e03bb9e267bf4b39c37fde02b9a5f5e115a0cabe2e889a97e83a09d71f0f97a69c3ed98a9b23ddb18d8b0b186ed17f1fbfa3fbe3668ac41fd868d3568fc3680aadbd632a82a7abd1e1eddfc741ae3da8bd73691324406ecdc0a9c9c0260e389c77cb016b355f83ac9cbed90994ec4b37596c272eac281f24592da17a776de7d801853297f9b7c41c9df38cf31aa50bf2b69f372be87bee2e70bff41b0c353b31544564fdffc9e910e80255236dc3d3853e06a1f6574fc31f6c2196b33d93089f904eb31a6e66c859f2931139679ddc0848989244bed844095666dce633b5e42cab73cb8bdc889407b608aa958f4500d79bd7e99914f4e013b9f2a95af1f2400e0b217acc5eaa58bd3100ae0d8d474d8c0d46c611f83f6a3665bbad9b59ab1b401573d4ebd641c265b3d4fdbf8c3969ab58c8300b4069e7a893748afb6b91e7df4db3876e264730ecdaf1d5df3e2757d7a253b00c0ceadd0e99395ee366ea455de9a207d4aa360e9ccac62db4235f6918c2724d53c4f82cd3491a9e9a7c12cb7c45b18a8109809aac7a6d4e339be79d46c8ef8560b47a626cffc33c94239d8c7ae07db0ac0a440967414580160aecd6183d0cab251cc3140fdb910379219a7805bd3aba0a7bde680113825e52a62bb385e4140155e6c440945dd1155206b286e771d7370b1e01440c5a06a1665a6950cabd15f62cfcf7570397ad04deba1e54be0ea3d1855c5ea3567e1d2f3cff631adebfec34731533ec7c6471b7b685bf60c0285be7d17b42775db5ed06ccbc2614bb7d94063100859df3626d626531b08b7815e1bd3b2e3ebf73e8c93d34dc15001569fb60073ca8f82d46cd5faeaf449e8a6f529f91bf992a55852028b94dfe5b56508dfa133324037ab9063d48110349ebf9648551b9393d8e6655fa05ee5d04a8cc13acd29f67fd86a885b71ce9eb2ad1a8c5ad111e8724027a081958d2163152b96f420c4cdbf90d8a8cfdc30885601b9f2d703ad16100e2cc1e042f63243d3cf31cbf698aae03587f0630b55f2c1c7eb4f6000fed53869255f68305376b69911cd184f814deb81c3fbbd7f5b62bee5f5aff4b033f5ef7fec091c3a72b44fae41cb37be9ef4a892b26628f5f9365b0c62533538d6e7eb36f5d26bd092aded7a1b30f1b561630d6ad706c8274e9cc0e6addba20d806baeb8144b162f6c656ffe77cf0ee8f62db4bcb2eb1ab15127ad01270a3ba1f80d7852ca27a1d1ad41d149f3d8d68d6735d0336212aba33a2f9072354112631c6c69db620fa77239996deed0ef146d054c0058de81ca22854c0091a462d0d80728681e623326a2858715004b15c7ccad94f4890169d41031c0d304c642131b8802b1bfc6e0962c6ac674a0a5aa23512162a330266d4450ffa51297abc891e7d63436fa1c2cd0a9e3d0f5f70d6509abd79c8973cf382de457c5e6addb70f4d8716f5727da6c09cd8c8619d46cfff5593315997ed0180640f53596a78dc1f1986da03ae8a8c71ab42c6c9b8f651511ec3f700877defb702aa62b969d864e7724b5ad65d247ef05667ace2a22768985b09d19c8513ab16c56a435c6208d9b3895e6b5b1ad667c3820f42547c9455bb180d85362b3a0767d43707b6706acaaaf6c54697fcdaf492cc6be3b5b4d0058d4816001142330e624b1f966cb23ffdca332532a4ad7bf65669330b981253a1906b0095d67477c0f2e0ab062044f12dbff32a2670675fd248c849a3944708848f3ebcfc9e812d4898e00bd9c2405e528802c5c8a1e8fdd071c3994f467c671da92c578f9b5ebd23c53274fe2d12736a5b64996962553db750687b6a5519b4c6ea52a31dbd85b1bbb6a9bb7eed30662ad40508dd7069075df41cbcb4132f15cf73ff8280e1d9bf2e4eb763af881975dd32a971f07f702df79089edf142862365263524da030bb6220b11cb0a40d408b026db9a35e542d5cd9a72ea983af8dc5ccc9c98501a0a30ee910142dfe9a1cca76313d4997924b99bfc9776d2b4047002ce82874810846402cce9883fa8f4b30134100456129cdc021b323b3568f3a58058004ce85bcb03575504402b2f2bfe67cc865effc151b98503380b23f684d07ab3716023e1639a4a805a3b0e6380b187bc7008aed5b806fdf3798710078f3abaec548b7e3c65005eeb8e7c1810c88c7aa99c4a0f69cd46dafeb76f5712a63d7b20d633375fb7abc418ca88de90dd37f1030d663f57a3d7ceeebdf2c09dcb49b333682b3ce5933504f556dd8d5ee1dc1068819f89dc54652cf8b6052c1727c3c04f78939ab821b48e7fd01d6399ada57c418e019f8791c6b3022938def26865cea4386ec994df979eb6fb2aad29cc0776b2b558c40b1a02390d31cf508891baac60e52271e4c6e82b2090152a68c19081460e64646e4f9adc2f0e17541cce8cd200e62048eee1c4798f8cb949c83c761314d5b6d6aaa062827b096a8366e1f4ba41ef49eaf01870fc4a815c35979f61a9cbb62693af7d48e6770626aaaaf7dd8172909bf1bf6c4766a63206d80518fd5f6beedbf539169b6d783d8622deb30f906d9d1de6fdcb405eb373c599cd79cbf70cdaaa1bae2e03ee0813bacb27a014ab6f6221ab113ff51bcfb39f5b8f2621d4c23bd0f16e3b7ff7d3c6728b4ba690a3631b74a1e5b29f802aacd96d60f452784dcb0eb7db662bfe3df642b859ed601b05461424680360d84d255085bc4070fe43225a962d7cdfc7a189a7ff23ee17731b61d8ab89ddac8484d125085b1d491b5114059deb06a9a83e5eeafec01487c444044ffa814c536bb77401fb8dd65ab0f11c18fbce1d56925ba71cbd3d8b8655b6adfc61e06252ef719f4be665bf5f97aee1ac4eabfb3cd3d48f7417d06b1af3699d80ec3641824fb139bb762e7de03b0a5860078cdabae1b3ed6837740f7eef2f6491f6998bf7f879cd9ce3cacdc0bf9c91e50a9e4781466efb6126a1a298fe62fc2b765b146951f51c0bd39975a0996836a0fd600bab46d4800253ccded8c898e7f8bad446469078aa59167053a0a8834bf36e33405feb3553043199d0ce5410b23d3cb37e62b07843642e68ede4d30a2ef18743e1949e9ce2699ce18943db4618c89a50807e69b0752f9230147254554b906c8447bc0edff0addbea59f399671aebcf83c2c5f34dfcdb377ff413cf0d8137d1a3268b4bdb6f7757b3e3f08a8dae6697b3f08c806cd3d08d0ea3e6d6ca97e5dcb3e6c89384c8ffaef47fff973e57dd36ef99285b8f2a273533f09e737bebce3f380f69023c05254cb0f0cf395b8d9a344e5cb62a1bce60c6a913bce788c35f1ce879214ce833cbf6b9d8c8cc4feab3550ffd487cb609392cccd79f661c0a7f08852eb76eab652d5a51d0896c09c4e3f51dd20b1fdb569e3b5184b326534048c698a405a4c565e0aad5bc515287011e8e2c676d7107be2836733e3bb73d5250fe58bbc0284334cccca982d442ccd15343dd5b7ba6533eec929e817ff094a4b434eb6b5e7aec539abf23702dc7adbed98a16f7da897467c0c02a936996763467c6d106b1b24c72080e4f7c358d26cf20c62926db2d573f1753effad7b1fc2e6edcf580300c0392b4fc7da73d6b4cb7cf800f48bff049c9c7256107b401e354d44585c95dcf23da9fa1b43dd564d5f0fca64ab90af2ff71334f0da08690c6fe7ecaa2100e93bdd9d71f4ff978662c6a5e1c33aaa7ce5f3efb095882ce900f68b1405394919711c8acd301abf12aaf42f3ffb63f3402a25cafe9594b55c732e80440b7a472530ca1b9b7e816b6155634bccbeb4ad5d8398a95a996e1e28543532a7643c8d7f99920f3cca771ae1c9c7a1dffa0ab43793ad57d6b76f7e957de75233e3d6ed3bf1ed0d4f863c943c6d093c0cb44e05d06603b04100742afdb94d1b736a5bee0deac3d76b965583f230160600333333f8d7afdd85a3c74f80a3facd3ff03248a793faa82ab437d3f8f0c9c71b3984964c90883f445c697904bc8eb3fef298c14921a95a0ab39bb23ae01c2a03f5d9afd6c11b5a1eaad2d30020d904f6bb9e911769c09892b04e7c5595e5fbf7d80ac0a20e9ae71b60c010cb1e46fbe66f4a5f8d6795185cec8e446387d2de588814f6e68336d6b15437b6e944c7519bd6e9654e7ec6aa617c9a8c9774c9b5c6977e55a92034b7e431793267acab84ed0fd44bbdbec3ae7feb2bc043dfac2e35e3bce0c20b70fafcb9454ac1a1c347f1d92fdde12cabb1533bbbaa13bf6606c35809b7e3796a90e273a70234f5b941fb4c2c5f1b08d6fb542c5f9bacb5be6d4b603bb76fdf01fccb17bf518a91404570fa82b978c1f9e7b683eb43df6c7c68aca1ac469aeba48f3377dec250d867589b36eac96ecb282bd08dae5ad9234aa667b5e790f945bdbfcfd35220526648a0a73137b1bc568afe747bbfcc9bf48bd21d5fd584ef89ad004c74507ea4503290db3464a450c858170c801c5a05fc006900843120e443d9781468eeac4a29b1ff02ee9d36564e309657e768da8380d0b8456eff61c8c21409acc4fb853c267b99356ba25625ab4a7fe238f4b64f401f7fc041cce45abd6a05aeb9fca2b412f8cc17bf8efd070ee531a84f1ba3e073b30144a3563f1b696353bc0ce4d7fcbe6dbc41e0c66ddbc6ae659aeda8751ab404e5e3effee57398ee59116cbc78cde51763f5aa1559ee5e0ffaf803d0db3e019c38d6179b65f4980756b84b61d752643d6f002b9f1461c12abc1c270d6119656c2bf221eebc717e0cb615bfd3fc926468e9498d9aa454c607cf832006df1b5b61ac03c818af251bc00eb436c11ca43c9028f09c69a06f0cbb4c61149928c1648cb1d85e9a1640d14a591fc532ba2f903921d48d91fab9d162eea84e7dc379bf0046d2ab3896ef73f21f1134dff323f6be38f1e821e8673f0a3cf978d2a13b3a86575ef342970f000e1c3a8c5bbff8f58189ce479db0c392fe54f68eda807010ab69afe2ed63b4cd3148c6d9746e9b876568034f3bb76dc72e7ce9cefbe034a71cafbce685e88cd017f6a936cbf9cf7e14387a085cc0fd7a2d0fc42a5629a265fe2253e449bed9133a482be05832dbd8fd6420eb6d6dfac6f7be063e11c5c69c1268d97bca571f8ff5377bbb24df235b41c7ca92d016578ccdf6c2408a40a6a864aac4dd8d321b193c949418cfc7c806060a00488ce3d5c382cf29a894c714cc390865cde8968c554094815c0375c7d80ce12431aa566433f617462eaf4d4d02bd207d9a9d6a77530e1f807eeaaf800d8fa4c47fc975d762726ca438ad19e8335fba1dbb76ef6d0599414bbed936a6db12bc6dfcd9966aa732ef2020adafcf767eb6b906b1c641f37fe6f35fc5a6a777261a3339368297bcec2559ef0d8f34be2a5fca48d918efabd8b1f37dcb2d930951fcd162779f5bd0e727d82c966fcc60e84593ec9ae3d60a8d71208b03272470e04abe325969bba8e956ec5bc57f12e87b602b00131da88e69b96a1f8b5103165ae2a8a371b943687d501257c4614f8476fca5acc32bcae970573bac7821ed7591be013ef68f190c04fe52bea727a33e076ed32e969e40382acf48ccc836cee9bc7d49a2415ed4293eb23e0ea810e0d07ee8273f0c7cf5d3c0d471a82ac6c7c7f0a6d75c1fb328f0c4e6a7f0917ffe5cdfc86dc95f1f6d7b4eb32de1da8ed9c0e3548f7a795857fe5aa741f2d4cbcb53659c766ed3e6adf8e417bfd1d7e74dafb901e3e5d79df5c431e0ab9f6e7c7488be05d6688daf24c40b26ff96003f9bd4c86bd59d8b643fe89bdfad600bdba7f46fd8bae54d238b46ef7427bf364d44a2d07bb2d7c0be9668457e0337270cfd737d0f6d35d681c858b695145b71f014319d2cf19dbb6248455038201ed23400aae67056e3862fa3558e538d2a61f398a14231357f9573657ed463a91b53e87a034416185570a4ca5739a488117e0fe0ce58c977181daae2daf1a3d0db3f07fdf4df00bbb6415471e5a517627c6cb454ae46c74f7ff11bb8fbfe474897f644aeafb7014b9de835500d0293baffa9ee2d0d9ab36de9386869571fb331ccb679edbdaae2ef3ff579ecd8bd2f5d1b1f1bc59597bea0b989b36b1bf4d37f03bdfd73c08963799ce26f2fe6cab11b37989436a9a33822588df7e82f72ce90cad896274616b4809a156923103ea2d0d8866936532112d1bab4345bb2eda4962eeb0a55e49b07f2fdb195cad808caa67b8c1c4cc71e31600d1ae4f6a6d1de3b28386c5478435aab393218c424ea4246108a1b3e639f3126ba93e70d1a941632625b4cf3b9fe44ea07bda8426c076b6a775b2d9910f641038eaca63b73a6073c762fb0ed49e0a2ab70dd95d761d9e285786ae76e3480af3870e8106ef9ab7fc44baebcb48f21b51dc3dad4fb4aadfa55ed07f51f689fef429efafca0b68396936dac6c981c5bb73e8d4f7cfe6bf0182949b86cf1425c77c95ae8973e093c762ff4c09ec42869e51471054b474f379e0d7ea11227af205a64d598b31ea03915ab03ce2f8f3927412516ed6bbcabfe5ae9562e154e50cebba09153cd5b2326c49ebe7fb61aebe4b320e42fc1e854b3700335b45667313671939791a8ec8866c8001443d988794a0097c1aef3069c55096656e58e421f43a8ed21f91a1dc67ea46a2354e5ccb1a49cff3550529a27b53106573bc2e0dcfe39b817b8eb0b18f9b3f7e3274e67a06b5a3ffcf8467cf81f3e9d1e73f86e59cea9ec07cdc6e0dafab7cd752a7db840d4a035a8ff20b01d26a7fd3d7af418fee0cf3f8693333db84fcab59f385d30f267ef07eefa0270706f250333007a5dfd9bdd9f6365d8f236cf137d84180e97d138cf64c0e6579ab379ef5b39d49fc78cca9b5ffa62a414d76492f800f0f7dd560a45078aa9ea6cf3c7351318d5b42a64b6f026be3c60ed35fd11fb4703e55d48ad0d67af0b8cf85065a3526d4fa8182370b04a34f1a7ea0df0d8381a1cd57f54c3151121034ad2379b9dd42ca06ce7eb18543f6f8ecced527a4e4fe125ddc35833a7d377f123fffc393cb171cb40e0e173f5466b9df0f56b6e3f6c3f8bdbd7fd0731af36f9eaeba70abe6dc0d506766d7a7ef91b77e31bf73cec3965e7d7cc11bca47b183a3d853a7e6d8b3a9c96729a9c5d01bd5671e08c680878a57f15f6b890cb61736b40558eeee6bd6781047c903558bd68585d37cdeb5c6c1de5fb6faba98e42a7a27d519d26728c31918d1f5a605b2f0204861f174d6c06290a3064595bc6fa0211525a48f3da76c9d48d27c1aec4a0d0fad138644a9fb35c32e36a9a9bba2102a04c49ec8e9496a0d8bcf7e736239616094a73a4e40256cf11bc6c511706d536d19efd07f1e17ff8348e9f981aca84daae39788ba4d77caddebf3b95f1dbfaf01cb3c954036c1bd0cca66bdb6b3e44049b9f7c0a1ffad8a7303d33e3feb7395fb6a88b33e77450ef53367fa48aa40644625560f29540cb8b8c0000200049444154d24eab8558b6a1f471814ad184e753d9abf1996cc9654cdf978146ff515edbf054fcfbca18c763043f19315e44f6065822c4a4219f3d5b0964aa2322532e6dc946db0f62e114f05f350e3a111a0850fdcea0e17e31a01b29e33c037186588d2526cd91e0909cec863385e3dbf069fc18c7c0246422c698ae853e44e6c229eea4d0c9ed44b235e3c6ddd3b491efb789c9c58d83f0b695a398d3e128695edf76c73df8db4ffcafc42cda58cc6c1bd8a7b271ddc68678afaf66376d0034db7e5a2d7f1b5baae7691b7f505f3bfeeca39fc4933b9e693e686bf20298d301deb672b4c8c1e3312761502ee35bdcf95d354d20186358877aaf0e5e946d3c919ac90cd22bceb7c5503a887c44dfbca71b77f32d01224673f6b12d9e3d5b2974aa03e0b80f670857e9ea02fbcc762bd22c218680119412e8684c281994bf748a00d0b03dc64a8d4801a1ca22f1d5369eecb4f9adf4c447c1b2282664a04c771c3fa53830df65659667fb792ca9994692a876e3801b2a0914d0d9b87ce998e08dcb47e95a039cd3d33dfcd53f7d1677df1fcf700d5a66d58ca74e72069f9a29f1387c9efbd640d306826d6054f7affbb531b161afdb189db7e9293efbf9afe0b63beff5eaef090ce08dcb47b1744c9c41871b72a27abc97eb468e8284e42d81268ef3e33051b439fd232f42066df244c92e12231803a928808f97ced37879474c69bc503ee9cb83d0eb67db56021cef0098e20c73a659576bd46d92e542be44040b90919e61aa8a4a12f575472510a08a2884eca56d882cdedeef4678741a80112408492c84f2a9f4b8c4c922415f730272bfb45fc54966f30315f859bb46ce2e046f5cdec544c7124cdc19878e1cc57ffb93bfc4ce67f60c042b7bddb6e4aa41ae6d3fa86e73aa0ca96e3b88ed0d02b8b6b906c9319b7caa8aaddb77e08ffffa133871723ae5a048f301b5372eef62447829a3c9e3e29de2ac20e5639d321ef7c6d8950a6caa801510187bb7760a2a14a1548090af3062139e4139cec4e8fec9160b411758537b2bceac1369f02cdb4aa646009d5294cd69b13496a087042ab1b4a9cf17c3d541cef099588c8dd3bc57667682b8ae94d4a8ae311820df92852799d0eb66c0e696ac4dd4ffdc4fb0900a9cdc2d16428482fcca9d5e279de6d761387fed4b5bc90ebe70b28b2b177471fbfe1926b900149bb7edc41f7de86ff19f7ff16731396f5e62402e7de5979a3d6dd9b0110f3df0107afdc4e47971740478e1e52fc49af3ceedbbd6c62c6b7feeddbb1fbff5fbff13dbf7ec8bda482179e5822e2e9ce41be6ecbff075fa79b79623af06a2bf15d72634061582a4552605d4c0187d9b1fdbc09ae5e11ca9ffa6b871909214b394d0adaf9f055b4d8da8c27f4b3d76af62731cfe1c95ad6ee3b910a984098344c5b0a59a237d1fa889770cd6c68f10f0325513a1b3f1acd8f81d159faf80a19bc5408280caf431b9dc1245c704408d9cb6fcf3aa64b691245aff9100d98228c06bd83ecf8faf18c1fd076770acc7c1d2e8f6f93beec559abcfc07bdefe96d6006e5b1adaf9e9e969fcf53f7f0e9ffcd25dc3247fce8f37be621b7ee517ff4f8c8e8ece929c99e9cd4c4fe3837ffd0fb877fd460729aa7b98d3696ccbcf16e5246b7b4dac1aeccdfa6ad3879938bfe6513cae8de551056fc883448c192b92889f2696f3a896570c722ca16db968aaf685cc247b643b3c87b63ade1191fd966841ec2c21f9f926f1c7171ce18d8139d3314b06cf4c400a94a588a51ac0cbb486dd2a018ee1416c06dae307cd5e95b192984b4af57499cb9ca2f4788412d84ac3a60468c62ebe0bb9cd99a173b35758f4505eca8569f9f673f3ba0ccacbc662ea68eb276879d9fcbd666117174d76e03ffbedf32ab4a7f8d8a73e8ffff5f9aff42d05675bd21d3e7214b7dd791f7a006614e8293003454f91ce35af35cea169e7e7f83cb72faf7b65cc1944df1ee89abf8e363cd697eebc0f878f1cedd38ff56cd3f1ef3ff5aff8cc97ef6a6c1c61e6ff5e34d9c1350bbbc5e1eab166b6856d11a4d7d1df439dda05398ffcd0d23d8a87b5b371a4c49d810948065a7059be94b179254390e2e067716b851d1437462e6ccfd7515383edf4db00cfa5adf67700ec753d0b83b01e966cbc3ee6a4e24a156295938e479afe3adba2eb4e6078acc0331f537dd96a4e2326452d137c378d29484b40f8f70c0978d3bb963b332f3858ba934ca71220a60bdf6515ebe54c324de414d8cc6f6c2b96ba8289aee0c7568ca223a04f20c466eda1a327f0fb7ffe313cfce02389de0fda0fb2364fac7f02078e4d45b016d9ed8e2fb346dfff2b216295ddcd6ee7052d734a8445e91b2ecbd4d40b6b981f078e4de1d147d6f7816fbdfce5e31b77de833ffecb7fc48993d3b07d210fb3f2bcdc8fad18c54497ea3efbceef5037ffd9de69938fb1e91ca9699be0b6ef14be329089154aa9ac2651fa2eac52347d79063fef45cffbd437198a0cb42473d7dad71d5b8a5b97085a00552c826cc060f51cd84a157b3b0076331b3164b6848129cfacc30ce53408e985b9bf69ead10b1bde1b987332d4c561c0596893570209f4d532b6cf6ccbc922b02d17e3ce6518d8ef209a3f8436858b03da808b1345c368a519df7620ec74a0222db9610126c2c1c8ae52c95ebca88b172fea44e2f972b7d161cfe163f895dffd1f78e881878af934fde5d7a6fba7bf7217ab4b7e106f6fe7f90e27b73596abdaa7129d8892e2c1cc63047e67dbd85b016efdda378bbffa01aa3ef7f5dbbf895fff833fc3f193d3e07ce6f15fbca883172fea26019cb59636cabd2c7684d910bc5801b6cda11173950e9aced74ac39779ceaea9e0080fe4fd39ae981410b0d33f9e27423ee6e5202126a5689ef739b29588ecee00babbe472413b4d060bc6699bc1c2775a8b88129314e896a2bc7d50dade3325855092564609c51a658523cde73285a80c092f5d9baa124b562a10501aadb1be904f7c99582041680cce00af027d39a4345706651e223b2a96b796f266018160b20bbce7cc317409b019154415dbf71cc4fbfff46ff0d8238f910d187c62be9d3b77e13b9bb6f892d9aa1e1fc2e7aa6bb9ec47810de0d20454b654773f1499e8a6be9765e5e14b4c3dfe9dcd787adb8eb404ac1f8d5055dcf5cdfbf03b1ffa28f61f2e1f58e61f47284376a5b1e5fc11d6a0bce06fd8f4788d988b62695a567668892f270512efa31822eb6b79a139ac525e22f7b1329dc2a2ea63177cdc645fda37765bd5f35439f32cdb4a81dd1d40f65894c597e08b079f0d2e34a59d89ad2a0b8862687e888d9ecc35e417e79bd68ead97f4a1f7cc7e90c605c41de6234928ae08c3354c2aaa4b5ac744479aa75518be38604952dab817cc52ac33350da4a239cb7f2eaae0a2c90edeb06c24e6142eb84ddbef3cbd13bff9810f63e7d3db5232d79bf04f6edb850d4fef8490b3f3ddd13c7edfb25258b9f82f3ea3d674166b5b3daa6263f6b101d8c673d66bf38eddd8b27d574b350ebd366e7c12ffed837f83a79fd9eb36a51d510fb3372c1bc145939db0b9bb2becdd373ef21d58e1eb040acd0e434ed0b8a13448efd864761e94ecdbd8217c558841a56374e179a34f30697120b7d5470a498e09509fe7d6567b3a000e02984659de38252527db5ad314f78fd9d03209148b42491fb8d4048bb2e226287d14212abcf4fd6b47600827802490aa8de81053aa49064886e24668756758492af217d66872fbde9eb2ec316ebda4cc13c58944626afca3f61d11bc7dd528968d49db653f1edfba03bff4fe0fe099ad5bfb80ca7cfcf5afdf89e99ea63188eb905e7986789c248a1460775cc39756b5d55ff3b266808ad552846bc78c2a3effc5afb90e7c8808d63fbe01effb7ffe145b76eca60bf5f8c0b2b1c6869d5a86beef4f67308c2257b709b9e9ae5d0c5ab5ebef9a050c9bb7f9359a4ad4d87232c9551717632d2d71cff3c6705a9daf62e0d9b7d5b4000747001c54d569818c702567c6c280275af634f82b261c2caaaae8d2c7c59c0e317e2c5732e078900f653a5443fb963ffc88447fa0b3d009d88dca16d9c5ecc99f782fe0cb7a3359b38fe8d8e316a61e3f3ce21f53c8a2b8ad634d1e76396b4e076f3b63141f78720a3d5ee631b042f1d8966df8bfdfffc7b8e9bd3f898baeb89c5456cc9c3c89db1fb6df3ca4aa0e60e1fc49ac3c7d69bfad9ea543a1d8b67337f61f38e45b06cd7382c0bd8f6fc6b1a3c730316722f9facb5fbb0bbff7e71fc3f6dd7b4b618c1b32f1352a8d9e6f3b63146b26729114dfe7b0020570bcb94f51b1889278ce24c9af36425ae2b81bb5bcb6397cf432356d35283dd2404d7d1ed38d00c38ab99640b4f87276544e849d00d0463d3fa7961ec078ae6c0599863680b55f20c7153a110e363bd206b605b5c4230569dd6bd6a8002a2359582c9d326333e021ae31303910b96d2ba4240705b9c941919e4532e8707069f4f20d790d31c49ceab19681ae7696313e2e00aeb28d4948c5623a05a7ea667617085ebfac8b2fedede0a143bd3c79e96621b0fea99df8b53ffaff70e3cfbe152f7ee98bdd1ef7deff309ed9d37c8366b8ac19bfd311bcedcdafc5bacb2e46a7a621dfe7a3a78a7b1f5a8fdffbd0df2610b520dfbd773feebef741bcfcba17fb72f7b35ffc1afedf0fff0376ef3f14caabbd7463404470e9fc0e5ebfac5b15d15cf4389602539ac48a0f1a70c1e5640ec0e065540a6b8b2f46112a34beccf3ca18c91cfd10af49647b9dd203915b9ac040fce1ecb82bad6eefd0e979602be03804fb47a0d805d1e3fe953552f6a0ea645732940741019f183e254dfc154758cbfcd84322f4166222e5bddda9342b3b4a23aaa6cd2db0fdb3d80f20880c849420c68dd1d22e47ba632542cf7d590b3b57aa4d637441fe7ea2ecd0607b0690612bfab44db29392bdb98202c0d2b10edeb16a1437ae3f916d9b2a61d376e3aebdb8e90ffe02bf72620aaf7ee5f550008f6f7e1a878f9f08d90824f71d38843ff9cb8fe3977feea771fd8b2e6f61adf0f7a1661482b6073beb6bf503ac76eeee071ec59ffcd5c7b177ffc104f8761c9b3a89f51bb7e08697bd08333333f8e4e7be82dffb9f1fc189e969f857ea0a15dcea01c877ac1ac5d2b10ed4e3b02a3094dccd752b72d1d61f1b217ba3f24f4e4c7a48536211c445cf429c478de26c7b4f9447e2bd10f0648f08d5fb6316f34a73535c130bb26ef1680c9e2fb63a2ec0aeeeafbf7a9d02f2d310591ca0801096008ad9826dae0706c4069bc966c192831349d0f4ed8520814d11e2b1e1b0003c7372f46b80233df16ef29559e2bbafd4cfa75bf6214db02808814dd8c73f030929cb47b397c94040472ef39883102817c0b661dd36126352b09f312ed878b487278f1506a66413f28540706c6606b7dd791fba278ee1c2f3cec6cd7ff821ec3f7a1c759535df1d397a0c5fb9f35e2c3b6d31ce5db33afb170156767e1858b1bfdbf69e440433bd1ebe72d77df8cffffd16ecd97fd0754c763300debc05afbde1c5f8fd0ffe35fefce3b762bad7330b35f6564494d03764be6249176f5f358aaedb3f52d01e97119f8b750aa779ac92add8f6a0719c3c513110b50866c6425d2dde940abb54d7cabf796111f2f81e3283a16959c020d80d228711e3f5e9f0dcdb6a97007f24d337bf7744807b005c6609ef0fb1f91c7941a364bc4a12b220bd465d29b9324462c5d729dbfcccb0b214364a30839631534d21b094d00b9004c41c180d86f0ed5e7b44c306d0227771a233c1d080f50bb4d2c061024106b1247da9b0007c896a41b6fe480f3ff7c8711c9c495bdaad870218eb76f1da2bcec7adf7ac6ff6bf20449efbc7983b671c3ff8f26bf17fbcf1353877cdaa56e684629f41ac69b673db76eec6273ef7657cfcd6db70e8f0d1c430ace8505a4300ac5db91c9bb73f839ef9a7c48f47a996082afde777813fbd6402174e76c91e0689390638e65aed6881875c90638c903f80961e53905a9bb0a525b7ad0838b453646b5e46455def8fe59c11d4266d5700a2423958e9fc9cdb4a1e54e06a0180e99bdffb19003f64053c963ab1b4f24f8cd2d2d040a419932677c80c456259651b6f71dec08d13d4501b08c46514af95b76ad5d8c7a86ce51c135b620e039d1a68181ad9b8767852381e111f18e0f87efdbc43091636591b81af81bf19ea962d53f8cba74f227dcc305cd477b88e847c3910fbfb9db9f274bced4dafc50fffc075983b319e000766d301efdb1897aae2c4d4497cf5aefbf0e18f7f068f6f6c9e07e3b28364df96f7d4361525a1f700ba007e7ad5287e6ecd18a2e002ac65555f4b5ff26982742ee5e82f567d4534008616fc6554a2042d005317e22427e7604b8fac4b5540d1ee9b9a583c5f6c258a5bbbbff6c1378c94e137da3c5aca95b2d741092881fe2645d3360469f4ab844438a7cf00b5b9133830da82fa83460e430560967612b3c75d53de91623d0d88f2b90057b891d906bcd7d0f06dab4408e0249fa46f635040bd0fd2a4113611d639411bc7fec4ca51dc7f6806f71decf93449de24bb6574593a83aa33a34105f85bb7edc47fffe05fe35fbf76175ef7f297e0a5575f86552b96a13eda967cf5b177ff417cf38147f1af5fbd0b5fbef3de24a0af7ac976e2e78d4d45416ade07939214714ddfcb1774f0132b475d3116a94e27b387b8a163194511dce7f31a24d2cd21213b38a08acb6f927a11b63cab563c20172589eb17b6944a74dce4b6f9819098b73972d43c6f6c250d46d94fdb6eb2c162e32c2747da242bd1ec134b2d024d464c2b98d1000022e3c652cc008253b8856900792fc6e7c9ef6d421bd786b4b6ce38e8cbc8788948562ce06689159b98196cac5dc82b4ca5a4325fa66d6e1f5f86b7b45b380abc6fed38fee323c7f1cc54ec2bba39138291150bb2fba67b69637dc4278fb1ee7ff8713cb47e0326e7fe23aebcf40578f54bd7e1dc35abb060fe3ccc9f9c57b12fe0d88913387cf8280e1e3e82cd4f6dc757efba0f77ddf7300e1d398a29fb6e2a217b97c2e82946b2c75684520c3546140e2a32e1d251c1fbd68e63d1a825b2070a1c24c8fccdd9ec634b04938d8b95fb54223e1a9654e4942836aa31589d035cb892df2d0e786ef3627a405b7d11c4cb3597c9f75ad5f790023a08dc58dfe793ad44360101589bd5335a7c62de10b7655c2ce90241cde0a0b61eb48e0c612c09897d8ddbb7792b8cd1544dcd50643d63029e5c6a9be95af5c9a2a60a2e59961803ec5602dd7c87a5ae9c71926ce467fbf7757c082e6935e8369110723b1a0267cf11bc63f5287e6fd314b49ab24f4feb56fe893c27ae9beede860f4480999919ec3f74185fbee35e7cfdee07b064d1024cce9b837973e660fee45c2c3b6d3100e0993dfb71f8c8511c39760c478e1ec39e7d0771727a9aac62412aee472f99b68f2ab5f52a25dc9ea63425088077ac1ec5d9736829a5f0f93cde15b04d6b11045b63d03783515cba3f53bc7156d07e9a0428d8fb54a76c0acd3af347d462c4fed7b1b2b17c68e6b31b5b6e2b099902dc153ce9f3d15600360301584f09701882c9045a6502435740c2d825b98d7ab203da96057c1b9fa424a0c84ee02595c721aa43f805efc9f8c4d144eb6e01baf17c0bcf40815197157b4b549be7a9bf5f9bf7ecd2797a8d649e486921e7c382c7fa5125ee42f0e3678ce2eefd33f8eabe99d23b04e7c4a0a2981e2e8efa56bf6eabaccde8d33333d8b5671f76da97e3f15c344f9f66b5afa89f495c93cdccf2eb7e06aee18beb1777f1d633466972a95c6c3a567b3935badb0c7d0168c3e43848642fed0b1a304be56f0c8871335e0694d90ec9ff403880cd27426f2075da3cbf6c051c56e02900b0af59dcadd0a76c7e1b50ed1b12a26311591d5d038035234fd28836cb81d44e19ce5d70338cfa18ccf4d4d68894b026a7a37d99c328343fb6e00676859bf90d1b8bf6f9726df0e4108d793c5082be37e697aa25bc829861c22c81d25108d9b63c97d9ab99f17d6bc770f164071e49ec1685df3636e00a8cd4b0a7d94ecd96481f5235dbfbb37b65fba03997e5147aad28f31380db32de87a6b9e1cfb8256bc007b2916d4944725c3cd9c1fbd68ea54215bf2c4c06b1d82aa7cd8e1c8fe635efa53482c759a5b30752c4961da60b4bd1b04d8dea60362ba81fcf06f24c3c069fb7f7117f21a392fc92a578deda0a4f89ea6e20006b2f44b605fa7878c5e42660a1f076d557cb4e85b2704dbfd88c73b6a1067ce26d92e2fc99344af6b49f24344f9933723e00c9e466636999df8d9c8284fa5781c07dc355560d885dc06a08aa20a4b71277436c4a0764b32b8da6d508ec67f3c8f2f10e7ef1159761e9a2856ea33005436893200af309dd94a0bd069424322056a07c6015511d7cf9af643724e9fd1d153f978fedc77357953fdd46cd5f1702f89602b074f102fce22b2ec3f2f14e023681ed979807f8bdc9e28a476134e929ff9bbb5dd5ad79760f221e18c32d657d899fa429f9c61b77d5ab7a49e8e709cb9bf7391f6d0c2eec4df8c74da9e7adad806d2acdf7f619601d14e0a9a6d289cb2e64c6263623495325551388262f4064fb40aea7c001a1017145dbc395f921024dc0e476424910ad813064e2659f8f658e23207530757392c595df17d96b1452dee2ac812b86b5544d3b184255452c694bf8d2fedf60b9b26dae7cf90df8a577fd24ba9dea518c3a8635825849b67ac4bae24991b9be5a0dedaf72da04a6254b57fed53245d29080d772229f07ba5dc12fbdeb2771e5cb6f70a99407d52c37dbb8b663631b4a6237b9963d4cf2228dcb333008d77e77d8ae939d64a96f2c35530589f0216045ac192bdf11a72934c648033cbf6df594345fd2d00056f7a65b7a001e089082477793ec86bee213aaed63d998beaf65b84141c5bc10b4e96e7324ecd3befdaefc6d0e940a96641200e415441a238933294a2c677fb18470b9dc681286255b004a4ba10c5a7ef785b10d486fa24a5115e2277f3dbaec4e929460137fededc807fe85fd63e3906567e055d7adc34fbff9b518e97432d622e6acf70fb5e8e4d6d626f27dd95ad93d87ac8d6eb625395d37eea1dec352cd650062a94823fb9d63b6aa6ae34f558c743af8e937bf16afba6e1d64d919d0b1f102ca017b56e523044ab5827a8d8b2c923e91a5c4ba15b9d2b3d63ef5495ab87934cde3bed5a2bb661d6d7c7bd13064328edbb0c866716fd625fa627115c389cbf37cb4158007ba377db00704c3028007b524b9ed2179ade54f67a3808396db96c436120213fde39ac14bb10035728e903129d8c552d6a83f028d7929c846738339d0963eb6e701b3640b18c2faf2f9a68fef35000136627b6e69978a543047c5f5d68dc940e0f0b220335a98f76d69114b2759b21c98330f9d4e07effc0f3f8ad75d7f0d7d8099c2a4cc634cd5cd2095015d1409b62734840738d25fb37dd8b68995b00b2764a8caf27901b15984d2ac141b2b4c9d4e07afbbfe1abcf33ffc283a9d0e30675e630bb327c4e734d1fc99a112877e778a054b25a76c6380e3ccf2806c52c7200de7f90db347d1daed5f27b265be818d6d1448df3c69df35a2de63d49f6ab7f8958a513d4f6da5aa0fda6906acf5024c0b8dc293a928d40d576d0137d9ed490e448265b65436f1351e1b08658bd01a8688bd12817d7d7138dc00304051ad42f0dc1a4e54f0eb10cbdac51d3b4a273aef73a9ddf56c408af7eb0c4c9c72a7aa612e8b79636fafe9ebfb186aced62c2c8179ec0569d866d15260622e00607c7c0cffe9dd6fc70deb2ecbf291cd33c38aa06356ec4fa0972aea1bc140f6bd7b425c6f16df36f0d361b5d0b44bcc3ab783e6e50d1577dcb0ee32fca777bf1d13f61cd8c4dcc616d1d57d15fd380e7d3716fc7dff8d1c121329cb57ec40f1627161bed5741e1e1b4d1f6d7d6d99cc32b87cc956541855b32fab737e490068149fdaeecf375ba1f9aebef5360e03d67e001be3390966481ce4e2e5a079e08f8d48155024f79540fc0c77394803e51bcb09c9de6ce669000c6321e0121b2c89d1032db253c54eb7c819b8f289c40a484d4a40217b15879425ac01a894311591b44d50305bac92bbc86d4cc5970b926508bb17eb4e2e00ba233ef6a285f3f1eb37be1b575e727e49169489caf88418feb84a898120d5b5ad040697f1888bb58de1bd0d0de3d51a9c9c0190e62a4b1adfe7b4b14a080a622be1ca4bcec7afdff86e2c5a383fe4ec8e00930bbcca37e6345f4541c97b26c8edbd0dab57a059823d980fcd40f6f0261164f7274c86c20cad9d2dd1c57413da3a7150b3b8e27dc658f879e1a3786fe4a41b341e5fa1b31797e7a1ad006c94069b00106069f30bd077c34589e475c10c70aa4d62339f7fd4c345058236c63509fe1fed2830fd089485dfd113eaef7a19100ae2db40735f5189877695f4b389083cd4e793923ceaf38484845a617fd8dd1f7fedb2910b8bfc1ab3834e7b7845281a7051456549248a994c2e2446d85c9f3f6f2e7ef7a65fc455975ce000589e2f68e6b2a8f5791c62602c32429541ab585d6c8334072837e307a94d605ef2994ffd5b0a4c84228f07bad9a854b2ab2eb900bf7bd32f62febcb9488748630b902ef427ee6a2b4fe4a060c01255411dc88bd625a92da64c1702e1649590ddfda711011e539e7f76495dff502def40d95c3681f92724221bd05cea9f61b382f0bcb4d5dd00a6ec7487ae4fa9e83d10f43c49fa8c625a585a354a3b9029b52667b00122dded3c555a895aa0a94f289934aacd68f64fc6cb0067cb8a7eba5ada6a0667031e071524dcc84e36bd7959e3e34a54b76c99bec3dd4cc5225fb179f3150580790b5c27be89b068c1247efb7d3f87ebae7e61ea6595b65f9accb1b304b99d2d0b13fd03854035830319fbd7db1163cbe53a256d0782ebae7e217efb7d3f87450b26c17b6c7ecc5be053985b588e04127d6bd53a062bd020f1b24daafd1f9b9ce428a3a5f1f2bcb11689fc6933562cb1eaa1f27b4500154362069fe79dada03d85de03683f60756fba0550795080a321010317256d5fb069ba6ed4af56d5ea403c6b94c180560874ced6e1b48fe2c0197b3de6040f5c2546c3727812b3d9a821bda99ff1ca1df82099a47649cc9fdddc9aad7d47ba2386b09b924cfcda00ab4e6055c5b2d316e3376e7c375e7ad5a59512e26d421075504f75c32b6fccdd0fcf2c3b31b7d48aec412f04ea209a6a5e659b975d75297ee3c67763e99245315f9db5c5164d0229c557ec1b850061032ebcb90d526cb9d41aab0b40ab67a27201cb3ff2a1ade7fbc0cac6700ae21c294ce3ff508c5440cffbcab18940bc469f67b6028e0ae4c1ee4d1ff473bc870511dcabc0c1a8ee489b618948a4c417d09b4a401e43f29e8f96b095d8efc98ac51a5f6c81aea5f69732c000159be179b9617b43264f183314775d15e03d1d37ac2fb3fc1f385b43d9b78ac1dd5ce41a709226204bd494e5a811ac6131c1f28281f5a4838717e9ecb1670000200049444154aec58eb185d1ba669900162e98c41ffc971bf1faebd761a4dba9aa2a332319c0822298d5e5c865a91893d22ae68870e7fd4e14738a7f09a2b33ff5b70080916e07afbf7e1d7effbfdc88450be7f72d7f19a4778c2dc4c30bd762c6b9a27bcb65a07b5964e3c653cdb642304867ad7e2ee234529a0baa0dabfe5f4ee9b8894191dfd88e1f99a93e2cef72fb4a42fd7cc366c4e7adf7b4f8b0ef0ff33812f6d1736f2b400e02b897654e800560bf008f7a2e5be05428cc8cc751d482b40a70a7be053982c014b135ec1d2ed598d51cca6069fe61478b947b01121fcda1a48f8754ddd47117d2f0c2d99e92ede275869b329fa560cd201c80cbfcfc49656d1c6fe0989941d32e6f07943d370d80b0a7e4a1c0e19109fccbeaebf18717fe38b64f99bdfa90c68f4e47f0abbff04efcfc4ffd28164dce6bec2d96ecc452c312f1afefbf154bd1231eb633e84fbc13861717e583f14dc88ce22e49b8b970721e7efea77e14bffa0bef44b79b439797bfa6fff629e00f2ffc71fccbeaeb7164648e0be03556e14b756330b17b601bce2177dc450ec2c385d195a1b12079afc726b6f6b1e51040ed8f3870b086134a6cf1222e4044539fb26fc4fea264f35e54111afd5d80e7d856785469c31da800ab7bd32d50c59d7637ae118e92ab4828289bd825d079634628914c3adf3cf660376d69d1907c539097968dfed740426287c5752f468358dba69d171caa40be765694cf4c3a6ab95c2193cd2bb067adfcbc2d9b0245c31eb4bf67c06f0e7797d0e7e51a4c12af741c34264378bbd1ffe8c8043e74ee9bf0f1b35e8143a373b1732a27ae1d5ad972ee9c09bced475e8fdfbaf15d386dc1fc74cdcc107a865f1873581cd3adda850c1032d3c4953c8ea21a3b2fa1972e98c46fddf82ebced475e8fb9732652ff41b7e8774e090e8fcec5c7cf7a053e74ee9b70a43b4e392f31310bc2b2bb1df239bb8320ec389847ccc7d18fbc00fbc1dacc24aa39932510edc2d8f1c20aa39380fe23ce69f299cf64e14ae3e4d99f7d5b159272e7c84db724596a860511bd439b5fa8f0243566926ffb9be10b55d4b89768b37a925466f4e51d1b456def2212aef9cfe04e13e309b0d2bc5c7404a3164a86517b6fc1a374f790ba2ac048122c4f7c20673e304628f0af6b31f32bc809d6269219084055847eae8734a3674736efa6a58bbf3dfbb5b86be9c538d9697ea56de3541733d333a88fb6e561a7d3c1cb5e74056ef9afbf8c6bafbca44c60cb94d025c59aaa7f6cab61b12506545c3fb5a7e3c91556d9d312857532e0f6f543b4b9f6ca4b70cb7ffd65bc74dde5cd43a1d4cff4a9c179667a069ba6ba0014d39d11dcb5f462fcedd9afc5b4746116e5bd1e0615af95ec97081e0e2d4a3baad0e58adbd1e78ad8319bb9fe3e66b069efa74885d156389197421fba8f6b9e666a7128e59254baf950b0e74612c83e17b6121c17c11da88e3ec052c87a48f3dd33b64428342acdac0460b1ec6bda6bb9659e0229ed3104b258724a5286b33caaad7d5837ec5a9cae48cbce7a7b3bd8a926e3199b0a2aaa55fbb07db0a118c3f7d6d0e8de303f755d8311527b46554a4ea1f184bc6c401ef8d5c83b2d1d7c61c535b8edf4aba0d2f1ebeb0f4e63df89e9981f19e4c20771fddcb3cfc4efdcf40bf8a577be152b962d49ec2a315417b2f8ca977d8d02fecc4ff1bd5017f7b058bda305b3dbd0da35e7ce587a1a7ee99d6fc5efdcf40b38f7ec33bd88855ffb5fdb3ec9be13d378ece074d8513ab8edf4abf08515d714d092f003d93702c0d22b3fd2c19f29b66f7c21299a182980646c5c104bfc88618d62eca244bae76266b965853c0b1df11140c07bccc6da6dcd266627923d7c14990858883efbb692e6fbaffc81513bfa1916b0454ac34812a507442d294d37ab02b6e36f7b48c560b0bf34831b02f0070fc5d0df14a68d3ceb592c60ce31ece08d3bb5f96d3a1ad38b929059152e3f0c60550d1161cea24241f23320d3b9aad2f96bd30948c0a77ecd8c42fe1050c58507ee86c955f8c49937a0d7e92630daa9e3d8b4731f55c4b87961c0d5b6513d313e86b7bef975f8c06fde88975e7131e64c8c3b1859705bb1497766693c386bc83e56135bb972737cd15f0126c6c7f0d22b2ec6077ef346bcf5cdafc3c4f818d95dd3dffa303d37efdc875d3a4efd805ea7834f9c7903364cae326961f11a82898fc33ab09c5c4c81483a6f476385dde21104789c44af882720c515e2f11203ba044cee93fc3e5bda484228623843652cb12d5b35c5bed3b36b2b40d603ba05d5d1ad4ffce66ddfeafdfa2bd72d13c10fc176ec8078ead9d98805582079664d81c8b5e256475225376435d68408be9823aa7d53c18971158697588dbfe694b05bbaf08f1a7971b12f2d8b497235314f1be869c818952f74692a1b39334aa7333355d6b90467b5e4b6afb765b0fdd8d9afc1e3f3cf2c22550877e238d6ad98874eb7dbc73c925ddda6f1dfe2450bf083afb816e7ac5e81dd7bf662c7ee7d300b1abbb3fdb4901da47bdc614679525d446253dd1a97cfa7fab2a5b4bfeaa2f3f07fbde3c7f19eb7bf054b162d44a7d319a883cf5881706f7a1a1f7f621fb6f4c62ccd5cbe13dd714c7746b06ef763ce2a1a5773ea86c6292e14c9cf1e3d1e477117bc49ce72cd3b64d95374713cd95b2eae12f3d91d76aa131ecf1c5716bfe123ba8b0e5728f472a66c316c15d5c0ee59b415f02723377df04e54c7487da211426f03705ca01390481dfb989f83a6c033d136c9f9ee80f98393baca5b44721b08ab1b3804d234061bcbc6b7b1ea7d0d4f166d0c1d5f171b773c1a06407b07c9c80e31e5ffe4bc32943d1be640eefda39e385c4ba8aedc96ec11bd6b5b35b33c3eff4cdcb7f87ceaa480839ae2fee3e3b87fc7215cbd7a712b48b1cdeae521d0ec6dbdeaba17e1152fbd0677ddf3003efaa92fe29b0f3c86a999190254fb53ef3d92161af6f5d868898fb1912ed65d7e11def6c657e3c5575f9e408a8f3a06da5e8b08eedf7108f79d98f08475eb9564ba6ff1f9787cc199b8e0d056b7af175197cda1cee5649e63ef9bad00ba8923ac9a4671335b93cff891819c3b0554d2bcf5b572d60d5e6fb81333ab5830879ac185bdf717aa51d039089f1d5b1d57c56d68395a010b821d027950a12f1213cd03ce82d1a7812326f1457687555df419461df91becd3f0674977aec0b6b91e6eb6240521a955ed0c9e0c26b1095fce5920b1785e0529a921b0658d98b38d3098d92b5015f7b0c6d87e0d7d7dfa68386d901ae07ff9f42b7178644e040b050d001c45179fda7204979d3e89b1b131cc760c622bdd6e17d7aebb12975d7221b63cbd03b77ee1abf8d6c38fe3c9a77760ca37f60dacad7f2413db901c0b00181b1dc19a552b70cda517e0877ee0069cb56a0526eb8fd7cc226bdbeb935353f8d49623388a092454a4183c3c32075f3efd4a5c7068ab6b1046464e50d7ab1f3059a588df012004ebef06015967a05ed1af9fc557962159ed4694e963b967be01fcf3c01c7b340a8bc099fc6cd84a81074574075a8e56c012c84105ee149517f10760fd659d549ef8124a28073f19cc01cd3199e82d535d2dc4a12cfd0c53248ca6d07836c4a88b16f9924225a5944d6f531449341b358369b6b668be0e7700d73544041056e783ac90a8ba5d8e8a6567a7a58b6f9d7651a0a58376d9ec0600117cfbe438beb969175e7afe4a08dd591b960cfcd7ae8908e6cd9d838b2f588b8b2f588bfdfb0fe0fe471ad0faec17bf861d7b0f62eae4344ece4c437bc13ec95c908e60b43b82b1d111ac58b200af7ff5f558b36a05aeb8e4022c5ab43031bf3e165dbdafcf279955f1ad4d3bb1fee4b8cb1185d377610001be75da45f8990d9fc1a8cea0de114efe43663b6d4c1fdecc62a11431a9e299f2a09e275c9d4bbd8f1755ab2ac65ace21c9585e8470e0e560e89ad420c48a1fa091885dc1b3622b01ee44f3d068dfd11f09e598b9f9bd3f0cc5272018498a26319594e1442340318ad417742d74b56a17d7d42b7742ef326f54146d91c3d40c1645bb68fde161c99fe41f622d9f9311a9f626a9ec4dac52397de4ad823ef10d241f5d78367ee3b29fad06aff40400159c397310bf7ac5229cb66431da4060185059bb618788a0d7ebe1f1c737e0b12736e1d0d163383e7512277b4dbfd18e60626c14f3e7cec145e79f830b2e38179d4e675610aa5ff3759bb7ede6c1bebdfbf0fefbf761cbc802b70dfb379860e3dbdf78f02f70d1c127299ad5bffa2d0022ea82bde80fb1ccd8895083b710ecb4bf4afb034afd494e02bcfc2b567d78e46f54a96c4ad55873f4e7dcc8ed20b45d22758fef9bada655f1bf8ffcda073f8d96a37d49d88c711ba07b05582e7db3960c724355962b09ec402095a318a225828a3f81508a891b37e6d1666ca3d7a0c025ffd795278b1a891d460fd9d23e9c33c80c10ceae52525922f9db5cb5c04918805863be5b88c01302f4207860f1f934381f346e91616b773e3eb6e1307e66ee3ccc9988a5611b58b49d1fd40e68e432d0b8f0c2f371e185e7a76b6d6033b0e2b6cc37680fab7e6de31d3b761c1fdb70085bba0d584594892769ccdf24cdfd8bcfc30b0e3e596e956b041d2c3aca16b79b36df7bf318aa92dc424c693c979bed934287f5b1967df8116d106d18e0022468702b4685319b0c39504b1e245b95264456026cbf3fb65260af48fbfe15d0f258831d2337dd7254446eedafe200ef97b838c4aa4c1eea906e5fa62b0af80f8bd24397f664ac543d6a67785f10d29bb4e413bb9629ac0dd846a1280814b02488eac416097bf4817b011da6c1a91be9e8644be30df739d61dc7c6c995704548731e300a85e08ea363f8c2235bd09b8e67b3186cf8ef6c8caa5129cb55b39cb625dd30b6c4f30e9abf96ad6e3f73f224bef4c893f8c69171af4e2441020d4ef48d93ab70bc3b6ead3220a2c55f407c76d27ce589492cc5064c4e6aa2a79444b45db579b5f267ac2a6c4e6b43be681b8ccf3950b1dcd9de628397cefc21653ebe9fb612e0d6ee4db71ced1bac1c0301ab0cf129553dea62116b291224619a408990e0f8f3e7aafc0459dff38b9ee29578bc018af87c20831519d49e3f6124d1324f13f0342f2f2155c9b00508681e0f336b67c9821c246efb228626b414182bb3b15258a64034856d9cb87ebc3b8e3de38541e44f1d2340b38064f9df948ce01f0e4fe2f687372487b42d0943977e1069038db66564dd67d09e19b76bbb569f6f9bd77cf8cd879ec0df1d9a8fa94ed76a4bb1885bb3d83683d79ef10538d6a567b51c50c80f6a4f61c7f5a68d225c49a92ff1de0b01eb02c45deba4aa455a2e85d6d0f3222db5ac5880d24223ff237d605021427ad6f909ba29500cc9919aa5fadedb0acd37c57c0a438ea18025c0ed22b239ec140a5922257067726146d5341ebde947e460a9d5630d8582545b91e41429c121d11ef1b8444385e37ce821f49e96670968cc98b4a70503cb7089d867a6ec29ff849c1a32c06eebd21298d492fc0f8ca5cd48177be62dc5feb1f2b93f4779e59efe57101bf8c73182ffb17f21ee59bf19bde9e93e76d44c3378a9666dedbfba4f1bc8b48161cdb6f86f1bf0d5f3d77f7bd3d378f0b18df8d3fd0b7154468ba90894d52c61416b3e69deef1f9d8fbd7397a007db96af973ee2c5a64474d4d78656c09eea0f570bf9ba6d7eeb5f8a52300b581c09cc1ee27af83cc93e014264a912d6fdd5344aad785ca57a578a9d3d7a601208f2c3c3df2f5b2974b3426fc79063e01e56397600b81da217b3310258d8d874278f1e1e33a764f08aa7d4634024cb73250084b6cc149ce9b6f9574d88a85626823d9cc615aab968eb792d65472165a33194b5472c9aa1ca6ba1fb950656066c6570af1e45b4a8559cf866a310aa271d1c9d98c4c1b98bf1e4f20bb069c5c5d8dfebe0e8cc04c960289cef589aee7c77f5848ce243bb66f08eb167f0a27396a7679dea65dda92e116bb01ac4a8065de33186b2a896a3373383fb36efc45fec1ac3b1ee18507c560d5e0124e01e50c5d191097c72dd4fe1ec63bb70eeb647b06acf464c1e3b803953473c5d6bbb9abdd97fac675f187a338aa37221dd10d0e81f8ca37f1e3e1c63524eda352eef9107ccb69298427b5629bf6b1b94de6ed72898ff6e5b296e1791d6c719b226438e999bdffb43507c12c008dfd9b25f30191a6c88757208d732a512ad24a5daee04c1e705fc61391ed72a87c6380e3a4a7716bdd250f7444fd959066a266f3ceb65ba05001b60b739ac015b4d725ba50154042746e760ebd273b1e18c4bb17dc91aec59b0022747462110f4766fc783274603307dac0c5a1c7836915d9b8f69bcfdf469bcfc05abc23e6c5fb27ded87b6bb78c336d8879d1b764770b66b0070d7b7b7e22f768c609f8c85dbfb928cec6d4e56328b28d62c5e80c5739b65e1f8d4512c3bb00d2bf76cc279db1ec6197bb76064660ae29dd8d6bc614c375ac036d1e272ba4fa9115f5564e422e767f962ff5d7545c45abd619fedd8d823839c5088da5cd539123288411944e9c65135d7776d2bc534046feede74cbad1872ccc6b0a0c0fd227818d02bac2a18e3f18fc2800d16aca26912ac86bfed2d120dc43cda91186ea7fef7611c8320218704c8317c343d03781b166b60554434911def38084806aa8cc96a1e7255a513b823218219e9e099452bb165f905d8b0f2123c7dda5a4c8d8c6510b2d733d30046c22e3e56963172c5660f46790823b8654707078e6ec4eb2e3d0be363a36eebb6e5daa0e55e9b8f6a90e3366de3dbfb1a9cb2df734c4c9f3c89af3cbc097f7e6011a6d3f365367f598a50c1b0bbace4027770afd7f3a0383136174f2d3d0f5b979e8bbb2f7c0d264e1cc199cf7c076bb73f8235cf3c81c58776399cd87ccdd26750dc32f834efadc8d7baf108d666102305888569cd826c37aa3f965d027b3ca88c643fd5975609165b1eff318622aef57fd9659daba7682bc1c300eeef53b83a66052c51ec50d15b457185818c7db966e08fadbde11a055007d3b2074b416095aa11b9cdc33d2d7d4cf1c62ab667e1eb6d672c16a4713bd7d0a80906c497a455e337c2d3de8c488c695190b83427740086b5f7fd08b1e4014e76c7b06f7219b62e3b0f8f9d7935f62c588163e3f39abd1466aeec0815a037d3b77ce4244fcf94313abb5e8d703d08feeee024b63eb0133f76de429cbe787e1f636a03a536301b063c8336d50731b3b604e576fbf61fc4271edf83db8e2ec4b47400f4b70fa01abea4b4d8e8ce9c04640e9d8f22707c7c1e9e58751936acbc14734f1cc2e9fb9ec2855befc1aa3d9bb0e8c81e747bf6b43f057cdf3cfd4cdb8b2d2f09abd266e30673ac6ce56ac4768515ec3e313cdf24c006f16c55064849e39bbca9d854d75c5a5a65d4f99c968aedb6ba15cd16d4d0638037f3317df37b2e16c803808e7837622f6d420ca4f9158ded5b7c57ed6d29565780a64d5d2540e3d18376bc24748bdafc40505f9683ce59ff32be2dbba27033edadf7939a61663a23383e3a071b575e8a2756be10db179f8543731737635292d4b6633bf4b66dc2833abfff5a91359b51826571a9a5e220dac379a353f899b3c7b0f68ca5adeca7cf17680796d9641f34a6bd1e3a6faf876ddb76e22f369fc0c3d373d113be5714cb165f1283fce2e969cc856b8ee29c391d2c58bc24d80ac589c28a4cbe2bb9e8f06e9cb177332e78fa419cbd733dc6a78fa33b3343852df63623ae221ea3a05a740413cf2e62c5d0af159f8f64c8f3905e61d3dca60940631f5cf058189498895c883dd20a0f1246c7aa2ab00264670114d32ab87ce4a65b1e1d1858e598956115d91f57e036405e8bc267782905c906aa1316ac942779716263c5aa3d311202871adf02b46d2caef2e4908a19d8f2af90b0c6f462ccaabcaf1d6aff16b5e3839c56d7da6f279f189d8b0d675c8c0d2b2fc5a6d32fc2d189f9a98dc505230e134e03b2c67ebd642707181f8faa18a27f5cb6eba57048074f4c4fe0b7bf7d1c6f39b803af5c7b1a26c6c6862e477833fe544028c959c93edb12504470f2c409dcb561073eb2ad87dda393ec747850b85aea050e5ee0c2ce6c02b37b67663a4a45d259aa3d9b00c4fdf39761ffe4523c76d63a4c1edb8fb53b1ec5b9db1fc1393b1ec5f8f40938c841ea212b1d4bda6bf82bbe408fd9328328d2793256df4bde9ef19e640025fd5cd7245ac48bcf2fc6d5d8aa00ff2a770ee7ca01e42fdacbbb0dc0e33885e394004b8169013ea28a1b2098681434c306024ba918b543225aec9f40e6d662cd190ba4188d35bcfafbe444ef599d558d6be2a4d85b823ece90400e51d1621d1e43ba3c0e2e8a99ce08762c3e0bdf5e7d051e3deb1a1c199f8f5ea7eba3a5aac55fb1520159f38201d322961af6532bee5dde71c92bd7a9cfa1ce38fe6a97e2eebd3bf1636be6e2e255a7f5b1aa1ac486b1ac53b96ec7207054556c7d7a273ebee910be35b3002747bb247f30127b87523658f57aaba126f33558a671803099b499b8397168ce423c78ceb57864cd3a4c1e3f884b36df8d0b9e7e00cbf73f85aece5014e6a51ac74edb5e56cea11a782996d16e6782f31ca774dec3c907a6f8408eb181eeace3152db64aab99e41128e4b8083e02607ac00c6dd3cd7eccdcfcde1500be01602d100cb01945834a1681135b459d63f4c4afb7cdd114ac030e163630b306674a4ee7c948d6beaf4ab2661548314b240bf97cde3f537c88e0d8d83cec58b206f79d7b1d9e5eba1647c6e723e833015fb550c9cb08f4fdf576db36e3019d1ce8a320a66d20c6edeaa582459262014ee20da7f5f0f2354bb064720ea433fb9dc0a17b45d41e40e58bf6718f1c3e827b366ec7dfef9bc0339880ce323690f848a34f61081c76768993e6dcb91d4c2e5e9c1903056c955c1467d57ea1f58362f2f801ac7e6603aed8f80dacd8b7151353478ccea4faed8cbc1c1e572e072fe749eeea754504c996948ba4338707b321df72f1af5cb6f6b4d241ee9ba299e721c3dbb8250292fea2d8a882978ddc74cbacfb576cea533aa66f7ecf0744f11f9194e75bf87047a6bd937a9d54cddabfdf5514d460420d7848ba1e5f2ea8fe111ddf4a53840c4666cabf517981e4199431fb22421c41ecf356910c8ae3a373f0d039d7e2b133afc2f62567c7b780da5088c73b20928737fd5a12ac7eadbb9ec20327c7c1fc10883bb63e5ad1219b39fa0468866c694f448173ba27f0fae55ddc70de7274bbf97b1e4f652fab6e370cd4fcdacc0c1e79e2497c72670f0ff6e6978d754e46ee9f5963ec5f55bc839788a95034c7390be760e1bc4944d6060078213594517e0db43832249366b9b972ef665cb4f51ebc70d35d189f3e4e92f7778a25246db6fb14b4ede000c16807f897e79175ca6865a67a9f2c3d651eb633564db9d45f786df0167b781b962079857c207fdcbde9969fc7291edf1560cddcfcde17a9ea6720581a9dfb39416bd2a5aacfaaa3c44a75d784f693d2383e968dd4428a63d248d05959407687e3542b5b51cc4817db4e3b078f9d75351e59b30ec747edbb9c8405057fc23ecd5353b83eddfa659ddebf1b0f1f695574b0566d2c867af1fe49b2a43641b9568ee02de74ce2b2150b303a3ada0a3ec3eefc711ba01de47a27a7b0f9e99df8978d07714767197a9094ad9c06511807b18a2a6e984da4d74dbb73962cc4c2396315d318a087bbaede73c5603714bd27a68ee29227efc6455befc5ca3d9bd0d55ec552dbe5b4e1910a4cceba2c44bf3083ced8ca2062a1cda62d37918ab162bfbacd001598f3781169bba1fa86eeaf7df0ee7ad641c729ed61d1f13044be2caa6fe17d985094218c6fd922e7b1f74cfc0166441103b066701bdb0d6c95348a62abd193a139013c30e952056afe585f15a053dd316c3fed6cdc7dc1abf0f4d2b5383636cfc7f690f1f655d985855d093eaf4e045229d9e22ea000e88dcd010e1fa53603b3c4e7eedfc7c92cc1978696b1e41615c1064ce2039ba671f1b69d78edea7978c1d27998331e1bf3f5a30b2c37bf6e5bfaf54e9cc0d6ddfbf09527f7e3f6a949eced2c472cb762dcf6a2117aa65bee669b3e80ccb630bd473a11b341275cc86020840776c7bae912c593bff535eda789e0f8f83cdc73fe2bf0e89a7558b57b23d63d7e1b56eedd8cd1e92997dbd5947e86d2f6f8436b1da4f3ce6114146a64abd42e289d922e9cb836767e289aa46bf393e95504c91b3af832441ec677710c2fcf2dc7f4cdef7d2d80cf4af91c62a034dfe2e4919951f41bfdff6fefcba32dabca3b7ffbdc7bdf3cd5ab57f3201488c8a8088888068826a038201ad3a2ad74c7b4ae1ed2bd7a75bbd216cb95a5e92cbbd7ea986577ca4e4c6b3a6d62143551040411452406d008625922945054410daf5ebdaa7af5c67bcfd77f9cfd4dfb9cfbaa0a6a78056fb3a877ef397bf8f6b7bfeff70d7b9f731181c7795f5ec7d57a3be6d93026b12c46d0e6dbc5525a0d0d12ee79c0cd430d3b87d7e1c1b3aec6d695e762a6d10d879806b07ce86ab9e079617357ce8292552eee3ea0393b8d2776edc574adb3146e89a5345e007b4dce03b2201c69b520563a6d429043be1df91cceef25fcfab23ace5f3b8cce7aad92c74c97e5b3cd9b359b4d3cbbfd197c7be72cfee15003fbeabd2809baccdb80890844b296150a5d65c4d21c4c473e8733572c4547a3430722b875e4b0d0a71a82151503f6a104367e0e7a10b8a33985339edd8c8b1ffb0e568d6d43462d43b5871d6b78ab1feda1b84e697657f541c0a82493167018a78c020ae3940111f293fe0c4358a7e2350af6388498ec3c00d7d6366ebab3b4f0f394a3f5b010801f12702f085716872f5592d29d086901b86d4f8bec017aa4c0597d5397ec64a3c088350949afa42c1109e51a56294cbe431e8ab563c7a679087866e9e9f8f199afc3e6f51783627ecab9c1d26d7bfc5721435c7befa1b87c9f9060c43e101a94a3b73583e95aa7826d32aa1cb3b03c14efc3838a50e5f822ec8103e400cc660dfc680af8f15339363cbd03d7acedc5c52b7ad1d7db5d06504b530497d6d4241edfbe0bdfda3e89076809666abd4023529d8472b161256d259a9db405bd2200c55e95230bddd4449d72d135eed7f51c6caf90b573d7928528898688a69ed59b6d7463f3ba8bb065dd2bf1f26d3fc2458fdf8b35a35b119c312fdad8a21b5030c06c019a25c6d29c6a5ea9869d859d68d23e5e574f22b9553169774d6905d1bd0828fdc8c4e1ca510316010702f01708b83c001d55c1b7cd034848e03a31b8ce938b7eab766576ce822e4244306536630e2089eb2ae091a18db1104b09e829fc38769ed5f0ecf04bf0d05957e1572b5f5ee4a8640df86c985ff468735469e2ce483089495d60dd9828098a4e4b3e0510b200d4337bae8a9b94c1c9ad98b3c655757c7db5db7143ded42f9425c313790ffef7b61cb73cb31f97f6eec5abd70ee2b4a16e743612916a35b177fe6ab6b200002000494441546c1c8feed887ef8f03bf443fa6421f90592f493927a33a5e59caecc4c9d453c3291e09a356f2cc25bb4b59ad8e9065e093e2ce29013999f056d8822a12f9d7efe9aeb071c663e857ace5e6f517e38955e7e2b45d5b70c963f760f5d893ee14bd7a730a3cc1f5afa06c294cfc34af5f4c99f46b6babe4f107a5bd6c58aa737e25ff4dae05601621fc050895af419eafcc27b96d4beb131fee03f04f00ce14329230a79888113ae2839d401a42ccb765ee43c94834f1c14fb35bc216a7ddf8dc6f1420bee16d0e906735ec1958857f3af3f5786ccd8598ececf3e39bf862be2d7aad56711d7cf0f6707c8843c70fd9dc0c76ecd98b9dd4d19e07a548204d86ab522b8846f17740cf61b555320585f4f5befd98c359fd355cb124e0fc9583a883b067f71efcf0d9437870aa033b422f5a997d9c26b12495a248c97d43b4bba69fbddac22ba4f9d92702b0a4b38ef54b07906535cb6c37b7f9362d5c04e44c8c35a8e4a20bee9317cb6d3201e89e3d8497ee7804173d712f968d3f835a1e5fbc9800baea02e2bbd1921d404b9f77a82db4cb459521bb8b4de25cb07ef956c9672a03a7eb42f4018f13f0cafac6cf4ce028cb73022c00687efc43ff3984f0470032916154e71554198c005920329fdb11a46bacdcb716469cb5b40d7b3126e4442a40f1ef54472f7e70eeb578f4b4cb30ddd163eeabb5b57d2666d77c4fafa37cadbcd2f3d4610922ec1d1dc5d3b35563b62be52de5f4d452fba6156e4e521a7913c3ad49746601e8ea41e8ecc69a308d4673068fb5ba31d9cc31d76a610af5e43c5554f314d0dd6c2af86b3d9a76759235b0878cedfc5775d5b07c78080050362a884a6b97c91a527ba382acaab5845e62d0b2063d120200e8989bc2394f3d84d7fdec9be89b3e501ebff47d7eced979a7df749909fa9a1b0bc386972527c03a25767ed5fc20421e027ebfb671d37fc37328471d124a09e14b00de4f44e708fab22936ab47c94a8a4572f914764f19752a8499ad91780c70ccf7c7216c429b012d5aa70896f253de4498ab77e2e7eb5f85fbcfb906e3bd233149a8063984607e831050452826a8169c6f5509511bb0e25bed40cbb60d012b9a07f1340692706abe52ce54a4bcb2805832cfa65e3d6f62303451ebecc6304da3a7a38156df08906571da45fdc9d00f2260398f48841c287ea0626a0a07f20cb3cd26e6e69a988d67adca875981f29110088fecafba548724361d111c8671403542d3950a6fbb0aaa687e8c64fd7443055e0628adcf40d5668d8bd961b6d18d9f9c7105b6ae3a17afd9f22d9cfbe483728ecbb7b1f4598fa97cea4c2310fea62c653d55a08ae98004e6e6211b72d8b62a1fa9edb710f0a5d28d232ccf19b002681b103e1742f824804cdc5c2efc35e85596c5423f29de360a2ddb9e7a448e8c004897c18f63ad1e0bbd6edd5a41323931225056c3eea1d5f8fe796fc6932bcec65cad336e000016b1ca51382b8cb5b611ddac57e40ed5799ed852e575fafbbaa3d9570fc08ce6791213e9814f8c454a80559a36a08a808c72742147672d6059d644ad7f089db50c687480420d397bafc28f44e9998fc85007a1afb313e8ecc41008ad3c47abd5c2c4ec1c0e4d4ee3509ea1d96ca2658d5022f43a4d3b167996c99815f34b58dbd9d9896609abcbee52f195e2929a14b70b1ba2cf62d7d1d06204d9f5e714c41c9666820ff40ee3ee0b6fc063ab2fc4eb1fbd152bf6c51d45f6f88d312b7486fc83279686848f9a4a617253fd25941aa1bd6747287603fdd41d00e6003e1780d24fd01f69399c799eb7b43ef1a1f5047c2b209c5d106c43a718ae059e0ae07203f06b19cc9a32a095720ab65e25f5154aca7d3ae601338d6efc64c36bf1e0cbaec644d720abb0d25ee5fac70bf6856480f70e08d64ac17b6048e7434666cbc70e0aa5e3618bc9f74cedc7c3bb0ee050a35b1445e69c009780b3233f39dd9c7a2744c8008c601a7d7dfd18a803e8e907b29ad02e9b05d1e88884fa2576d85de695259bd06cb5303d338b03337338303d8b193240eec8afca6f7a90a954289f9340776b062f5bb312a8d512fac43eb60d77aa68a94c811939ad92a9f4f8c37cbc02083d3313b8e8f1efe155bffc1eba670f893e54ad636ca24b436a5cd4c86bded2aa94a44f10e5b23c7599b3bc04c0785555298748df1600bf59dbb8e93903d6730f090100615b003e4da0ff2504bad7ab708e89d1c6b48cff3817d5ac67f1c12a7951477348f13b33274a8c3c02c3cff999b180e25d505b579f8bef9df716ec1a5a6b16518f4d142358c0b05214d18f922309020432988ecb164f0448854673720c5609d058140730d935805573dbf078a3db7901a9b547302263b5c921beae1310d093cf62b0b38115dd0da06fa4e0a7b1c26a4dac8290722bcea76051e45da43f3d406af39d21143fb4dae8ada3af87b0920813870e6174720e879a2db400510007fe2e44f30aa8538f74041fd62dcda7812c4b401eced808fd155e6fb9e83a496d6675094c0ac36e43d523e1d564573fee3bef3afc7cfdc5b8e2d15bf1b2ed3f412d6f19b9d35c311b12d10f893894401b729be1118457a20c66520a6d7adf7bc31e3683d407e8d34078ce60e5fb7e8ea5f5890ff711d1c321840ddea3f0c5e728f8a23934e94c997e2406bef8b96cbd534f2235976ac1f60eacc4832fbd0a9b5f720966eb9dd2ceb2553d43f6166d7f4816ce1c2c840a8903cfe49d5c6085769648cdb2628b37ef366c9cddfe0436874101141f86247f53fe735f91d64039fa6ac08a06d0d53f807aa3010afabef776f4a65ea6635049c9abd7a4cc53739908799e636a760ee387a67060b685d9bc023c84d7ed84b9ea0e615d07b07464c4809571472af89ff6a186d24c8d3f560f7984da7604bc2242a3358bb3b63f8c4b7f7137568c3f5dd1f5110fd896864a561c668cca0d9da2da56105d58bbf9e877066d799e1e1600d04408e1d3007d12840ef0221a6b1e9d2318b305b146f1be3a53d66a920020c7e76e310b57c2030629d31840f3ac86c7d65c88fbce7b334607564120864985b11b496c6a13977ee7c482057b73e4d45a2c67fc500ebf129002b38ce76c58c1560f00f50ca03699a3c5c0c296d90a8be1b307f4c2320602bad1c2f2de06067b7b814687b413af2ff5da246f62ba575f547965bc0cbea7938c06c45a65d2f5b3b4665986beae4ef47675626476167b27a6303e3d8739d27a9c532a6dbab00cb82914576b20343abb8cafe0c7578f4108aa583b5e6fedc1bd53aa0df029af8413c9a723e3d55cad038fbee412ec587a3a2edb7217ce7ff287a8e54d33b4d978324c60630cb3a420987abaaea91cb1f1d2fa6cf838ae097e5cc818b301f83442785e6005e1c8f32cad4f7c780484bb28d02b986040955fc4c9782796a98512d985b198e797d50a86ca842c35b7146f6ca6de89075ef606dc7fce3568851ae310d712819465499433a136b95efcabde0750b9b32534f2fa7ba1e671f59d5aa61dacad2bae34f6edc2a31339a6b2068e660939e754478ed55d350c0df423ebe848fa882356595517765bd568371e0bb4ceb518c18349c510658088f44fcfcc60cfc143189bcd1d4fc08a82908c035963eea31339ce1eec44d63fd49e6e8b4d6da2868a966e2c210df0bf6160ef19fbfb5c7905225cf0ab7fc0eb1ebd150353e3a67bf2b863c677f28804d4243ae0b6a97c28a1a9bc57ed1012e82701e18db58d9b46cb3c3bba720c3c2c00c028023e1980bf00a807304013f4c88288175926a8bd2909853cb263ac8e1508e243792ca4643c35c2ae256b71cf05d7e3c9152f036535a7f8212e08c5bae2cdd8a01e30391cf5ba4451c5fa820d9033486e2a8a92b0792a51060e77c9c060506a2dbad5ba7ad071680253709222fcb1f9215b32008335c2ca815e74f6f44212ba153487aa509881446bc193e0c795d0d68288591feb09a56d82e94ff78e03ba3a3bb1aea3030387a6b0fbe02426dd7bd8fc4444b20c008600d4b30ca1a7cff3cdae5572bdb40193f0ca2969bc2f5e4cbcee4ecdbb819e3faf00e091d35f8367969e86d7fff456bc74c7c3c88c21ad326aa97c68decae6bd7c9b72b85736ce2523034c06844f12f0bcc10a38cc0fa91e69a96ddc0400771070670a3cec75883f6284ba4079cf5022128175e054b22e51008b6fe29202401eea787ccd05f8bbd7fc0e7eb5f2e5a04cdfe744f66f0892a20a111d94266f81d4a2c4ef2c94d68a3b0285d08acb0192bfe3c6e4eb58612c78a20c9deeeac7aad644045afeafe002050d03c8b4af81b0a62bc3da912174f5f68960ca7f0688ddb3804490e31af12847f15139495c2de874e5588a01ea4ad5319b1742730928d8f815bc0e5986c1be1eac5f3a80e18e5a244f692eba23a581aca2058cd00c42ad1e012182b6a54191c6781a910f4a8ef61de72df38896507643e5bad227b4593a9f07af1002460757e3b64b6ec40fcebd16538d6e318a21280d2042babcfc972502e69aa559d22c4c79d455999b9dbf701b7702b8a35e60c4f32e65e87d1ea5f5f10f5d8610ee214217c4ca475c36d6c3ed301184b125300b86497c8ff31de6b3cc848066bd033f3af34afce0dc6b31dbe884bc3d310a9d8dd58bcb513948158297aef8141c2d224a955636943ecbfd92753a7c29b5b163eed98187678a0479bb31b9d44078494f1dfd43439575d490db230749927e9e90e8b073abd2bef49a7cd71b49faa7741f00f23cc7e8be7dd8399d17efd1aa7483cc3502ce18ec427f7fbf9db80e54ed9054df3046a44cf0fc736b5b8e09af08e73ef500ae7ef86b724abea8adf25dd571f5b77492f3115c227e9a0857d56fde74d40f39b72bc7c4c3e242213c4484cf84805c096735f7ae2ce79d28f1c8920e35b607c0f9b0022fbc570504eceb5f81af5efe41dc73e1db3153ef14eb11e298e2b518af2900ea6525fd1563c2d42f2c2727ec6dd1e4b7556ef542bc427b8b9b5e87cc4cfb54cf2f8ed7d38725cd49a9de8e873d21c7994b7a31b064488d4452d29502f38aa1a8edfa247333de84d20c2ff732a871252c11564e2a07f55e4bc8322c5fba142fe9ef44a3e24572e21103200ae84513dd0dfdf1a7e26fd0ca76e984e516181217c8e974306de0eb453a9cf7eae83c96bc0af8d969afc6df5cf9eff0f4c819226dc55b79e7332c76edac1e1ca9a175f5f2020be8a1236c7cf4231c8bd2fac4873700b88788d657e6484a39295e28730df0de93e9a09c4b025aa18ea7969f85bb2efa2d8cf52f2bbc0e975002581d8b5666e50d8095de786bbd26a398edeca49dca7c6dd2438455e90dedd490692ad566a7b163b478109a39a30d8a3fdd1961fdf000ba3a3bcb39109b13441bcf209d83c1fab6f3316d2ac7b35452c22b7328b5c4b476ec211d6be2d024b6ed9fc45cc94f5003b1a4a386f54b0791854ce65fcad998714bce8c951133a7cab526c1296774abdb1c075e11a177fa00def0935b70d6f687516b350d2d026395fe913b06e3ae274976ebb5096f0801d846c055f58d9fd98a63588ea9870500b58d9bb612b02984d0048ccf602c76b1908ae0242cb48b14913dc41a9c630233b108555aa18e87375c8eafbfe603181b58a1e7b69cfe5ae0b230c88b627731d544567b169a1026f07252149e14ace267e2e30edcaf5a5463d340ee1b9cc5571c2fc66b353ab1343425b7e0e64a402300eb96f41bb06243ad391a329fb583aadc0fa90e3996701ec5cc473c67db964af30cc207033a01ce40b0a2aa5792b82fe4c9e9ebedc1bafe4ef334be75498aa4fd509623cb32ed85c74cd65e3c3853cf0d18bd7d973a60aa394f14e5c10384ed5bdb1c175e05e050f7206ebff83d78f0acabd1aad54576c5734abdc1f8c1416ff20333b6f00e66c2ac26019b8e355801c701b00020009f05e15ec42505a039ac54e882650219218ba0435ac12e7c40f12a98ef9dff16dcfd8a1b30d9d91795b25808b1ac25ef85fbb2da47ee7ee96801d95a8c20beb7448fa47f7dcc41fbb56156084aad059ec037931082011621a0abaf1f35e4e9d450cf80f5833de8e9ee96509559299e24200f76177334ee40e22902155bd74c7388fb776673c49e8b52a36376ac8c08e8a6867a428e5b4631918434143599225f0280818101acefa9177c09bc43ad32d8d53f10f96bd7d8031bf7570adf0407cb39585e66a6db4699825e4ca7f18a02c271e7d56ca31bf79e771deebae85d98ab354c6a80dcba16ef6f63a4356399cfaa9d0cec965742d3bd003e8be3508e0b60011805e86344180358e774c18a0f89868b055240b1e7b252af67b2b31f77beeab7f1e0cbae2e1681d4836063283b1ba28ca45e83dbd189235892c483527243ec87888151775c4a792b2211ec409e06ad49faaf4842947c135f5a9b6bb99177f763f5dc0111ea02b009abba1be8ebedd1b9096805f10c941263e1dd58aa64762788c31022ee4b7796acc19076ccab7858957925dc27356a9cb3b4bc72c5e672c8f4e7349a30383888659d7507140030185ae8a8d504e4343f0865a069e164c7d9d890d42b941dc4601f6277c23167882bbd9ae3ccab3cabe3e1d32fc7372f791f0e760f1aca756d8576d79ba537b8cfa63a8c451c0bc0c702c23139c69096e30258b58d9b80101e40c09f21200f669e6acb125732267238d91bcdbff469960afb7b8771d72bdf85874fbf1cadac0e3ec7e40cb0b31cbc1d1fcc2e6188bd1a0f2ea8e5500b1a4193e33da1cf5ae92898044d56063b56143c950fc30cb68f1c46b0200401cd40a1cc3f02906518ecec4040ce6a81c18e1a0607fab54e80cc97fbe56b214dda45e1645e0130e1835f37090fa35170206bd60cb226cc67450579e385cde9b0d764bc27b392e64f50b2a4bd8eb374a00f03356d1d88d0d7dd55bc61d4ae2978ad6d7fe9df620da974dd6e4898cd9850b461d9e2eead132bf4cae7e3cf2bca6af8f9fa57e15baffa6d8cf5152fffb1b97eebd1dbbca5752e8c23ade48a1e2107f067043c503b46c718d272bc3c2cd4366e9a0d089f04c2e389eb029e6688422e0b67911c219e1bb5e73d08135d03b8ed92f761f3fa8b9167993412bf819391169cb857060019cb9c96170be69d6d0b7cea46e8574b2fc020a0f0623daf224435426190bc10461616bdef7630935c4e1e328cd47334e24fd8d7a9853543bda8f3af4cc7fe791c1b9eb3924ae236b2584255a191a766ad3ee93aa6495f07fa9666fea8f5b91f9bc7e183a92102b83e2694f09cbb71ebabbc6c341a583dd8872cf2260bc0700787b07e49ab3f5bc3c53c09e57aba1cdecbe617e18104f4593e41808f1e4e20af42c02fd75c885b5ffdcf31de336c42d75426d3f9b765941de071803e59dfb86916c7a91c37c00280dac64de300fe234218176436163e5e488403e0708aad03e7490ef42cc5575efbbbc5c97541fae8a9c9e1422358e227149fc100805092cd90bedf8a9bc4fe0946478df5b25e964f7ec70a7c41bc2f764f20f4aa5c55e44d8c40154e68548668b92796aec1cae64104222cebed42bdd1c1e826ed0924afd1d5346e0479b2735172353c341e292b95c92b5a135d849c9627706f192d29a8cc3a0801723095d43329aa91f08bbd51f2ac047b3188bceaeceec68a46717120b490f50e5410a2fc7548e63af6755d3d56ee905414004af27be29a94fb3f91bcdab17403be72c58730d6b7c2441c9076e98f5fd809fa7b519608e308e13fd6367e661cc7b11c57c08ae54e10be48c54b27a3c76484da78006c7960ac205b9ebd03cb71db253762c7c8e97161632025211789dc105bf9a85885f7641fa1510193e0270abb786000f455395c57151de01d1df600392fa2caacf920dd04103ac84287f7140841f8a44e1d078e0af804a0997560b8a7137da185257d3d30138330156cd50bfadc2d3b9fa074499e8eeb443a1020a7dc5370f55f399cd5c124e7c27763bea66a4735c00ec8dc535353e8bcf6c9b99c108586fb5bd2df83ae90a3bfafcfd16893c42a0eeacdebdf483f5f037fb7b334bc907ac46c571a61729ea43b8a27855708d83db416dfbcf4bdd833b4c6f411f918fcdc5541a1b2251e1ff210f045808eea27bb9e4ba93023c7beb43ef1a10da0f035045c00c04c1ac23ceb9ada334304607fcf306ebbf4bd7872c5cb2a5f33a3064ed145a10291b151dd839ea8d7b1b478e3d9fe1c956d1182a13bb5fa5674f89e620820e1198363702dcd9454d883d21010d039b51f8766e6505f32e2eaa4c5ce41e8b23c20131e22a14b80d2cf41e96c377fe58d7ee739a0b4eec21b19a7f8e0cfaec56bc97cec19211d833031b617ddfd03a87574e8db820470aa79e5f865264ddca00dbdea859779453276fbf3532783576b469fc09bfff1afb07462374a033b86705f7a0c23f6fa08115d5fbff9d81f6348cb09012c00687de2c3af07e87642e8110b0f06052095b4225c010e750de04bafffd7d839bc4ec965854290f7e9c94bf5020c2b2de4c4d1485d69b99f00a8f42b83255e8bdc648b53160841a4905c739fadbd34561ef05493a915b4a6bbefe42798eb8900a71aa20c4d04d4035ab98a998300ad2a422aefce30b521217d956fa9b4b32c95758df1b2b4b03b959cd6b500e70e3c5775995805cd4755805009902c2df08023754e3caf460eecc4bbbff769f44fee13597146df57575e019308e1dadac64df71e669463524e4448c8e57e22fc8f009a15573328784889cc0a044c740fe2f64bde839d4bd6f97023007c56c5b2969f0554cf3d240fc69ada14bfb3dbcd1e4bccd5e8c608038bf621c7058a0928a088eb1c9c05970f327050a1b4b327614bd23808286aa8a0ed42695e701d85447b24bce37fdd2ea4999b7813910dcc5c4ae710a40df38115db866aea1992f04a42140611681be1a1104c912c139e91e983e9e77f654d7948d6b410975efbe0c1022c8d800dcd997ea9a98cf4f422ed97aa6981bd767279353ab012b75df25e1cec5922fd70ba84e5cef228123e4b01ff03c0fd3841e58401566de3a66608e18f01dc2fbb6576c13c6c61baa31bdf3fef3afc72f5f9606b23f90240df4aa01dc07a420c33d6732aaa7a8faa386ee0434392c5e6cfc19b40515efe2a9d952c9b3a65dea26b0ecf1e61a5084cdc1f89a200f6547e62f302cc75ebb14570e29c53bc17cc43de32bd3666581c070ba46c54ac4f180cbf05e48218003fef2075d243a7e2ad053b6eb036c0854c6cf498cf92f80f964f24f52caadb3973382b3443e90e420bf97e990f2287863e25d6ac0d047002df63b039c9bc02027eb5f2e5f8de056fc354478ff200baa4c60216ed88ee0f087f5cdbb8a98913544ea48785dac64d6384f01102b6175778118dca129023e0fbe7bd058f9cfe9ae2b9406696f11ef810a4574fef39b83b1cb2c8bf494905991594d8da18d98a0d28684fe2831981921601d0037f70c2adf52034da3971a255842790f283c7260b167e5aac2e5251ae9207743e8e61c25df6529c47179253cd120ef1dc74cec6bc80371274ce71bee63535c5a5645e32270ed52c1d76a2a6af44a97592cc10dd80b1f55542f49e999af2457865ea45da1c8b791c43a7028e1960a1f00a013f5b7f09be7bc1dbd00a99eb3bd5290ad88e103e52dbb8690c27b09c50c00280003c1442b81944725643dd5f20cf32dc7fce35f8a733ae40cebfc80bf20c1797d7899300839c51e145233190518eaced95436fc9e17b9b9b5181d6750bf13f923e8a310c8489942bfdfcaca3d62153473fdb361a72f9f94b70122073664592f10466a1c2cfdea681f8c06fc6b0d816ccfc83f6278e413cdcebacb97b59140fca601be989f4c97c0dbdca07a038f11d5559de9211d7964cf86f804243223b650682c45011d3420ab654a1dc88b211e7136c0e53c6f4cc132312c7552f573b779b370b845794053c72da6b70df79d7156fe8051b5b7528009a0dc0cd44c7f64d0c47524e3860d5366eca09f812057c16401ecd3842287ed1e6f1d5e7e38767bf11ad5a43980e0072da181017dcdb1bfeccfeaaa993781d54f549048e14ecd8bb6105b5ca2a2dd94d2faeea677b70d512a042c280a849566be379fc20e0d0cefab352d8704130ce020a8f1f4188c7f56790eccc2cffd8fb63f066b886fc4df9c34a577c36730c11642594b221b9863dea192b28179f8beb1cca73357992c17051d68e77c5c8d066c3297eb22230ad6ab46c3fca484f1bf3d9f6cb219c0b23255fb87079d5caea78f0acabf08bb5af28de3166bcb5102807c26709f852fde6cf9807594f4c39e1800500f58d9b2603c2cd001e124b0060cfd01a7ce715efc05ca30b00e24f21c5058d0974e313442367b41f9087ac3913543aef2575b417528989d760ee1b7916a5653a8c05e52e1323ce38601fe128c8d5504a41ceb7e2b057e8371638e95d9d27c0d4d79e7c7d61885c4ebd0f558412de171c881e04f3d2d6f39fcdb92fe838eedc5405dfed47323c70342a2a73c5f8ddef0d473c91aa4a9be9d7e412ad67abe065294ce9f15c9679c12e9f9727feb45079355befc03d17be1dbb96ac63f6309c3e44849beb1b374de224949302584091cf02e126005b806247f0b64b6ec478dfb268115128127b014eeaa08e8a842cec89f0521a654c9c1c5631b2c71588ef04edd32eb0b4d7bc04839d8408622515ce3ccd6aae8d7a28bda47f011b927afabd706a9f3604d570d95411865831e57c6070f7b5a9ddeae0f902f2806ee2ade9c2154d5cba0b9093f5c221a744864512de307d0950e8b455a14380055e121e15edf920ac847066cdabf29a64e62c75557b95fef899f37d36ac56835baee7e6b2007975a067296ebfe4461cec1e6289dd42c04df59b4f6cdeca969306580080802d003e32576b8cddfd8a1bb06bc9ba7839ea0191b3f46c668afb9a08674f2bb5f292bf8a1579ed3887a53aade768fc76731001939a9208834a6460f0e2408949b5ed8afe15a658a08c7483cc33dfd1ba47f49687ef899287a82348d8b0ce7c2c78a89023981c3f8bde06e3a70966463e104525b5fd4423214a1a79c500cdeeb03c381c0c8da43f1ec2c4c679594f80c35c0ecb75274fa8d0750a3a1109871850d56502243452a3647f1054d71dfaf68f5050c3b9ce623edaa7e641ed110f5dde62397d1867ecdf82e6d5eea135b8fb153760aed61823e023006dc1492c2715b06a1b37e521843b7e7ada659ffdc5da571aef08f257b338ca4bbd6f7c1951566337820283b7a0d6c2a9552984393654f172ed38edc174a8d04460101932f029fd465a380f61e3481ed766fef992456244200c2c5f24ca50245705f100f7598750eee93936de3c708c6620665e8600a7ebb17b7502437c0b27e94056fe2569139416e643881b18325763ac4cb247c08509906f06858dd50a40f21a2de35988d5e209d875775c832c3c2978f1a68e3d886c65cec94ac22b5f162eaff210f0d8da57e0a7a75df6d98070477de389cf5bd972723d2c146f756865f58f8eec7fe6ce90e7002b91419d227c43e26d99958f0b5b5c767b5f2ea40a0c7e469fc7c8f0c10000200049444154b8bed5e604320c3dc56ab257c220c463f12e8c0aa915189959f2ae239e8d919c4a0abcd0c91d2ba841c73094c3f24a0e42b27535c91d7bc239806477caf35da96383ecc84ad0b56a83443c3de3d11802633d3f9a1c2121eb391404902322483f82bbe4ba320fc7dba2bcb241b0fd0b336e295524f2c17df8fb364cf4170c050b9057599e6364ff3377b6b2fa476bc7f12d0c475a4e3a6001c0f0c1ddcd0d3b37df343039762f12412830c12e63bccff13ad712e5510b496e2118680a850a46e82075d5db92dd394626c61156eef8d778ee71a0e0ea7127948895a7d3d321c069e62396b5d886829c5d7312ce4c306dcd5f2e5657dca3374293f286430b9f73b18b144c7b98f1d48b95230124d0ab5ea8eb4f432a5ebf82c7ecd11866931a0ac85a1966b0e13286c2eefad913ec820af63ba91191b7e918e154c98bf49ac50d41e72cf062fb20337e247221f20a4418981cbb77c3cecd370d4fec3e618743e72b950eeac92a7f7fe7fd173fb9e2ecbf3cd8b3e41ceb0571f2d3f809f15ff6a6280a4a10d90b216967fa739fcd9762d70b40cc05d81d460e73345f037038103f427f7c94eb46f108e65c181824e0fa2bd165433d4b238f1f2f85b499a1db860f60e04d0a872a98a78ea91dff06dbd08ce32e3b5ed95e1daf2ae65cbe96f28ae07e0cc1f00a8e96647d983bec193b9929f3caca125c7f9e57763dfd242d79655e39195f80bcea9f1adf7cdaae2def7fdb6f5c7ec2cf5bb52b0b0ab000e06b77fdf0ca5fad7cf93726bb06fa0a0009a5b5b200527c5501f213b2c267c104887e048a07a615fa52604887b380e1bc73d37b4a43918c4f1a24a50d86cafcdcc1852a9e98ebdc858574ab3daab4652ae2b15b1db162a2f2fb8d9574f839a44a6f6f26cb583df9aacb297f6cffd59d969b3983580512157d57827a194ce45c1d22472db04875ddd103b0e078d5337d60e2f49d3f7fcbf56fbcecbbe5014e5e591021a12dabc6b67d77f5dea7ded7393b35699383d640b06b6fa1c79f5e57efca4a65d0daf2999c65b40ba7bd49151386da24ba489aed8122b12ca569cec2cd834f2253323445ba2dd05a0a79a68619868e00dec533f70234b76c62d12242305e926343d247c22b79d325a834871079e5c3215385074cbbe7cffc3fd72ba18e1957ee1918377dbbfd0cc3d2409aaf4b8928ab72d0b930e9a2f87a9d4d5d356c42f96f4474a1f0aa73766a72f5dea7deb76a6cdb774be49fe4b2e000ebb277ff16ce7dea81af0f1fdcf9b1467366d2ad0abbdef17f5572161e5d14b16f220cd6438128b99c8fb14a87c40a71e1c436b124a9a4f18f524823237dee8d144680f40843fc1ef498801d4fc6b271a008bec26f493bd84b7448ce7549fa29bc46fd1e52f74be83446c02a43b0f52ac0d5864ccc175b4a2131f1d4f43b51e9017231419cdb330e0b2b76faf0af1fd78c1fb4ae0d8d1c2630fbe5edad76be3c78303c2fac03a56371bb2a37f724f3aad19c991c3eb8f363e76dfbc7af5ff6eedf2ad37792cb82032c0038fffdbf939fb3eda13f5d36bee353b556b32980e264af1014de69b36781c09e0558ac60ce9e20ca022f5a3caa20a092285c08482d6f1041326069fb771acc57ccb101aec2c22b12adb4707b1576ab14b147f7f22f1664db36185ed97eec7402d86b7061b3f122942551b14195cfc619f7c0cc96dc75cb9de27654b0683d78338193becc43fbd2466d2a23e8dfb26570d3b50e8a5e4f90c9dcf10645fbf0f4312825bc356b6f28d0796301f18a08b5d65c73d9f88e4f9dbbeda13f3def9f7ff0a41e5f68572a207ee194fb6ff94ad7132bcfb979dbb297fe973c6485de8883c016ae7dfb62217dfe88818fc325018da281f3808aaf656f835f252cce0e0b0d0c7eb86f3282ef8a48e6e44e28cb442d7dc6aa9b31d4d12bf27d0c24025ba4e06ce750b5f2c20e71e848bf4315c1f2b06d1f4c9b1bb07ae3c113a6d7d23a8e039c3cb6081411c5a5dc4873482a2f36e11eb3765e83133acb9f55e303f455daa1c45b5e6354f6b7707815f216d6eff9e57f3d63e7e68f5ffece1ba6b140cb82062c00b8ef96af75fd72cdf97fb273c94b3ed0aad53baaea585fa25d823a7ed3e4b9008c6a683bc12803978a83d6568df034d816e937ee47e9876bad0257749decfe44baac70f246057b8ee551c98c63e66599e3e8d559b69b1792565acff4c2ce5a2550562b718af5e90ea85368d7b95d4f33fff4bbe3952207f3daf3c18cefc50a69c5763bbf25d355ce809f145ed55aadd995fb9efafc4b773cf27b57bcf31d0b16ac80051a12da72c53baf9f3e63e7cf3fb27c7cfb17b3bcd54c130bea4f940ff3117b433017cc020723206221e12d91556a75abf5fc95781c0872525f3c1fa6c5596f2f98dc8f460811a0b84fc1c11428829b283f17290016b84f2ad19ef2888976e1524a03b1576afa8a37987eedcaac85f016da2629766ec184552e882680df7460cfaa717b72f42b2f60e810bae2ffeaa51b3727e6786c16400084db07be4ed2cce17eb067f874de2a2f7a8f4cff278357599e37978f6fffe2193b7ffe91850e56c029e06171b9fbef6f1f7e7ad9999b9e1e39e3b7603c076bc77d780413c2545b2b2944f20c5c299c40f042672c27ff7456d06ea09d2862f93e007d9368e2c951807d9cc27a3945d7f1384232218a1dbb472e90dee7284010304ec2d3914e8143d480b6be990bbb0a25d40d84ea304947534f86ccc64528f5c3231fd6bb304050e6953516e64c150c8d0ec0e33cd525829e69e29dd3c41be6b954085cc9bb4901ed84f38ab076f4892faddff3f887af7edbb527ed81e6a329a70c6001c06db77db76fe7f0fabf7c76c9fab75256aba7f7ed2922a750460a58e142c9a7af2e3e744449a0aab4a51412891b15dcef57289ac6f60670ab421a4f40f2b7445799d80a18138e957e944166623937fffc53be960c85e1438956ae6c3d18a440e881b49d8297f0290152b16c55eb5f69dd92f548e981a7cd5807bf541e0d9d7777a27915f25673d5be6d5f5f39b6edfd6f7ad39513159c589065c18784b6bce94d574eacdaf7d40797efdff1a52c6f26cf35f9238ff6a92cdef82ff0a1f02a2871a7f53360bec676c15d703b63d273f199d2fad19b89c658efa4afa8758a172142e3ba64bc8abf55429d5c2ceb6748874cea5acea5f50c5091b95509665c35f6152aee1aef41bc3ac90893a9427e8c2a254fef8902737f6510a0b44dbcc85e54d1ccf33a54cd35981b3ccf50e615879e151498ce8f0fafb2bc39bb7cff8e2faddab7ed83a7125801a7186001c0b5d75e35b6e1d9cdff76e5d8b6cf87bc657ce6422ae5b064da906cf863ce2e453cb07e923651efc5a61342951471ff0ef18c629408b27d047192f41a09d8f96930c1e6bc1648ae5b85901e6d7f094f582189e7e06840f9bb245cda2183b9eae2eb725fe6ed39beaf846cf66e74789f4496eba96764d735e16f6a98e416699f8ad5da51956c717ecc7eb742631fb6ae0a154bd78f13af408490b7b0726cdbe7cf7876f3bfbdf6da2b4f8930d0966aee9d02e5fe5bbedab575c5d9373fbdecccffdcac35ead64569e3d0c77fc954f5a7e39dcbdc665c1b4d581f268a88f3f2ca34545f908f828f0a566de988ff54ca7f8275b62e1ff5102c757428e0a7792d9967c53d2473d3f1b4655aafc423bb00d50b083986922aa2a5ab22a4e35ca4e309f9f1f9f12c3274cb70f2b77cc4c1858991c93e9f540146f62b03d209e055ad35d75cb7e7f1ffb661d7968f5f7e0a24d8abca290b5800f08f7ffbb75d5bd6bef2a3bb96acfbf7b38dce3ef186e4856ca80c75bc40f8875c39295ed4b3ffa66de337eb21b93e62cffc4baf161828f615785c7303709b050aa0a4fd1ab7c17a03e9d955565200fc3bb3fa188d092ba4185d57100db2b920fd7073619c5068f8cafda447347c05060877395db764be5527f17dd7e65c140fc273a6845e53af34ac28bb991551390f6966cd4763c8bec7ca3334b9786278d531373db162dfd39f3a7bfb3ffde1abdffdee5312ac80531cb000e0e1bffa5cd78fcf7cddbfd933b8fa0f66ebdd3d3e47a930c321436ab5ca0978f540a4072b6ca480928c50e1552567c2aabcaeb843c900a580610e34f2c3d966072841bca24dc9fbb17333ea1c070a25bea467bbec155ffc7d038ea96795eec655b2c25fd0b589009becccb6a3c627dcd57a14533400908076998c8afe123a1d5f4cc5d8329d6099fe769ee071e055c7dcd4e4b2fdcf7ceca227beff3f2f7cef4da72c58012f00c00280fbbefc95ec89d5e7bf637470d55f4e75f6f500467028fa4841c1c1b954b178e048dc6956c6a05e56e16944c5371857b69f0a1aaa2fa997a00a0250e219047d3b42e20d6aeba83c899c5b9b6d8ba303414eee8b27110721063540e7606874210f193ae4b1111f2e11f38aa9b0fc863cfd29348584997cc0d6aea19e9932c73ae2faa53978c738e329a6d8e18e339468e0b12a3c22c0795f5297bd1d5e4bf3cbd3ea051f1f5e75cf1e9a1cd9ffecfbcf7cf6d1afbef69def58908fdb1c4d7941001600dcf5f56f61bc6fe4ea1d231bfed744d7e0d9e205b1774d51dd59d182577020f170ac172270510136d66a47af47baade26ec91a978da8b5d26565d279541a72936453dc8d331054f59d3200bb7ba917e2c09092b094ab19e44e892bf1da80693b29ac624cac2fec86b32dcebb2bf7c79c8de09cbe7934a5afe48151b962d502264d64b5794cc767a02c8815b41c35af087dd3fbb7ac19ddfaaf872646bff3c6b7fe669b86a75679c1001697afddf5c34bb78f6cf8ef077a865f4f591665dc782c01f2cc1d5b49482d0366488dabcd65e9633c65fd4e1e9f21f640142cf519b66254b5b4f6bc0c236d1c3f18c1e74f69e2d7dc57218f76382a0905780bcf74c2ea5175e2b86abcf45a695ecebbb0406cc29f34b465cfc1f5e9f952f9f88b784d650411afd885940a60e933a7d22ea53394e9f368cefc563a0bfba1e9073d58da662d9f27afb23cc7c0e4d8bd6b47b7fea7ebdf78d903780195171c6001c077feeeb6d55b579df3b95d43eb7e4341cbba5a41d63cf5a4bc286bb1b6d5e50d601c1606259419ab02a5fdb8ebe6460998649c363b63475034cc55c12e862b2b36d2ba8943e701048e7316f02534318dc9f0bdf4b89101ffaa3a699eb08a2f2500e57b62518a7f4acfe459b04a0c06cfa18aff0092f999d0d45229ebdbc61d3b46bc0a798e15e34fdfb9e1d9cd375dfdf6373d531ee8d42ea7dc39ac2329a7effaf93397fee2ee37af1b7dfc53f5e6eca41ce7649935b2c9874c39e722bb6e822024fd8644c959845223eb64956ca5e29e153db69c21149e818734e71f96e971b9ae08cb44a6aaaf1b38149279b30765e75890ea78145f6a5efcdc5712e22430cfe314df0c28f05caa7495fbf5ce8382a25454e0119049f8922ab63d2c4a099d76ce3e4436fd86f98d82dc92614c5d324b10e0765b0dc932e6f3e555bd393bb96ef4f14f5dfa8bbbdfbc61d7e6171c58012f500f8bcb1ddffc4ecf8ea5a7fff6bebe657f38ddd1b3d2e68e4a0695af5b054c8d236053445ac547042541a358a9f2549584315cd7873af65ed24cbc18212e6d9fd0e3b371edea576f08547e33fc518f3109c94a49b8f6b7aa5d533f866be39850f6042bfb6b5bfc7382e9c44a7dcfdbbe623a6997d56ef873e31511ba6627772e99d8f3d1357b7ff5c56bde7cf549f955e613515ed0800500f77ef5ebd9f6910d17efed5ff185fdbdc31b286499c98446858f6a2c9e122b800fd7bce257e47210e12ea9c7c354329ba2e58d39147f64c21e4b8857081ad2917a689a840e95002734da30c8cc571fbc2ebc98e893ba30a7dde397a9624a9a05cacb12f019e40f9c5bf301679b504d3a885e13f3280edc660db5895e47e415e723a571c58ea8b50c1e34231d55bc4d78ce6bed8ea804a8383e075e81281f3c34b675e9c15d37aed9bbf5a15fbbfeada7fc4ee07ce5050f585ceefbcadfadfef9da577e726c60c57b666b1d45281c8cb44457c68aa6e67b8a122cd00910b5cb7c954f66b927e90be9834ffdc77f232d169412474ac6525956b0494db1808f999757b26a1f93ec7c535d3274b5718a126555ba7cce8b95d5d427b810893d4dfeb72af1ef8a09bf5d3d1e97cc67e7b6c5ab555e9d066ace0b233309f5cc49409be94d79cf74a7b9b7a3e155476b361f3eb0ebafcfdefee38fbcee86eb5f9021605a5e34800500dff9fbdbfb460756fece53cbcffae84ca37b04a9d7e0c00b4801c76aabbb27ed4212c2791f2b0d195280ab5a8e3404aada2d2c3762fa19aa28c1b160d4cd7b15e998e928ce1b909b297025fdd91a4e618d020a6510803a5cb8e8e7e03d281f4e596faa5dbfe5947ee1a819af8d09b46dcc9a1ac7ced36377f6c878d1150ed591f00a44e89c9b1a7dc9eec7fe70e4c0cecf5efdb66b4fa907989f4f7951011600dc77cbd7b21d4b4fbf72e7f0fa3f99e81e3c879065d598614213b0205ae5281f25702589504af027826b40d07a5356f9d23152cd607a63fb741ec59f001f2641c74530de6310da45c7cc706e1710657ef813e04543a1cbb4f1bb78c5b86908ac8e8f1ec790c78b4c98a6a48736bc81efbbc45672fc49062f832faf9d80987a781c5abb83b395bc525e5b213b1caf3250de37b57ff3cab16dbfb766ecc9ef5e71c3db5fd021605a5e7480c5e52b773fb07af7d0ba3fd8df33fc2fe6ea8d4c1f83016c28587c75beb8088f2aaec29b3d77638b9cf509be6e39acf2792a0b693a0e041624196c0083cffa58faacc720dbf4d2bffe55cbce00c174dbb35a8572bb9c93f354e2fc6de8896026c110aec59d3fab387cab5e52e27d90e929e5a501d950458780910725eb69173d2bf8948ea180790501389bf32cbab263da7ecb61e07cbc6ab4e6f2c1c9b1ffb37cfce98fddf0eb97be2842c0b4bc68010b006ebde3de8ea98edeeb768c9cf1c9439dfd67521654d95cb1b926850bb19451582bb256f26fa1cb218d565c3d67f609f11114eb0db9de4cff29ac95031cb7d1d00604d37ecbee4835cd6eac2427688145c6699b3f4b3c0e25b73cba77b21c3069574990e759607d5b04c77b0f30141bbbc43c50e1b5a6de2bdc3cbd6c99891e865721cfd13b73f0f135a34f7ca47bf6d0add75df3fae45d702f9ef2a2062c2edfb8e3be73760fadf9d89ec155d735eb9d3dcecb21ef8d0048940db002dbaed800d3bbfd562152b0ab4ab623a1a1fabbeb5b7a9abf7e293c034ab496db973d37ad13c7d68ecaedd3108e605ef5d29ea7be3be555994f2652747c4992d9a19ae3ede07abe9b555edee1dab5e355bd3933b96cffb3b72e1fdff1076fb9e68acded4879b19445c08ae51fbe7c4bcfd61567bf7dcfe09a3f99e81e1ce178c4855c80f30e9c7f6342c4f9ce32395070e11c90024a111998904d945c43547bad30d476db9d55b0b0e2e96e95af4f063b92dc92f31e7467ac0ce446cd53a04932f9f61199f44880844aec69b401669bd4d647f43cad6e9e3075621c573e3b55f417927b2ea11f415a42fc248c2b037f3b43d10ef10ac9ea9bda3fba6cff8edfdbb06bcbdfbde65def7cc19ead3a9ab2085849f9cedfdfbefa176b5ff1f1fd3d4bded3ac7776a5aebc58e1f8e02c995c0ee75658417c696fabab7249656f8c131fa1a29bb2d756baa67129dc992b73afea2c91034ea3e84549c2ad607925891aa1b9742e2df1ba348f53e1152591b07c75cde3e86607b3bcd3ab751d6fb82f0b922e4fa6f75df4db8e4004a5d301a2de16394a5e64566fcd4c0f4eeefbebb3b6ffe4e65f7fdbb52fca5c55bbb2085815e5abdf7eb0e75057df1bc6fb96fdc1c1aec10bf25a3d2b92d9c57d7f708f052e240a45286105829372f5d08a7bd50751e1f332d1fa569ddf99374c343187785288e785e0772bad1721b4711ba79366fe49c0a57385dc537faf9c736b5fdaf45df15d3d9b4873294f96f47c98309eeb3060b7a7617e7364f96aa8153ae340c8f256de3fbdff91a1893d1feb9d9ef8f63bde70c9a2579594dac926602196bffdbf7f3e77cb86fc17931d7ddfa490b566eb9d2fcd6b8d5e51761b32855084245020f1610c3fb36784de794c108528014ffc2ec705cc7757cf79750666ac97108200ad604888df8ca2339dda5f92c70b42b0fb9bee56f2bc1c70440f4dc90a0a7c80f0b11c0efa231fde0b357dc9dac4b9894de13906c55033171f6aebda529c9fdbd14d4365e641fcec5fd06769b3a13df33eca07e5e89a9ddcbd72dfd37f7ae6333ffdf76f79e0af1e3cf703bf3b771422fba2298b1ed61194db6ffbce054f8f9cf9fb63032b7ebb596bc4abe5700a728715d9fb145212735cc8b68d2f8cc75632dd15d65d420cf5327c889878807289fcb927065d3b3fe99343a35411530f27685f49985a8e00fdaede3c534c4a9b0a49a838df7395fabd1cb755d1650f9f4a63547997863c32f742722f967a6b16c307767d71dde8e37f74ed9bae7e64be592f9645c03ae272d737eeea181d58f9865d4bd6fefe6447dfe51226ba38300a70297161933236448000897d5e0cb0405291bc89f5e67f2c883d3aa527ddf9d34755589323c0bab0934cf741fa567fb11ab08eac54f1cadc0ef66219a4cbb96bb61e503e27a053995b34734bdb94fa4e5b57f57998360090b59a79cfecc4fd2bf66dffa391033bbffdc6b7bcf1457b54e168ca22601d65f9f23d3f5e79b06bf09dfb7b97fe87a9ceded3f2ac9ef90db0f2ee97cd0aa549751f7279a7cae73bccee5f02305c6cc8e17709d3f1ad3766e8100043e2a154299eeea25526ebe3f03e290ddd79b31e1ee05261b1a63ed4cca06a422e3747fb03b5910872a1a7f16213b0a7086eceab348067d7801064a340c241d3ac12e81cf9845adecabb670e3d397868ef1ff74fefbfe55d575db4138be588cb22603d87d2fcc487f08373ae5dfbc4aaf33e78a067f8df4c740f0d979d2ad288019ae342a878b816641e783e9cfcb392ab42c18ccd391a724aa8212119b00b5e15cb3e1a157e54399aa4e84806750a2391960f2e2c2403d3692465508ecc8e5939b4b544382b21e356866e21d21a79e3c0583c3a43831ddb809c8fbd51f6e25267144c4f3168ffd4f8d8c0e4d8ff3ce3d947fffcb59befd85edfb8a93cafc5326f5904ace751befbb55bd1ac3586762c3dfd3f8c0dacf8ddc9cebe9584cc6970db079e931d34bddde6babbef4128a9e0da96b7de91689409f29271534fad9d67977a8a5563db736c9614abe86da5b16adc765ea69baff7787d979e1eeb11b60d6d4b9ea7bf5e5502e5e89999d8397c60d79fadd9fbab3faeb7e6c6afbcfeba36135d2c872b8b80750ccaa37ff9d9eca7a75d7ad6a1ae817f35de3bf28e9946d77aca8a0d58f7f80ea2a587f17e24c48bdf6d106277e7b83eb84a85c63bd725c963f135ebb110c92fd088f766e6e53d21030af0de5bd538492065e8e64e4cece8dc480555776acb7a430c1af259e7548d1d5471af2d3a9a1d4cb3fb5ae14515bd90a15fbdd88c7274ce4d6f1b3a34fad5dee903fffbfc271f78ecbcf7ffce8bea41e5e3511601eb1896fbbffc956ccfe0ea73f60cae7edf81eea10f4c75f68d509665800de0d453299f9807bca7a28a06eba9b1571414280a5d578fcbe56624dca4125ef078e0a0b04af113074bcfa44505e53055c226c0131be76240c7bd75c1bb62e239b91f234dc08e73540230b69e195f760263e8260f7e47972addfd949030f6c161a1fff92e9f1f93f6080894e7dd3313a30353e39f5fb6ff99bf5ab6ff99cd97bfeb8645a03a466511b08e53b9e76bb78eec1839e3bdfbfa46fee5819e25e789c715ffb5e00418afc7782cde572a7b3bf33eaa2310997867b19de69eecb849f0543a4b646bfa3ebdf76600c2e6d218a823233c00b3b7a2de4c29e95f8a443df00b83053893337125f7cb7bb36a000e57127a881028c7c0e4be47974c8cfec59ad127fedf55d75f377a041d2d96a32c8b80751ccb7db77c35db33b46668bad17dcd9ec135374d77745f3edbe8eef160523ef3ed533abe96444dc9f99fe2924701b7aba5ade7a138f5d4fc69777b25dd3f83033502126fcf4695950f94b397c6f53c133c7382fd5a3eda51e9b0257df80713bc27eae8f15d97fae9684e4d76cd4eddbf6cff8ecf75cd4dddb16c7cc7f8152f801f2c5da86511b04e50f9f6d7bf35b06dd94b2f6ed6eabfb7af6fd9c5adacbebc95d5eaed1eab29ae71c891846ad173d10b85f7e21ec03520e20f74b253c0215030c7107c3dc43c11997a4c97194ae82f8aad47a5f1750e6d5025e6d514409268d0d2cf6325e1a245a8aa778eb970b0ccec046ccbeb92b59acd5adedcbd6462cf43f556f34fd6eff9e5436f78eb6f1ec06239ee6511b04e42f9d637ef3ef3e9a567bc69aed1f1c1f1de65e7b4b25a56b9fb265fad7f94867a40f5322a80b47b63c061fdad52a8e5bd2ae711557710efb71fa54d94575d497245a6cf3693b0ac738faf9b70b11d28598fd0765f6b35f3a1437b3637e666ff7cddde276efbcd37fffae36d27b6588e4b5904ac93547ef0e5af60f7d09a8e8eb9998b762e5d7fd344d7e06f1cea1a382dcf321837a9628538ec31cff555566e1353191099777bde349fff04bbad084509fe2eb7aa8f1768171cbaa6cf08a675cb9e50fb7781cd33662511e5ba59de42eff48127fba6f7dfb972efb6cfcd363a7fbc7c7cc7ec6bdf75c311f6bb588e655904ac05501efa9b2fd49f5db2feb4dd4bd65e3a5bebbcf160cf9273f290ad6dd61a75072c4ea7aa002a01bae470e5fcf9a076150097fd9284b826c9ed3ba4ca1e22eff6997b0c607667d1ee62b2e7863414a6f8d98c45e651a1e0f1cccd2dbeee594fcf739f09e811a1de9a6b66946fef9fdcb7b9a335f385e5fbb63fb06adfb6272ffe673736b1584e6a5904ac05561ef9bf9fcb762e59bbe1e965675c41217bf7819e25574c75f6f5104256de113309793e0a8018fc9833443690b3bfec53f962bf2471547ae19c7d27bc3d7cc9293593db4a41ae2059c1d7e4b8f5c8844b67f9bc9cade73eb7e9cbe5feaa40df0c1a4079f7ccc4e4c0e4befb02e57fbb6ecf13f7ad1cdfbef582f7ddb498405f406511b01678f9d9e7ff6ce0a9e567bdf38955e7be36cf6a6f98ecec5bdbaa358adf55b4ee0ea0af51815e96039d06b8dc09768a950559e2f758aab6fbab1e8e76872d03927359e92b772ac23803904c83fdd1d3d2e1cd1474e267066954de56002402eaf95cde3333b13dcb5bdf3ee3d99ffde025bb1fbbe5dc0ffcee62f27c019745c03a45caf7befaf58e67969e3632d135b0ba6b6eeac6bdfd2bcf6bd56a67376b1dabe7f88761b944ef07c1c10c0ae5e7cfa967c5611a6013d202402e799f1cea4cc628279f9c8be5ef1d3ef33f6fd23ef508db86ca4468b466f37a6bf6995aabb565e9c19d8f4e37babfd0377de099d57b9f1cfdb577bc75f16d09a7405904ac53b43cf8375fe81a1d5879f6334b376c08945f3bd9d57ff9819ee1e5201aa02ceba090996305dadaa8d300000273494441541a02da334bec156964640fa2eadfb6fd708e0a0c68ec00b9876a228e4863136a027c0ade394f0cb8dcd61e844d8e4a586f8fe2382114f9ae90e7b308e1c0c0e4d8ee9ee983f753c86e5fbd77ebd691033bb75cf2cf6e9c3eee8bb4588e795904ac1748f9f23d3f46a33973da6467df957bfb57aeafe5cd5703b8fc60f792a1b94627000eb7f49196727ca5d7e7dba14be0a5ad10b903a36e4f207d454c6988f63b9304f7a48f2d8db919f44fed1b07707f2babffe3d2833bb7f5cc4c7c77aedef9e4bbaebaa80d958be5542a8b80f5022c3ffaebff8703dd4bba4607577565796ba896cf5db1bf77e925bb87d6ae0d946fa8e5adb5cd5a6378b6d195c9db254a2155c5ae237b6580de7309f10a90291de40410eceb742a8e1ec86e207b4eead105e4e8989bceebadb9b15656db4e21dbba7c7cfbf6c1437b1f6c658dfbf2ac363eb2ffd9e981a97dd3af7acf7b9f2f2b17cb022b8b80f5222adfb8e307758056ce34ba46c6fb46faeaadb9b58dd6dcab66eb1d671fea1a5879b07ba807c05096e77d00755108f53cab6579a8c10294beeb2bc490308289f9692cf7b242fb03b28478bc0031dc04108f3364d44296b7f240d404c2749e651300c6fba7c6277ba70fecec68ce6e99ab357ed4ac35b60f4d8c4e74ce4d8f12c2ceb75ef3dac5e3062f92b208588b45ca9ddfb8ab6bb6d1b9210ff5b5b3f5cee1435d7d3d077a863be6ea9d7d9d7353a7d7f2e66900d6e6211b06d04321abe721abe759addeca6a592bab67f12f00a0963751cb5b792d6fe6b5bc956779ab9951de0c9437014c66948f01d8decaea4fce34ba7fd568ce4c0c4c8ecdf64e4f4c763467c6326a6eef989bd9fa1b6f79e362be69b10000fe3fda793218d0729e850000000049454e44ae426082, '2025-08-06 22:53:22', '2025-08-06 22:53:22', 'c7f5d13d3cc0e804384b0514d632b86a', NULL, '2021-07-27 00:00:00', NULL, '', NULL);

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `appointments`  AS SELECT `an`.`id_appointment` AS `id_appointment`, `an`.`id_patient` AS `id_patient`, `an`.`motive` AS `motive`, `an`.`description` AS `description`, `an`.`historial` AS `historial`, `an`.`appointment_date` AS `appointment_date`, `an`.`nex_appointment_date` AS `nex_appointment_date`, `an`.`register_date` AS `register_date`, `an`.`update_date` AS `update_date`, `an`.`id_user` AS `id_user`, `an`.`id_doc` AS `id_doc`, `an`.`id_status_appointment` AS `id_status_appointment` FROM `appointment_new` AS `an` ;

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
-- Indices de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `appointment_new`
--
ALTER TABLE `appointment_new`
  ADD PRIMARY KEY (`id_appointment`),
  ADD KEY `fk_appointmentnew_appointment_types` (`id_appointment_type`);

--
-- Indices de la tabla `appointment_status`
--
ALTER TABLE `appointment_status`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `appointment_types`
--
ALTER TABLE `appointment_types`
  ADD PRIMARY KEY (`id_appointment_type`);

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
  ADD PRIMARY KEY (`id_patient`),
  ADD KEY `fk_clinic_patients_marital_status` (`id_marital_status`);

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
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_event` (`id_event`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `notification_events`
--
ALTER TABLE `notification_events`
  ADD PRIMARY KEY (`id_event`),
  ADD UNIQUE KEY `event_name` (`event_name`);

--
-- Indices de la tabla `notification_event_roles`
--
ALTER TABLE `notification_event_roles`
  ADD PRIMARY KEY (`id_event_role`),
  ADD KEY `fk_eventrole_event` (`id_event`),
  ADD KEY `fk_eventrole_role` (`id_role`);

--
-- Indices de la tabla `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD PRIMARY KEY (`id_event`,`id_role`),
  ADD KEY `id_role` (`id_role`);

--
-- Indices de la tabla `patients_status`
--
ALTER TABLE `patients_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_status` (`status`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_role`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `appointment_new`
--
ALTER TABLE `appointment_new`
  MODIFY `id_appointment` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `appointment_status`
--
ALTER TABLE `appointment_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `appointment_types`
--
ALTER TABLE `appointment_types`
  MODIFY `id_appointment_type` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `app_logs`
--
ALTER TABLE `app_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `blood_type_catalog`
--
ALTER TABLE `blood_type_catalog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `clinic_patients`
--
ALTER TABLE `clinic_patients`
  MODIFY `id_patient` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clinic_prescription`
--
ALTER TABLE `clinic_prescription`
  MODIFY `id_prescription` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `doc`
--
ALTER TABLE `doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notification_events`
--
ALTER TABLE `notification_events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `notification_event_roles`
--
ALTER TABLE `notification_event_roles`
  MODIFY `id_event_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `patients_status`
--
ALTER TABLE `patients_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `appointment_new`
--
ALTER TABLE `appointment_new`
  ADD CONSTRAINT `fk_appointmentnew_appointment_types` FOREIGN KEY (`id_appointment_type`) REFERENCES `appointment_types` (`id_appointment_type`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `clinic_patients`
--
ALTER TABLE `clinic_patients`
  ADD CONSTRAINT `fk_clinic_patients_marital_status` FOREIGN KEY (`id_marital_status`) REFERENCES `marital_status_catalog` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `notification_events` (`id_event`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notification_event_roles`
--
ALTER TABLE `notification_event_roles`
  ADD CONSTRAINT `fk_eventrole_event` FOREIGN KEY (`id_event`) REFERENCES `notification_events` (`id_event`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eventrole_role` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD CONSTRAINT `notification_roles_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `notification_events` (`id_event`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_roles_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

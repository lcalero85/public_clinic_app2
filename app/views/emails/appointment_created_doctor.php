<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Appointment</title>
    <link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 
</head>
<body>
    <p>Dear Dr. <?= htmlspecialchars($doctor['full_names'] ?? ''); ?>,</p>

    <p>A new appointment has been created and assigned to you:</p>
    <ul>
        <li><strong>Patient:</strong> <?= htmlspecialchars($patient['full_names'] ?? ''); ?></li>
        <li><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date'] ?? ''); ?></li>
        <li><strong>Motive:</strong> <?= htmlspecialchars($appointment['motive'] ?? ''); ?></li>
        <li><strong>Status:</strong> <?= htmlspecialchars($status ?? ''); ?></li>
    </ul>

    <p>Please review it in the system as soon as possible.</p>
</body>
</html>

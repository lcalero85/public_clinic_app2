<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Appointment Created</title>
    <link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 
</head>
<body>
    <p>Dear <?= htmlspecialchars($patient['full_names'] ?? ''); ?>,</p>

    <p>Your appointment has been created successfully:</p>
    <ul>
        <li><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date'] ?? ''); ?></li>
        <li><strong>Motive:</strong> <?= htmlspecialchars($appointment['motive'] ?? ''); ?></li>
        <li><strong>Doctor Assigned:</strong> <?= htmlspecialchars($doctor['full_names'] ?? ''); ?></li>
    </ul>

    <p>Please be on time and contact us if you need to reschedule.</p>
</body>
</html>


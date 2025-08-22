<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Reminder</title>
    <link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 
</head>
<body>
<div class="container">
    <h2>Appointment Reminder</h2>

    <p>Dear <strong><?= htmlspecialchars($patient['full_names'] ?? 'Patient'); ?></strong>,</p>

    <p>This is a friendly reminder that you have an upcoming appointment.</p>

    <div class="details">
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['appointment_date'] ?? ''); ?></p>
        <p><strong>Doctor:</strong> <?= htmlspecialchars($doctor['full_names'] ?? ''); ?></p>
    </div>

    <p>Please make sure to attend on time. If you need to reschedule, kindly log in to the system.</p>

    <div class="footer">
        <p>Thank you!<br>
        <em>Your Clinic Team</em></p>
    </div>
</div>
</body>
</html>

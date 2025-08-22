<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 

<div class="email-container">
    <h3 class="email-header">Appointment Request Denied</h3>

    <p>Dear <strong><?= htmlspecialchars($patient['full_names'] ?? 'Patient'); ?></strong>,</p>

    <p>We regret to inform you that your appointment request has been 
    <strong>denied</strong> due to scheduling limitations.</p>

    <ul class="email-list">
        <li><strong>Reason from Administrator:</strong> 
            <?= htmlspecialchars($adminResponse ?? 'Not specified'); ?>
        </li>
    </ul>

    <p>If you believe this is a mistake or you would like to reschedule, 
    please log in with your credentials and submit a new request.</p>

    <div class="email-footer">
        <p>Thank you for your understanding.<br>
        <strong>Your Clinic Team</strong></p>
    </div>
</div>


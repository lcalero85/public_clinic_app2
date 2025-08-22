<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 

<div class="email-container">
    <h3 class="email-header">Your Appointment Request</h3>

    <p>Your appointment request has been submitted successfully with the following details:</p>

    <ul class="email-list">
        <li><strong>Motive:</strong> 
            <?= isset($appointment['motive']) && !empty($appointment['motive']) 
                ? htmlspecialchars($appointment['motive']) 
                : "Not provided"; ?>
        </li>
        <li><strong>Description:</strong> 
            <?= isset($appointment['description']) && !empty($appointment['description']) 
                ? htmlspecialchars($appointment['description']) 
                : "Not provided"; ?>
        </li>
        <li><strong>Requested Date:</strong> 
            <?= isset($appointment['requested_date']) && !empty($appointment['requested_date']) 
                ? htmlspecialchars($appointment['requested_date']) 
                : "Not provided"; ?>
        </li>
    </ul>

    <div class="email-footer">
        <p>We will notify you once your appointment request is reviewed by the administrator.</p>
    </div>
</div>



<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 

<div class="email-container">
    <h3 class="email-header">
        Dear <?php echo htmlspecialchars($patient['full_names']); ?>,
    </h3>

    <p>We are pleased to inform you that your appointment request has been 
    <strong>approved</strong>.</p>

    <ul class="email-list">
        <li><strong>Motive:</strong> 
            <?php echo !empty($appointment['motive']) 
                ? htmlspecialchars($appointment['motive']) 
                : "Not provided"; ?>
        </li>
        <li><strong>Description:</strong> 
            <?php echo !empty($appointment['description']) 
                ? htmlspecialchars($appointment['description']) 
                : "Not provided"; ?>
        </li>
        <li><strong>Requested Date:</strong> 
            <?php echo !empty($appointment['requested_date']) 
                ? htmlspecialchars($appointment['requested_date']) 
                : "Not provided"; ?>
        </li>
        <li><strong>Confirmed Date:</strong> 
            <?php echo !empty($appointment['appointment_date']) 
                ? htmlspecialchars($appointment['appointment_date']) 
                : "Not scheduled"; ?>
        </li>
        <li><strong>Doctor:</strong> 
            <?php echo !empty($doctor['full_names']) 
                ? htmlspecialchars($doctor['full_names']) 
                : "Not assigned"; ?>
        </li>
        <li><strong>Status:</strong> <?php echo $status; ?></li>
    </ul>

    <p>Please log in with your credentials to review the details of your approved appointment 
    and manage any future requests.</p>

    <p>We look forward to seeing you on your scheduled date.</p>

    <div class="email-footer">
        <p>Thank you,<br>
        <strong>Your Clinic Team</strong></p>
    </div>
</div>

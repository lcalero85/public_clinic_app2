<h3>Your Appointment Request</h3>

<p>Your appointment request has been submitted successfully with the following details:</p>

<ul>
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


<h3>New Appointment Request Received</h3>

<ul>
    <li><strong>Patient:</strong> 
        <?= isset($patient['full_names']) && !empty($patient['full_names']) 
            ? htmlspecialchars($patient['full_names']) 
            : "Not provided"; ?>
    </li>
    <li><strong>Email:</strong> 
        <?= isset($patient['email']) && !empty($patient['email']) 
            ? htmlspecialchars($patient['email']) 
            : "Not provided"; ?>
    </li>
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

<p>You can review the request in the system:</p>
<p>
   <p>
    Please log in with your credentials to the system.<br>
    Once logged in, you will be able to review all pending appointment requests from your dashboard.
</p>
</p>


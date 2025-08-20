<h3>New Appointment Request Received</h3>

<ul>
    <li><strong>Patient:</strong> <?php echo htmlspecialchars($patient['full_names']); ?></li>
    <li><strong>Email:</strong> <?php echo htmlspecialchars($patient['email']); ?></li>
    <li><strong>Motive:</strong> <?php echo htmlspecialchars($appointment['motive']); ?></li>
    <li><strong>Description:</strong> <?php echo htmlspecialchars($appointment['description']); ?></li>
    <li><strong>Requested Date:</strong> <?php echo htmlspecialchars($appointment['requested_date']); ?></li>
</ul>

<p>You can review the request in the system:</p>
<p><a href="<?php echo SITE_ADDR; ?>/appointments/view/<?php echo $appointment['id']; ?>">Review Appointment Request</a></p>

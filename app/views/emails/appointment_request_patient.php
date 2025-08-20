<h3>Dear <?php echo htmlspecialchars($patient['full_names']); ?>,</h3>

<p>Your appointment request has been submitted successfully with the following details:</p>

<ul>
    <li><strong>Motive:</strong> <?php echo htmlspecialchars($appointment['motive']); ?></li>
    <li><strong>Description:</strong> <?php echo htmlspecialchars($appointment['description']); ?></li>
    <li><strong>Requested Date:</strong> <?php echo htmlspecialchars($appointment['requested_date']); ?></li>
</ul>

<p>Our team will confirm your appointment shortly.</p>

<p>Thank you,<br>Clinic Administration</p>

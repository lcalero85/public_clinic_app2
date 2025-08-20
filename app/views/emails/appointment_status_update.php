<h3>Dear <?php echo htmlspecialchars($patient['full_names']); ?>,</h3>

<p>The status of your appointment has been updated.</p>

<ul>
    <li><strong>Motive:</strong> <?php echo htmlspecialchars($appointment['motive']); ?></li>
    <li><strong>Description:</strong> <?php echo htmlspecialchars($appointment['description']); ?></li>
    <li><strong>Requested Date:</strong> <?php echo htmlspecialchars($appointment['requested_date']); ?></li>
    <li><strong>New Status:</strong> <?php echo htmlspecialchars($status); ?></li>
</ul>

<p>If you have any questions, please contact our office.</p>

<p>Thank you,<br>Clinic Administration</p>

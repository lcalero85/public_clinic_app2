<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/emails.css"> 
</head>
<body>
  <h2>Expired Appointments Report</h2>
  <p>The following appointments were marked as <b>expired</b> on <?= $reportDate ?>:</p>
  
  <?php if (!empty($appointments)): ?>
  <table>
    <tr>
      <th>Patient</th>
      <th>Doctor</th>
      <th>Appointment Date</th>
    </tr>
    <?php foreach ($appointments as $appt): ?>
    <tr>
      <td><?= htmlspecialchars($appt['patient_name']) ?></td>
      <td><?= htmlspecialchars($appt['doctor_name']) ?></td>
      <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
  <?php else: ?>
    <p>No expired appointments found.</p>
  <?php endif; ?>
</body>
</html>

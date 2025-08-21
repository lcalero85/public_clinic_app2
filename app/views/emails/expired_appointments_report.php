<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: Arial, sans-serif; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; }
    th { background: #f4f4f4; }
  </style>
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

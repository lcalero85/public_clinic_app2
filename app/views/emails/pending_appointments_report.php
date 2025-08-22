<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #2c3e50;
        }
        p {
            margin: 8px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 13px;
        }
        table th {
            background-color: #f4f4f4;
            text-align: left;
        }
        table tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>
<body>
    <h2>Pending Appointments Report</h2>
    <p>Report generated on <strong><?php echo $reportDate; ?></strong></p>
    <p>The following appointments are still pending confirmation and need to be managed:</p>

    <?php if (!empty($appointments)) : ?>
        <table>
            <thead>
                <tr>
                    <th>ID Request</th>
                    <th>Request Date</th>
                    <th>Patient Register Request</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['id_appointment']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($a['requested_date']) ?>
                        </td>
                        <td><?php echo htmlspecialchars($a['patient_name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p><em>No pending appointments found.</em></p>
    <?php endif; ?>
</body>
</html>

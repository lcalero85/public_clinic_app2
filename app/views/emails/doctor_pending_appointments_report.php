<style>
/* ================================
   Estilos Globales para Emails
   ================================ */

/* Fondo general suave */
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: 14px;
    color: #2c3e50;
    background-color: #f4f7fa;
    margin: 0;
    padding: 20px;
}

/* Contenedor principal */
.email-container {
    background: #ffffff;
    border-radius: 8px;
    padding: 20px;
    max-width: 700px;
    margin: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Encabezados */
h2 {
    color: #34495e;
    font-size: 20px;
    margin-bottom: 10px;
    border-left: 5px solid #3498db;
    padding-left: 10px;
}

/* Párrafos */
p {
    line-height: 1.6;
    margin: 10px 0;
}

/* Tablas */
table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 15px;
    background-color: #fff;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

th, td {
    border: 1px solid #e0e0e0;
    padding: 10px;
    text-align: left;
}

th {
    background: #3498db;
    color: #fff;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    background: #fafafa;
    color: #333;
}

/* Alternar colores en filas */
tbody tr:nth-child(even) td {
    background: #f2f9fc;
}

/* Footer de correos */
.email-footer {
    margin-top: 20px;
    font-size: 12px;
    color: #7f8c8d;
    text-align: center;
}
</style>
<h2><?php echo $title; ?></h2>
<p>Dear Doctor,</p>
<p>Here is the list of your pending appointments:</p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Motive</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?php echo $appointment['patient']; ?></td>
                <td><?php echo $appointment['motive']; ?></td>
                <td><?php echo $appointment['appointment_date']; ?></td>
                <td><?php echo $appointment['status']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

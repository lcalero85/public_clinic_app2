<?php
// 🔹 Asegurar que las variables existen
$mode = $mode ?? ($view_data['mode'] ?? 'list');
$rows = $rows ?? ($view_data['rows'] ?? []);
$patient = $patient ?? ($view_data['patient'] ?? null);
$appointments = $appointments ?? ($view_data['appointments'] ?? []);
$prescriptions = $prescriptions ?? ($view_data['prescriptions'] ?? []);
?>

<div class="container mt-4">
    <h3 class="text-center mb-4">Clinical Historial</h3>

    <!-- Botones de exportación -->
    <div class="mb-3 text-center">
        <a href="?report=historial_clinico&export=pdf" class="btn btn-danger btn-sm">
            <i class="fa fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="?report=historial_clinico&export=excel" class="btn btn-success btn-sm">
            <i class="fa fa-file-excel"></i> Exportar Excel
        </a>
        <a href="?report=historial_clinico&export=csv" class="btn btn-info btn-sm">
            <i class="fa fa-file-csv"></i> Exportar CSV
        </a>
    </div>

    <?php if ($mode == "list") { ?>
        <!-- 📌 LISTADO GENERAL -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Patient</th>
                        <th>Document Number</th>
                        <th>Birthdate</th>
                        <th>Gender</th>
                        <th>Last Appointment</th>
                        <th>Motive</th>
                        <th>Prescription</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rows)) { ?>
                        <?php foreach ($rows as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_names']); ?></td>
                                <td><?php echo htmlspecialchars($row['document_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['birthdate']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['motive']); ?></td>
                                <td><?php echo htmlspecialchars($row['description_prescription']); ?></td>
                                <td>
                                    <a href="<?php echo print_link("report/historial_clinico?patient_id=" . $row['id_patient']); ?>" 
                                       class="btn btn-sm btn-info">
                                        <i class="fa fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay datos disponibles.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    <?php } elseif ($mode == "single") { ?>
        <!-- 📌 DETALLE DE UN PACIENTE -->
        <?php if ($patient) { ?>
            <h4 class="mb-3">Paciente: <?php echo htmlspecialchars($patient['full_names']); ?></h4>

            <table class="table table-bordered">
                <tr>
                    <th>Document Number</th>
                    <td><?php echo htmlspecialchars($patient['document_number']); ?></td>
                </tr>
                <tr>
                    <th>Birthdate</th>
                    <td><?php echo htmlspecialchars($patient['birthdate']); ?></td>
                </tr>
                <tr>
                    <th>Gender</th>
                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                </tr>
            </table>

            <h5 class="mt-4">Appointments</h5>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Motive</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($appointments)) { ?>
                        <?php foreach ($appointments as $app) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($app['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($app['motive']); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="2" class="text-center">No appointments found</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <h5 class="mt-4">Prescriptions</h5>
            <ul class="list-group">
                <?php if (!empty($prescriptions)) { ?>
                    <?php foreach ($prescriptions as $presc) { ?>
                        <li class="list-group-item"><?php echo htmlspecialchars($presc); ?></li>
                    <?php } ?>
                <?php } else { ?>
                    <li class="list-group-item text-center">No prescriptions found</li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <div class="alert alert-warning text-center">Patient not found</div>
        <?php } ?>
    <?php } ?>
</div>


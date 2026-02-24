<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    <hr>

    <div class="row text-center mb-4">
        <div class="col-md-2"><div class="card p-3 bg-info text-white">Doctors<br><?= $totalDoctors ?></div></div>
        <div class="col-md-2"><div class="card p-3 bg-warning text-dark">Pending Doctors<br><?= $totalPendingDoctors ?></div></div>
        <div class="col-md-2"><div class="card p-3 bg-success text-white">Patients<br><?= $totalPatients ?></div></div>
        <div class="col-md-2"><div class="card p-3 bg-danger text-white">Admins<br><?= $totalAdmins ?></div></div>
        <div class="col-md-2"><div class="card p-3 bg-secondary text-white">Appointments<br><?= $totalAppointments ?></div></div>
        <div class="col-md-2"><div class="card p-3 bg-dark text-white">Pending Appointments<br><?= $totalPendingAppointments ?></div></div>
    </div>

    <h3>Recent Appointments</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Doctor</th>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($recentAppointments as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= $a['doctor_name'] ?></td>
                <td><?= $a['patient_name'] ?></td>
                <td><?= $a['appointment_date'] ?></td>
                <td><?= $a['appointment_time'] ?></td>
                <td><?= $a['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
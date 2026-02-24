<?php
namespace App\Controllers\Patient;

use App\Database\Connection;
use PDO;
use App\Constants\Status;

class PatientDashboard {

    public function index() {
        

        // Login check
        if (!isset($_SESSION['user'])) {
            http_response_code(Status::UNAUTHORIZED);
            echo json_encode(['error' => 'Login required']);
            exit;
        }

        // Role check
        if ($_SESSION['user']['role'] !== 'patient') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Patient access only']);
            exit;
        }

        $db = Connection::make();
        $patientId = $_SESSION['user']['id'];

        // Total appointments booked by patient
        $totalAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE patient_name = (SELECT name FROM users WHERE id=?)");
        $totalAppointments->execute([$patientId]);
        $totalAppointments = $totalAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Upcoming appointments
        $upcomingAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE patient_name = (SELECT name FROM users WHERE id=?) AND status='approved'");
        $upcomingAppointments->execute([$patientId]);
        $upcomingAppointments = $upcomingAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Completed appointments
        $completedAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE patient_name = (SELECT name FROM users WHERE id=?) AND status='completed'");
        $completedAppointments->execute([$patientId]);
        $completedAppointments = $completedAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Recent appointments
        $recentAppointments = $db->prepare("SELECT * FROM appointments WHERE patient_name = (SELECT name FROM users WHERE id=?) ORDER BY created_at DESC LIMIT 5");
        $recentAppointments->execute([$patientId]);
        $recentAppointments = $recentAppointments->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON
        echo json_encode([
            'totalAppointments' => $totalAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'completedAppointments' => $completedAppointments,
            'recentAppointments' => $recentAppointments
        ]);
    }
}
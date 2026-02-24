<?php
namespace App\Controllers\Doctor;

use App\Database\Connection;
use PDO;
use App\Constants\Status;

class DoctorDashboard {

    public function index() {
        // Session already started globally

        // Login check
        if (!isset($_SESSION['user'])) {
            http_response_code(Status::UNAUTHORIZED);
            echo json_encode(['error' => 'Login required']);
            exit;
        }

        // Role check
        if ($_SESSION['user']['role'] !== 'doctor') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Doctor access only']);
            exit;
        }

        $db = Connection::make();
        $doctorId = $_SESSION['user']['id'];

        // Total appointments assigned to this doctor
        $totalAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE doctor_name = (SELECT name FROM users WHERE id=?)");
        $totalAppointments->execute([$doctorId]);
        $totalAppointments = $totalAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Pending appointments
        $pendingAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE doctor_name = (SELECT name FROM users WHERE id=?) AND status='pending'");
        $pendingAppointments->execute([$doctorId]);
        $pendingAppointments = $pendingAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Completed appointments
        $completedAppointments = $db->prepare("SELECT COUNT(*) as total FROM appointments WHERE doctor_name = (SELECT name FROM users WHERE id=?) AND status='completed'");
        $completedAppointments->execute([$doctorId]);
        $completedAppointments = $completedAppointments->fetch(PDO::FETCH_ASSOC)['total'];

        // Recent appointments
        $recentAppointments = $db->prepare("SELECT * FROM appointments WHERE doctor_name = (SELECT name FROM users WHERE id=?) ORDER BY created_at DESC LIMIT 5");
        $recentAppointments->execute([$doctorId]);
        $recentAppointments = $recentAppointments->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON
        echo json_encode([
            'totalAppointments' => $totalAppointments,
            'pendingAppointments' => $pendingAppointments,
            'completedAppointments' => $completedAppointments,
            'recentAppointments' => $recentAppointments
        ]);
    }
}
<?php
namespace App\Controllers\Admin;

use App\Database\Connection;
use PDO;
use App\Constants\Status; // for HTTP status codes

class AdminDashboard {

    public function index() {
        // ✅ No need for session_start() here because it's already started

        // Login check
        if (!isset($_SESSION['user'])) {
            http_response_code(Status::UNAUTHORIZED);
            echo json_encode(['error' => 'Login required']);
            exit;
        }

        // Role check
        if ($_SESSION['user']['role'] !== 'admin') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Admin access only']);
            exit;
        }

        $db = Connection::make();

        // Fetch dashboard stats
        $totalDoctors = $db->query("SELECT COUNT(*) as total FROM users WHERE role='doctor'")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPatients = $db->query("SELECT COUNT(*) as total FROM users WHERE role='patient'")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalAdmins = $db->query("SELECT COUNT(*) as total FROM users WHERE role='admin'")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalAppointments = $db->query("SELECT COUNT(*) as total FROM appointments")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPendingDoctors = $db->query("SELECT COUNT(*) as total FROM users WHERE role='doctor' AND status='pending'")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalPendingAppointments = $db->query("SELECT COUNT(*) as total FROM appointments WHERE status='pending'")->fetch(PDO::FETCH_ASSOC)['total'];

        $recentAppointments = $db->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON for API / Postman
        echo json_encode([
            'totalDoctors' => $totalDoctors,
            'totalPendingDoctors' => $totalPendingDoctors,
            'totalPatients' => $totalPatients,
            'totalAdmins' => $totalAdmins,
            'totalAppointments' => $totalAppointments,
            'totalPendingAppointments' => $totalPendingAppointments,
            'recentAppointments' => $recentAppointments
        ]);
    }
}
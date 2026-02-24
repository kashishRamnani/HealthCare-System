<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Middleware/Admin.php';
use function App\Middleware\requireRole;

use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Auth\ForgotPasswordController;
use App\Controllers\Patient\BookAppointmentController;
// use App\Controllers\Doctor\PatientsController;
use App\Controllers\Admin\patients;
use App\Controllers\Admin\Appointments;
use App\Controllers\Doctor\DoctorDashboard;
use App\Controllers\Patient\PatientDashboard;
use App\Constants\Status;
use App\Controllers\Admin\Doctors;
use App\Controllers\Admin\AdminDashboard;
use App\Controllers\Patient\MyAppointment;
use App\Controllers\NotificationController;
use App\Controllers\Doctor\DoctorAppointments;
// -------------------
// Normalize URI
// -------------------
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$basePath = '/HealthCare-System/public';
if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}
$uri = '/' . trim($uri, " \t\n\r\0\x0B/");
if ($uri === '') $uri = '/';

$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

function route($path, $httpMethod, $callback) {
    global $uri, $method;
    if ($uri === $path && $method === $httpMethod) {
        $callback();
        exit;
    }
}

// -------------------
// Auth Routes
// -------------------
route('/register', 'POST', function() {
    (new RegisterController())->register();
});
route('/verify-otp', 'POST', function() {
    (new RegisterController())->verify();
});
route('/login', 'POST', function() {
    (new LoginController())->login();
});
route('/logout', 'POST', function() {
    (new LogoutController())->logout();
});
route('/forgot', 'POST', function() {
    (new ForgotPasswordController())->forgot();
});
route('/reset-password', 'POST', function() {
    (new ForgotPasswordController())->reset();
});


// -------------------
// Doctor Routes
// -------------------

route('/doctor/appointments/complete', 'POST', function() {
    requireRole('doctor');
    (new DoctorAppointments())->complete();
});

route('/patient/appointments', 'POST', function() {
    requireRole('patient');
    (new BookAppointmentController())->store();
});

// View all appointments for logged-in patient
route('/patient/appointments', 'GET', function() {
    requireRole('patient');
    (new MyAppointment())->index($_SESSION['user']['id']); 
});
route('/patient/dashboard', 'GET', function() {
    requireRole('patient');
    (new PatientDashboard())->index();
});
// View single appointment
if ($method === 'GET' && preg_match('#^/patient/appointments/(\d+)$#', $uri, $matches)) {
    requireRole('patient');
    (new MyAppointment())->show((int)$matches[1]);
    exit;
} 
route('/doctor/appointments', 'GET', function() {
    requireRole('doctor');
    (new Appointments())->index();
});
route('/doctor/dashboard', 'GET', function() {
    requireRole('doctor');
    (new DoctorDashboard())->index();
});

// if ($method === 'GET' && preg_match('#^/admin/appointments/(\d+)$#', $uri, $matches)) {
//     requireRole('doctor');
//     (new Appointments())->show((int)$matches[1]);
//     exit;
// }

route('/doctor/appointments/approve', 'POST', function() {
    requireRole('doctor');
    (new Appointments())->approve();
});

route('/doctor/appointments/cancel', 'POST', function() {
    requireRole('doctor');
    (new Appointments())->cancel();
});


route('/admin/dashboard', 'GET', function() {
    requireRole('admin'); // ✅ ensures only admin can access
    (new AdminDashboard())->index();
});
route('/admin/appointments', 'GET', function() {
    (new Appointments())->index(); 
});

// Get single appointment by ID
if ($method === 'GET' && preg_match('#^/admin/appointments/(\d+)$#', $uri, $matches)) {
    (new Appointments())->getAppointmentById((int)$matches[1]); 
    exit;
}

route('/admin/appointments/approve', 'POST', function() {
    requireRole('admin');
    (new Appointments())->approve();
});

route('/admin/appointments/cancel', 'POST', function() {
    requireRole('admin');
    (new Appointments())->cancel();
});

route('/admin/doctors', 'GET', function () {
    requireRole('admin');
    (new Doctors())->index();
});

if ($method === 'GET' && preg_match('#^/admin/doctors/(\d+)$#', $uri, $matches)) {
    requireRole('admin');
    (new Doctors())->getById((int)$matches[1]);
    exit;
}

if ($method === 'PUT' && preg_match('#^/admin/doctors/(\d+)/status$#', $uri, $matches)) {
    requireRole('admin');
    (new Doctors())->updateDoctorStatus((int)$matches[1]);
    exit;
}

if ($method === 'DELETE' && preg_match('#/admin/doctors/(\d+)$#', $uri, $matches)) {
    requireRole('admin');
    (new Doctors())->delete((int)$matches[1]);
    exit;
}
route('/admin/patients', 'GET', function () {
    requireRole('admin');
    (new patients())->index();
});
if ($method === 'GET' && preg_match('#^/admin/patients/(\d+)$#', $uri, $matches)) {
    requireRole('admin');
    (new patients())->getPatientById((int)$matches[1]);
    exit;
}
if ($method === 'DELETE' && preg_match('#^/admin/patients/(\d+)$#', $uri, $matches)) {
    requireRole('admin');
    (new Patients())->delete((int)$matches[1]);
    exit;
}

// Get all notifications for logged-in user
if ($method === 'GET' && preg_match('#^/notifications$#', $uri)) {
    $userId = $_SESSION['user']['id'] ?? ($_GET['user_id'] ??1); 
    (new NotificationController())->index($userId);
    exit;
}

// POST create notification
if ($method === 'POST' && preg_match('#^/notifications$#', $uri)) {
    (new NotificationController())->create();
    exit;
}

// POST mark notification as read
if ($method === 'POST' && preg_match('#^/notifications/(\d+)/read$#', $uri, $matches)) {
    (new NotificationController())->markRead((int)$matches[1]);
    exit;
}

// -------------------
// 404 Fallback
// -------------------
http_response_code(Status::NOT_FOUND);
echo json_encode(['message' => 'Route not found']);
exit;
?>
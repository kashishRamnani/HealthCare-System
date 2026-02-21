<?php
namespace App\Controllers\Admin;

use App\Services\AppointmentService;
use App\Repositories\AppointmentRepository;
use App\Constants\Status;

class Appointments
{
    private AppointmentService $service;

    public function __construct()
    {
        
        $repo = new AppointmentRepository();
        $this->service = new AppointmentService($repo);
    }

    private function adminOnly(): void
    {
         
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Admin access only']);
            exit;
        }
    }



    public function index(): void
    {
        $this->adminOnly(); 
        header('Content-Type: application/json');

        try {
            $appointments = $this->service->getAllAppointments();
            http_response_code(Status::SUCCESS);
            echo json_encode($appointments ?? []);
        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getAppointmentById(int $appointmentId): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');
        try {
            $appointment = $this->service->getAppointmentById($appointmentId);

            if (!$appointment) {
                http_response_code(Status::NOT_FOUND);
                echo json_encode(['message' => "Appointment with ID {$appointmentId} not found"]);
                return;
            }

            http_response_code(Status::SUCCESS);
            echo json_encode($appointment);
        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function approve(): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $appointmentId = $data['appointment_id'] ?? null;

        if (!$appointmentId) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode(['error' => 'appointment_id is required']);
            return;
        }

        $this->service->updateStatus((int)$appointmentId, 'approved', 'admin');
        http_response_code(Status::SUCCESS);
        echo json_encode(['message' => 'Appointment approved']);
    }

    public function cancel(): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);
        $appointmentId = $data['appointment_id'] ?? null;

        if (!$appointmentId) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode(['error' => 'appointment_id is required']);
            return;
        }

        $this->service->updateStatus((int)$appointmentId, 'cancelled', 'admin');
        http_response_code(Status::SUCCESS);
        echo json_encode(['message' => 'Appointment cancelled']);
    }

    public function delete(int $appointmentId): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');

        if (!$this->service->getAppointmentById($appointmentId)) {
            http_response_code(Status::NOT_FOUND);
            echo json_encode(['error' => 'Appointment not found']);
            return;
        }

        $this->service->deleteAppointment($appointmentId);
        http_response_code(Status::SUCCESS);
        echo json_encode(['message' => 'Appointment deleted successfully']);
    }
}
?>
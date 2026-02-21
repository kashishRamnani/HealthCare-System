<?php
namespace App\Controllers\Doctor;

use App\Services\AppointmentService;
use App\Repositories\AppointmentRepository;
use App\Exceptions\ValidationException;
use App\Constants\Status;

class DoctorAppointments
{
    private AppointmentService $service;

    public function __construct()
    {
        $repo = new AppointmentRepository();
        $this->service = new AppointmentService($repo);
        $this->doctorOnly();
    }

    private function doctorOnly(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'doctor') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Doctor access only']);
            exit;
        }
    }

    public function index(): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->service->getAllAppointments());
    }

    public function show(int $appointmentId): void
    {
        header('Content-Type: application/json');
        $appointment = $this->service->getAppointmentById($appointmentId);
        if (!$appointment) {
            http_response_code(Status::NOT_FOUND);
            echo json_encode(['error' => 'Appointment not found']);
            return;
        }
        echo json_encode($appointment);
    }

    public function complete(): void
    {
        header('Content-Type: application/json');

        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $appointmentId = $data['appointment_id'] ?? null;

            if (!$appointmentId) {
                throw new ValidationException("appointment_id is required");
            }

            $this->service->updateStatus((int)$appointmentId, 'completed', 'doctor');

            echo json_encode([
                'message' => 'Appointment marked as completed',
                'appointment_id' => $appointmentId
            ]);

        } catch (ValidationException $e) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>
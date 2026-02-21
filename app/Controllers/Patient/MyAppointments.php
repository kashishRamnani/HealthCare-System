<?php
namespace App\Controllers\Patient;

use App\Services\AppointmentService;
use App\Repositories\AppointmentRepository;
use App\Constants\Status;
use App\Exceptions\ValidationException;

class MyAppointment
{
    private AppointmentService $service;

    public function __construct()
    {
        $this->service = new AppointmentService(new AppointmentRepository());
    }

    // List all appointments for a patient
    public function index(int $patientId): void
    {
        header('Content-Type: application/json');

        $appointments = $this->service->getAllAppointments($patientId);

        if (empty($appointments)) {
            http_response_code(Status::NOT_FOUND);
            echo json_encode(['message' => 'No appointments found']);
            return;
        }

        http_response_code(Status::SUCCESS);
        echo json_encode($appointments);
    }

    // Show single appointment details
    public function show(int $appointmentId): void
    {
        header('Content-Type: application/json');

        $appointment = $this->service->getAppointmentById($appointmentId);

        if (!$appointment) {
            http_response_code(Status::NOT_FOUND);
            echo json_encode(['error' => 'Appointment not found']);
            return;
        }

        http_response_code(Status::SUCCESS);
        echo json_encode($appointment);
    }

   

    // Update or reschedule an appointment
    // public function update(int $appointmentId): void
    // {
    //     header('Content-Type: application/json');
    //     $data = json_decode(file_get_contents("php://input"), true);

    //     $appointment = $this->service->getAppointmentById($appointmentId);
    //     if (!$appointment) {
    //         http_response_code(Status::NOT_FOUND);
    //         echo json_encode(['error' => 'Appointment not found']);
    //         return;
    //     }

    //     try {
    //         $this->service->updateAppointment($appointmentId, $data);
    //         http_response_code(Status::SUCCESS);
    //         echo json_encode(['message' => 'Appointment updated']);
    //     } catch (ValidationException $e) {
    //         http_response_code(Status::BAD_REQUEST);
    //         echo json_encode(['error' => $e->getMessage()]);
    //     }
    // }

    

    // Delete an appointment
    
}
?>

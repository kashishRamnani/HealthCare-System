<?php
namespace App\Controllers\Patient;

use App\Services\AppointmentService;
use App\Repositories\AppointmentRepository;
use App\Repositories\UserRepository;
use App\Repositories\PatientRepository;
use App\Requests\AppointmentRequest;
use App\Exceptions\ValidationException;
use App\Constants\Status;

class BookAppointmentController
{
    private AppointmentService $service;
    private AppointmentRepository $repo;
    private UserRepository $userRepo;
    private PatientRepository $patientRepo;

    public function __construct()
    {
        $this->repo = new AppointmentRepository();
        $this->service = new AppointmentService($this->repo);
        $this->userRepo = new UserRepository();
        $this->patientRepo = new PatientRepository();
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user'])) {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Login required']);
            return;
        }

       
        if ($_SESSION['user']['role'] !== 'patient') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Only patients can book appointments']);
            return;
        }

        try {
          
            $data = json_decode(file_get_contents("php://input"), true);

            if (!$data) throw new ValidationException("Invalid JSON data");

           
            $patient = $this->patientRepo->findByUserId($_SESSION['user']['id']);
            if (!$patient) throw new \Exception("Patient record not found");
            $data['patient_name'] = $patient['name'];

         
            if (empty($data['doctor_name'])) {
                throw new ValidationException("doctor_name is required");
            }
            $doctor = $this->userRepo->findByName($data['doctor_name'], 'doctor');
            if (!$doctor || $doctor['status'] !== 'approved') {
                throw new ValidationException("Doctor not found or not approved");
            }
            $data['doctor_name'] = $doctor['name'];

           
            if (empty($data['status'])) {
                $data['status'] = 'pending';
            }

           
            $request = new AppointmentRequest($data);
            $request->validate();

           
            $appointmentId = $this->service->bookAppointment($request->getData());
            $dataToReturn = $request->getData();
            $dataToReturn['appointment_id'] = $appointmentId;
            echo json_encode([
                'message' => 'Appointment booked successfully',
                'appointment' => $dataToReturn]);
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

<?php
namespace App\Controllers\Admin;

use App\Services\PatientService;
use App\Constants\Status;

class Patients
{
    private PatientService $service;

    public function __construct()
    {
        $this->service = new PatientService();
    }

   
    private function adminOnly(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Admin access only']);
            exit;
        }
    }

   
    public function index(): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');
        echo json_encode($this->service->allPatients());
    }
    
    public function getPatientById(int $patientId): void
    {
        $this->adminOnly();
        header('Content-Type: application/json');

        $patient = $this->service->getPatientById($patientId);

        if ($patient) {
            echo json_encode($patient);
        } else {
            http_response_code(Status::NOT_FOUND);
            echo json_encode(['error' => 'Patient not found']);
        }
    }


  public function delete(int $patientId): void
    {
        $this->adminOnly();
        $this->service->delete($patientId);
        echo json_encode(['message' => 'Patient Delete']);
    }
}

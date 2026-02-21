<?php 
namespace App\Controllers\Admin;

use App\Services\DoctorService;
use App\Constants\Status;

class Doctors {
    private DoctorService $service;

    public function __construct()
    {
        $this->service = new DoctorService();
        
    }

    private function adminOnly(): void
    {
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? null) !== 'admin') {
            http_response_code(Status::FORBIDDEN);
            echo json_encode(['error' => 'Admin access only']);
            exit;
        }
    }

    public function store(): void
    {
        $this->adminOnly();
        $data = json_decode(file_get_contents("php://input"), true);
        $doctorId = $this->service->create($data);
        echo json_encode([
            'message' => 'Doctor added successfully',
            'doctor_id' => $doctorId
        ]);
    }

    public function index(): void
    {
        $this->adminOnly();
        echo json_encode($this->service->getAll());
    }

    public function update(int $id): void
    {
        $this->adminOnly();
        $data = json_decode(file_get_contents("php://input"), true);
        $this->service->update($id, $data);
        echo json_encode(['message' => 'Doctor updated']);
    }

   public function getById(int $doctorId): void {
    header('Content-Type: application/json');

    $doctor = $this->service->getDoctorById($doctorId);

    if (!$doctor) {
        http_response_code(Status::NOT_FOUND);
        echo json_encode(['message' => 'Doctor not found']);
        return;
    }

    http_response_code(Status::SUCCESS);
    echo json_encode($doctor);
}


    public function updateDoctorStatus(int $doctorId): void
    {
        $this->adminOnly();
         $data = json_decode(file_get_contents("php://input"), true);
    $status = $data['status'] ?? null;

        $allowed = ['approved','rejected'];
        if (!$status || !in_array($status, $allowed)) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => 'Invalid status value']);
            return;
        }

        $this->service->update($doctorId, ['status' => $status]);

        echo json_encode(['message' => "Doctor status updated to $status"]);
    }

    public function delete(int $id): void
    {
        $this->adminOnly();
        $this->service->delete($id);
        echo json_encode(['message' => 'Doctor deleted']);
    }
}
?>

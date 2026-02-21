<?php 
namespace App\Services;

use App\Repositories\PatientRepository;

class PatientService{
    private PatientRepository $repo;
    public function __construct()
    {
       $this->repo = new PatientRepository();
    }

    public function allPatients():array{
        return $this->repo->getAllPatients();
    }

    public function getPatientById(int $patientId):? array{
        return $this->repo->getPatientById($patientId);

    }
    
    public function delete(int $patientId): bool
    {
        return $this->repo->delete($patientId);
    }
}
?>
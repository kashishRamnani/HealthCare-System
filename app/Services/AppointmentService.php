<?php
namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Exceptions\ValidationException;

class AppointmentService
{
    // private AppointmentRepository $repo;

    // public function __construct()
    // {
    //     $this->repo = new AppointmentRepository();
    // }
    private AppointmentRepository $repo;

public function __construct(AppointmentRepository $repo)
{
    $this->repo = $repo;
}

    // Book appointment
    public function bookAppointment(array $data): int
    {
        // Validate required fields
        if (!isset($data['patient_name'],$data['doctor_name'],$data['appointment_date'], $data['appointment_time'])) {
            throw new ValidationException('Missing required appointment data');
        }

        // Repository will check availability and doctor approval
        return $this->repo->create($data);
    }

    // Get all appointments (Admin)
    public function getAllAppointments(): array
    {
        return $this->repo->getAllAppointments();
    }

 

    // Update appointment status (by doctor or admin)
    public function updateStatus(int $appointmentId, string $status, string $role): bool
    {
        if (!in_array($role, ['doctor', 'admin'])) {
            throw new ValidationException('Unauthorized');
        }

       $allowed = ['approved', 'rejected', 'completed', 'cancelled']; 
        if (!in_array($status, $allowed, true)) {
            throw new ValidationException('Invalid status');
        }

        return $this->repo->updateStatus($appointmentId, $status);
    }

    // Cancel appointment
   
    public function cancelAppointment(int $appointmentId, string $status, string $role): bool
{
    if (!in_array($role, ['doctor', 'admin'], true)) {
        throw new ValidationException('Unauthorized');
    }

    $allowed = ['approved', 'rejected', 'completed', 'cancelled'];

    if (!in_array($status, $allowed, true)) {
        
        throw new ValidationException('Invalid status');
    }

    return $this->repo->updateStatus($appointmentId, $status);
}

    // Get single appointment
    public function getAppointmentById(int $appointmentId): ?array
    {
        return $this->repo->getAppointmentById($appointmentId);
    }

    // Delete appointment
    public function deleteAppointment(int $appointmentId): bool
    {
        return $this->repo->delete($appointmentId);
    }
}
?>

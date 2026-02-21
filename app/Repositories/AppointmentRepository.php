<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class AppointmentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::make();
    }

    /**
     * Create a new appointment (status is automatically 'pending')
     */
    public function create(array $data): int
    {
    

        $stmt = $this->db->prepare("
            INSERT INTO appointments 
            (doctor_name, patient_name, appointment_date, appointment_time, status)
            VALUES (:doctor_name, :patient_name, :appointment_date, :appointment_time, :status)
        ");

        $stmt->execute([
            ':doctor_name' => $data['doctor_name'],
            ':patient_name' => $data['patient_name'],
            ':appointment_date' => $data['appointment_date'],
            ':appointment_time' => $data['appointment_time'],
            ':status' => $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Get all appointments
     */
    // public function getAllAppointments(): array
    // {
    //     $stmt = $this->db->query("
    //         SELECT doctor_name, patient_name, appointment_date, appointment_time, status 
    //         FROM appointments
    //         ORDER BY appointment_date, appointment_time
    //     ");
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
public function getAllAppointments(): array
{
    $stmt = $this->db->query("
        SELECT id, doctor_name, patient_name, appointment_date, appointment_time, status 
        FROM appointments
        ORDER BY appointment_date, appointment_time
    ");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    /**
     * Get appointment by ID
     */
public function getAppointmentById(int $appointmentId): ?array
{
    $stmt = $this->db->prepare("SELECT * FROM appointments WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $appointmentId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}
     
    public function getPendingAppointments(): array
{
    $stmt = $this->db->prepare("
        SELECT * FROM appointments 
        WHERE status = 'pending'
        ORDER BY appointment_date, appointment_time
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    public function updateStatus(int $appointmentId, string $status): bool
{
    $allowedStatuses = ['approved', 'rejected', 'completed', 'cancelled'];

    if (!in_array($status, $allowedStatuses)) {
        return false;
    }

    $stmt = $this->db->prepare("
        UPDATE appointments 
        SET status = :status 
        WHERE id = :id
    ");

    return $stmt->execute([
        ':status' => $status,
        ':id' => $appointmentId,
    ]);
}

    /**
     * Delete appointment
     */
    public function delete(int $appointmentId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM appointments WHERE id = :id");
        return $stmt->execute([':id' => $appointmentId]);
    }
}

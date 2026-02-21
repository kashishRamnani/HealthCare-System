<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

class PatientRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::make();
    }

   
    public function create(int $userId): bool
    {
        $code = $this->generatePatientCode();

        $stmt = $this->db->prepare("
            INSERT INTO patients (user_id, patient_code)
            VALUES (?, ?)
        ");

        return $stmt->execute([$userId, $code]);
    }

   
    private function generatePatientCode(): string
    {
        $count = $this->db->query("SELECT COUNT(*) FROM patients")
                          ->fetchColumn();

        return "PT-" . str_pad($count + 1, 4, "0", STR_PAD_LEFT);
    }

   
    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.name, u.email
            FROM patients p
            JOIN users u ON p.user_id = u.id
            WHERE p.user_id = ?
        ");

        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    
    public function getAllPatients(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.patient_code, u.name, u.email
            FROM patients p
            JOIN users u ON p.user_id = u.id
            ORDER BY u.name
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getPatientById(int $patientId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.patient_code, u.name, u.email
            FROM patients p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = ?
        ");

        $stmt->execute([$patientId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
   
// public function delete(int $patientId): bool
// {
//     $stmt = $this->db->prepare("DELETE FROM patients WHERE id = ?");
//     $stmt->execute([$patientId]);

//     return $stmt->rowCount() > 0;
// }
   public function delete(int $patientId): bool
{
    try {
        // 1️⃣ Get user_id for this patient
        $stmt = $this->db->prepare("SELECT user_id FROM patients WHERE id = ?");
        $stmt->execute([$patientId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false; 
        }

        $userId = $user['user_id'];

        $stmtPatient = $this->db->prepare("DELETE FROM patients WHERE id = ?");
        $stmtPatient->execute([$patientId]);
        $stmtUser = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmtUser->execute([$userId]);

        return $stmtPatient->rowCount() > 0;

    } catch (\PDOException $e) {
        die($e->getMessage());
    }
}
}
?>
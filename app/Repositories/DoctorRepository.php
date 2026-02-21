<?php
namespace App\Repositories;

use App\Database\Connection;
use PDO;

class DoctorRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Connection::make();
    }

   
public function create(int $userId, array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO doctors (user_id, department, specialization)
        VALUES (:user_id, :department, :specialization)
    ");

    return $stmt->execute([
        ':user_id' => $userId,
        ':department' => $data['department'],
        ':specialization' => $data['specialization'],
    ]);
}

   
   public function getAll(): array
{
    $stmt = $this->db->prepare("
        SELECT 
            d.id AS doctor_id,
            u.id AS user_id,
            u.name,
            u.email,
            u.status,
            d.department,
            d.specialization
        FROM users u
        INNER JOIN doctors d ON u.id = d.user_id
        WHERE u.role = 'doctor'
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getById(int $doctorId): ?array
{
    $stmt = $this->db->prepare("
        SELECT 
            d.id AS doctor_id,
            u.id AS user_id,
            u.name,
            u.email,
            u.status,
            d.department,
            d.specialization
        FROM users u
        INNER JOIN doctors d ON u.id = d.user_id
        WHERE u.role = 'doctor' AND d.id = :doctor_id
        LIMIT 1
    ");

    $stmt->execute([':doctor_id' => $doctorId]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
public function findByUserId(int $userId): ?array
{
    $stmt = $this->db->prepare("
        SELECT *
        FROM doctors
        WHERE user_id = :user_id
        LIMIT 1
    ");

    $stmt->execute([':user_id' => $userId]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

    public function update(int $id, array $data): bool
    {
        try {
            $this->db->beginTransaction();

            // Update users table
            $userStmt = $this->db->prepare("
                UPDATE users
                SET name = :name, email = :email
                WHERE id = :id AND role = 'doctor'
            ");

            $userStmt->execute([
                ':name' => $data['name'],
                ':email' => $data['email'],
                ':id' => $id,
            ]);

            // Update doctors table
            $doctorStmt = $this->db->prepare("
                UPDATE doctors
                SET department = :department
                WHERE user_id = :id
            ");

            $doctorStmt->execute([
                ':department' => $data['department'],
                ':id' => $id,
            ]);

            $this->db->commit();

            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    
    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete from doctors table
            $stmt1 = $this->db->prepare("DELETE FROM doctors WHERE user_id = :id");
            $stmt1->execute([':id' => $id]);

            // Delete from users table
            $stmt2 = $this->db->prepare("DELETE FROM users WHERE id = :id AND role = 'doctor'");
            $stmt2->execute([':id' => $id]);

            $this->db->commit();

            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>
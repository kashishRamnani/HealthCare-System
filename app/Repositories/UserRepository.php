<?php
namespace App\Repositories;

use App\Database\Connection;
use PDO;

class UserRepository {
    private $db;

    public function __construct() {
        $this->db = Connection::make();
       
    }

    public function findByEmail($email) {
        error_log("findByEmail called for: $email");
        $stmt = $this->db->prepare("
            SELECT id, name, email, password, role, status, is_verified, otp, otp_created_at, department
            FROM users 
            WHERE email = :email
        ");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("findByEmail result: " . json_encode($user));
        return $user;
    }

    public function findAdmin() {
        error_log("findAdmin called");
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("findAdmin result: " . json_encode($admin));
        return $admin;
    }

    public function findByRole($role) {
        error_log("findByRole called for role: $role");
        $stmt = $this->db->prepare("SELECT * FROM users WHERE role = ? LIMIT 1");
        $stmt->execute([$role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        error_log("findByRole result: " . json_encode($user));
        return $user;
    }

public function create($data): int
{
    error_log("Creating user: " . json_encode($data));

    $stmt = $this->db->prepare(
        "INSERT INTO users (name, email, password, role, status, otp, otp_created_at, is_verified) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $data['name'],
        $data['email'],
        $data['password'],
        $data['role'],
        $data['status'] ?? 'pending',
        $data['otp'],
        $data['otp_created_at'],
        $data['is_verified'] ?? 0
    ]);

    $userId = (int) $this->db->lastInsertId();

    error_log("User created with ID: " . $userId);

    return $userId;
}

    // public function setPasswordOtp($email, $otp) {
    //     error_log("setPasswordOtp called for $email with OTP: $otp");
    //     $stmt = $this->db->prepare(
    //         "UPDATE users SET otp = ?, otp_created_at = NOW() WHERE email = ?"
    //     );
    //     $result = $stmt->execute([$otp, $email]);
    //     error_log("setPasswordOtp result: " . ($result ? "success" : "failure"));
    //     return $result;
    // }

    public function verifyOTP($email, $otp) {
        error_log("verifyOTP called for $email with OTP: $otp");
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND otp = ?");
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("verifyOTP failed: OTP not found for $email");
            return false;
        }

        $otpTime = strtotime($user['otp_created_at']);
        if (time() - $otpTime > 300) { 
            error_log("verifyOTP failed: OTP expired for $email");
            return false;
        }

        error_log("verifyOTP success for $email");
        return $user;
    }

    // public function verifyPasswordOtp($email, $otp) {
    //     error_log("verifyPasswordOtp called for $email with OTP: $otp");
    //     $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? AND otp = ?");
    //     $stmt->execute([$email, $otp]);
    //     $user = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if (!$user) {
    //         error_log("verifyPasswordOtp failed: OTP not found for $email");
    //         return false;
    //     }

    //     if (time() - strtotime($user['otp_created_at']) > 300) {
    //         error_log("verifyPasswordOtp failed: OTP expired for $email");
    //         return false;
    //     }

    //     error_log("verifyPasswordOtp success for $email");
    //     return $user;
    // }

    public function setVerified($email) {
        error_log("setVerified called for $email");
        $stmt = $this->db->prepare(
            "UPDATE users 
             SET is_verified = 1, otp = NULL, otp_created_at = NULL 
             WHERE email = ?"
        );
        $result = $stmt->execute([$email]);
        error_log("setVerified result: " . ($result ? "success" : "failure"));
        return $result;
    }

    public function updatePassword($email, $password) {
        error_log("updatePassword called for $email");
        $stmt = $this->db->prepare(
            "UPDATE users SET password = ?, otp = NULL, otp_created_at = NULL WHERE email = ?"
        );
        $result = $stmt->execute([$password, $email]);
        error_log("updatePassword result: " . ($result ? "success" : "failure"));
        return $result;
    }

    public function findByName(string $name, string $role) {
        error_log("findByName called for name: $name, role: $role");
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE name = ? AND role = ? AND status = 'approved' 
            LIMIT 1
        ");
        $stmt->execute([$name, $role]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        error_log("findByName result: " . json_encode($user));
        return $user;
    }
    public function createPasswordResetOtp(int $userId, string $email, int $otp, int $expiryMinutes = 5): int
{
    $stmt = $this->db->prepare("
        INSERT INTO reset_password (user_id, email, otp, expires_at)
        VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL ? MINUTE))
    ");
    $stmt->execute([$userId, $email, $otp, $expiryMinutes]);
    return (int)$this->db->lastInsertId();
}

public function verifyResetOtp(string $email, int $otp)
{
    $stmt = $this->db->prepare("
        SELECT * FROM reset_password
        WHERE email = ? AND otp = ? AND used = 0 AND expires_at > NOW()
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$email, $otp]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // mark OTP as used
        $update = $this->db->prepare("UPDATE reset_password SET used = 1 WHERE id = ?");
        $update->execute([$row['id']]);
        return $row;
    }

    return false;
}
}
?>
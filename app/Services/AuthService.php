<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\MailService;
use App\Constants\Status;
class AuthService
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function createAdmin(array $data)
    {
      
        if ($this->userRepo->findByRole('admin')) {
            throw new \Exception('Admin already exists');
        }
        if ($this->userRepo->findByEmail($data['email'])) {
            throw new \Exception('Email already in use');
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

        return $this->userRepo->create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => $hashedPassword,
            'role'           => 'admin',
            'is_verified'    => 1,
            'otp'            => null,
            'otp_created_at' => null
        ]);
    }
public function register(array $data): int
{
    if ($this->userRepo->findByEmail($data['email'])) {
        throw new \Exception('Email already exists');
    }

    if (($data['role'] ?? '') === 'admin') {
        throw new \Exception('Admin registration is not allowed');
    }

    $role = $data['role'] ?? 'patient';
    $status = ($role === 'doctor') ? 'pending' : 'approved';

    if ($role === 'doctor') {
        if (empty($data['department'])) {
            throw new \Exception('Department is required for doctors.');
        }
        $department = $data['department'];
    } else {
        $department = null;
    }

    $otp = rand(100000, 999999);
    $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

    $userData = [
        'name'           => $data['name'],
        'email'          => $data['email'],
        'password'       => $passwordHash,
        'role'           => $role,
        'status'         => $status,
        'is_verified'    => 0,
        'otp'            => $otp,
        'otp_created_at' => date('Y-m-d H:i:s'),
        'department'     => $department
    ];
    $userId = $this->userRepo->create($userData);
    if ($role === 'patient') {
        $patientRepo = new \App\Repositories\PatientRepository();
        $patientRepo->create($userId);
    }elseif ($role === 'doctor') {
        if (empty($data['department']) || empty($data['specialization'])) {
            throw new \Exception('Department and Specialization are required for doctor.');
        }
        $doctorRepo = new \App\Repositories\DoctorRepository();
$doctorRepo->create($userId, [
    'department' => $data['department'],
    'specialization' => $data['specialization']
]);
}
    $mailResponse = MailService::sendOTP($data['email'], $otp);
    if (!$mailResponse['success']) {
        throw new \Exception($mailResponse['message'] ?? 'Failed to send OTP email');
    }

    return $otp;
}


public function verifyOTP(string $email, string $otp): bool
{
    $otp = trim((string)$otp);

    $user = $this->userRepo->findByEmail($email);
    if (!$user) {
        throw new \Exception('Invalid email or OTP');
    }

    if ((string)$user['otp'] !== $otp) {
        throw new \Exception('Invalid OTP');
    }

    if (!$user['otp_created_at'] || (time() - strtotime($user['otp_created_at'])) > 3600) {
        throw new \Exception('OTP expired');
    }

    $this->userRepo->setVerified($email);
    return true;
}



    public function login(string $email, string $password): array
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        if ($user['is_verified'] == 0) {
            throw new \Exception('Account not verified. Please check your email for OTP.');
        }

        if (!password_verify($password, $user['password'])) {
            throw new \Exception('Invalid password');
        }
        if ($user['role'] === 'doctor') {
       if ($user['status'] === 'pending') {
        throw new \Exception("Doctor account is pending approval by admin");

    } elseif ($user['status'] === 'rejected') {
        throw new \Exception("Doctor account has been rejected by admin");
    }
}

        

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
            'status' => $user['status'],
            'is_verified' => $user['is_verified']
        ];

        return $_SESSION['user'];
    }

    // public function forgotPassword(string $email): int
    // {
    //     $user = $this->userRepo->findByEmail($email);
    //     if (!$user) {
    //         throw new \Exception("Email not found");
    //     }
    //     $otp = rand(100000, 999999);
    //     $this->userRepo->setPasswordOtp($email, $otp, date('Y-m-d H:i:s'));
    //     $response = MailService::sendOTP($email, $otp);
    //     if (empty($response['success'])) {
    //         throw new \Exception("Failed to send OTP: " . ($response['message'] ?? 'Unknown error'));
    //     }

    //     return $otp;
    // }

    // public function resetPasswordWithOTP(string $email, int $otp, string $newPassword): bool
    // {
    //     $user = $this->userRepo->verifyPasswordOtp($email, $otp);
    //     if (!$user) {
    //         throw new \Exception("Invalid or expired OTP");
    //     }

    //     $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    //     $this->userRepo->updatePassword($email, $hashedPassword);

        

    //     return true;
    // }
    public function forgotPassword(string $email): int
{
    $user = $this->userRepo->findByEmail($email);
    if (!$user) {
        throw new \Exception("Email not found");
    }

    $otp = rand(100000, 999999);
    $this->userRepo->createPasswordResetOtp($user['id'], $email, $otp, 5);

    $response = MailService::sendOTP($email, $otp);
    if (empty($response['success'])) {
        throw new \Exception("Failed to send OTP: " . ($response['message'] ?? 'Unknown error'));
    }

    return $otp;
}
    public function resetPasswordWithOTP(string $email, int $otp, string $newPassword): bool
{
    $resetEntry = $this->userRepo->verifyResetOtp($email, $otp);
    if (!$resetEntry) {
        throw new \Exception("Invalid or expired OTP");
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
    $this->userRepo->updatePassword($email, $hashedPassword);

    return true;
}
}
?>
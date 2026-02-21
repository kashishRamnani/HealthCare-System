<?php
namespace App\Controllers\Auth;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Services\MailService;
use App\Requests\Register;
use App\Constants\Status;

class RegisterController
{
    private $authService;

    public function __construct()
    {
        
        $userRepo = new UserRepository();
        $this->authService = new AuthService($userRepo);
       
    }



public function register()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(Status::INVALID_REQUEST);
        echo json_encode(['message' => 'Invalid JSON payload']);
        return;
    }

    $role = $data['role'] ?? 'patient';
    if ($role === 'admin') {
        http_response_code(Status::INVALID_REQUEST);
        echo json_encode(['error' => 'Admin registration is not allowed']);
        return;
    }

    $request = new Register($data);

    if (!$request->validate()) {
        http_response_code(Status::INVALID_REQUEST);
        echo json_encode(['errors' => $request->errors()]);
        return;
    }

    try {
        $data = $request->getData();
        $data['role'] = $role;

        $otp = $this->authService->register($data);

       
        $response = MailService::sendOTP($data['email'], $otp);
        if (empty($response['success'])) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Failed to send OTP',
                'error' => $response['message'] ?? 'Mail server error'
            ]);
            return;
        }

        http_response_code(Status::SUCCESS);
        echo json_encode([
            'message' => 'Your account is registered! Verify your account, OTP has been sent to your email.'
        ]);
        return;

    } catch (\Exception $e) {
        http_response_code(Status::BAD_REQUEST);
        echo json_encode(['error' => $e->getMessage()]);
        return;
    }
}

 public function sendOTPToEmail(string $email): int
    {
        $otp = rand(100000, 999999);
        $response = MailService::sendOTP($email, $otp);

        if (empty($response['success'])) {
           
            throw new \Exception("Failed to send OTP: " . ($response['message'] ?? 'Unknown error'));
        }
        return $otp;
        
    }

  public function verify()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    $email = $data['email'] ?? null;
    $otp   = $data['otp'] ?? null;

    if (!$email || !$otp) {
        http_response_code(Status::INVALID_REQUEST);
        echo json_encode(['error' => 'Email and OTP are required']);
        return;
    }

    try {
       $this->authService->verifyOTP($email, trim($otp));

        http_response_code(Status::SUCCESS);
        echo json_encode([
            'message' => 'OTP verified successfully! You can now login.'
        ]);
    } catch (\Exception $e) {
        http_response_code(Status::BAD_REQUEST);
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }
}

}
?>
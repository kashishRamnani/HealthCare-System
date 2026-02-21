<?php
namespace App\Controllers\Auth;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Constants\Status;

class ForgotPasswordController
{
    private $authService;

    public function __construct()
    {
        $userRepo = new UserRepository();
        $this->authService = new AuthService($userRepo);
    }

    // 1️⃣ Send OTP to email
    public function forgot()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['email'])) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode(['message' => 'Email is required']);
            return;
        }

        try {
            $this->authService->forgotPassword($data['email']);
            http_response_code(Status::SUCCESS);
            echo json_encode(['message' => 'OTP sent to your email']);
        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    
    public function reset()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['email']) ||
            empty($data['otp']) ||
            empty($data['new_password'])
        ) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode([
                'message' => 'Email, OTP, and new password are required'
            ]);
            return;
        }

        try {
            $this->authService->resetPasswordWithOTP(
                $data['email'],
                $data['otp'],
                $data['new_password']
            );

            http_response_code(Status::SUCCESS);
            echo json_encode(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>
<?php
namespace App\Controllers\Auth;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Constants\Status;

class LoginController
{
    private AuthService $authService;

    public function __construct()
    {
        $userRepo = new UserRepository();
        $this->authService = new AuthService($userRepo);
    }

    public function login()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(Status::INVALID_REQUEST);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        try {
            $user = $this->authService->login($data['email'], $data['password']);

           
            if ($user['role'] === 'doctor') {
                if ($user['status'] === 'pending') {
                    throw new \Exception("Doctor account is pending admin approval.");
                }
                if ($user['status'] === 'rejected') {
                    throw new \Exception("Doctor account has been rejected by admin.");
                }
            }

           
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'status' => $user['status']
            ];

            http_response_code(Status::SUCCESS);
            echo json_encode([
                'message' => 'Login successful!',
                'user' => $_SESSION['user']
            ]);

        } catch (\Exception $e) {
            http_response_code(Status::BAD_REQUEST);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>

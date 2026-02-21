<?php namespace App\Controllers\Auth;

use App\Constants\Status;

class LogoutController
{
    public function logout()
    {
       

        
        session_unset();
        session_destroy();

        http_response_code(Status::SUCCESS);
        echo json_encode(['message' => 'Logged out successfully']);
        exit;
    }
}
?>

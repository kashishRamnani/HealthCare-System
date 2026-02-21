<?php 
namespace App\Middleware;
use App\Constants\Status;
class AuthMiddleware
{
    public static function checkRole($allowedRoles,$userRole){
        if(!in_array($allowedRoles,$userRole)){
            http_response_code(Status::FORBIDDEN);
             echo json_encode(['error' => 'Access denied']);
            exit;
        }
    }
}
?>
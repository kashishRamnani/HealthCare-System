<?php
namespace App\Request;

class Login{
    public static function validate(array $data):void
    {
        if(empty($data['email'])||empty($data['password'])){
            throw new \Exception("Email and password are required");
            
        }
        if(!filter_var($data['email'],FILTER_VALIDATE_EMAIL)){
            throw new \Exception("Invaild email formate");
        }
    }
}
?>

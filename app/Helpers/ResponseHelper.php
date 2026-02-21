<?php

namespace App\Helpers;

use App\Constants\Status;

class Response
{
    public static function json($success, $message, $data = null, $code = Status::SUCCESS)
    {
        http_response_code($code);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }
}
?>
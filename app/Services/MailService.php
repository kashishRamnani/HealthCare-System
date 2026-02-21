<?php
namespace App\Services;

use App\Helpers\Env;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class MailService
{
    public static function sendOTP(string $toEmail, int $otp): array
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = 'error_log';

            $mail->Host       = Env::get('MAIL_HOST');
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get('MAIL_USERNAME');
            $mail->Password   = Env::get('MAIL_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port       = (int) Env::get('MAIL_PORT');

            $mail->setFrom(
                Env::get('MAIL_USERNAME'),
                Env::get('MAIL_FROM') ?? 'HealthCare System'
            );

            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is <b>{$otp}</b>. It is valid for 5 minutes.";

            $mail->send();

            return [
                'success' => true,
                'message' => 'OTP sent successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
           
        }
    }
}

?>
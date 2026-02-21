<!-- <?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ramnanikashish282@gmail.com'; 
    $mail->Password   = 'wige rakn wogj xsdd';      
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('ramnanikashish282@gmail.com', 'HealthCare System');
    $mail->addAddress('kashishramnani337@gmail.com'); 
    $mail->isHTML(true);
    $mail->Subject = 'Test Email';
    $mail->Body    = 'This is a test email from PHPMailer.';

    $mail->send();
    echo 'Email sent successfully';
} catch (Exception $e) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}

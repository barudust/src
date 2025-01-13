<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Ajusta según la ruta

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Obtener el correo desde el formulario

    include 'conexion.php';
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generar un código único
        $codigo = bin2hex(random_bytes(16));

        // Guardar el código en la base de datos
        $stmt = $pdo->prepare("UPDATE usuarios SET codigo_recuperacion = ? WHERE email = ?");
        $stmt->execute([$codigo, $email]);

        // Configuración de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP de Mailtrap
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io'; // Mailtrap SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'd9afd7b05f875a'; // Tu usuario de Mailtrap
            $mail->Password = '58e2dfee097a00'; // Tu contraseña de Mailtrap
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('coreodeladmin@financiamiento.com', 'Financiamiento');
            $mail->addAddress($email); // Destinatario

            // Asunto y mensaje
            $mail->Subject = 'Recuperación de Contraseña';
            $mail->Body    = "Hola, haz clic en el siguiente enlace para recuperar tu contraseña: \n\nhttp://localhost/mi-aplicacion/src/src/views/cambiar.php?codigo=$codigo";

            // Enviar el correo
            $mail->send();
            echo json_encode(["success" => true, "message" => "Correo enviado."]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Error al enviar el correo: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Correo no registrado."]);
    }
}
?>

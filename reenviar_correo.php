<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "SELECT *
                FROM usuario
                WHERE ID = $data->id";

        $result = $conn->query($sql); 
        $usuario = $result->fetch_assoc();

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'app.monitoreofase2@gmail.com';                     // SMTP username
            $mail->Password   = 'appmonitoreo';                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom('app.monitoreofase2@gmail.com', 'APP Campamentos');
            $mail->addAddress($usuario["email"]);                              // Name is optional
        
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Activación de Cuenta';

            $url = "https://udicat.muniguate.com/apps/campamentos/app-campamentos/#/activar_cuenta/".$data->id;

            $mail->Body    =    '<h1>¡Bienvenido!</h1>' .
                                '<h3>Solo falta un paso</h3>' .
                                '<p>Para finalizar el proceso de registro es necesario que verifique su cuenta, por favor dar clic en el enlace que se le muestra a continuación.</p>' .
                                '<a href="'.$url.'">Activar Cuenta</a>' . 
                                '<p>Gracias.</p>';
        
            $mail->send();

            // $data = [
            //     "status" => 200,
            //     "message" => "<p>Se ha enviado un correo electronico a la dirección <strong>$usuario->email</strong>, por favor revise su buzón y siga las instrucciones indicadas para activar su cuenta.</p>"
            // ];

        } catch (Exception $e) {

        }

        echo json_encode($data);

    }

?>
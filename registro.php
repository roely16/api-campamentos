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
        
        $sql = "select *
                from usuario
                where email = '$data->email'
                or telefono = '$data->telefono'";

        $result = $conn->query($sql);

        $usuario = $result->fetch_assoc();

        if (!$usuario) {

            $campamento = $data->campamento->id;
            $rol = $data->rol->id;

            $sql = "INSERT INTO usuario (nombre, apellido, telefono, email, id_campamento, id_rol, created_at) VALUES ('$data->nombre', '$data->apellido', '$data->telefono', '$data->email', $campamento, $rol, NOW())";

            $result = $conn->query($sql);

            $id_usuario = $conn->insert_id;

            if ($result) {
                
                // Obtener el usuario registrado
                $sql = "SELECT t2.nombre as campamento, t3.nombre as rol
                        FROM usuario t1
                        INNER JOIN campamento t2
                        ON t1.id_campamento = t2.id
                        INNER JOIN rol t3
                        ON t1.id_rol = t3.id
                        WHERE t1.id = $id_usuario";

                $result = $conn->query($sql);

                $bk_usuario = $result->fetch_assoc();

                $campamento = $bk_usuario["campamento"];
                $rol = $bk_usuario["rol"];

                // Enviar correo de activación

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
                    $mail->addAddress("$data->email");                              // Name is optional
                
                    // Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = 'Activación de Cuenta';

                    $url = "https://udicat.muniguate.com/apps/campamentos/app-campamentos/#/activar_cuenta/".$id_usuario;

                    $mail->Body    =    '<h1>¡Bienvenido!</h1>' .
                                        '<h3>Solo falta un paso</h3>' .
                                        '<p>Para finalizar el proceso de registro es necesario que verifique su cuenta, por favor dar clic en el enlace que se le muestra a continuación.</p>' .
                                        '<a href="'.$url.'">Activar Cuenta</a>' . 
                                        '<p>Gracias.</p>';
                
                    $mail->send();

                    $data_response = [
                        "status" => 200,
                        "message" => "<p>Se ha enviado un correo electronico a la dirección <strong>$data->email</strong>, por favor revise su buzón y siga las instrucciones indicadas para activar su cuenta.</p>"
                    ];

                    // Enviar correo de aprobación
                    $sql = "SELECT *
                            FROM correos_verificacion";

                    $result_ = $conn->query($sql);

                    while ($row_ = $result_->fetch_assoc()) {

                            $mail = new PHPMailer(true);
                            
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
                            $mail->addAddress($row_["email"]);                              // Name is optional
                        
                            // Content
                            $mail->isHTML(true);                                  // Set email format to HTML
                            $mail->Subject = 'Aprobación de Cuenta';

                            $url_aceptar = "http://localhost:8080/#/aceptar_cuenta/".$id_usuario;

                            $url_rechazar = "http://localhost:8080/#/rechazar_cuenta/".$id_usuario;

                            $mail->Body    =    '<h1>¡Atención!</h1>' .
                                                '<h3>Un nuevo usuario requiere de su aprobación para completar el registro.</h3>' .
                                                '<p><strong>Nombre: </strong>'. $data->nombre .' '. $data->apellido .'</p>' .
                                                '<p><strong>Teléfono: </strong>'. $data->telefono .'</p>' .
                                                '<p><strong>Email: </strong>'. $data->email .'</p>' .
                                                '<p><strong>Campamento: </strong>'. $campamento .'</p>' .
                                                '<p><strong>Rol: </strong>'. $rol .'</p>' .
                                                '<p>Para aceptar o rechazar la cuenta deberá dar clic en el botón correspondiente.</p>' .
                                                '<table width="100%" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <table cellspacing="10" cellpadding="0">
                                                            <tr>
                                                                <td style="border-radius: 2px; margin-right: 30px;" bgcolor="#1bb53a">
                                                                    <a href="'.$url_aceptar.'" target="_blank" style="padding: 8px 12px; border: 1px solid #1bb53a;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                                                                        Aceptar             
                                                                    </a>
                                                                </td>
                                                                <td style="border-radius: 2px;" bgcolor="#ED2939">
                                                                    <a href="'.$url_rechazar.'" target="_blank" style="padding: 8px 12px; border: 1px solid #ED2939;border-radius: 2px;font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;text-decoration: none;font-weight:bold;display: inline-block;">
                                                                        Rechazar             
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                              </table>' .
                                                '<p>Gracias.</p>';
                        
                            $mail->send();

                    }



                } catch (Exception $e) {

                }

            }

        }else{

            $data_response = [
                "status" => 100,
                "message" => "Ya existe un usuario con el correo electrónico o teléfono indicado."
            ];

        }

        echo json_encode($data_response);

    }

?>
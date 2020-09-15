<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT *
                FROM usuario
                WHERE id = $data->id_usuario";

        $result = $conn->query($sql);

        $usuario = $result->fetch_assoc();

        $hash = $usuario["password"];

        if (password_verify($data->actual, $hash)) {

            $hash_password = password_hash($data->nueva, PASSWORD_DEFAULT);

            // Actualizar la clave
            $sql = "UPDATE usuario SET password = '$hash_password', updated_at = NOW() WHERE id = $data->id_usuario";
            $result = $conn->query($sql);
            
            if ($result) {
                
                $data = [
                    "status" => 200,
                    "message" => "Contraseña actualizada exitosamente."
                ];

            }
            

        }else{

            // Retornar mensaje de error

            $data = [
                "status" => 100,
                "message" => "Actual contraseña incorrecta, favor de verificar."
            ];

        }
        echo json_encode($data);

    }

?>
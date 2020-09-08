<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        // Cambiar el estado a en linea
        if ($data->en_linea) {
            
            $sql = "UPDATE usuario SET en_linea = 'S' WHERE id = $data->id_usuario";

        }else{

            $sql = "UPDATE usuario SET en_linea = NULL WHERE id = $data->id_usuario";


        }

        $result = $conn->query($sql);

        // Registrar la clinica
       if ($data->clinica != 0) {
        
            // Desasignar de cualquier otra clinica
            $sql = "UPDATE clinica SET id_medico = NULL WHERE id_medico = $data->id_usuario";
            $result = $conn->query($sql);

            $sql = "UPDATE clinica SET id_medico = $data->id_usuario WHERE id = $data->clinica";
            $result = $conn->query($sql);

       }else{

            $sql = "UPDATE clinica SET id_medico = NULL, ocupada = NULL, id_paciente_asignado = NULL WHERE id_medico = $data->id_usuario";
            $result = $conn->query($sql);

       }

        echo json_encode($result);

    }

?>
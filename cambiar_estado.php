<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "UPDATE paciente SET id_estado = $data->id_estado, updated_at = NOW() WHERE id = $data->id_paciente";

        $result = $conn->query($sql);

        // Si cambia a estado tres buscarlo en la tabla de la clinica y eliminarlo 
        $sql = "UPDATE clinica SET ocupada = NULL, id_paciente_asignado = NULL WHERE id_paciente_asignado = $data->id_paciente";
        $result = $conn->query($sql);

        // Escribir en el historial de pacientes por clinica
        if ($data->id_estado == '3') {
            
            $sql = "SELECT id
                    FROM clinica
                    WHERE id_medico = $data->id_medico";

            $result = $conn->query($sql);
            $clinica = $result->fetch_assoc();

            $id_clinica = $clinica["id"];

            if (!$clinica) {
            
                $id_clinica = "NULL";

                // echo json_encode('sin clinica');
                // die;
            }

            $sql = "INSERT INTO clinica_paciente (id_clinica, id_paciente, id_medico, created_at) VALUES ($id_clinica, $data->id_paciente, $data->id_medico, NOW())";

            $result = $conn->query($sql);

            // Borrar de la tabla medico_paciente 
            $sql = "DELETE FROM medico_paciente WHERE id_paciente = $data->id_paciente AND id_medico = $data->id_medico";
            $result = $conn->query($sql);


        }
        echo json_encode($result);

    }

?>
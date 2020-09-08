<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "INSERT INTO medico_paciente (id_medico, id_paciente) VALUES ($data->id_medico, $data->id_paciente) on DUPLICATE KEY UPDATE id_medico = $data->id_medico";

        $result = $conn->query($sql);

        if ($result) {
            
            // Actualizar al paciente con estado A Consulta 
            $sql = "UPDATE paciente SET id_estado = 2 WHERE id = $data->id_paciente";

            $result = $conn->query($sql);            

        }

        echo json_encode($data);

    }

?>
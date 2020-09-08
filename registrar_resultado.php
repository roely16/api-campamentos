<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        // Si se le entrego kit eliminar de los reportes del paciente el kit de medicamento 
        if ($data->entrega_kit) {
            
            $sql = "UPDATE bitacora_paciente SET kit_medicamento = '' WHERE id_paciente = $data->id_paciente";

            $result = $conn->query($sql);

        }

        $sql = "UPDATE paciente set resultado_prueba = '$data->resultado', observaciones_prueba = '$data->observaciones' WHERE id = $data->id_paciente";

        $result = $conn->query($sql);

        echo json_encode($result);

    }

?>
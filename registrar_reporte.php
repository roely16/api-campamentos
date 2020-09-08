<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        if ($data->kit_medicamento) {
            
            $data->kit_medicamento = 'S';
        }

        if ($data->requiere_azitromicina) {
            
            $data->requiere_azitromicina = 'S';
        }

        if ($data->requiere_constancia) {
            
            $data->requiere_constancia = 'S';
        }

        if ($data->requiere_prueba) {
            
            $data->requiere_prueba = 'S';
        }

        if (!$data->razon_no_medicamento) {
            
            $data->razon_no_medicamento = '';
        }

        if (!$data->otro_diagnostico) {
            
            $data->otro_diagnostico = '';
        }

        $sql = "INSERT INTO bitacora_paciente (id_paciente, observacion, medicamento, id_medico, kit_medicamento, requiere_azitromicina, created_at, requiere_medicamento, razon_no_medicamento, otro_diagnostico, requiere_constancia, requiere_prueba) VALUES ($data->id_paciente, '$data->observacion', '$data->medicamento', $data->id_medico, '$data->kit_medicamento', '$data->requiere_azitromicina', NOW(), '$data->requiere_medicamento', '$data->razon_no_medicamento', '$data->otro_diagnostico', '$data->requiere_constancia', '$data->requiere_prueba')";

        $result = $conn->query($sql);

        echo json_encode($result);

    }

?>
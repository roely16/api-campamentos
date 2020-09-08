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

        $sql = "UPDATE bitacora_paciente SET observacion = '$data->observacion', medicamento = '$data->medicamento', kit_medicamento = '$data->kit_medicamento', requiere_azitromicina = '$data->requiere_azitromicina', updated_at = NOW(), requiere_medicamento = '$data->requiere_medicamento', razon_no_medicamento = '$data->razon_no_medicamento', otro_diagnostico = '$data->otro_diagnostico', requiere_constancia = '$data->requiere_constancia', requiere_prueba = '$data->requiere_prueba' WHERE id = $data->id";

        $result = $conn->query($sql);

        echo json_encode($result);

    }

?>
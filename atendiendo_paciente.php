<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "UPDATE paciente SET id_estado = 6 WHERE id = $data->id";

        $result = $conn->query($sql);

        // Actualizar la clinica
        $sql = "UPDATE clinica SET ocupada = 'S' WHERE id_paciente_asignado = $data->id";

        $result = $conn->query($sql);
        
        echo json_encode($result);

    }

?>
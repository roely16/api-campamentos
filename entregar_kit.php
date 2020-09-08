<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "UPDATE paciente set persona_recibe_kit = '$data->recibe', comentarios_entrega_kit = '$data->comentarios', id_estado = 4 WHERE id = $data->id_paciente";

        $result = $conn->query($sql);

        echo json_encode($result);

    }

?>
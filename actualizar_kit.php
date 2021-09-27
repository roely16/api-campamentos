<?php

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $entrega_kit = $data->add ? 'S' : null;

        $sql = "UPDATE bitacora_paciente
                SET kit_medicamento = '$entrega_kit'
                WHERE id_paciente = '$data->id'
                ORDER BY id DESC
                LIMIT 1";

        $result = $conn->query($sql);

        echo json_encode($result);

    }

?>
<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "SELECT t2.*
                FROM clinica t1
                INNER JOIN paciente t2
                ON t1.id_paciente_asignado = t2.id
                WHERE id_medico = $data->id_usuario
                AND (t1.ocupada IS NULL OR t1.ocupada = '')";

        $result = $conn->query($sql);

        $paciente = $result->fetch_assoc();

        echo json_encode($paciente);

    }

?>
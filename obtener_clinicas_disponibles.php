<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT *
                FROM clinica
                WHERE id_campamento IN (
                    SELECT id_campamento
                    FROM usuario
                    WHERE id = $data->id_usuario
                )
                AND id_medico IS NULL";

        $result = $conn->query($sql);

        $clinicas = [];

        $ninguna = [
            "id" => NULL,
            "id_campamento" => NULL,
            "id_medico" => NULL,
            "nombre" => "Ninguna",
            "numero" => NULL
        ];

        $clinicas[0] = $ninguna;

        while ($row = $result->fetch_assoc()) {
            
            $clinicas [] = $row;

        }

        echo json_encode($clinicas);

    }

?>
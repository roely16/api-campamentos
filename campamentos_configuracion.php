<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT t2.*
                FROM usuario t1
                INNER JOIN campamento t2
                ON t1.id_campamento = t2.id
                WHERE t1.id = $data->id";

        $result = $conn->query($sql);

        $campamento = $result->fetch_assoc();

        $campamentos = [];

        if ($campamento["privado"] != 'S') {

            $sql = "SELECT *
            FROM campamento
            WHERE privado IS NULL";

            $result = $conn->query($sql); 

            while ($row = $result->fetch_assoc()) {

                $campamentos [] = $row;

            }

        }

        $data = [
            "campamentos" => $campamentos
        ];

        echo json_encode($data);

    }

?>
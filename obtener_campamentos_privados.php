<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');
    
    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT *
                FROM rol
                WHERE privado != ''";

        $result = $conn->query($sql); 

        $roles = [];

        while ($row = $result->fetch_assoc()) {

            $roles [] = $row;

        }

        $sql = "SELECT *
                FROM campamento
                WHERE privado != ''
                AND id_empresa = $data->id_empresa";

        $result = $conn->query($sql); 

        $campamentos = [];

        while ($row = $result->fetch_assoc()) {

            $campamentos [] = $row;

        }

        $data = [
            "roles" => $roles,
            "campamentos" => $campamentos
        ];

        echo json_encode($data);

    }

?>
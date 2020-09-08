<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT id_rol
                FROM usuario
                WHERE id = $data->id_usuario";

        $result = $conn->query($sql);

        $rol = $result->fetch_assoc();

        $id_rol = $rol["id_rol"];

        // Buscar estados del rol

        $sql = "SELECT t2.*
                FROM rol_estado t1
                INNER JOIN estado t2
                ON t1.id_estado = t2.id
                WHERE t1.id_rol = $id_rol
                AND t1.cambiar = 'S'";

        $result = $conn->query($sql);

        $estados = [];

        while ($row = $result->fetch_assoc()) {
            
            $estados [] = $row;

        }

        echo json_encode($estados);

    }

?>
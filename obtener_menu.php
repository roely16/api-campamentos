<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
       
        // Buscar el rol del usuario
        $sql = "SELECT *
                FROM usuario
                where id = $data->id";

        $result = $conn->query($sql);
        $usuario = $result->fetch_assoc();

        $id_rol = $usuario["id_rol"];

        $sql = "SELECT t2.*
                FROM menu_rol t1
                INNER JOIN menu t2
                ON t1.id_menu = t2.id
                WHERE id_rol = $id_rol";

        $result = $conn->query($sql); 

        $menu = [];

        while ($row = $result->fetch_assoc()) {

            $menu [] = $row;

        }

        echo json_encode($menu);

    }
    
?>
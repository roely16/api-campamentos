<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT id_campamento
                FROM usuario
                WHERE id = $data->id_usuario";
        
        $result = $conn->query($sql);
        $usuario = $result->fetch_assoc();

        $id_campamento = $usuario["id_campamento"];

        // Obtener la empresa
        $sql = "SELECT id_empresa
                FROM campamento
                WHERE id = $id_campamento";

        $result = $conn->query($sql);
        $campamento = $result->fetch_assoc();

        $id_empresa = $campamento["id_empresa"];

        $sql = "SELECT id, CONCAT(nombre, CONCAT(' ', apellido)) as nombre
                FROM usuario
                WHERE id_campamento IN (

                    SELECT id
                    FROM campamento
                    WHERE id_empresa = $id_empresa

                )
                AND id_rol = 6";

        $result = $conn->query($sql);

        $medicos = [];

        while ($row = $result->fetch_assoc()) {
         
            $medicos [] = $row;

        }

        echo json_encode($medicos);
        
    }

?>
<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), true);

    $id_usuario = $data["id"];

    if($data){

        $sql = "insert into bitacora_usuario (id_usuario, logout_at) values ('$id_usuario', NOW())";

        $result = $conn->query($sql);

        // Quitar el estado de en linea y eliminar de la clinica
        $sql = "UPDATE usuario SET en_linea = NULL WHERE id = $id_usuario";
        $result = $conn->query($sql);

        $sql = "UPDATE clinica SET id_medico = NULL WHERE id_medico = $id_usuario";
        $result = $conn->query($sql);

    }

    echo json_encode($result);

?>
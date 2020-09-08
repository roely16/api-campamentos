<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    $sql = "select t1.id, t1.verificada, t1.aprobada, t1.nombre, t1.apellido, t2.id as id_campamento, t2.nombre as campamento, t3.nombre as rol
            from usuario t1
            inner join campamento t2
            on t1.id_campamento = t2.id
            inner join rol t3
            on t1.id_rol = t3.id
            where telefono = '$data->telefono'";

    $result = $conn->query($sql);

    $usuario = $result->fetch_assoc();

    if ($usuario) {

        $id_usuario = $usuario["id"];

        $sql = "insert bitacora_usuario(id_usuario, login_at)
                values ('$id_usuario', NOW())";

        $result = $conn->query($sql);

    }

    echo json_encode($usuario);

?>
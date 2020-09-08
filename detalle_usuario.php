<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT t1.*, t2.agregar_paciente, t2.editar_paciente, t2.ver_bitacora, t2.agregar_bitacora, t2.atiende_paciente, t2.filtrar_campamentos, t2.editar_paciente_consulta, t2.ver_bitacora_consulta, t2.editar_clasificacion
                FROM usuario t1
                INNER JOIN rol t2
                ON t1.id_rol = t2.id
                WHERE t1.id = $data->id_usuario";

        $result = $conn->query($sql);

        $usuario = $result->fetch_assoc();

        // Obtener la clinica asignada

        $sql = "SELECT *
                FROM clinica
                WHERE id_medico = $data->id_usuario";

        $result = $conn->query($sql);

        $usuario["clinica"] = $result->fetch_assoc();

        echo json_encode($usuario);

    }
?>
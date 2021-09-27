<?php

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT id_campamento, id_rol
                FROM usuario
                WHERE id = $data->id";

        $result = $conn->query($sql);
        $usuario = $result->fetch_assoc();

        if (!$data->id_campamento_filtrado) {

            $id_campamento = $usuario["id_campamento"];

        }else{

            $id_campamento = $data->id_campamento_filtrado;
        }

        $id_rol = $usuario["id_rol"];

        $offset = ($data->page * 20) - 20;

        $sql = "SELECT
                    t1.id,
                    t1.correlativo,
                    UPPER(CONCAT(t1.nombre, CONCAT(' ', CONCAT(t1.segundo_nombre, CONCAT(' ', CONCAT(t1.apellido, CONCAT(' ', t1.segundo_apellido))))))) as nombre,
                    t2.nombre as estado,
                    t2.color, UPPER(t3.nombre) as campamento,
                    UPPER(t1.direccion) as direccion,
                    t1.zona,
                    UPPER(t4.nombre) as colonia,
                    t1.numero_contacto as telefono,
                    UPPER(t1.otra_colonia) as otra_colonia,
                    DATE_FORMAT(t1.created_at, '%d/%m/%Y %h:%i') as fecha_registro
                FROM paciente t1
                INNER JOIN estado t2
                ON t1.id_estado = t2.id
                INNER JOIN campamento t3
                ON t1.id_campamento = t3.id
                LEFT JOIN colonia t4
                on t1.id_colonia = t4.id
                WHERE t3.id = $id_campamento
                AND (
                    UPPER(CONCAT(t1.nombre, CONCAT(' ', CONCAT(t1.segundo_nombre, CONCAT(' ', CONCAT(t1.apellido, CONCAT(' ', t1.segundo_apellido)))))))  like UPPER('%$data->busqueda%')
                    OR UPPER(t1.direccion) LIKE UPPER('%$data->busqueda%')
                    OR t1.zona LIKE '%$data->busqueda%'
                    OR UPPER(t4.nombre) LIKE UPPER('%$data->busqueda%')
                    OR t1.numero_contacto LIKE '%$data->busqueda%'
                    OR DATE_FORMAT(t1.created_at, '%d/%m/%Y %h:%i') LIKE '%$data->busqueda%'
                )
                ORDER BY t1.id desc
                LIMIT 20 OFFSET $offset";

        $result = $conn->query($sql);

        $pacientes = [];

        while ($row = $result->fetch_assoc()) {

            if ($id_rol == "3") {

                $id_paciente = $row["id"];

                //Buscar si se marco el kit de medicina
                $sql = "SELECT COUNT(*) as total
                        FROM bitacora_paciente
                        WHERE id_paciente = $id_paciente
                        AND kit_medicamento != ''";

                $result_ = $conn->query($sql);
                $total = $result_->fetch_assoc();

                if (intval($total["total"]) > 0) {

                    $row["correlativo"] = number_format($row["correlativo"]);

                    $pacientes [] = $row;

                }

            }else{

                $row["correlativo"] = number_format($row["correlativo"]);

                $pacientes [] = $row;

            }

        }

        // Total de pacientes
        $sql = "SELECT COUNT(*) AS total
                FROM paciente t1
                LEFT JOIN colonia t4
                on t1.id_colonia = t4.id
                WHERE id_campamento = 1
                AND (
                    UPPER(CONCAT(t1.nombre, CONCAT(' ', CONCAT(t1.segundo_nombre, CONCAT(' ', CONCAT(t1.apellido, CONCAT(' ', t1.segundo_apellido)))))))  like UPPER('%$data->busqueda%')
                    OR UPPER(t1.direccion) LIKE UPPER('%$data->busqueda%')
                    OR t1.zona LIKE '%$data->busqueda%'
                    OR UPPER(t4.nombre) LIKE UPPER('%$data->busqueda%')
                    OR t1.numero_contacto LIKE '%$data->busqueda%'
                    OR DATE_FORMAT(t1.created_at, '%d/%m/%Y %h:%i') LIKE '%$data->busqueda%'
                )";

        $result = $conn->query($sql);

        $total = $result->fetch_assoc();

        $pagination = ceil($total["total"] / 20);

        $headers = [
            [
                "text" => "Centro",
                "value" => "campamento",
                "sortable" => false,
                "width" => "15%"
            ],
            [
                "text" => "ID",
                "value" => "id",
                "width" => "5%"
            ],
            [
                "text" => "Paciente",
                "value" => "nombre",
                "width" => "20%"
            ],
            [
                "text" => "Dirección",
                "value" => "direccion",
                "width" => "20%"
            ],
            [
                "text" => "Zona",
                "value" => "zona",
                "width" => "10%"
            ],
            [
                "text" => "Colonia",
                "value" => "colonia",
                "width" => "10%"
            ],
            [
                "text" => "Teléfono",
                "value" => "telefono",
                "width" => "10%"
            ],
            [
                "text" => "Registro",
                "value" => "fecha_registro",
                "width" => "25%"
            ],
            [
                "text" => "Acción",
                "value" => "accion",
                "width" => "10%"
            ]
        ];

        $data = [

            "items" => $pacientes,
            "headers" => $headers,
            "pagination" => $pagination

        ];

        echo json_encode($data);

    }

?>
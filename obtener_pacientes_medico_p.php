<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        if ($data->tab == 0) {
            
            $sql = "SELECT t2.id, t2.correlativo, UPPER(CONCAT(t2.nombre, CONCAT(' ', t2.apellido))) as nombre, 
                    UPPER(t3.nombre) as estado, t3.color, t2.numero_contacto, UPPER(t2.direccion) as direccion, t2.zona
                    FROM medico_paciente t1
                    INNER JOIN paciente t2
                    ON t1.id_paciente = t2.id
                    INNER JOIN estado t3
                    ON t2.id_estado = t3.id
                    WHERE id_medico = $data->id_usuario";

        }else{
            
            $sql = "SELECT t2.id, t2.correlativo, UPPER(CONCAT(t2.nombre, CONCAT(' ', t2.apellido))) as nombre, 
                    UPPER(t3.nombre) as estado, t3.color, t2.numero_contacto, UPPER(t2.direccion) as direccion, t2.zona, DATE_FORMAT(t2.created_at, '%d/%m/%Y') as fecha_registro
                    FROM clinica_paciente t1
                    INNER JOIN paciente t2
                    ON t1.id_paciente = t2.id
                    INNER JOIN estado t3
                    ON t2.id_estado = t3.id
                    WHERE t1.id_medico = $data->id_usuario
                    ORDER BY t2.created_at DESC";

        }

        $result = $conn->query($sql);

        $pacientes = [];

        while ($row = $result->fetch_assoc()) {
            
            $pacientes [] = $row;

        }

        if ($data->tab == 0) {

            $headers = [
                [
                    "text" => "Caso",
                    "value" => "correlativo",
                    "sortable" => false,
                    "width" => "10%"
                ],
                [
                    "text" => "Nombre",
                    "value" => "nombre",
                    "width" => "20%"
                ],
                [
                    "text" => "Teléfono",
                    "value" => "numero_contacto",
                    "width" => "10%"
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
                    "text" => "Estado",
                    "value" => "estado",
                    "width" => "20%"
                ],
                [
                    "text" => "Acción",
                    "value" => "accion",
                    "align" => "end",
                    "width" => "10%"
                ]
            ];

        }else{

            $headers = [
                [
                    "text" => "Caso",
                    "value" => "correlativo",
                    "sortable" => false,
                    "width" => "10%"
                ],
                [
                    "text" => "Nombre",
                    "value" => "nombre",
                    "width" => "20%"
                ],
                [
                    "text" => "Teléfono",
                    "value" => "numero_contacto",
                    "width" => "10%"
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
                    "text" => "Estado",
                    "value" => "estado",
                    "width" => "10%"
                ],
                [
                    "text" => "Fecha",
                    "value" => "fecha_registro",
                    "width" => "10%"
                ],
                [
                    "text" => "Acción",
                    "value" => "accion",
                    "align" => "end",
                    "width" => "10%"
                ]
            ];

        }

        

        $table = [
            "items" => $pacientes,
            "headers" => $headers
        ];

        echo json_encode($table);

    }

?>
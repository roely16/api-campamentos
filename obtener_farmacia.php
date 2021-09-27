<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $sql = "SELECT t1.*, t2.nombre as colonia,  DATE_FORMAT(t1.created_at, '%d/%m/%Y %h:%i') as fecha_registro
                FROM paciente t1
                LEFT JOIN colonia t2 
                ON t1.id_colonia = t2.id
                WHERE t1.id_estado != 4
                AND t1.id_campamento = $data->id_campamento
                ORDER BY t1.correlativo DESC";

        $result = $conn->query($sql);

        $pacientes = [];

        while ($row = $result->fetch_assoc()) {
            
            $id_paciente = $row["id"];

            $sql = "SELECT COUNT(*) as total
                    FROM bitacora_paciente
                    WHERE id_paciente = $id_paciente
                    AND kit_medicamento != ''";
        
            $result_ = $conn->query($sql);
            $kit = $result_->fetch_assoc();

            if (intval($kit["total"]) > 0) {

                // $row["bitacoras"] = intval($kit["total"]);

                // Buscar si requiere azitromicina en los reportes
                $sql = "SELECT COUNT(*) as total
                        FROM bitacora_paciente
                        WHERE id_paciente = $id_paciente
                        AND requiere_azitromicina != ''";
                
                $result_ = $conn->query($sql);
                $total = $result_->fetch_assoc();

                if (intval($total["total"]) > 0) {

                    $row["requiere_azitromicina"] = true;

                }

                $pacientes [] = $row;
            }

        }

        $headers = [
            [
                "text" => "Caso",
                "value" => "correlativo",
                "sortable" => false,
                "width" => "5%"
            ],
            [
                "text" => "Nombre",
                "value" => "nombre",
                "width" => "15%"
            ],
            [
                "text" => "DPI",
                "value" => "dpi",
                "width" => "10%"
            ],
            [
                "text" => "Dirección",
                "value" => "direccion",
                "width" => "15%"
            ],
            [
                "text" => "Zona",
                "value" => "zona",
                "width" => "5%"
            ],
            [
                "text" => "Colonia",
                "value" => "colonia",
                "width" => "10%"
            ],
            [
                "text" => "Teléfono",
                "value" => "numero_contacto",
                "width" => "10%"
            ],
            [
                "text" => "Azitromicina",
                "value" => "requiere_azitromicina",
                "width" => "10%",
                "sortable" => false
            ],
            [
                "text" => "Registro",
                "value" => "fecha_registro",
                "width" => "10%"
            ],
            // [
            //     "text" => "Bitacoras",
            //     "value" => "bitacoras",
            //     "width" => "10%"
            // ],
            [
                "text" => "Acción",
                "value" => "accion",
                "align" => "end",
                "width" => "20%",
                "sortable" => false
            ]
        ];

        $data = [
            "items" => $pacientes,
            "headers" => $headers
        ];

        echo json_encode($data);

    }

?>
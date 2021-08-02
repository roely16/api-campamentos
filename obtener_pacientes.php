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

        $year = $data->year_filtro;

        $sql = "SELECT t1.id, t1.correlativo, UPPER(CONCAT(t1.nombre, CONCAT(' ', t1.apellido))) as nombre, 
                UPPER(t2.nombre) as estado, t2.color, t1.numero_contacto, UPPER(t1.direccion) as direccion, t1.zona, DATE_FORMAT(t1.created_at, '%d/%m/%Y %h:%i') as fecha_registro, t1.edad, t1.genero
                FROM paciente t1
                INNER JOIN estado t2
                ON t1.id_estado = t2.id
                WHERE t1.id_campamento = $id_campamento
                AND t1.id_estado IN (
                    SELECT id_estado
                    FROM rol_estado
                    WHERE id_rol = $id_rol
                    AND tabla = 'S'
                )
                AND DATE_FORMAT(created_at, '%Y') = $year
                ORDER BY t1.correlativo desc";

        $result = $conn->query($sql);
        
        $pacientes = [];

        while ($row = $result->fetch_assoc()) {
            
            $id_paciente = $row["id"];

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

            }elseif($id_rol == "2"){

                
                $sql = "SELECT CONCAT(nombre, CONCAT(' ', apellido)) AS medico
                        FROM usuario
                        WHERE id = (
                            SELECT id_medico
                            FROM clinica
                            WHERE id_paciente_asignado = $id_paciente
                        )";

                $result_ = $conn->query($sql);
                $medico = $result_->fetch_assoc();
                
                if($medico){

                    $row["medico"] = $medico["medico"];

                }
                
                //$row["medico"] = "Test";
                $pacientes [] = $row;

            }else{

                $row["correlativo"] = number_format($row["correlativo"]);

                $pacientes [] = $row;

            }

        }

        
        if ($id_rol == "2") {
            
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
                    "text" => "Edad",
                    "value" => "edad",
                    "width" => "7%"
                ],
                [
                    "text" => "Dirección",
                    "value" => "direccion",
                    "width" => "18%"
                ],
                [
                    "text" => "Zona",
                    "value" => "zona",
                    "width" => "7%"
                ],
                [
                    "text" => "Estado",
                    "value" => "estado",
                    "width" => "10%"
                ],
                [
                    "text" => "Registro",
                    "value" => "fecha_registro",
                    "width" => "15%"
                ],
                [
                    "text" => "Médico Asignado",
                    "value" => "medico",
                    "width" => "15%"
                ],
                [
                    "text" => "Acción",
                    "value" => "accion",
                    "align" => "end",
                    "width" => "15%"
                ]
            ];

        }else{

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
                    "text" => "Edad",
                    "value" => "edad",
                    "width" => "7%"
                ],
                [
                    "text" => "Género",
                    "value" => "genero",
                    "width" => "5%"
                ],
                [
                    "text" => "Teléfono",
                    "value" => "numero_contacto",
                    "width" => "10%"
                ],
                [
                    "text" => "Dirección",
                    "value" => "direccion",
                    "width" => "18%"
                ],
                [
                    "text" => "Zona",
                    "value" => "zona",
                    "width" => "7%"
                ],
                [
                    "text" => "Estado",
                    "value" => "estado",
                    "width" => "10%"
                ],
                [
                    "text" => "Registro",
                    "value" => "fecha_registro",
                    "width" => "15%"
                ],
               
                [
                    "text" => "Acción",
                    "value" => "accion",
                    "align" => "end",
                    "width" => "15%"
                ]
            ];

        }

        /*
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
                "text" => "Edad",
                "value" => "edad",
                "width" => "7%"
            ],
            [
                "text" => "Dirección",
                "value" => "direccion",
                "width" => "18%"
            ],
            [
                "text" => "Zona",
                "value" => "zona",
                "width" => "7%"
            ],
            [
                "text" => "Estado",
                "value" => "estado",
                "width" => "10%"
            ],
            [
                "text" => "Registro",
                "value" => "fecha_registro",
                "width" => "15%"
            ],
           
            [
                "text" => "Acción",
                "value" => "accion",
                "align" => "end",
                "width" => "15%"
            ]
        ];
        */

        $data = [

            "items" => $pacientes,
            "headers" => $headers

        ];

        echo json_encode($data);

    }

?>
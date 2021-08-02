<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');
    
    // ID para outbreak Guatemala
    $outbreakId = "a44faf32-bf27-4b39-a4fb-b9fcf29ac2d7";

    /** INICIO DE SESIÓN  */

    // Credenciales 
    $user = "jgutierrezgomez@gmail.com";
    $pass = "$7vumyg7dyzu7";

    $url = "https://godataguatemala.mspas.gob.gt/api/users/login";

    $body = [
        "email" => $user,
        "password" => $pass
    ];

    $payload = json_encode($body);

    // Inicializar de cURL
    $ch = curl_init();

    // Configurar cURL
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json') );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    // Ejecutar y cerrar
    $result = curl_exec($ch);
    $err_status = curl_error($ch);

    curl_close($ch);

    // Convertir respuesta a un array 
    $login_data = json_decode($result, true);
    $token = $login_data["id"];
    
    /** PROCESAMIENTO DE DATOS */

    // Obtener los pacientes de la vista
    $sql = "SELECT *
            FROM datos_generales
            ORDER BY id ASC";

    $result = $conn->query($sql);

    $pacientes = [];

    $pacientes_bk = [];

    while ($row = $result->fetch_assoc()) {
        
        $dpi_paciente = $row["dpi"];

        // Validar que no exista otro registro 
        $sql = "SELECT COUNT(upload_godata) as uploads, COUNT(*) as total
                FROM paciente
                WHERE dpi = '$dpi_paciente'";

        $result_ = $conn->query($sql);

        $paciente_dpi = $result_->fetch_assoc();

        $cantidad_dpi = intval($paciente_dpi["total"]);
        $uploads = intval($paciente_dpi["uploads"]);        

        if ($cantidad_dpi == 1 || $uploads == 0 || !$dpi_paciente) {

            $pacientes_bk [] = $row["id"];

            $id_paciente = $row["id"];
            
            // Formato de Fecha VISTA 
            $fecha_vista  = explode(" ", $row["fecha"]);

            // Validar DPI o Pasaporte
            $tipo_documento = "";
            $cui = "";

            if ($row["dpi"] || $row["cui"]) {
                
                $tipo_documento = 1;

                $cui = $row["dpi"] ? $row["dpi"] : $row["cui"];

            }elseif($row["pasaporte"]){

                $tipo_documento = 2;

                $cui = $row["pasaporte"];

            }

            // Embarazo

            $sql = "SELECT *
                    FROM paciente_verificacion
                    WHERE id_paciente = $id_paciente
                    AND id_verificacion = 24
                    AND marcado = 'S'";

            $result_ = $conn->query($sql);
            $embarazo = $result_->fetch_assoc();

            $text_embarazo = "";

            $text_embarazo = $embarazo ? "LNG_REFERENCE_DATA_CATEGORY_PREGNANCY_STATUS_YES_TRIMESTER_UNKNOWN" : "LNG_REFERENCE_DATA_CATEGORY_PREGNANCY_STATUS_NOT_PREGNANT";

            // Fecha del Reporte

            date_default_timezone_set('America/Guatemala');

            $fecha_reporte = date("Y-m-d");
            $hora_reporte = date("H:i:s");

            $time_stamp = $fecha_reporte . "T" . $hora_reporte . ".000Z";

            // Enfermedades Asociadas

            $sql = "SELECT t2.*
                    FROM paciente_verificacion t1
                    INNER JOIN verificacion t2
                    ON t1.id_verificacion = t2.id
                    WHERE t1.id_paciente = $id_paciente
                    AND t2.enfermedad = 'S'";

            $result_ = $conn->query($sql);

            $enfermedades_asociadas = [];

            while ($row_ = $result_->fetch_assoc()) {
                
                $enfermedades_asociadas [] = $row_["value"];

            }

            // Otras enfermedades 

            $sql = "SELECT t2.*
                    FROM paciente_verificacion t1
                    INNER JOIN verificacion t2
                    ON t1.id_verificacion = t2.id
                    WHERE t1.id_paciente = $id_paciente
                    AND t2.enfermedad = 'O'";

            $result_ = $conn->query($sql);

            $otra_enfermedad = [];

            while ($row_ = $result_->fetch_assoc()) {
                
                $otra_enfermedad [] = $row_["nombre"];

            }

            $text_otras_enfermedades = implode(",", $otra_enfermedad);

            // Si tiene otras enfermedades colocar OTROS en el array Enfermedades Asociadas 

            if ($otra_enfermedad) {
                
                array_push($enfermedades_asociadas, "14");

            }

            // Sintomas 

            $sql = "SELECT t2.*
                    FROM paciente_verificacion t1
                    INNER JOIN verificacion t2
                    ON t1.id_verificacion = t2.id
                    WHERE t1.id_paciente = $id_paciente
                    AND t2.sintoma = 'S'";

            $result_ = $conn->query($sql);

            $sintomas = [];

            while ($row_ = $result_->fetch_assoc()) {
                
                $sintomas [] = $row_["value"];

            }

            // Otros Sintomas
            $sql = "SELECT t2.*
                    FROM paciente_verificacion t1
                    INNER JOIN verificacion t2
                    ON t1.id_verificacion = t2.id
                    WHERE t1.id_paciente = $id_paciente
                    AND t2.sintoma = 'O'";

            $result_ = $conn->query($sql);

            $otros_sintomas = [];

            while ($row_ = $result_->fetch_assoc()) {

                $otros_sintomas [] = $row_["nombre"];

            }

            $text_otros_sintomas = implode(",", $otros_sintomas);

            // Si tiene otros sintomas colocar OTROS en el array Sintomas
            if ($otros_sintomas) {
                
                array_push($sintomas, "14");

            }

            // Información de la bitacora del paciente
            $sql = "SELECT 
                        COUNT(IF (kit_medicamento = 'S', 1, NULL)) AS kit, 
                        COUNT(IF (requiere_azitromicina = 'S', 1, NULL)) AS azitromicina, 
                        COUNT(IF (requiere_constancia = 'S', 1, NULL)) AS constancia, 
                        COUNT(IF (requiere_prueba = 'S', 1, NULL)) AS prueba
                    FROM bitacora_paciente
                    WHERE id_paciente = $id_paciente";

            $result_ = $conn->query($sql);

            $bitacora = $result_->fetch_assoc();

            $kit = intval($bitacora["kit"]);
            $azitromicina = intval($bitacora["azitromicina"]);
            $constancia = intval($bitacora["constancia"]);
            $prueba = intval($bitacora["prueba"]);

            // Resultado de la muestra
            if (!$row["resultado_prueba"]) {

                $resultado_muestra = "3";
                
            }else{

                if ($row["resultado_prueba"] == 'P') {
                    
                    $resultado_muestra = "1";

                }else{

                    $resultado_muestra = "2";

                }

            }

            // Se crea un nuevo array para darle la forma solicitada
            $paciente_godata = [
                "firstName" => $row["first_name"],
                "lastName" => $row["last_name"],
                "visualId" => 'RCC-2020-99999',
                "gender" => $row["genero"] == 'F' ? 'LNG_REFERENCE_DATA_CATEGORY_GENDER_FEMALE' : 'LNG_REFERENCE_DATA_CATEGORY_GENDER_MALE',
                "age" =>  [
                    "years" => $row["edad"]
                ],
                "pregnancyStatus" => $text_embarazo,
                "addresses" => [
                    [
                        "city" => $row["zona"],
                        "addressLine1" => $row["address_line"],
                        "typeId" => "LNG_REFERENCE_DATA_CATEGORY_ADDRESS_TYPE_USUAL_PLACE_OF_RESIDENCE",
                        "locationId" => $row["dms"],
                        "date" => $fecha_vista[0] . "T" . $fecha_vista[1] . ".000Z",
                        "phoneNumber" => $row["numero_contacto"]
                    ]
                ],
                "dateOfReporting" => $time_stamp,
                "outbreakId" => $outbreakId,
                "classification" => $row["id_clasificacion"] != '1' ? "LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT" : "LNG_REFERENCE_DATA_CATEGORY_CASE_CLASSIFICATION_SUSPECT",
                "outcomeId" => "LNG_REFERENCE_DATA_CATEGORY_OUTCOME_ALIVE",
                "dateOfOutcome" => $fecha_vista[0] . "T" . $fecha_vista[1] . ".000Z",
                "dateOfOnset" => $fecha_vista[0] . "T" . $fecha_vista[1] . ".000Z",
                "questionnaireAnswers" => [
                    "Case_WhichForm" => [
                        [
                            "value" => "Ficha Epidemiológica 1"
                        ]
                    ],
                    "FE103no_de_ficha_de_notificacion" => [
                        [
                            "value" => $row["no_caso"]
                        ]
                    ],
                    "FE101unidad_notificadora" => [
                        [
                            "value" => "3"
                        ]
                    ],
                    "clinicas_temporales" => [
                        [
                            "value" => $row["value"]
                        ]
                    ],
                    "FE108documento_de_identificacion" => [
                        [
                            "value" => "$tipo_documento"
                        ]
                    ],
                    "FE10801numero_de_documento_cui" => [
                        [
                            "value" => $cui
                        ]
                    ],
                    "FE124tipo_de_vigilancia" => [
                        [
                            "value" => ["1"]
                        ]
                    ],
                    /*
                    "kit" => [
                        [
                            "value" => $kit >= 1 ? "1" : "2"
                        ]
                    ],
                    "recibio_antibioticos" => [
                        [
                            "value" => $azitromicina >= 1 ? "1" : "2"
                        ]
                    ],
                    "solicito_constancia" => [
                        [
                            "value" => $constancia >= 1 ? "1" : "2"
                        ]
                    ],
                    "FE121se_tomo_una_muestra_respiratoria" => [
                        [
                            "value" => "1"
                        ]
                    ],
                    "FE12102fecha_y_hora_de_toma_de_la_muestra" => [
                        [
                            "value" => ""
                        ]
                    ],  
                    "FE12101tipo_de_muestra" => [
                        [
                            "value" => ["3"]
                        ]
                    ],
                    "tipo_de_prueba" => [
                        [
                            "value" => "2"
                        ]
                    ],
                    "FE12103resultado_de_la_muestra" => [
                        [
                            "value" => $resultado_muestra
                        ]
                    ],
                    "FE12105virus_detectado_clone_3254952a-242f-4c8b-b222-427d5c70cc4e" => [
                        [
                            "value" => ""
                        ]
                    ],
                    */
                    "FE113enfermedades_asociadas" => [
                        [
                            "value" => $enfermedades_asociadas
                        ]
                    ],
                    "FE11301especifique" => [
                        [
                            "value" => $text_otras_enfermedades
                        ]
                    ],
                    "FE114sintomas" => [
                        [
                            "value" => $sintomas
                        ]
                    ],
                    "FE11401especifique" => [
                        [
                            "value" => $text_otros_sintomas
                        ]
                    ],
                    "listado_de_contactos"=> []
                ]
            ];

            $pacientes [] = $paciente_godata;


            /* ENVIO A GO DATA */

            $url = "https://godataguatemala.mspas.gob.gt/api/outbreaks/" . $outbreakId . "/cases";

            $payload = json_encode($pacientes);

            // Inicializar de cURL
            $ch = curl_init();

            // Configurar cURL
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:'.$token) );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYHOST, FALSE );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

            // Ejecutar y cerrar
            
            $result_ = curl_exec($ch);
            $err_status = curl_error($ch);
            

            curl_close($ch);

            $data_response = [
                "response" => json_decode($result_),
                "data" => $pacientes
            ];

            // Guardar JSON
            
            
            $fecha_upload = date('d.m.Y H.i.s');

            $fp = fopen('uploads/upload '.$fecha_upload.' id ' . $id_paciente . '.json', 'w');
            fwrite($fp, json_encode($data_response, JSON_UNESCAPED_UNICODE));
            fclose($fp);

            $pacientes = [];
            

        }

    }

    // Actualizar los registros que se han subido 
    
    
    foreach ($pacientes_bk as $id) {
        
        $sql = "UPDATE paciente SET upload_godata = NOW() WHERE id = $id";
        $result = $conn->query($sql);

    }
    
    
    echo json_encode($data_response);

?>
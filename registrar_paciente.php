<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {

        $year = date("Y");

        // Buscar la cantidad registrar en el campamento
        $sql = "SELECT correlativo
                FROM paciente
                WHERE id_campamento = $data->id_campamento
                ORDER BY id DESC";

        $result = $conn->query($sql);
        $registrados = $result->fetch_assoc();

        if(intval($registrados["correlativo"]) <= 0){

            $correlativo = 1;
            
        }else{

            $correlativo = intval($registrados["correlativo"]) + 1;

        }
        
        if ($data->contacto_paciente_positivo) {
            
            $data->contacto_paciente_positivo = 'S';
        }

        if ($data->vive_mismo_lugar) {
            
            $data->vive_mismo_lugar = 'S';
        }

        if ($data->atencion_sin_equipo) {
            
            $data->atencion_sin_equipo = 'S';
        }

        if($data->vacunado){

            $data->vacunado = 'S';

        }

        try {
            
            if ($data->esquema_completo) {
            
                $data->esquema_completo = 'S';
    
            }

        } catch (\Throwable $th) {

            $data->esquema_completo = '';

        }

        if ($data->astrazeneca) {
            
            $data->astrazeneca = 'S';

        }

        if($data->sputnik_v){

            $data->sputnik_v = 'S';

        }

        $data->id_clasificacion = !empty($data->id_clasificacion) ? "'$data->id_clasificacion'" : "NULL";

        $sql = "INSERT INTO paciente (nombre, apellido, dpi, genero, fecha_nacimiento, edad, zona, afiliacion_igss, observaciones, id_campamento, registrado_por, correlativo, id_estado, created_at, segundo_nombre, segundo_apellido, toma_temperatura, calle, avenida, nomenclatura, barrio, observaciones_direccion, contacto_paciente_positivo, cuanto_tiempo_contacto, por_cuanto_tiempo_contacto, vive_mismo_lugar, atencion_sin_equipo, numero_contacto, direccion, id_colonia, otra_colonia, id_clasificacion, cui, pasaporte, frecuencia_cardiaca, frecuencia_respiratoria, saturacion_oxigeno, edad_meses, vacunado, esquema_completo, astrazeneca, sputnik_v, otra_vacuna ) VALUES ('$data->nombre', '$data->apellido', '$data->dpi', '$data->genero', '$data->fecha_nacimiento', '$data->edad', '$data->zona', '$data->afiliacion_igss', '$data->observaciones', '$data->id_campamento', '$data->registrado_por', '$correlativo', 1, NOW(), '$data->segundo_nombre', '$data->segundo_apellido', '$data->toma_temperatura', '$data->calle', '$data->avenida', '$data->nomenclatura', '$data->barrio', '$data->observaciones_direccion', '$data->contacto_paciente_positivo', '$data->cuanto_tiempo_contacto', '$data->por_cuanto_tiempo_contacto', '$data->vive_mismo_lugar', '$data->atencion_sin_equipo', '$data->numero_contacto', '$data->direccion', '$data->id_colonia', '$data->otra_colonia', $data->id_clasificacion, '$data->cui', '$data->pasaporte', '$data->frecuencia_cardiaca', '$data->frecuencia_respiratoria', '$data->saturacion_oxigeno', '$data->edad_meses', '$data->vacunado', '$data->esquema_completo', '$data->astrazeneca', '$data->sputnik_v', '$data->otra_vacuna')";

        $result = $conn->query($sql);

        $id_paciente = $conn->insert_id;

        if ($result) {
        
            // Registrar los sintomas
            foreach ($data->categorias as $categoria) {
                
                foreach ($categoria->verificaciones as $verificacion) {

                    if ($verificacion->checked || $verificacion->comentario != "") {

                        if ($verificacion->checked) {
                            
                            $verificacion->checked = 'S';

                        }

                        $sql = "INSERT INTO paciente_verificacion (id_paciente, id_verificacion, comentario, marcado, created_at) VALUES ($id_paciente, $verificacion->id, '$verificacion->comentario', '$verificacion->checked', NOW())";
                        $result = $conn->query($sql);

                    }

                }

                // $sql = "INSERT INTO sintoma_paciente (id_paciente, id_sintoma) VALUES ($id_paciente, $sintoma->id)";
                // $result = $conn->query($sql);

            }

            // Registrar los contactos
            if (isset($data->contactos)) {
                # code...
                foreach ($data->contactos as $contacto) {
                
                    $sql = "INSERT INTO contacto (id_paciente, nombre, apellido, telefono, direccion, parentesco) VALUES ('$id_paciente', '$contacto->nombre', '$contacto->apellido', '$contacto->telefono', '$contacto->direccion', '$contacto->parentesco')";
    
                    $result = $conn->query($sql);
                    
                }
            }
            

            $data = [
                "code" => 200,
                "title" => "<h6>Paciente Registrado</h6>",
                "message" => '<h1 style="color: blue">' . $data->nombre . ' ' . $data->apellido . '</h1>'.
                            '<h2 style="color: green">Caso No. ' . number_format($correlativo) . '</h2>'
            ];

        }else{

            $data = [
                "code" => 100,
                "title" => "<h6>Error al registrar al paciente</h6>",
                "message" => '<p>Se a presentado un error en el registro, por favor verificar los datos ingresados.</p>'
            ];

        }

        echo json_encode($data);   

    }

?>
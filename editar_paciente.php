<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        if ($data->contacto_paciente_positivo) {
            
            $data->contacto_paciente_positivo = 'S';
        }

        if ($data->vive_mismo_lugar) {
            
            $data->vive_mismo_lugar = 'S';
        }

        if ($data->atencion_sin_equipo) {
            
            $data->atencion_sin_equipo = 'S';
        }


        // Vacunación

        if($data->vacunado){

            $data->vacunado = 'S';

        }else{

            $data->vacunado = null;
        }

        try {
            
            if ($data->esquema_completo) {
            
                $data->esquema_completo = 'S';
    
            }else{

                $data->esquema_completo = null;

            }

        } catch (\Throwable $th) {

            $data->esquema_completo = null;

        }

        $data->id_clasificacion = !empty($data->id_clasificacion) ? "'$data->id_clasificacion'" : "NULL";

        $data->vacuna_id = !empty($data->vacuna_id) ? "'$data->vacuna_id'" : "NULL";

        $sql = "UPDATE paciente SET nombre = '$data->nombre', apellido = '$data->apellido', dpi = '$data->dpi', genero = '$data->genero', fecha_nacimiento = '$data->fecha_nacimiento', edad = '$data->edad', direccion = '$data->direccion', colonia = '$data->colonia', zona = '$data->zona', afiliacion_igss = '$data->afiliacion_igss', observaciones = '$data->observaciones', updated_at = NOW(), segundo_nombre = '$data->segundo_nombre', segundo_apellido = '$data->segundo_apellido', toma_temperatura = '$data->toma_temperatura', calle = '$data->calle', avenida = '$data->avenida', nomenclatura = '$data->nomenclatura', barrio = '$data->barrio', observaciones_direccion = '$data->observaciones_direccion', contacto_paciente_positivo = '$data->contacto_paciente_positivo', cuanto_tiempo_contacto = '$data->cuanto_tiempo_contacto', por_cuanto_tiempo_contacto = '$data->por_cuanto_tiempo_contacto', vive_mismo_lugar = '$data->vive_mismo_lugar', atencion_sin_equipo = '$data->atencion_sin_equipo', numero_contacto = '$data->numero_contacto', direccion = '$data->direccion', id_colonia = '$data->id_colonia', otra_colonia = '$data->otra_colonia', id_clasificacion = $data->id_clasificacion, cui = '$data->cui', pasaporte = '$data->pasaporte', frecuencia_cardiaca = '$data->frecuencia_cardiaca', frecuencia_respiratoria = '$data->frecuencia_respiratoria', saturacion_oxigeno = '$data->saturacion_oxigeno', edad_meses = '$data->edad_meses', vacunado = '$data->vacunado', esquema_completo = '$data->esquema_completo', otra_vacuna = '$data->otra_vacuna', vacuna_id = $data->vacuna_id WHERE id = $data->id";

        $result = $conn->query($sql);

        if ($result) {

            // Eliminar los sintomas y volver a registrar
            $sql = "DELETE FROM paciente_verificacion WHERE id_paciente = $data->id";
            $result_ = $conn->query($sql);

            foreach ($data->categorias as $categoria) {
                
                foreach ($categoria->verificaciones as $verificacion) {

                    if ($verificacion->checked || $verificacion->comentario != "") {

                        if ($verificacion->checked) {
                            
                            $verificacion->checked = 'S';

                        }

                        $sql = "INSERT INTO paciente_verificacion (id_paciente, id_verificacion, comentario, marcado, created_at) VALUES ($data->id, $verificacion->id, '$verificacion->comentario', '$verificacion->checked', NOW())";
                        $result = $conn->query($sql);

                    }

                }

            }

            // Eliminar los contactos y volver a registrar
            $sql = "DELETE FROM contacto WHERE id_paciente = $data->id";
            $result_ = $conn->query($sql);

            // Registrar los contactos
            foreach ($data->contactos as $contacto) {
                
                $sql = "INSERT INTO contacto (id_paciente, nombre, apellido, telefono, direccion, parentesco) VALUES ('$data->id', '$contacto->nombre', '$contacto->apellido', '$contacto->telefono', '$contacto->direccion', '$contacto->parentesco')";

                $result = $conn->query($sql);
                
            }

            $data = [
                "code" => 200,
                "title" => "<h6>Actualización Realizada</h6>",
                "message" => '<h1 style="color: blue">' . $data->nombre . ' ' . $data->apellido . '</h1>'.
                            '<h2 style="color: green">Caso No. ' . number_format($data->correlativo) . '</h2>'
            ];

        }

        echo json_encode($data);

    }

?>
<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $result = false;

        if ($data->id_clasificacion == '1') {
            
            $result = true;

        }  
        
        if ($data->contacto_paciente_positivo) {
            
            $result = true;

        }  

        if ($data->atencion_sin_equipo) {
            
            $result = true;

        }
        
        // Verificar en los sintomas 
        $sintomas = ["1", "2", "4", "5"];
        $i = 0;

        foreach ($data->categorias as $categoria) {

            foreach ($categoria->verificaciones as $verificacion) {

                if ($verificacion->checked) {
                            
                    if (in_array($verificacion->id, $sintomas)) {
                        
                        $i++;

                    }

                }

            }

        }

        if ($i == 4) {
            
            $result = true;

        }

        echo json_encode($result);

    }

?>
<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "SELECT *, CONCAT(nombre, CONCAT(' ', apellido)) as nombre_completo
                FROM paciente
                WHERE id = $data->id";

        $result = $conn->query($sql);

        $paciente = $result->fetch_assoc();

        $paciente["zona"] = intval($paciente["zona"]);

        if ($paciente["otra_vacuna"]) {
            
            $paciente["otra_vacuna_check"] = true;

        }

        // // Sintomas
        // $sql = "SELECT *
        //         FROM sintoma";

        // $result = $conn->query($sql);

        // $sintomas = [];

        // while ($row = $result->fetch_assoc()) {
        
        //     $id_sintoma = $row["id"];
        //     $id_paciente = $paciente["id"];

        //     // Buscar sintoma del paciente 
        //     $sql = "SELECT *
        //             FROM sintoma_paciente
        //             WHERE id_paciente = $id_paciente
        //             AND id_sintoma = $id_sintoma";

        //     $result_ = $conn->query($sql);
        //     $sintoma = $result_->fetch_assoc();

        //     $sintoma ? $row["checked"] = true : $row["checked"] = false;
            
        //     $sintomas [] = $row;

        // } 

        // Verificaciones

        $sql = "SELECT *
                FROM categoria";

        $result = $conn->query($sql); 

        $categorias = [];

        while ($row = $result->fetch_assoc()) {

            $id_paciente = $paciente["id"];

            // Buscar los contactos del paciente
            $sql = "SELECT *
                    FROM contacto
                    WHERE id_paciente = $id_paciente";

            $result_ = $conn->query($sql);

            $contactos = [];
            $i = 0;
            while ($row_ = $result_->fetch_assoc()) {
                
                $row_["index"] = $i;
                $contactos [] = $row_;
                $i++;

            }

            $id_categoria = $row["id"];

            $sql = "SELECT *
                    FROM verificacion
                    WHERE id_categoria = $id_categoria";

            $result_ = $conn->query($sql);

            $verificaciones = [];

            while ($row_ = $result_->fetch_assoc()) {
                
                $id_verificacion = $row_["id"];

                // Buscar sintoma del paciente 
                $sql = "SELECT *
                        FROM paciente_verificacion
                        WHERE id_paciente = $id_paciente
                        AND id_verificacion = $id_verificacion";

                $result_2 = $conn->query($sql);
                $verificacion = $result_2->fetch_assoc();

                if ($verificacion) {
                    
                    if ($verificacion["marcado"]) {

                        $row_["checked"] = true;

                    }else{

                        $row_["checked"] = false;

                    }
                    
                    
                    if (!$verificacion["comentario"]) {
                        
                        $row_["comentario"] = "";

                    }else{

                        $row_["comentario"] = $verificacion["comentario"];

                    }

                    $row_["mostrar_observacion"] = false;
                    
                }else{

                    $row_["checked"] = false;
                    $row_["mostrar_observacion"] = false;
                    $row_["comentario"] = "";   

                }

                
                $verificaciones [] = $row_;

            }

            $row["verificaciones"] = $verificaciones;

            $categorias [] = $row;

        }
        
        // Bitacora
        $sql = "SELECT t1.*, DATE_FORMAT(t1.created_at, '%d/%m/%Y %H:%i:%s') as created_at, CONCAT(t2.nombre, CONCAT(' ', t2.apellido)) as medico
                FROM bitacora_paciente t1
                INNER JOIN usuario t2
                ON t1.id_medico = t2.id
                WHERE id_paciente = $data->id";

        $result = $conn->query($sql);

        $bitacora = [];

        while ($row = $result->fetch_assoc()) {
            
            // Validar si el reporte es ingresado por el mismo usuario
            if ($row["id_medico"] == $data->id_usuario) {

                $row["editar_reporte"] = true;

            }else{

                $row["editar_reporte"] = false;

            }   

            $row["edit"] = false;
            
            $bitacora [] = $row;

        }

        // Clasificacion
        $sql = "select *
                from clasificacion";

        $result = $conn->query($sql);

        $clasificacion = [];
    
        $id_clasificacion = $paciente["id_clasificacion"];

        while ($row = $result->fetch_assoc()) {
            
            if ($id_clasificacion == $row["id"]) {
                
                $row["checked"] = true;

            }else{

                $row["checked"] = false;
            }

            $clasificacion [] = $row;
    
        }

        $paciente["categorias"] = $categorias;
        $paciente["bitacora"] = $bitacora;
        $paciente["clasificacion"] = $clasificacion;
        $paciente["contactos"] = $contactos;

        echo json_encode($paciente);

    }

?>
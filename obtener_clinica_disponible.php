<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "SELECT *
                FROM usuario
                WHERE id = $data->id_usuario";

        $result = $conn->query($sql);

        $usuario = $result->fetch_assoc();

        // Buscar una clinica del campamento disponible 
        $id_campamento = $usuario["id_campamento"];

        // Debe ser una clinica sin asignar en la misma ronda
        $sql = "SELECT t1.*
                FROM clinica t1
                INNER JOIN usuario t2
                ON t1.id_medico = t2.id
                WHERE t1.id_campamento = $id_campamento
                AND (t1.ocupada = '' OR t1.ocupada IS NULL)
                AND t1.id_medico IS NOT NULL
                AND t2.en_linea IS NOT NULL";

        $result = $conn->query($sql);
        // $clinica = $result->fetch_assoc();

        // Clinicas disponibles
        $clinicas_disponibles = [];

        while ($row = $result->fetch_assoc()) {
            
            $clinicas_disponibles [] = $row["id"];

        }

        // Seleccionar de forma aleatoria del array

        if (count($clinicas_disponibles) > 0) {
            
            $random_clinica = array_rand($clinicas_disponibles, 1);

        }
        

        if (count($clinicas_disponibles) > 0) {
            
            $sql = "SELECT *
                    FROM paciente 
                    WHERE id_estado = 2
                    AND id_campamento = $id_campamento
                    ORDER BY id ASC
                    LIMIT 1";

            $result = $conn->query($sql);
            $paciente = $result->fetch_assoc();

            if ($paciente) {

                $id_paciente = $paciente["id"];
                $id_clinica = $clinicas_disponibles[$random_clinica];

                // Buscar si el paciente ya esta asignado a una clinica
                $sql = "SELECT *
                        FROM clinica 
                        WHERE id_paciente_asignado = $id_paciente";

                $result = $conn->query($sql);
                $asignado = $result->fetch_assoc();

                if (!$asignado) {
                
                    $sql = "UPDATE clinica SET id_paciente_asignado = $id_paciente WHERE id = $id_clinica";
                    $result = $conn->query($sql);

                    $sql = "SELECT *
                            FROM clinica 
                            WHERE id_paciente_asignado = $id_paciente";

                    $result = $conn->query($sql);
                    $clinica = $result->fetch_assoc();
                
                }else{

                    $sql = "SELECT *
                            FROM clinica 
                            WHERE id_paciente_asignado = $id_paciente";

                    $result = $conn->query($sql);
                    $clinica = $result->fetch_assoc();

                }

                $data = [
                    "clinica" => $clinica,
                    "paciente" => $paciente
                ];

            }

        }else {

            $data = NULL;

        }

        echo json_encode($data);

    }

?>
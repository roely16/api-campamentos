<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $fecha = date('d/m/Y');

    $sql = "SELECT id, nombre
            FROM campamento
            WHERE privado IS NULL";

    $result = $conn->query($sql);

    $campamentos = [];

    while ($row = $result->fetch_assoc()) {
        
        $id_campamento = $row["id"];

        $sql = "SELECT 
                COUNT(IF (genero = 'M', 1, NULL)) Hombres,
                COUNT(IF (genero = 'F', 1, NULL)) Mujeres,
                COUNT(IF (id_estado = 5, 1, NULL)) Traslados
                FROM paciente
                WHERE id_campamento = $id_campamento
                AND DATE_FORMAT(created_at, '%d/%m/%Y') = '$fecha'";

        $result_ = $conn->query($sql);
        $datos_campamento = $result_->fetch_assoc();
        $datos_campamento["Campamento"] = $row["nombre"];

        $campamentos [] = $datos_campamento;

    }

    echo json_encode($campamentos);
    

?>
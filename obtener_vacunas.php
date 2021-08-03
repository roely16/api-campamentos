<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $sql = "    SELECT *
                FROM vacuna";

    $result = $conn->query($sql);
            
    $vacunas = [];

    while ($row = $result->fetch_assoc()) {
        
        $vacunas [] = $row;

    }

    echo json_encode($vacunas);

?>
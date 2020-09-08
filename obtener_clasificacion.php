<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $sql = "SELECT *
            FROM clasificacion
            ORDER BY orden asc";

    $result = $conn->query($sql);

    $clasificacion = [];

    while ($row = $result->fetch_assoc()) {
        
        $clasificacion [] = $row;

    }

    echo json_encode($clasificacion);

?>
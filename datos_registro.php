<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');
    
    $sql = "SELECT *
            FROM rol
            WHERE privado IS NULL";

    $result = $conn->query($sql); 

    $roles = [];

    while ($row = $result->fetch_assoc()) {

        $roles [] = $row;

    }

    $sql = "SELECT *
            FROM campamento
            WHERE privado IS NULL
            AND inactivo IS NULL";

    $result = $conn->query($sql); 

    $campamentos = [];

    while ($row = $result->fetch_assoc()) {

        $campamentos [] = $row;

    }

    // Empresas
    $sql = "SELECT *
            FROM empresa";

    $result = $conn->query($sql);

    $empresas = [];

    while ($row = $result->fetch_assoc()) {
        
        $empresas [] = $row;

    }

    $data = [
        "roles" => $roles,
        "campamentos" => $campamentos,
        "empresas" => $empresas
    ];

    echo json_encode($data);

?>
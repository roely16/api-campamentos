<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    $data = json_decode(file_get_contents("php://input"), false, 512, 1);

    if ($data) {
        
        $sql = "SELECT *
                FROM colonia
                WHERE zona = $data->zona
                ORDER BY id ASC";

        $result = $conn->query($sql);

        $colonias = [];

        while ($row = $result->fetch_assoc()) {

            $colonias [] = $row;

        }

        echo json_encode($colonias);

    }

?>
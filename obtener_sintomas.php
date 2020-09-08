<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once('db.php');

    // $sql = "SELECT *
    //         FROM sintoma";

    // $result = $conn->query($sql);

    // $sintomas = [];

    // while ($row = $result->fetch_assoc()) {
        
    //     $row["checked"] = false;

    //     $sintomas [] = $row;

    // }

    $sql = "SELECT *
            FROM categoria";

    $result = $conn->query($sql); 

    $categorias = [];

    while ($row = $result->fetch_assoc()) {

        $id_categoria = $row["id"];

        $sql = "SELECT *
                FROM verificacion
                WHERE id_categoria = $id_categoria
                ORDER BY orden ASC";

        $result_ = $conn->query($sql);

        $verificaciones = [];

        while ($row_ = $result_->fetch_assoc()) {
        
            $row_["checked"] = false;
            $row_["mostrar_observacion"] = false;
            $row_["comentario"] = "";
            $verificaciones [] = $row_;

        }

        $row["verificaciones"] = $verificaciones;

        $categorias [] = $row;

    }

    echo json_encode($categorias);
?>
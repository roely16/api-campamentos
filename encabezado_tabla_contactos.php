<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    $headers = [
        [
            "text" => "Nombre Completo",
            "value" => "nombre_completo",
            "width" => "25%"
        ],
        [
            "text" => "Dirección",
            "value" => "direccion",
            "width" => "10%"
        ],
        [
            "text" => "Teléfono",
            "value" => "telefono",
            "width" => "10%"
        ],
        [
            "text" => "Parentesco",
            "value" => "parentesco",
            "width" => "10%"
        ],
        [
            "text" => "Acción",
            "value" => "accion",
            "align" => "end",
            "sortable" => false,
            "width" => "20%"
        ]
    ];

    echo json_encode($headers);

?>
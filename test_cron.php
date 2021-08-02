<?php 

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    $fecha_upload = date('d.m.Y H.i.s');

    $fp = fopen('uploads/test '.$fecha_upload. '.json', 'w');
    fwrite($fp, "Test");
    fclose($fp);

?>
<?php 
    //CRIAR CONEXÃO COM O BANCO
    $db_name = "moviestar";
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";

    $connectdb = new PDO("mysql:dbname=" . $db_name . ";host=" . $db_host, $db_user, $db_pass);

    //HABILITAR ERROS PDO
    $connectdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connectdb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
?>
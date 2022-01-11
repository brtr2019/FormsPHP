<?php
    // Указываем кодировку
    header('Content-Type: text/html; charset=utf-8');

    // подключение к bradsitereact.tk
        $server = "fdb13.awardspace.net";
        $username = "2104939_musicstorage";
        $password = "Braduloff1983!";
        $database = "2104939_musicstorage";
 
    // Подключение к базе данных через MySQLi
    $mysqli = new mysqli($server, $username, $password, $database);

    // Проверяем, успешность соединения. 
    if (mysqli_connect_errno()) { 
        echo "<p><strong>Ошибка подключения к БД</strong>. Описание ошибки: ".mysqli_connect_error()."</p>";
        exit(); 
    }

    // Устанавливаем кодировку подключения
    $mysqli->set_charset('utf8');

    //Для удобства, добавим здесь переменную, которая будет содержать название нашего сайта
    $address_site = "http://bradsitereact.ml/";

    //Почтовый адрес администратора сайта
    $email_admin = "admin@bradsitereact.ml";

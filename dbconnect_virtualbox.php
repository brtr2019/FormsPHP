<?php
header('Content-Type: text/html; charset=utf-8');

// подключение к локальному серверу(Virtualbox)

$server = "localhost";
$username = "phpadmin";
$password = "Braduloff1983!";
$database = "musicstore";

$mysqli = new mysqli($server, $username, $password, $database);

if(!$mysqli){
    die("<p>Ошибка подключения к БД.</p><p>Код ошибки:".mysqli_connect_errno."</p><p>Описание ошибки:".mysqli_connect_error()."</p>");
}

$mysqli->set_charset('utf-8');

$address_site = "http://musicstore.ru";













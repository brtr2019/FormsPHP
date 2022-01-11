<?php 
    //Запускаем сессию
    session_start();
    //Добавляем файл подключения к БД
    require_once("dbconnect_bradsitereact.php");
    if(isset($_COOKIE["password_cookie_token"]) && !empty($_COOKIE["password_cookie_token"])){
        $select_user_data = $mysqli->query("SELECT email,password FROM `users` WHERE password_cookie_token='".$_COOKIE["password_cookie_token"]."'");
        if(!$select_user_data){
            echo "<p class='message_error'>Ошибка выборки из БД</p>".$mysqli->error();
        }else{
            $array_user_data = $select_user_data->fetch_array(MYSQLI_ASSOC);
            if($array_user_data){
                $_SESSION["email"] = $array_user_data["email"];
                $_SESSION["password"] = $array_user_data["password"];
            }
        }

    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Название нашего сайта</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>

<div id="header">
    <h2>Шапка сайта</h2>

    <a href="/index.php">Главная</a>

    <div id="auth_block">
    <?php 
        //Проверяем, авторизован ли пользователь
        if(!isset($_SESSION['email']) && !isset($_SESSION['password'])){
    ?>
            <div id="link_register">
                <a href="form_register.php">Регистрация</a>
            </div>

            <div id="link_auth">
                <a href="form_auth.php">Авторизация</a>
            </div>
    <?php
        }else{
            //Если пользователь авторизован, то выводим ссылку Выход              
    ?>
            <div id="link_logout">
                <a href="logout.php">
                <?php
                    if(!isset($_SESSION['first_name'])){
                        //Возвращаем пользователя на страницу авторизации
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ".$address_site."form_auth.php");
                        exit();
                    }else{
                        echo "Выход(".$_SESSION['first_name'].")";
                    }          
                ?>        
                </a>
            </div>
    <?php
        }
    ?>
    </div>
     <div class="clear"></div>
</div>


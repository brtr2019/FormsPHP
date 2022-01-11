<?php
    //Запускаем сессию
    session_start();

    require_once("dbconnect_bradsitereact.php");

    if(isset($_COOKIE["password_cookie_token"])){
    	
    	$update_password_cookie_token = $mysqli->query("UPDATE users SET password_cookie_token='' WHERE email='".$_SESSION["email"]."'");
    	if(!$update_password_cookie_token){
    		echo "<p>Ошибка</p>".$mysqli->error();
    	}else{
    		setcookie("password_cookie_token","",time()-3600);
    	}
    }

    unset($_SESSION["email"]);
    unset($_SESSION["password"]);
    
    // Возвращаем пользователя на ту страницу, на которой он нажал на кнопку выход.
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".$_SERVER["HTTP_REFERER"]);
?>
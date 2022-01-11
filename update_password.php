<?php
	//Запускаем сессию
	session_start();

	//Добавляем файл подключения к БД
	require_once("dbconnect_bradsitereact.php");

	if(isset($_POST["set_new_password"])&&!empty($_POST["set_new_password"])){
		//Проверяем, если существует переменная токена в глобальном массиве POST
		if(isset($_POST['token'])&& !empty($_POST['token'])){
			$token = $_POST['token'];

		}else{
			//Сохраняем в сессию сообщение об ошибке
			$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Отсутствие проверочного кода(передается скрытно) </p>";

			//Возвращаем пользователя на страницу усиновки нового пароля
			header("HTTP/1.1 301 Moved Permanently");
			header("Location:".$address_site."set_new_password.php?email=$email&token=$token");
			//Останавливаем скрипт
			exit();
 		}
 		//Проверяем,если существует переменная email в глобальном массиве POST 
 		if(isset($_POST['email'])&& !empty($_POST['email'])){
 			$email = $_POST['email'];
 		}else{
 			//Сохраняем в сессию сообщение об ошибке
 			$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Отсутсвует почтовый адрес(передается скрытно)</p>";
 			//Возвращаем на страницу установки нового пароля
 			header("HTTP/1.1 301 Moved Permanently");
 			header("Location:".$address_site."set_new_password.php/?email=$email&token=$token");
			//Останавливаем скрипт
			exit(); 
 		}

 		if(isset($_POST["password"])){
 			//Убираем пробелы с начала и конца строки
 			$password = trim($_POST["password"]);
 			//Проверяем совпадают ли пароли
 			if(isset($_POST["confirm_password"])){
 				//Убираем пробелы с начала и конца строки
 				$confirm_password = trim($_POST["confirm_password"]);
 				//Проверяем совпадают ли пароли
 				if($password!=$confirm_password){
 					//Сохраняем в сессию сообщение об ошибке
 					$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Пароли не совпадают</p>";
 					//Возвращаем на страницу установки нового пароля
 					header("HTTP/1.1 301 Moved Permanently");	
 					header("Location:".$address_site."set_new_password.php?eamil=$email&token=$token");
 					//Останавливаем скрипт
 					exit();
 				}else{
 					if(!empty($password)){
 						$password = htmlspecialchars($password,ENT_QUOTES);
 						//Шифруем пароль
 						$password = md5($password."top_secret");
 					}else{
 						//Сохраняем в сессию сообщение об ошибке
 						$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Отутствует поле для ввода пароля</p>";
 						//Возвращаем пользователя на страницу устанвоки нового пароля 
 						header("HTTP/1.1 301 Moved Permanently");
 						header("Location:".$address_site."set_new_password.php?email=$email&token=$token");
 						//Останвливаем скрипт
 						exit();  
 					}
 					//(2)
 					$query_update_password = $mysqli->query("UPDATE users SET password='$password' WHERE email='$email'");
 					if(!$query_update_password){
 						//Сохраняем в сессию сообщение об ошибке
 						$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка при изменении пароля. Ошибка".$mysqli->error."</strong></p>";
 						//Возвращаем на страницу установки нового пароля 
 						header("HTTP/1.1 301 Moved Permanently");
 						header("Location:".$address_site."set_new_password.php?email=$email&token=$token");
 						//Останавливаем скрипт
 						exit();
 					}else{
 						//Подключение шапки
 						require_once("header.php");

 						//Выводим сообщение, что пароль установлен успешно
 						echo '<h1 class="success_message text_center">Пароль успешно изменен</h1>';
 						echo '<h1 class="text_center">Вы можете зайти в свой аккаунт.</h1>';

 						//Подключение подвала
 						require_once("footer.php");
 					}
 				}  
 			}
 		}
	}else{
		exit("<p><strong>Ошибка!</strong>Вы зашли на эту страницу напрямую, поэтому нет данных для обработки. Вы можете перейти на <a href=".$address_site."главную страницу</a></p>");
	}



<?php
	//запускаем сессию
	session_start();

	//Добавляем файл подключения к БД
	require_once("dbconnect_bradsitereact.php");

	//Объявляем ячейку для добавления ошибок, котоыре могут возникнуть при обработке формы.
	$_SESSION["error_messages"] = '';
	//Объявляем ячейку для добавления успешных сообщений
	$_SESSION["success_messages"] = '';
	//Если кнопка Восстановить была нажата
	if(isset($_POST["send"])){
		//Проверяем, отправлена ли капча
		if(isset($_POST["captcha"])){

			$captcha = trim($_POST["captcha"]);//(1)
			if(!empty($captcha)){
				//Сравниваем полученное значение со значением из сессии 
				if(($_SESSION["rand"]!=$captcha)&&($_SESSION["rand"]!="")){
					//Если капча не верна, то возвращаем пользователя на страницу восстановления пароля и там выведем ему сообщение об ошибке,что он ввел неправильную капчу.
					//Сохраняем в сессию сообщение об ошибке
					$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Вы ввели неправильную капчу!</p>";
					//Возвращаем пользователя на страницу восстановления пароля
					header("HTTP/1.1 301 Moved Permanently");
					header("Location:".$address_site."reset_password.php");
					//Останвливаем скрипт
					exit();
 				}
			}else{
				//Сохраняем в сессию сообщение об ошибке
				$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Поле для ввода капчи не должно быть пустым!</p>";
				//Возвращаем пользователя на страницу восстановления пароля
				header("HTTP/1.1 301 Moved Permanently");
				header("Location:".$address_site."reset_password.php");
				//Останавливаем скрипт
				exit(); 
			}
			//Обрабатываем полученный почтовый адрес
			if(isset($_POST["email"])){
				//Обрезаем пробелы с начала и конца строки
				$email = trim($_POST["email"]);
				if(!empty($email)){
					$email = htmlspecialchars($email, ENT_QUOTES);
					//Проверяем формат полученного почтового адреса с помощью регулярного выражения
					$reg_email = "/^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i";
					//Если формат введенного почтового адреса не соответствует регулярному выражению 
					if(!preg_match($reg_email,$email)){
						//Сохраянем в сессию сообщение об ошибке
						$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Вы ввели неправилный email</p>"; 
						//Возвращаем пользователя на страницу восстановления пароля
						header("HTTP/1.1 301 Moved Permanently");
						header("Location:".$address_site."reset_password.php");
						//Останавливаем скрипт
						exit();
					} 
				}
			}else{
				//Сохраняем в сессию сообщение об ошибках
				$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Отсутствует поле для ввода email </p>";
				//Возвращаем пользователя на страницу восстановления пароля
				header("HTTP/1.1 301 Moved Permanently");
				header("Location:".$address_site."reset_password.php");
				//Останавливаем скрипт
				exit();
			} 
		//Запрос к БД на выборку пользователя
		$result_query_select = $mysqli->query("SELECT email_status FROM `users` WHERE email='".$email."'");

		if(!$result_query_select){
			//Сохраняем в сессию сообщение об ошибке
			$_SESSION["error_messages"] = "<p class='message_error'>Ошибка запроса при выборке из БД!</p>";
			//Возвращаем пользователя на страницу восстановления пароля
			header("HTTP/1.1 301 Moved Permanently");
			header("Location:".$address_site."reset_password.php");
			//Останавливаем скрипт  
			exit();
		}else{
			//Проверяем, если в базе нет пользователя с такими данными, то выводим сообщение об ошибке
			if($result_query_select->num_rows==1){
				//Проверяем, подтвержден ли указанный email
				while(($row=$result_query_select->fetch_assoc())!=false){
					//Если email не подтвержден
					if((int)$row["email_status"]===0){
						//Сохраняем в сессию сообщение об ошибке
						$_SESSION["error_messages"]="<p class='message_error'><strong></strong>Неподтвержденный пользователь</p>";
						//Возвращаем пользователя на страницу восстновления пароля
						header("HTTP/1.1 301 Moved Permanently");
						header("Location:".$address_site."reset_password.php");
						//Останавливаем скрипт 
						exit();
					}else{
						//Составляем уникальный и зашифрованный токен
						$token = md5($email.time());
						//Сохраняем токе н в БД
						$query_update_token =$mysqli->query("UPDATE users SET reset_password_token='$token' WHERE email='$email'");
						if(!$query_update_token){
							//Сохраняем в сессию сообщение об ошибке
							$_SESSION["error_messages"] = "<p class='message_error'>Ошибка сохранения токена</p>";
							//Возвращаем пользователя на страницу восстновления пароля
							header("HTTP/1.1 301 Moved Permanently");
							header("Location:".$address_site."reset_password.php");
							//Останавливаем скрипт
							exit();
						}else{
							//Составляем ссылку на страницу установки нового пароля.
					        $link_reset_password = $address_site."set_new_password.php?email=$email&token=$token";
					 
					         //Составляем заголовок письма
					         $subject = "Восстановление пароля на сайте ".$_SERVER['HTTP_HOST'];
					 
					         //Устанавливаем кодировку заголовка письма и кодируем его
					         $subject = "=?utf-8?B?".base64_encode($subject)."?=";
					 
					         //Составляем тело сообщения
$message = 'Hi! For password recovery use this current link <a href="'.$link_reset_password.'">Get New Password</a>.';
					          
					         //Составляем дополнительные заголовки для почтового сервиса mail.ru
					         //Переменная $email_admin, объявлена в файле dbconnect.php
					         $headers = "From: admin@mysite.ml" ."\r\n" ;
					          
					         //Отправляем сообщение с ссылкой на страницу установки нового пароля и проверяем отправлена ли она успешно или нет. 
					        if(mail($email, $subject, $message, $headers)){
					 			//var_dump("Mail was sent");die();
					           $_SESSION["success_messages"] = "<p class='success_message' >Ссылка на страницу установки нового пароля, была отправлена на указанный E-mail ($email) </p>";
 
             					//Отправляем пользователя на страницу восстановления пароля и убираем форму для ввода email
             					header("HTTP/1.1 301 Moved Permanently");
             					header("Location: ".$address_site."reset_password.php?hidden_form=1");
             					exit();
							}else{
								var_dump("Error");die();
								$_SESSION["error_messages"] = "<p class='message_error'>Ошибка при отправлении письма на почту".$email.",c ссылкой на страницу установки нового пароля</p>";
								//Возвращаем пользователя на страницу восстановления пароля
								header("HTTP/1.1 301 Moved Permanently");
								header("Location:".$address_site."reset_password.php");
								//Останаливаем скрипт
								exit();
 							}  
						}
					}//if((int)$row["email_status"]===0)
				
				} //End while			
			}else{
				//Сохраняем в сесию сообщение об ошибке
				$_SESSION["error_messages"] = "<p class='message_error'><strong>Ошибка!</strong>Такой пользователь не зарегистрирован!</p>";
				//Возвращаем пользователя на страницу восстановления пароля
				header("HTTP/1.1 301 Moved Permanently");
				header("Location:".$address_site."reset_password.php");
				//Останавливаем скрипт
				exit();
			}
		}


		}else{//if(isset($_POST["captcha"])){
			//Если капча не передана
			exit("<p><strong>Ошибка!</strong>Отсутствует проверочный код, то есть код капчи. Вы можете перейти на <a href=".$address_site.">главную страницу</a></p>");
		}
	}else{//if(isset($POST["send"]))
		exit("<p><strong>Ошибка!</strong>Вы зашли на эту страницу напрямую, поэтому нет данных дял обработки. Вы можете перейти на <a href=".$address_site.">главную страницу</a></p>");
	}

?>
<?php
//Добавляем файл подключения к БД
require_once("dbconnect_bradsitereact.php");

//Проверим, если существует переменная токен в глобальном массиве GET
if(isset($_GET['token']) && !empty($_GET['token'])){
	$token = $_GET['token'];
}else{
	exit("<p><strong>Ошибка!</strong>Отсутствует проверочный код</p>");
}

//Проверим, если существует переменная токен в гобальном массиве GET
if(isset($_GET['email']) && !empty($_GET['email'])){
	$email = $_GET['email'];
}else{
	exit("<p><strong>Ошибка!</strong>Отсутствует адрес электронной почты</p>");
}

//Делаем запрос в БД на выборку токена из таблицы users
$query_select_user = $mysqli->query("SELECT reset_password_token FROM `users` WHERE `email`='".$email."'");
//Если ошибок в запросе нет
if(($row=$query_select_user->fetch_assoc()) != false){
	//Если такой пользователь существует
	if($query_select_user->num_rows==1){
		//Проверяем, совпадает ли токен
		if($token == $row['reset_password_token']){
		require_once("header.php");	
?>
		<!--Код JavaScript-->
		<script type="text/javascript">
			$(document).ready(function(){
				"use strict";
				//==================Проверка паролей===================
				var password = $('input[name=password]');
				var confirm_password = $('input[name=confirm_password]');
				password.blur(function(){
					if(password.val() != ''){
						//Если длина введенного пароля меньше 6 символов, то выводим сообщение об ошибке
						if(password.val().length<=6){
							//Выводим сообщение об ошибке
							$('#valid_password_message').text('Минимальная длина пароля 6 символов');
							//Проверяем,если пароли не совпадают,то выводим сообщение об ошибке
							if(password.val() !== confirm_password.val()){
								//Выводим сообщение об ошибке
								$('#valid_confirm_password_message').text('Пароли не совпадают');
							}
							//Дезактивируем кнопки отправки
							$('input[type=submit]').attr('disabled',true);  
						}else{
							//Иначе,если длина первого пароля больше 6 символов, то мы также проверяем если они совпадают
							if(password.val()!==confirm_password.val()){
								//Выводим сообщение об ошибке
								$('#valid_confirm_password_message').text('Пароли не совпадают');
								//Дезактивируем кнопку отправки
								$('input[type=submit]').attr('disabled',true);
							}
							else{
								//Убираем сообщение об ошибке у поля для ввода повторного пароля  
								$('#valid_confirm_password_message').text('');
								$('input[type=submit]').attr('disabled',false);
							}
							//Убираем сообщение об ошибке у поля для ввода пароля
							$('#valid_password_message').text(''); 
						}
					}else{
						$('#valid_password_message').text('Введите пароль');
					}
				});
				confirm_password.blur(function(){
					//Если пароли не совпадают
					if(password.val() !== confirm_password.val()){
						//Выводим сообщение об ошибке
						$('#valid_confirm_password_message').text('Пароли не совпадают');
						//Дезактивируемм кнопку отправки
						$('input[type=submit]').attr('disabled',true);						
					}else{
						//Иначе проверяем длину пароля
						if(password.val().length>6){
							//Убираем сообщение об ошибке
							$('#valid_password_message').text('');
							//Активируем кнопку отправки
							$('input[type=submit]').attr('disabled',false);
						}
						//Убираем сообщение об ошибке
						$('#valid_confirm_password_message').text('');
					}
				});
			});
		</script>
		<div class="center_block">
			<h2>Установка нового пароля</h2>
			<!-------Форма установки нового пароля------->
			<form action="update_password.php" method="post">
				<table>
					<tr>
						<td>Введите новый пароль</td>
						<td>
							<input type="password" name="password" placeholder="минимум 6 символов" required="requred" /><br/>
							<span id="valid_password_message" class="message_error"></span>
						</td>
					</tr>
					<tr>
						<td>Повторите пароль:</td>
						<td>
							<input type="password" name="confirm_password" placeholder="минимум 6 символов" required="required"><br/>
							<span id="valid_confirm_password_message" class="message_error"></span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="hidden" name="token" value="<?=$token?>">
							<input type="hidden" name="email" value="<?=$email?>">
							<input type="submit" name="set_new_password" value="Изменить пароль" />
						</td>
					</tr>
				</table>
			</form>
			
		</div>

<?php
	//Подключение подвала 
	require_once("footer.php");
		}else{
			exit("<p><strong>Ошибка!</strong>Неправильный токен</p>");
		}
	}else{
		exit("<p><strong>Ошибка!</strong>Такой пользователь не существует</p>");
	}
}else{
	exit("<p><strong>Ошибка!</strong>Сбой при выборе пользователя из БД </p>");
}
//Завершение запроса выбора пользователя из таблицы users
$query_select_user->close();
//Закрываем подключение к БД
$mysqli->close();

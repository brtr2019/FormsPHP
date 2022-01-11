<?php
//подключение шапки
require_once("header.php");
?>
<!--Проверяем Ввел ли пользователь правильный почтовый адрес-->
<script type="text/javascript">
	$(document).ready(function(){
		"use strict";
		//регулярное выражение для проверки email
		var pattern =  /^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i;
		var mail = $('input[name=email]');

		mail.blur(function(){
			if(mail.val()!=''){
				//проверяем, если email соответствует регулярному выражению
				if(mail.val().search(pattern) == 0){
					//убираем сообщение об ошибке
					$("#valid_email_message").text('');
					//Активируем кнопку отправки
					$('input[type=submit]').attr('disabled',false); 
				}else{
					//Выводим сообщение об ошибке
					$("#valid_email_message").text('Неправильный email');
					$('input[type=submit]').attr('disabled',true);
				}	
			}else{
				$("#valid_email_message").text('Введите ваш email');
			}
		});
	});
</script>

<!----Блок для вывода сообщений------>
<div class="block_for_messages">
	<?php
		if(isset($_SESSION["error_messages"])&& !empty($_SESSION["error_messages"])){
			echo $_SESSION["error_messages"];
			//Уничтожаем ячейку error_messages, чтобы не появлялись сообщения об ошибке при обновлении страницы
			unset($_SESSION["error_messages"]);
		}
		if(isset($_SESSION["success_messages"])&& !empty($_SESSION["success_messages"])){
			echo $_SESSION["success_messages"];
			//Уничтожаем ячейку error_messages, чтобы не появлялись сообщения об ошибке при обновлении страницы
			unset($_SESSION["success_messages"]);
		}	
	?>
</div>

<?php
	//Проверяем, если пользователь не авторизован, то выводим форму регистрации
	//иначе выводим сообщение, что пользователь авторизован 
	if((!isset($_SESSION["email"]) && !isset($_SESSION["password"]))){
		if(!isset($_GET["hidden_form"])){
?>
		<div id="center_block">
			<h2>Восстановление пароля: </h2>
			<!-------Абзац---------->
			<p class="text_center message_error" id="valid_email_message"></p>
			<form action="send_link_reset_password.php" method="post" name="form_request_email">
				<table>
					<tr>
						<td>Введите ваш Email</td>
						<td>
							<input type="email" name="email" placeholder="" />
						</td>
					</tr>
					<tr>
						<td>Введите капчу:</td>
						<td>
							<p>
								<img src="captcha.php" alt="Капча"/><br/>
								<input type="text" name="captcha" placeholder="Проверочный код">
							</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="text_center">
							<input type="submit" name="send" value="Восстановить">
						</td>
					</tr>
				</table>
			</form>
		</div>

<?php
		}//закрывем условие hidden 
	}else{
?>
	<div id="authorized">
		<h2>Вы уже авторизованы</h2>
	</div>
<?php
	}
	//Подключение подвала
	require_once("footer.php");
?>

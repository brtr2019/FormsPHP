<?php
    //Запускаем сессию
    session_start();

    //Добавляем файл подключения к БД
    require_once("dbconnect_bradsitereact.php");

    //Объявляем ячейку для добавления ошибок, которые могут возникнуть при обработке формы.
    $_SESSION["error_messages"] = '';

    //Объявляем ячейку для добавления успешных сообщений
    $_SESSION["success_messages"] = '';


    /*
        Проверяем была ли отправлена форма, то есть была ли нажата кнопка Войти. Если да, то идём дальше, если нет, то выведем пользователю сообщение об ошибке, о том, что он зашёл на эту страницу напрямую.
    */
    if(isset($_POST["btn_submit_auth"]) && !empty($_POST["btn_submit_auth"])){

        //Проверяем полученную капчу
        if(isset($_POST["captcha"])){

            //Обрезаем пробелы с начала и с конца строки
            $captcha = trim($_POST["captcha"]);

            if(!empty($captcha)){

                //Сравниваем полученное значение со значением из сессии. 
                if(($_SESSION["rand"] != $captcha) && ($_SESSION["rand"] != "")){
                    
                    // Если капча не верна, то возвращаем пользователя на страницу авторизации, и там выведем ему сообщение об ошибке что он ввёл неправильную капчу.

                    $error_message = "<p class='message_error'><strong>Ошибка!</strong> Вы ввели неправильную капчу </p>";

                    // Сохраняем в сессию сообщение об ошибке. 
                    $_SESSION["error_messages"] = $error_message;

                    //Возвращаем пользователя на страницу авторизации
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."form_auth.php");

                    //Останавливаем скрипт
                    exit();
                }

            }else{

                $error_message = "<p class='message_error'><strong>Ошибка!</strong> Поле для ввода капчи не должна быть пустой. </p>";

                // Сохраняем в сессию сообщение об ошибке. 
                $_SESSION["error_messages"] = $error_message;

                //Возвращаем пользователя на страницу авторизации
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."form_auth.php");

                //Останавливаем скрипт
                exit();

            }

             //(2) Место для обработки почтового адреса
            if(isset($_POST["email"])){

                //Обрезаем пробелы с начала и с конца строки
                $email = trim($_POST["email"]);

                if(!empty($email)){
                    $email = htmlspecialchars($email, ENT_QUOTES);

                    //Проверяем формат полученного почтового адреса с помощью регулярного выражения
                    $reg_email = "/^[a-z0-9][a-z0-9\._-]*[a-z0-9]*@([a-z0-9]+([a-z0-9-]*[a-z0-9]+)*\.)+[a-z]+/i";

                    //Если формат полученного почтового адреса не соответствует регулярному выражению
                    if( !preg_match($reg_email, $email)){
                        // Сохраняем в сессию сообщение об ошибке. 
                        $_SESSION["error_messages"] .= "<p class='message_error' >Вы ввели неправильный email</p>";
                        
                        //Возвращаем пользователя на страницу авторизации
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: ".$address_site."form_auth.php");

                        //Останавливаем скрипт
                        exit();
                    }

                }else{
                    // Сохраняем в сессию сообщение об ошибке. 
                    $_SESSION["error_messages"] .= "<p class='message_error' >Поле для ввода почтового адреса(email) не должна быть пустой.</p>";
                    
                    //Возвращаем пользователя на страницу регистрации
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."form_register.php");

                    //Останавливаем скрипт
                    exit();
                }
                

            }else{
                // Сохраняем в сессию сообщение об ошибке. 
                $_SESSION["error_messages"] .= "<p class='message_error' >Отсутствует поле для ввода Email</p>";
                
                //Возвращаем пользователя на страницу авторизации
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."form_auth.php");

                //Останавливаем скрипт
                exit();
            }

            
             //(3) Место для обработки пароля
            if(isset($_POST["password"])){

                //Обрезаем пробелы с начала и с конца строки
                $password = trim($_POST["password"]);

                if(!empty($password)){
                    $password = htmlspecialchars($password, ENT_QUOTES);

                    //Шифруем пароль
                    $password = md5($password."top_secret");
                }else{
                    // Сохраняем в сессию сообщение об ошибке. 
                    $_SESSION["error_messages"] .= "<p class='message_error' >Укажите Ваш пароль</p>";
                    
                    //Возвращаем пользователя на страницу регистрации
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."form_auth.php");

                    //Останавливаем скрипт
                    exit();
                }
                
            }else{
                // Сохраняем в сессию сообщение об ошибке. 
                $_SESSION["error_messages"] .= "<p class='message_error' >Отсутствует поле для ввода пароля</p>";
                
                //Возвращаем пользователя на страницу регистрации
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."form_auth.php");

                //Останавливаем скрипт
                exit();
            }
            
            //Удаляем пользователей с таблицы users, которые не подтвердили свою почту в течении сутки
            $query_delete_users = $mysqli->query("DELETE FROM `users` WHERE `email_status` = 0 AND `date_registration` < ( NOW() - INTERVAL 1 DAY )");
            if(!$query_delete_users){
                exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта. Код ошибки: ".$mysqli->errno."</p>");
            }


            //Удаляем пользователей из таблицы confirm_users, которые не подтвердили свою почту в течении сутки
            $query_delete_confirm_users = $mysqli->query("DELETE FROM `confirm_users` WHERE `date_registration` < ( NOW() - INTERVAL 1 DAY)");
            if(!$query_delete_confirm_users){
                exit("<p><strong>Ошибка!</strong> Сбой при удалении просроченного аккаунта(confirm). Код ошибки: ".$mysqli->errno."</p>");
            }

            // (4) Место для составления запроса к БД
            //Запрос в БД на выборке пользователя.
            $result_query_select = $mysqli->query("SELECT * FROM `users` WHERE email = '".$email."' AND password = '".$password."'");

            if(!$result_query_select){
                // Сохраняем в сессию сообщение об ошибке. 
                $_SESSION["error_messages"] .= "<p class='message_error' >Ошибка запроса на выборке пользователя из БД</p>";
                
                //Возвращаем пользователя на страницу регистрации
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$address_site."form_auth.php");

                //Останавливаем скрипт
                exit();
            }else{

                //Проверяем, если в базе нет пользователя с такими данными, то выводим сообщение об ошибке
                if($result_query_select->num_rows == 1){
                    
                    //Проверяем, подтвержден ли указанный email
                    while(($row = $result_query_select->fetch_assoc()) !=false){
                        
                        //Если email не подтверждён
                        if((int)$row["email_status"] == 0){

                            // Сохраняем в сессию сообщение об ошибке. 
                            $_SESSION["error_messages"] = "<p class='message_error' >Вы зарегистрированы, но, Ваш почтовый адрес не подтверждён. Для подтверждения почты перейдите по ссылке из письма, которую получили после регистрации.</p>
                                <p><strong>Внимание!</strong> Ссылка для подтверждения почты, действительна 24 часа с момента регистрации. Если Вы не подтвердите Ваш email в течении этого времени, то Ваш аккаунт будет удалён.</p>";

                            
                            //Возвращаем пользователя на страницу авторизации
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ".$address_site."form_auth.php");

                            //Останавливаем скрипт
                            exit();

                        }else{
                            
                            //Выборка из базы авторизовавшегося пользователя
                            $select_firstname = $mysqli->query("SELECT * FROM `users` WHERE `email` = '".$email."'");
                            if(($row = $select_firstname->fetch_assoc()) != false){
                                if($select_firstname->num_rows==1){
                                    $first_name = $row['first_name'];
                                }else{
                                    echo "Нет данных об этом пользователе";
                                }       
                            }else{
                                    echo "Запрос не выполнился";
                            }
                            //=====================Обработка галочки 'Запомнить меня'===============
                            //Проверяем, если галочка была поставлена 
                            if(isset($_POST["remember_me"])){
                                //Создаем токен
                                $password_cookie_token = md5($array_user_data["id"].$password.time());
                                //Добавляем созданный токен в базу данных
                                $update_password_cookie_token = $mysqli->query("UPDATE users SET password_cookie_token='".$password_cookie_token."' WHERE email='".$email."'");
                                if(!$update_password_cookie_token){
                                    //Сохраняем в сессию сообщение об ошибке
                                    $_SESSION["error_messages"] = "<p class='message_error'>Ошибка функционала 'Запомнить меня'</p>";
                                    //Возвращаем пользователя на страницу авторизации
                                    header("HTTP/1.1 301 Moved Permanently");
                                    header("Location:".$address_site."form_auth.php");
                                    //Останавливаем скрипт
                                    exit();
                                }
                                /*Устанавливаем куку. Параметры функций setcookie():
                                    * 1 параметр - Название куки
                                    * 2 параматр - Значение куки
                                    * 3 параметр - Время жизни куки. Мы указали 30 дней.
                                */    
            setcookie("password_cookie_token", $password_cookie_token, (int)(time() + (1000 * 60 * 60 * 24 * 30)));
                            }else{
                                //Если галочка не была поставлена, то удаляем куки
                                if(isset($_COOKIE["password_cookie_token"])){
                                    //Очищаем поле куки password_cookie_token из БД
                                    $update_password_cookie_token = $mysqli->query("UPDATE users SET password_cookie_token='' WHERE email='".$email."'");
                                    //Удаляем куку password_cookie_token
                                    setcookie("password_cookie_token","",time() - 3600); 
                                }
                            }

                            //место для добавления данных в сессию
                            // Если введенные данные совпадают с данными из базы, то сохраняем логин и пароль в массив сессий.   
                            $_SESSION['email'] = $email;
                            $_SESSION['password'] = $password;
                            $_SESSION['first_name'] = $first_name;

                            //Возвращаем пользователя на главную страницу
                            header("HTTP/1.1 301 Moved Permanently");
                            header("Location: ".$address_site."index.php");

                            //Останавливаем скрипт
                            exit();
                        }

                    }


                    

                }else{
                    
                    // Сохраняем в сессию сообщение об ошибке. 
                    $_SESSION["error_messages"] .= "<p class='message_error' >Неправильный логин и/или пароль</p>";
                    
                    
                    //Возвращаем пользователя на страницу авторизации
                    header("HTTP/1.1 301 Moved Permanently");
                    header("Location: ".$address_site."form_auth.php");

                    //Останавливаем скрипт
                    exit();
                }
            }

        }else{
            //Если капча не передана
            exit("<p><strong>Ошибка!</strong> Отсутствует проверочный код, то есть код капчи. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
        }

    }else{
        exit("<p><strong>Ошибка!</strong> Вы зашли на эту страницу напрямую, поэтому нет данных для обработки. Вы можете перейти на <a href=".$address_site."> главную страницу </a>.</p>");
    }
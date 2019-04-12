<?php
// Страница авторизации
// Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

// Соединямся с БД
$dbconn=pg_connect("host=127.0.0.1 dbname=geobd user=geobd1 password=geobd1");

if(isset($_POST['submit']))
{
    // Вытаскиваем из БД запись, у которой логин равняеться введенному
    $query = pg_query($dbconn,"SELECT user_id, user_password FROM users WHERE user_login='{$_POST['login']}' LIMIT 1");
    $data = pg_fetch_assoc($query);

    // Сравниваем пароли
    if($data['user_password'] === md5(md5($_POST['password'])))
    {
        // Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));

        if(empty($_POST['not_attach_ip']))
        {
            // Если пользователя выбрал привязку к IP
            // Переводим IP в строку
            $insip =$_SERVER['REMOTE_ADDR'];
        }
        // Записываем в БД новый хеш авторизации и IP
        pg_query($dbconn, "UPDATE users SET user_hash='{$hash}',user_ip='{$insip}' WHERE user_id='{$data['user_id']}'");


        // Ставим куки
        setcookie("id", $data['user_id'], time()+60*60*24*30);
        setcookie("hash", $hash, time()+60*60*24*30,null,null,null,true); // httponly !!!

        // Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: index.php"); exit();
    }
    else
    {
        print "Вы ввели неправильный логин/пароль";
    }
}
?>
<!--
<form method="POST">
<center>Логин <input name="login" type="text" required></center><br>
<center>Пароль <input name="password" type="password" required></center<br>
<br><br><input name="submit" type="submit" value="Войти">
</form> -->

<link rel="stylesheet" href="style_login.css"/>
<form class="form-1" method="POST">
    <p class="field">
        <input type="text" name="login" placeholder="Логин">
        <i class="icon-user icon-large"></i>
    </p>
        <p class="field">
        <input type="password" name="password" placeholder="Пароль">
        <i class="icon-lock icon-large"></i>
    </p>       
    <p class="submit">
        <button type="submit" name="submit"><i class="icon-arrow-right icon-large"></i></button>
    </p>
</form>

<?php
require_once __DIR__ . '/../vendor/autoload.php';
$remoteIp = $_SERVER['REMOTE_ADDR'];
$gRecaptchaResponse = $_REQUEST['g-recaptcha-response'];
$secret = '6Le4Xi4UAAAAAIDgujIdBshZIjjkVnHEYZaQ9-SD';
$recaptcha = new \ReCaptcha\ReCaptcha($secret);
$resp = $recaptcha->verify($gRecaptchaResponse, $remoteIp);
if (!$resp->isSuccess()) {
    echo 'Пожалуйста пройдите тест Тьюринга. Для нас важно знать, что вы человек';
    die();
}

require 'config.php';
$dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
//Фаза 1. Регистрация или "авторизация" пользователя.
$email = trim(strtolower($_POST['email']));
$query = "SELECT * FROM users WHERE email = ?";
$stmt = $dbh->prepare($query);
$stmt->execute([$email]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($users) === 0) {
    //Новый клиент
    $values = [
        $_POST['email'],
        $_POST['name'],
        $_POST['phone']
    ];
    $query =
        'INSERT INTO users (email, name, phone)' .
        'VALUES(?, ?, ?)';
    $stmt = $dbh->prepare($query);
    $stmt->execute($values);
    $user_id = $dbh->lastInsertId();
} else {
    //Старый клиент
    $user_id = $users[0]['id'];
}

//Фаза 2. Оформление заказа.
$address = "улица {$_POST['street']}, дом {$_POST['home']}";
if ($_POST['part'] !== '') {
    $address .= ", корпус {$_POST['part']}";
}
if ($_POST['appt'] !== '') {
    $address .= ", кв. {$_POST['appt']}";
}
if ($_POST['part'] !== '') {
    $address .= ", этаж {$_POST['floor']}";
}
$values = [
    $user_id,
    $address,
    $_POST['comment'],
    $_POST['payment'],
    $_POST['callback'] === 'on' ? 1 : 0
];
$query = 'INSERT INTO orders 
(user_id, address, comment, payment_method, callback)
VALUES(?, ?, ?, ?, ?)';
$stmt = $dbh->prepare($query);
$stmt->execute($values);

//Фаза 3. Отправка
$message = "<h1>Заказ №{$dbh->lastInsertId()}</h1>
DarkBeefBurger за 500 рублей, 1 шт<br>
Ваш заказ будет доставлен по адресу:<br>
$address<br>";

$result = $dbh->query("SELECT COUNT(*) FROM orders WHERE user_id = $user_id");
$count = $result->fetch(PDO::FETCH_NUM)[0];
if ($count === 1) {
    $message .= 'Спасибо - это ваш первый заказ';
} else {
    $message .= "Спасибо! Это уже $count заказ";
}
include 'mailer.php';
echo 'Спасибо! Заказ принят';

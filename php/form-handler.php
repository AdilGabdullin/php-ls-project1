<?php
require 'config.php';
$dbh = new PDO(DSN, DB_USER, DB_PASSWORD);

//Фаза 1. Регистрация или "авторизация" пользователя.
$email = trim(strtolower($_POST['email']));
$result = $dbh->query("SELECT * FROM users WHERE email = '$email'");
$users = $result->fetchAll(PDO::FETCH_ASSOC);
if (count($users) === 0) {
    //Новый клиент
    $values = [
        null,
        $_POST['email'],
        $_POST['name'],
        $_POST['phone']
    ];
    $stmt = $dbh->prepare('INSERT INTO users VALUES (?, ?, ?, ?)');
    $stmt->execute($values);
    $user_id = $dbh->lastInsertId();
} else {
    //Старый клиент
    $user_id = $users[0]['id'];
}

//Фаза 2. Оформление заказа.
$address =
    "улица {$_POST['street']} дом {$_POST['home']} " .
    "корпус {$_POST['part']} кв. {$_POST['appt']} этаж {$_POST['floor']}";
$values = [
    '',
    $user_id,
    $address,
    $_POST['comment'],
    $_POST['payment'],
    $_POST['callback'] === 'on' ? 1 : 0
];
$stmt = $dbh->prepare('INSERT INTO orders VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute($values);

//Фаза 3. Запись в файл
$message = <<<EOT
<h1>Заказ №{$dbh->lastInsertId()}</h1>
DarkBeefBurger за 500 рублей, 1 шт<br>
Ваш заказ будет доставлен по адресу:<br>
$address<br>

EOT;
$result = $dbh->query("SELECT COUNT(*) FROM orders WHERE user_id = $user_id");
$count = $result->fetch(PDO::FETCH_NUM)[0];
if ($count === 1) {
    $message .= 'Спасибо - это ваш первый заказ';
} else {
    $message .= "Спасибо! Это уже $count заказ";
}
echo $message;
file_put_contents('../mail/' . date('H.i.s') . '.html', $message);

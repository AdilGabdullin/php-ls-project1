<?php
require_once __DIR__ . '/../vendor/autoload.php';
$mailer = new PHPMailer;
$mailer->isSMTP();
$mailer->Host = 'smtp.mail.ru';
$mailer->SMTPAuth = true;
$mailer->Username = 'misterr_burgerr@mail.ru';
$mailer->Password = 'kV21p6sp7OlCW3SdDNVm';
$mailer->SMTPSecure = 'ssl';
$mailer->Port = 465;
$mailer->setFrom('misterr_burgerr@mail.ru', 'Mister Burger');
$mailer->addAddress($email);
$mailer->isHTML(true);
$mailer->CharSet = "utf-8";
$mailer->Subject = 'Заказ у мистера бургера';
$mailer->Body = $message;
$mailer->send();

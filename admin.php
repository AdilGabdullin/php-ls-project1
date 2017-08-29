<?php
require_once 'vendor/autoload.php';
require 'php/config.php';
$AvailableTables = ['users', 'orders'];
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);
$tableName = $_GET['table'];
// Если не "users" и не "orders" выводим список ссылок
if (!in_array($tableName, $AvailableTables)) {
    echo $twig->render('availableTables.tmpl', ['list' => $AvailableTables]);
    die();
}
$dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
$result = $dbh->query("SELECT * FROM $tableName");
$table = $result->fetchAll(PDO::FETCH_ASSOC);
$data = [
    'tableName' => $tableName,
    'table'     => &$table,
];
echo $twig->render('table.tmpl', $data);

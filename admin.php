<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка</title>
    <style>
        table {
            border-collapse: collapse;
        }

        td {
            border: 1px solid grey;
            padding: 4px;
            text-align: center;
        }
    </style>
</head>
<body>
<?php
function printTable($tableName)
{
    global $dbh;
    $result = $dbh->query("SELECT * FROM $tableName");
    $table = $result->fetchAll(PDO::FETCH_ASSOC);
    echo
    '<table>', PHP_EOL,
    "    <caption>$tableName</caption>", PHP_EOL;
    foreach ($table as $row) {
        echo '    <tr>';
        foreach ($row as $cell) {
            echo '<td>', $cell, '</td>';
        };
        echo '</tr>', PHP_EOL;
    }
    echo '</table>', PHP_EOL;
}

require 'php/config.php';
$dbh = new PDO(DSN, DB_USER, DB_PASSWORD);
printTable('users');
printTable('orders');
?>
</body>
</html>

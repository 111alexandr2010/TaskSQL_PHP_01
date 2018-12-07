<?php

$dsn = 'mysql:dbname=clinic;host=127.0.0.1:3307';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);

    $sqlWithMax = 'SELECT name, weight, nameInRussian, nameInLatin
            FROM animals a
	        LEFT JOIN kindAnimals k
		      ON k.id = speciesId
            WHERE weight >= :minWeight AND weight <= :maxWeight';

    $sqlNoMax = 'SELECT name, weight, nameInRussian, nameInLatin
            FROM animals a
	        LEFT JOIN kindAnimals k
		      ON k.id = speciesId
            WHERE weight >= :minWeight';

    $sthWithMax = $dbh->prepare($sqlWithMax, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sthNoMax = $dbh->prepare($sqlNoMax, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    $minWeight = isset($_GET['minWeight']) ? ($_GET['minWeight']) : 0;
    $maxWeight = isset($_GET['maxWeight']) ? ($_GET['maxWeight']) : 0;

    if (isset($_GET['maxWeight']) && $_GET['maxWeight'] > 0) {
        $sthWithMax->execute(array(':minWeight' => $minWeight, ':maxWeight' => $maxWeight));
        $quarryArray = $sthWithMax->fetchAll();
    } else {
        $sthNoMax->execute(array(':minWeight' => $minWeight));
        $quarryArray = $sthNoMax->fetchAll();
    }

    create_table($quarryArray);

} catch (PDOException $e) {
    echo 'Соединение не установлено! ' . $e->getMessage();
}
?>
<html>
<body>
<form method="get" action="/SQL_PHP/testdb.php">
    <p>Введите минимальный вес <input type="text" name="minWeight"></p>
    <p>Введите максимальный вес <input type="text" name="maxWeight"></p>
    <p><input type="submit" value="OK"></p>
</form>


<?php
function create_table($array)
{
    ?>
    <form method="get" action="/SQL_PHP/testdb.php">
        <table width="60%" border="1">
            <thead>
            <style type="text/css">
                TH {
                    background: yellow;
                    color: black;
                }

                TD {
                    background: whitesmoke;
                    color: grey;
                }
            </style>
            <tr>
                <th>name</th>
                <th>weight</th>
                <th>nameInRussian</th>
                <th>nameInLatin</th>
            </tr>
            </thead>
            <?php
            for ($i = 0; $i < count($array); $i++) {
                echo '<tr>';
                for ($j = 0; $j < 4; $j++) {
                    ?>
                    <td><?= $array[$i][$j] ?></td>
                <?php }
                echo '</tr>';
            }
            ?>
        </table>
    </form>
    </body>
    </html>
    <?php
} ?>

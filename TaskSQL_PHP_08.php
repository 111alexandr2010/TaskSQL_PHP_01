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

    $minWeight = isset($_GET['minWeight']) ? ($_GET['minWeight']) : 0;
    $maxWeight = isset($_GET['maxWeight']) ? ($_GET['maxWeight']) : 0;

    $sthWithMax = $dbh->prepare($sqlWithMax, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    $sthNoMax = $dbh->prepare($sqlNoMax, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

    if (isset($_GET['maxWeight']) && $_GET['maxWeight'] > 0) {
        $sthWithMax->execute(array(':minWeight' => $minWeight, ':maxWeight' => $maxWeight));
        $quarryArray = $sthWithMax->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $sthNoMax->execute(array(':minWeight' => $minWeight));
        $quarryArray = $sthNoMax->fetchAll(PDO::FETCH_ASSOC);
    }

    create_table($quarryArray);

} catch (PDOException $e) {
    echo 'Соединение не установлено! ' . $e->getMessage();
}
?>
<html>
<body>
<form method="get" action="/SQL_PHP/Test.php">
    <p>Введите минимальный вес <input type="text" name="minWeight"></p>
    <p>Введите максимальный вес <input type="text" name="maxWeight"></p>
    <p><input type="submit" value="OK"></p>
</form>

<?php
function create_table($array)
{
?>
<form method="get" action="/SQL_PHP/Test.php">
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
        foreach ($array as $itemArray) {
            echo '<tr>';
            foreach ($itemArray as $key => $value) {
                ?>
                <td><?= $value ?></td>
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


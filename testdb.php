<?php

/*$minWeight = isset($_GET['minWeight']) ? ($_GET['minWeight']) : 0;
*/

$dsn = 'mysql:dbname=clinic;host=127.0.0.1:3307';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);

    $weightMaximum = 'SELECT MAX(weight)
                  FROM animals';

    $rowCount = 'SELECT COUNT(weight)
                  FROM animals 
	              WHERE weight >= :minWeight AND weight <= :maxWeight';

    $sql = 'SELECT name, weight, nameInRussian, nameInLatin
            FROM animals a
	        LEFT JOIN kindAnimals k
		      ON k.id = speciesId
            WHERE weight >= :minWeight AND weight <= :maxWeight';

    $sth1 = $dbh->prepare($weightMaximum, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth1->execute(array());
    $weightMax = $sth1->fetchColumn();

    $minWeight = isset($_GET['minWeight']) ? ($_GET['minWeight']) : 0;
    $maxWeight = isset($_GET['maxWeight']) ? ($_GET['maxWeight']) : $weightMax ;

    $sth2 = $dbh->prepare($rowCount, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth2->execute(array(':minWeight' => $minWeight, ':maxWeight' => $weightMax));

    $sth3 = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth3->execute(array(':minWeight' => $minWeight, ':maxWeight' => $weightMax));

    $rowsCount = $sth2->fetchColumn();
    $quarryArray = $sth3->fetchAll();

    create_table($rowsCount, $quarryArray);

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
</body>
</html>

<?php
function create_table($rowsCount, $array)
{
    ?>
    <html>
    <body>
    <form method="get" action="testdb.php">
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
            for ($i = 0; $i < $rowsCount; $i++) {
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


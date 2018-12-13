<?php

$dsn = 'mysql:dbname=clinic;host=127.0.0.1:3307';
$user = 'root';
$password = '';

try {
    $dbh = new PDO($dsn, $user, $password);

    $sql = 'SELECT * FROM kindAnimals';

    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array());
    $queryArray = $sth->fetchAll();

    if (isset ($_GET['Delete'])) {
        $rowNumber = (int)$_GET['Delete'];
        rowDelete($dbh, $rowNumber);
        header('Connection:/TaskSQL_PHP_01/TaskSQL_PHP_09.php');
    }

    if (isset ($_GET['nameInRussian']) && isset ($_GET['nameInLatin'])) {
        $nameInRussian = $_GET['nameInRussian'];
        $nameInLatin = $_GET['nameInLatin'];

        if (isset ($_GET['insert']) && $nameInRussian != null && $nameInLatin != null) {
            rowInsert($dbh, $nameInRussian, $nameInLatin);
            header('Connection:/TaskSQL_PHP_01/TaskSQL_PHP_09.php');
        }
    }

    createTable($queryArray);

} catch (PDOException $e) {
    echo 'Соединение не установлено!' . $e->getMessage();
}

function rowInsert($dbh, string $nameInRussian, string $nameInLatin)
{
    $sql = 'INSERT INTO kindAnimals(nameInRussian, nameInLatin)
                VALUES (:nameInRussian, :nameInLatin)';
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(':nameInRussian' => $nameInRussian, ':nameInLatin' => $nameInLatin));
}

function rowDelete($dbh, int $rowNumber)
{
    $dbh->exec("DELETE FROM kindAnimals WHERE id = $rowNumber");
}

function createTable($array)
{
    ?>
    <form method="get" action="/TaskSQL_PHP_01/TaskSQL_PHP_09.php">
        <table width="70%" border="1">
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
                <th>___id___</th>
                <th>nameInRussian</th>
                <th>nameInLatin</th>
                <th>isDelete / isInsert</th>
            </tr>
            </thead>
            <?php
            for ($i = 0; $i < count($array); $i++) {
                echo '<tr>';
                for ($j = 0; $j < 3; $j++) {
                    ?>
                    <td><?= $array[$i][$j] ?></td>
                <?php } ?>
                <td>
                    <form method="get" action="/TaskSQL_PHP_01/TaskSQL_PHP_09.php">
                        <p><input type="submit" style="color: red" name="Delete"
                                  value="<?= $array[$i][0] ?>. 'Удалить'"></p>
                    </form>
                </td>
                <?php
                echo '</tr>';
            }
            ?>
            <form method="get" action="/TaskSQL_PHP_01/TaskSQL_PHP_09.php">
                <td></td>
                <td><label for="nameInRussian">Введите nameInRussian </label>
                    <input type="text" id="nameInRussian" name="nameInRussian"></td>
                <td><label for="nameInLatin">Введите nameInLatin </label>
                    <input type="text" id="nameInLatin" name="nameInLatin"></td>
                <td>
                    <p><input type="submit" name="insert" value="Добавить"></p>
            </form>
            </td>
        </table>

    </form>
    </body>
    </html>
    <?php
}

?>

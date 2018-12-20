<?php

final class DB2
{
    protected static $_pdo = null;

    protected static $dbHost = '127.0.0.1:3307';
    protected static $dbName = 'clinic2';
    protected static $dbUser = 'root';
    protected static $dbPass = '';

    protected function __construct()
    {
        echo 'Running the constructor .<br/>';
    }

    protected function __clone()  { }

    protected function __wakeup() { }

    public static function pdo()
    {
        if (is_null(self::$_pdo)) {
            echo 'Creating new connection.<br/>';
            self::$_pdo = new PDO ('mysql:dbname=' . DB2::$dbName . ';host=' . DB2::$dbHost, DB2::$dbUser, DB2::$dbPass);
        };
        return self::$_pdo;
    }
}

class DBS
{
    protected static $db;

    public static function setDb(Pdo $db)
    {
        self::$db = $db;
    }
}

class ActiveRecord extends DBS
{
    const FIND_BY_ID = "SELECT id, name, phone, salary FROM doctors WHERE id = :id";

    const INSERT_DATA = "INSERT INTO doctors (name, phone, salary) VALUES (:name, :phone, :salary)";
    const UPDATE_DATA = "UPDATE doctors SET name = :name, phone = :phone, salary = :salary WHERE id = :id";
    const DELETE_DATA = "DELETE FROM doctors WHERE id = :id";

    public static function findById(int $id)
    {
        try {
            $sth = self::$db->prepare(self::FIND_BY_ID);
            $sth->execute(array(':id' => $id));
            $row = $sth->fetchAll(PDO::FETCH_ASSOC);

            if (count($row) !== 0) {
                $doctor = new Doctor();

                $doctor->id = $id;
                $doctor->name = $row[0]['name'];
                $doctor->phone = $row[0]['phone'];
                $doctor->salary = $row[0]['salary'];
                echo 'Найдена строка: '.$doctor->id. ' - ' . $doctor->name . ' -  ' . $doctor->phone. ' - ' . $doctor->salary. '<br/>';
                return $doctor;
            } else {
                echo "Записи под номером " . $id . "  не существует!" . '<br/>';
                return null;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function insert()
    {
        if (strlen($this->name) < 256 && preg_match("/^\+7[0-9]{10}$/", $this->phone)) {
            $sth = self::$db->prepare(self::INSERT_DATA);
            $sth->execute(array(':name' => $this->name, ':phone' => $this->phone, ':salary' => $this->salary));
            $this->id = self::$db->lastInsertId();
            echo "Внесены новые данные в таблицу : " . $this->id .' -- ' .$this->name. ' -- '. $this->phone.' -- '.$this->salary.'<br/>';
            return $this->id;
        } else {
            echo 'Ввод невозможен - данные некорректны!' . '<br/>';
            return false;
        }
    }

    public function update()
    {
        if (!isset($this->id)) {
            throw new LogicException("Записи с таким номером ID = ".$this->id."  в таблице не существует!");
        } elseif (strlen($this->name) < 256 && preg_match("/^\+7[0-9]{10}$/", $this->phone)) {
            $sth = self::$db->prepare(self::UPDATE_DATA);
            $sth->execute(array(':id' => $this->id, ':name' => $this->name, ':phone' => $this->phone, ':salary' => $this->salary));
            echo "Изменились данные в строке " . $this->id .' на:  ' .$this->name. ' -- '. $this->phone.' -- '.$this->salary. '<br/>';
            return true;
        } else {
            echo 'Изменения невозможны - данные некорректны!'. '<br/>';
            return false;
        }
    }

    public function delete()
    {
        if (isset($this->id)) {
            $sth = self::$db->prepare(self::DELETE_DATA);
            $sth->execute(array(':id' => $this->id));
            echo "Удалены данные из строки " . $this->id .' -- ' .$this->name. ' -- '. $this->phone.' -- '.$this->salary.'<br/>';
            $this->id = null;
            return true;
        } else {
            echo "Записи под номером " . $this->id . " не существует" . '<br/>';
            return false;
        }
    }
}

class Doctor extends ActiveRecord
{
    public $id;
    public $name;
    public $phone;
    public $salary;
}

$db = DB2::pdo();

DBS::setDb($db);

$result = $db->query("SELECT * FROM doctors")->fetchAll(PDO::FETCH_ASSOC);

$doctor = new Doctor();

$doctor->name = 'Дуров Иван';
$doctor->phone = '79132540030';
$doctor->salary = 29100;

$doctor->insert();

$doctor = Doctor::findById(10);

$doctor = Doctor::findById(33);

if (($doctor !== null)) {
    $doctor->name ='Теркин Василий';
    $doctor->phone = '+79157376652h';
    $doctor->salary = 36200;
    $doctor->update();
};

$doctor = Doctor::findById(44);

if ($doctor !== null) {
    try {
        $doctor->delete();
    } catch (\Exception $e) {
        echo $e->getMessage();
    }
}

$result = $db->query("SELECT * FROM doctors")->fetchAll(PDO::FETCH_ASSOC);
echo "_________Данные записей таблицы \"Doctors\":". '<br />';
foreach ($result as $row) {
    echo $row['ID'] . ' - ' . $row['Name'] . ' --  ' . $row['Phone'] . ' -- ' . $row['Salary'] . '<br />';
}





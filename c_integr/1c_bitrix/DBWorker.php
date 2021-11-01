<?php

namespace CiFrame\DB;

/**
 * Класс модели таблицы
 * Каждый экземпляр класса - одна строка из таблицы
 *
 * К значениям полей можно обратиться двумя способами
 *
 * - как к св-ву объекта, в этом случае перед чтением/записи св-ва
 *   будет произведен поиск метода setPopertyName/getPropertyName
 *   и если они есть они будут вызваны и возвращен результат этого вызова
 *
 * - как к массиву, в этом случае данные будут записаны/возвращены как есть
 */
abstract class AbstractModel implements \ArrayAccess
{
    /**
     * Поля записи
     * @var array
     */
    protected $fields = false;

    protected $connection;

    private $sql; // хранит текст последнего запроса

    /**
     * Конструктор класса
     *
     * @param Connection $connection
     * @param mixed $id ID или массив полей сущности
     */
    public function __construct(Connection $connection, $id = false)
    {
        $this->connection = $connection;
        $this->fields = $this->getFields();
        $this->load($id);
    }

    /**
     * Возвращает соединение с БД
     *
     * @return \CiFrame\DB\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Возвращает набор полей таблицы
     * [
     *  fieldName => defaultValue
     * ]
     *
     * @return array
     */
    public abstract function getFields();
    /**
     * Возвращает имя таблицы
     *
     * @return string
     */
    public abstract function getTableName();

    /**
     * Возвращает инстанс PDO
     *
     * @return \mysqli
     */
    public function getMySqli()
    {
        return $this->getConnection()->getMySqli();
    }

    /**
     * Получает поля сущности из БД
     *
     * @param  mixed $id ID или массив полей сущности
     *
     * @return bool
     */
    public function load($id)
    {
        if (!$id) {
            return false;
        }

        $data = is_array($id)
            ? $id
            : $this->findFirst($id)
        ;

        if (!$data) {
            return false;
        }

        $this->fields = $data;
        $this->afterLoad();

        return true;
    }

    /**
     * Вызывается после получения полей сущности из БД
     *
     * @return void
     */
    public function afterLoad()
    {}

    /**
     * Добавляет запись в таблицу
     *
     * @return bool
     * @throws \Exception
     */
    public function insert()
    {
        if ($this->id) {
            throw new \Exception('Record is exists');
        }

        $fields_ = $this->fields;
        if (array_key_exists('ID', $fields_)) { unset($fields_['ID']); }
        $values = $fields_;
        $fields       = array_keys($values);
        $values       = $this->prepareParms($values);

        foreach ($values as $i=>$val) {
            if ($values[$i]) $values[$i] = $this->getMySqli()->real_escape_string($values[$i]);
        }

        $newdata = join(', ', array_map(function ($value) {
            return $value === null ? 'NULL' : "'$value'";
        }, $values));

        $this->sql = "INSERT INTO {$this->getTableName()} (".implode(',', $fields).") VALUES (".$newdata.")";

        $ret = $this->getMySqli()
            ->query($this->sql);

        if (!$ret) var_dump(mysqli_error($this->getMySqli()));

        if ($ret) {
            $newID = $this->getMySqli()->insert_id;
            $this->id = $newID;
        }

        return $ret;
    }

    /**
     * Обновляет запись в таблице
     *
     * @return bool
     * @throws \Exception
     */
    public function update()
    {
        if (!$this->id) {
            throw new \Exception('Record is not exists');
        }

        $values       = $this->fields;
        $fields       = array_keys($values);
        $values       = $this->prepareParms($values);
        $values       = array_values($values);

        $this->sql = "UPDATE {$this->getTableName()} SET ";
        foreach ($fields as $i => $field) {
            $values[$i] = $values[$i] === null ? 'NULL' : "'".$this->getMySqli()->real_escape_string($values[$i])."'";
            $this->sql .= $field .'='. $values[$i] .',';
        }

        $this->sql = trim($this->sql, ',') . ' WHERE id = ' . $this->id;

        $ret = $this->getMySqli()->query($this->sql);

        if (!$ret) var_dump(mysqli_error($this->getMySqli()));

        return $ret;
    }

    /**
     * Сохраняет запись вне зависимости от ее состояния
     *
     * @return bool
     */
    public function save()
    {
        if ($this->id) {
            $response = $this->update();
        } else $response = $this->insert();

        if ($response != TRUE) {
            $msg = mysqli_error($this->getMySqli());
            echo "<pre style='color: red;'>";
            echo 'text: ';
            print_r($msg);
            echo '<br>SQL:<br>';
            print_r($this->sql);
            echo "</pre>";
        }


        return $response;
    }

    /**
     * Удаляет запись из таблицы
     *
     * @return bool
     */
    public function delete()
    {
        if (!$this->id) {
            throw new \Exception('Record is not exists');
        }

        $sql = 'DELETE FROM '. $this->getTableName() ." WHERE id = '$this->id'";

        $ret = $this->getMySqli()->query($sql);

        if ($ret) {
            $this->id = null;
        }

        return $ret;
    }

    /**
     * Возвращает представление записи в виде массива
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->fields;
    }

    /**
     * Проверяет существование св-ва
     *
     * @param  string  $prop
     * @return boolean
     */
    public function __isset($prop)
    {
        $prop = $this->camelCaseToUnderScore($prop);
        return array_key_exists($prop, $this->fields);
    }

    /**
     * Удаляет св-во сущности
     *
     * @param string $prop
     *
     * @return void
     */
    public function __unset($prop)
    {
        throw new \Exception("Can\'t be removed property {$prop}");
    }

    /**
     * Получает значение св-ва сущности
     *
     * @param  string $prop
     *
     * @return mixed
     */
    public function __get($prop)
    {
        $method = 'get'. $this->underScoreToCamelCase($prop, true);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        $prop = $this->camelCaseToUnderScore($prop);
        if (!$this->__isset($prop)) {
            throw new \Exception("Missing property {$prop}");
        }

        return $this->fields[$prop];
    }

    /**
     * Задает значение св-ва сущности
     *
     * @param string $prop
     * @param mixed $value
     *
     * @return void
     */
    public function __set($prop, $value)
    {
        //$prop = ucfirst($prop);
        $method = 'set'. $this->underScoreToCamelCase(ucfirst($prop), true);

        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        $prop = $this->camelCaseToUnderScore($prop);

        if (!$this->__isset($prop)) {
            throw new \Exception("Missing property {$prop}");
        }

        $this->fields[$prop] = $value;
    }

    /**
     * @param string $prop
     *
     * @return bool
     */
    public function offsetExists($prop)
    {
        return $this->__isset($prop);
    }

    /**
     * @param string $prop
     *
     * @return void
     */
    public function offsetUnset($prop)
    {
        throw new \Exception("Can\'t be removed property {$prop}");
    }

    /**
     * @param string $prop
     *
     * @return mixed
     */
    public function offsetGet($prop)
    {
        if (!$this->offsetExists($prop)) {
            throw new \Exception("Missing property {$prop}");
        }

        return $this->fields[$prop];
    }

    /**
     * @param string $prop
     *
     * @return void
     */
    public function offsetSet($prop, $value)
    {
        if (!$this->offsetExists($prop)) {
            throw new \Exception("Missing property {$prop}");
        }

        $this->fields[$prop] = $value;
    }

    /**
     * Выборка записей
     *
     * $parms = "id = 1" or
     * $parms = [
     *  'select' => '*',
     *  'where'  => 'id = :id',
     *  'order'  => 'id asc',
     *  'limit'  => '0,1',
     *  'bind'   => [':id' => 1]
     * ]
     *
     * @param string|array $parms
     *
     * @return object|bool
     */
    public function find($parms = array())
    {
        $parms = is_array($parms)
            ? $parms
            : array(
                'where' => $parms,
            )
        ;

        $sql = sprintf('SELECT %s FROM %s %s %s %s',
            isset($parms['select'])     ? $parms['select'] : implode(',', array_keys($this->getFields())),
            $this->getTableName(),
            isset($parms['where'])      ? "WHERE {$parms['where']}" : '',
            isset($parms['order'])      ? "ORDER BY {$parms['order']}"   : '',
            isset($parms['limit'])      ? "LIMIT {$parms['limit']}"      : ''
        );

        $result  = $this->getMySqli()->query($sql);
        //$result = isset($parms['bind']) ? $query->execute($parms['bind']) : $query->execute();
        //d($result->fetch_all(MYSQLI_ASSOC));
        //return $result ? $query : false;
        return $result ? $result : false;
    }

    /**
     * Выборка одной записи, псевдномим над find limit 0,1
     *
     * @param int|string|array $parms
     *
     * @return array
     */
    public function findFirst($parms = array())
    {

        if (is_numeric($parms)) {
            //$parms = array('where' => 'id = :id', 'bind' => array('id' => $parms));
            $parms = array('where' => "id = $parms");
        } elseif (is_string($parms)) {
            $parms = array('where' => $parms);
        }

        $parms['limit'] = '0,1';

        $data = $this->find($parms);
        if ($data)
        {
            $data = $data->fetch_all(MYSQLI_ASSOC);
            return @$data[0];
        }
        else return array();

    }

    public function findLast($parms = array())
    {

        if (is_numeric($parms)) {
            //$parms = array('where' => 'id = :id', 'bind' => array('id' => $parms));
            $parms = array('where' => "id = $parms");
        } elseif (is_string($parms)) {
            $parms = array('where' => $parms);
        }

        $parms['limit'] = '0,1';

        if (!isset($parms['order'])) {
            $parms['order'] = 'ID';
            $parms['order_direction'] = 'DESC';
        }

        $data = $this->find($parms);
        if ($data)
        {
            $data = $data->fetch_all(MYSQLI_ASSOC);
            return @$data[0];
        }
        else return array();

    }

    /**
     * Составляет массив bind-values для передачи в PDO
     *
     * @param array $parms
     *
     * @return array
     */
    protected function prepareParms($parms)
    {
        /* $ret = array();
        foreach ($parms as $k => $v) {
            $ret[':'. $k] = $v;
        } */

        $ret = $parms;

        return $ret;
    }

    /**
     * Переводит строку из under_score в camelCase
     *
     * @param  string  $string                   строка для преобразования
     * @param  boolean $capitalizeFirstCharacter первый символ строчный или прописной
     *
     * @return string
     */
    protected function underScoreToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        // символы разного регистра
        if (/*strtolower($string) != $string
			&&*/ strtoupper($string) != $string
        ) {
            return $string;
        }

        $string = strtolower($string);
        $string = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $string[0] = strtolower($string[0]);
        }

        return $string;
    }

    /**
     * Переводит строку из camelCase в under_score
     *
     * @param  string  $string    строка для преобразования
     * @param  boolean $uppercase
     *
     * @return string
     */
    protected function camelCaseToUnderScore($string, $uppercase = true)
    {
        // символы разного регистра
        if (strtolower($string) != $string
            && strtoupper($string) != $string
        ) {
            $string = ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $string)), '_');;
        }

        if ($uppercase) {
            $string = strtoupper($string);
        }

        return $string;
    }
}

/**
 * Класс реализует соединения с БД и организует доступ к таблицами
 */
class Connection
{
    protected static $instance;

    /**
     * @var array
     */
    protected $tables = array();

    /**
     * Возвращает инстанс подключения
     *
     * @param \CiFrame\DB\DBConfig $config
     * @return Connection
     * @throws \Exception
     */
    public static function getInstance(DBConfig $config)
    {
        return self::$instance = self::$instance ?: new static($config);
    }

    /**
     * Конструктор класса
     *
     * @param \CiFrame\DB\DBConfig $config
     */
    public function __construct(DBConfig $config)
    {
        $dbConfig = $config->get('DB');

        $this->config   = $config;
        $this->host     = $dbConfig['HOSTNAME'];
        $this->username = $dbConfig['USERNAME'];
        $this->password = $dbConfig['PASSWORD'];
        $this->dbname   = $dbConfig['DBNAME'];
        $this->mysqli   = null;

        self::$instance = $this;

        $this->init();
    }

    /**
     * Возвращает конфиг
     *
     * @return \CiFrame\DB\DBConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the PDO object associated with this connection
     *
     * @return \mysqli
     */
    public function getMySqli()
    {
        if ($this->mysqli && $this->mysqli->ping() == false) {
            $this->mysqli = null;
        }

        if (is_null($this->mysqli)) {
            $this->mysqli = @mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
        }

        return $this->mysqli;
    }

    public function is_connect()
    {
        return $this->getMySqli() == true;
    }

    /*
     * real_escape_string(string)
     */
    public function escape($string)
    {
        return $this->getMySqli()->real_escape_string($string);
    }

    protected function init()
    {
        if ($this->getMySqli()) {
            mysqli_query($this->getMySqli(), "SET NAMES 'utf8'");
            mysqli_query($this->getMySqli(), "SET CHARACTER SET 'utf8'");
            mysqli_query($this->getMySqli(), "SET SESSION collation_connection = 'utf8_general_ci'");
        }
    }
}

/**
 * Класс конфиг базы данных
 */
class DBConfig
{

    protected $options;

    /**
     * Конструктор
     *
     * @param array $params массив опция для переопределения
     */
    public function __construct($params = array())
    {
        $this->options = array(
            /** Данные для подключения к БД */
            'DB' => array(
                'HOSTNAME' => '',
                'USERNAME' => '',
                'PASSWORD' => '',
                'DBNAME' => ''
            ),
        );

        $this->init($params);
    }

    /**
     * Получение значения опции
     *
     * @param string $option       Название опции
     * @param mixed  $defaultValue Значение по умолчанию, если опция не определена
     *
     * @return mixed
     */
    public function get($option, $defaultValue = null, $subKey = null)
    {
        if (!isset($this->options[$option])) {
            return $defaultValue;
        }

        if (isset($subKey) && is_array($this->options[$option])) {
            if (isset($this->options[$option][$subKey])) {
                return $this->options[$option][$subKey];
            }

            return $defaultValue;
        }

        return $this->options[$option];
    }

    /**
     * Запись значения опции
     *
     * @param string $option Название опции
     * @param mixed  $value  Значение опции
     *
     * @return self
     */
    public function set($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Вызывается после создания объекта
     *
     * @param array $params
     * @return void
     */
    protected function init($params = array())
    {
        $this->options = array_merge($this->options, $params);
    }
}

class DBWorker {

    /**
     * Возвращает инстанс MySqli
     *
     * @param Connection $connect
     * @param $sql
     * @param bool $save_array
     * @return array | bool
     */
    public static function query($connect, $sql, $save_array = false) {

        $mysqli = $connect->getMySqli();
        $result = $mysqli->query($sql);

        if (!is_bool($result)) {
            $result = $result->fetch_all(MYSQLI_ASSOC);
            if ($save_array) return $result;
            return count($result) > 1 ? $result : @$result[0];
        }

        return $result;
    }
}

class Options extends AbstractModel {

    public function getTableName()
    {
        return 'ci_options';
    }

    public function getFields()
    {
        return array(
            'ID' => null,
            'CODE' => '',
            'NAME' => '',
            'VALUE' => '',
            'DEF_VALUE' => '',
            'TYPE' => ''
        );
    }

    public function findByCode($code, $select = '*')
    {
        $data = $this->findFirst(array(
            'select' => $select,
            'where'  => 'CODE = :code',
            'bind'   => array(
                ':code' => $code,
            )
        ));

        if ($data) {
            $this->load($data);
            return true;
        }

        return false;
    }
}

class OptionsManager extends AbstractModel{

    private $options;

    public function getTableName()
    {
        return 'ci_options';
    }

    public function getFields()
    {
        return array();
    }


    function __construct(Connection $connection, $id = false)
    {
        parent::__construct($connection, $id);
    }

    /**
     * @return Options[]
     */
    public function getOptions() {

        $data = $this->find(array(
            'select' => '*',
        ))->fetchAll();

        $this->options = array();
        foreach ($data as $item) {
            $this->options[$item['CODE']] = new Options($this->connection, $item['ID']);
        }

        return $this->options;
    }

}
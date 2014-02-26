<?php
class Indi_Db {

    /**
     * Singleton instance
     *
     * @var Indi_Db
     */
    protected static $_instance = null;

    /**
     * PDO object
     *
     * @var PDO
     */
    protected static $_pdo = null;

    /**
     * Array of loaded models, with model class names as keys
     *
     * @var array
     */
    protected static $_modelA = array();

    /**
     * Array of existing entities, with capitalized entity table names as keys
     *
     * @var array
     */
    protected static $_entityA = array();


    /**
     * Array of table names of existing entities, which have `useCache` flag turned on
     *
     * @var array
     */
    protected static $_cacheA = array();

    /**
     * Store queries count
     *
     * @var Indi_Db
     */
    public static $queryCount = 0;


    /**
     * Flag for cache usage
     *
     * @var bool
     */
    public static $useCache = false;

    /**
     * Initial database setup, if $config argument is provided, or just return the singleton instance otherwise
     *
     * @static
     * @param array $config
     * @return null|Indi_Db
     */
    public static function factory($config = array())
    {
        // If singleton instance is not yet created
        if (null === self::$_instance) {

            // Create it
            self::$_instance = new self();

            // Setup a PDO object
            self::$_pdo = new PDO($config->adapter . ':dbname=' . $config->dbname . ';host=' . $config->host,
                $config->username, $config->password);

            // Setup encoding
            self::$_instance->query('SET NAMES utf8');
            self::$_instance->query('SET CHARACTER SET utf8');

            // Get info about existing entities
            $entityA = self::$_instance->query('SELECT * FROM `entity`')->fetchAll();

            // Get info about existing entities
            $fieldA = self::$_instance->query('SELECT * FROM `field` ORDER BY `move`')->fetchAll();

            // Group fields by their entity ids
            $eFieldA = array(); foreach ($fieldA as $fieldI) $eFieldA[$fieldI['entityId']][] = $fieldI; unset($fieldA);

            // Group fields by their entity ids, and set aliases as keys for second dimension
            $efFieldA = array();
            foreach ($eFieldA as $entityId => $fieldA)
                foreach ($fieldA as $fieldI)
                    $efFieldA[$entityId][$fieldI['alias']] = $fieldI; unset($eFieldA);

            // Foreach existing entity
            foreach ($entityA as $entityI) {

                // Create an item within self::$_entityA array, containing some basic info
                self::$_entityA[ucfirst($entityI['table'])] = array(
                    'id' => $entityI['id'],
                    'extends' => $entityI['extends'],
                    'useCache' => $entityI['useCache'],
                    'fields' => $efFieldA[$entityI['id']]
                );

                // Free memory, used by fields array for current entity
                unset($efFieldA[$entityI['id']]);

                // If cache usage is setup for current entity, we append it's table name as a key in self::$_cacheA array
                if ($entityI['useCache']) self::$_cacheA[$entityI['table']] = true;
            }
        }

        // Return instance
        return self::$_instance;
    }

    /**
     * Loads and returns the model by model entity id, or model class name, or entity table name.
     *
     * @static
     * @param int|string $identifier
     * @return Indi_Db_Table
     * @throws Exception
     */
    public static function model($identifier){
        // If $identifier argument is an entity id
        if (preg_match('/^[0-9]+$/', $identifier)) {

            // Try to find that id within ids of existing entities
            foreach (self::$_entityA as $className => $info) {
                if ($info['id'] == $identifier) {
                    $identifier = $className;
                    break;
                }
            }

            // If was not found, throw exception
            if ($identifier != $className) throw new Exception('Entity with id ' . $identifier . ' does not exist');
        }

        // Uppercase the first char, as keys in self::$_modelA and self::$_entityA arrays are capitalized
        $identifier = ucfirst($identifier);

        // If model is already loaded, we return it
        if (array_key_exists($identifier, self::$_modelA) == true) {
            return self::$_modelA[$identifier];

        // Else if model not loaded, but it's entity exists within self::$_entityA array
        } else if (array_key_exists($identifier, self::$_entityA)) {

            // If model class exists
            if (class_exists($identifier)) {

                // Create a model, push it to self::$_modelA array as a next item, and return that item
                return self::$_modelA[$identifier] = new $identifier(array(
                    'fields' => self::$_entityA[$identifier]['fields']
                ));

            // If model class is no exist
            } else {

                // Get model's parent class name from self::$_entityA array. If not parent class name there, set
                // it as 'Indi_Db_Table' by default
                if (!($extends = self::$_entityA[$identifier]['extends'])) $extends = 'Indi_Db_Table';

                // Declare model class, using php eval()
                eval('class ' . $identifier . ' extends ' . $extends . '{}');

                // Create a model, push it to self::$_modelA array as a next item, and return that item
                return self::$_modelA[$identifier] = new $identifier(array(
                    'fields' => self::$_entityA[$identifier]['fields']
                ));
            }
        }

        // Throw exception
        throw new Exception('Model "' . $identifier . '" does not exists');
    }

    /**
     * Execute a sql-query. If $silence argument is set to true, no mysql error will be displayed
     *
     * @param $sql
     * @param bool $silence
     * @return Indi_Cache_Fetcher|int|PDOStatement
     */
    public function query($sql, $silence = false) {

        // Trim the query
        $sql = trim($sql);

        // Here we separate all queries by their type, and and this time we deal with queries, that provide affected
        // rows count as return value of execution
        if (preg_match('/^UPDATE|DELETE|INSERT/', $sql)) {

            // Execute query and get affected rows count
            $affected = self::$_pdo->exec($sql);

            // If no rows were affected and error reporting ($silence argument) is turned on
            if ($affected === false && $silence == false) {

                // Display error message, backtrace info and make the global stop
                echo array_pop(self::$_pdo->errorInfo()) . '<br>';
                echo "SQL query: " . $sql . '<br>';
                d(debug_print_backtrace());
                die();
            }

            // Return affected rows count as a result of query execution
            return $affected;

        // If cache usage is turned on, and current query match cache usage requirements
        } else if (Indi_Db::$useCache == true && $params = self::shouldUseCache($sql)) {

            // Pass query to Indi_Cache::fetcher() method
            return Indi_Cache::fetcher($params);

        // Else if query was not UPDATE|DELETE|INSERT, and query did not match Indi_Cache::fetcher() requirements
        } else {

            // Exectute query by PDO->query() method
            $stmt = self::$_pdo->query($sql);

            // If query execition was not successful and mysql error reporting is on
            if (!$stmt && $silence == false) {

                // Display error message, backtrace info and make the global stop
                echo array_pop(self::$_pdo->errorInfo()) . '<br>';
                echo "SQL query: " . $sql . '<br>';
                d(debug_print_backtrace());
                die();

            // Else if all was ok, setup fetch mode as PDO::FETCH_ASSOC
            } else if ($stmt) {
                $stmt->setFetchMode(PDO::FETCH_ASSOC);
            }

            // Return PDO statement
            return $stmt;
        }
    }

    /**
     * Return PDO object. Currently, the only purpose of this method is to provide an ability to call lastInsertId()
     * method on it, in insert() method in Indi_Db_Table_Abstract class
     *
     * @return null|PDO
     */
    public function getPDO() {
        return self::$_pdo;
    }

    /**
     * Check if current sql query execution can be handled with Indi_Cache::fetcher() method,
     * and if so, return an array, containing table name, columns list and WHERE clause
     *
     * @param $sql
     * @return array|bool|null
     */
    public function shouldUseCache($sql) {

        // Replace newlines with spaces within a $sql argument, to make the query string single-line, for proper check
        // of match to Indi_Cache requirements
        $sql = preg_replace("/\n/", " ", $sql);

        // Check if query is enough simple for Indi_Cache::fetcher() to deal with it
        preg_match('/^SELECT (.*) FROM `(.*)` +WHERE(.*)$/', $sql, $matches);

        // If table name, got from query FROM clause is within keys of self::$_cacheA array
        if (self::$_cacheA[$matches[2]]) {

            // And if file with cached data for that table exists
            if (file_exists(Indi_Cache::fname(ucfirst($matches[2])))) {

                // Return info about catched table name, WHERE clause and columns, that should be retrieved
                return $matches;
            }
        }

        // Return false
        return false;
    }
}
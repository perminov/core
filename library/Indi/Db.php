<?php
class Indi_Db {
    /**
     * Store singleton instance
     *
     * @var Indi_Db
     */
    protected static $_instance = null;

    /**
     * Store PDO object
     *
     * @var PDO
     */
    protected static $_pdo = null;

    /**
     * Store queries count
     *
     * @var Indi_Db
     */
    public static $queryCount = 0;

    public static $useCache = false;

    /**
     * @static
     * @param array $config
     * @return null|Indi_Db
     */
    public static function factory($config = array())
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
            self::$_pdo = new PDO($config->adapter . ':dbname=' . $config->dbname . ';host=' . $config->host, $config->username, $config->password);
        }
        return self::$_instance;
    }

    public function shouldUseCache($sql) {
        $sql = preg_replace("/\n/", " ", $sql);
        preg_match('/^SELECT (.*) FROM `(.*)` +WHERE(.*)$/', $sql, $matches);
        if (in_array($matches[2], array(
            "entity","field", "fsection","orderBy",
            "dependentCount","joinFk","dependentRowset",
            "faction","fsection2faction","independentRowset",
            "joinFkForIndependentRowset","joinFkForDependentRowset",
            "dependentCountForDependentRowset","fconfig","seoTitle",
            "seoKeyword","seoDescription"
                                  ))) {
            if (file_exists(Indi_Cache::fname(ucfirst($matches[2])))) {
                return $matches;
            }
        }
        return false;
    }

    public function query($sql) {
        $sql = trim($sql);
        if (preg_match('/^UPDATE|DELETE|INSERT/', $sql)) {
            $affected = self::$_pdo->exec($sql);
            if ($affected === false) {
                echo array_pop(self::$_pdo->errorInfo()) . '<br>';
                echo "SQL query: " . $sql . '<br>';
                d(debug_print_backtrace());
                die();
            }
            return $affected;
        } else if (Indi_Db::$useCache == true && $params = self::shouldUseCache($sql)) {
            return Indi_Cache::fetcher($params);
        } else {
            $stmt = self::$_pdo->query($sql);
            if (!$stmt) {
                echo array_pop(self::$_pdo->errorInfo()) . '<br>';
                echo "SQL query: " . $sql . '<br>';
                d(debug_print_backtrace());
                die();
            }
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            return $stmt;
        }
    }

    public function getPDO() {
        return self::$_pdo;
    }
}
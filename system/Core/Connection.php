<?php

namespace Core;

class Connection {

    protected static $instance;
    protected $connection;
    

    /**
     * Connects with the database using the constants present in config.php
     */
    public function __construct(string $type) {
        try {
            $this->connection = new \PDO(strtolower($type) . ":host=" . WOLFF_SERVER . "; dbname=" . WOLFF_DB . "", WOLFF_DBUSERNAME, WOLFF_DBPASSWORD);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
        }
    }


    /**
     * Get a static instance
     * @param string $type the dbms
     * @return Connection the instance
     */
    public static function getInstance(string $type = 'mysql') {
        if (!self::$instance) {
            self::$instance = new self($type);
        }

        return self::$instance;
    }


    /**
     * Proxy to native PDO methods
     * @param mixed $method the method name
     * @param mixed $args the method arguments
     * @return mixed the function result
     */
    public function __call($method, $args) {
        return call_user_func_array(array(self::$connection, $method), $args);
    }
    

    /**
     * Run a query
     * @param string $sql the query
     * @param mixed $args the arguments
     * @return array the query result
     */
    public function run(string $sql, $args = []) {
        //Query without args
        if (!$args) {
            $result = self::$connection->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) <= 1) {
                return $result[0];
            }
    
            return $result;
        }

        //Query with args
        $stmt = self::$connection->prepare($sql);
        $stmt->execute($args);

        $result = $stmt->fetch();
        if (count($result) <= 1) {
            return $result[0];
        }

        return $result;
    }
    

    /**
     * Export a query to a csv
     * @param string $filename the filename
     * @param string $sql the query
     * @param mixed $args the arguments
     */
    public function toCsv(string $filename, string $sql, $args = []) {
        arrayToCsv($filename, self::run($sql, $args));
    }

}
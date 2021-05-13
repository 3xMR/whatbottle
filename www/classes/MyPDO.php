<?php

/**
 * Simple wrapper function for PDO database connection
 * to allow it to be included in a class and return itself as an instance 
 *
 * Credit to phpdelusions.net
 * 
 * Methods:
 * run - takes a sql statement and array of values to run prepared and execute statements
 * 
 */



//modify definitions based on dynamic environment detection
$root = filter_input(INPUT_SERVER,'DOCUMENT_ROOT');
If($root=="/Volumes/sites/whatbottle/01 whatbottle.test/www" || 
        $root=="/Users/magnus/Documents/Sites/whatbottle.test/www/" ){
    //development environment
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'whatbottle_dev');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    define('DB_CHAR', 'utf8');
} else {
    //public details are held in config file in config folder
    include_once('/home2/magnus/config/whatbottleConfig.php');
    define('DB_HOST', $DB_HOST);
    define('DB_NAME', $DB_NAME);
    define('DB_USER', $DB_USER);
    define('DB_PASS', $DB_PASS);
    define('DB_CHAR', $DB_CHAR);
}


class MyPDO {
    protected static $instance;
    protected $pdo;

    protected function __construct() {

        $opt  = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES   => FALSE,
        );
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
        $this->pdo = new PDO($dsn,DB_USER,DB_PASS,$opt);

    }

    // a classical static method to make it universally available
    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // a proxy to native PDO methods
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->pdo, $method), $args);
    }

    // a helper function to run prepared statements smoothly
    public function run($sql, $args = [])
    {
        if (!$args)
        {
             return $this->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
    
}

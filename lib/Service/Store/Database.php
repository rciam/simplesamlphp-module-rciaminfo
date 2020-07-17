<?php
/**
 * This class retrieves service information stored in a database. 
 * It should work against PostgreSQL, MySQL and SQLite.
 *
 * It has the following options:
 * - dsn: The DSN which should be used to connect to the database server. See 
 *        PHP Manual for supported drivers and DSN formats.
 * - username: The username used for database connection.
 * - password: The password used for database connection.
 * - table: The name of the table used. Optional, defaults to 'client_details'.
 *
 * @author  Nicolas Liampotis <nliam@grnet.gr>
 */
class sspmod_rciaminfo_Service_Store_Database
{
    /**
     * DSN for the database.
     */
    private $dsn;

    /**
     * The DATETIME SQL function to use
     */
    private $dateTime;

    /**
     * Username for the database.
     */
    private $username = null;

    /**
     * Password for the database;
     */
    private $password = null;

    /**
     * Table with service information.
     */
    private $table = "client_details";

    /**
     * The timeout of the database connection.
     *
     * @var int|null
     */
    private $timeout = null;

    /**
     * Database handle.
     *
     * This variable can't be serialized.
     */
    private $db;

    /**
     * Parse configuration.
     *
     * This constructor parses the configuration.
     *
     * @param array $config Configuration for database-based service store.
     */
    public function __construct($config)
    {
        if (!array_key_exists('dsn', $config)) {
            throw new Exception('rciaminfo:Service:Store:Database - Missing required option \'dsn\'.');
        }
        if (!is_string($config['dsn'])) {
            throw new Exception('rciaminfo:Service:Store:Database - \'dsn\' is supposed to be a string.');
        }

        $this->dsn = $config['dsn'];
        $this->dateTime = (0 === strpos($this->dsn, 'sqlite:')) ? 'DATETIME("NOW")' : 'NOW()';

        if (array_key_exists('username', $config)) {
            if(!is_string($config['username'])) {
                throw new Exception('rciaminfo:Service:Store:Database - \'username\' is supposed to be a string.');
            }
            $this->username = $config['username'];
        }

        if (array_key_exists('password', $config)) {
            if(!is_string($config['password'])) {
                throw new Exception('rciaminfo:Service:Store:Database - \'password\' is supposed to be a string.');
            }
            $this->password = $config['password'];
        }

        if (array_key_exists('table', $config)) {
            if (!is_string($config['table'])) {
                throw new Exception(
                    'rciaminfo:Service:Store:Database - \'table\' is supposed to be a string.'
                );
            }
            $this->table = $config['table'];
        }

        if (isset($config['timeout'])) {
            if (!is_int($config['timeout'])) {
                throw new Exception(
                    'rciaminfo:Service:Store:Database - \'timeout\' is supposed to be an integer.'
                );
            }
            $this->timeout = $config['timeout'];
        }
    }

    /**
     * Called before serialization.
     *
     * @return array The variables which should be serialized.
     */
    public function __sleep()
    {
        return array(
            'dsn',
            'dateTime',
            'username',
            'password',
            'table',
            'timeout',
        );
    }

    /**
     * Retrieve service information.
     *
     * This function should return a list of services.
     *
     * @return array Array of all services.
     */
    public function getServices()
    {
        $ret = array();

        $st = $this->executeStatement(
            'SELECT client_id, client_name, client_description, policy_uri ' .
            'FROM ' . $this->table . ' ' .
            'WHERE NOT dynamically_registered ' .
            'ORDER BY client_name ASC',
            array()
        );

        if ($st === false) {
            return array();
        }

        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            $ret[] = $row;
        }

        return $ret;
    }

    /**
     * Prepare and execute statement.
     *
     * This function prepares and executes a statement. On error, false will be
     * returned.
     *
     * @param string $statement  The statement which should be executed.
     * @param array  $parameters Parameters for the statement.
     *
     * @return PDOStatement|false  The statement, or false if execution failed.
     */
    private function executeStatement($statement, $parameters)
    {
        assert('is_string($statement)');
        assert('is_array($parameters)');

        $db = $this->getDB();
        if ($db === false) {
            return false;
        }

        $st = $db->prepare($statement);
        if ($st === false) {
            if ($st === false) {
                SimpleSAML_Logger::error(
                    'rciaminfo:Service:Store:Database - Error preparing statement \'' .
                    $statement . '\': ' . self::formatError($db->errorInfo())
                );
                return false;
            }
        }

        if ($st->execute($parameters) !== true) {
            SimpleSAML_Logger::error(
                'rciaminfo:Service:Store:Database - Error executing statement \'' .
                $statement . '\': ' . self::formatError($st->errorInfo())
            );
            return false;
        }

        return $st;
    }

    /**
     * Get database handle.
     *
     * @return PDO|false Database handle, or false if we fail to connect.
     */
    private function getDB()
    {
        if ($this->db !== null) {
            return $this->db;
        }

        $driver_options = array();
        if (isset($this->timeout)) {
            $driver_options[PDO::ATTR_TIMEOUT] = $this->timeout;
        }

        $this->db = new PDO($this->dsn, $this->username, $this->password, $driver_options);

        return $this->db;
    }

    /**
     * Format PDO error.
     *
     * This function formats a PDO error, as returned from errorInfo.
     *
     * @param array $error The error information.
     * 
     * @return string Error text.
     */
    private static function formatError($error)
    {
        assert('is_array($error)');
        assert('count($error) >= 3');

        return $error[0] . ' - ' . $error[2] . ' (' . $error[1] . ')';
    }

}

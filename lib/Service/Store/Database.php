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
     * Table with service information.
     */
    private $table = "client_details";

    /*
     * SimpleSAML\Database instance.
     */
    private $db;


    /**
     * Initialises a database-based service store based on the provided
     * configuration.
     *
     * @param array Configuration for database-based service store.
     */
    public function __construct($config)
    {
        if (array_key_exists('table', $config)) {
            if (!is_string($config['table'])) {
                throw new Exception(
                    'rciaminfo:Service:Store:Database - \'store.table\' must be a string.'
                );
            }
            $this->table = $config['table'];
        }

        // Database configuration is optional. If not set the global config
        // is used.
        $pdoConfig = null;
        if (array_key_exists('pdo', $config)) {
            if (!is_array($config['pdo'])) {
                throw new Exception(
                    'rciaminfo:Service:Store:Database - \'store.pdo\' must be an array.'
                );
            }
	    $pdoConfig = SimpleSAML_Configuration::loadFromArray($config['pdo']);
        }
        $this->db = SimpleSAML\Database::getInstance($pdoConfig);
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
        return $this->db->read(
            'SELECT client_id, client_name, client_description, policy_uri ' .
            'FROM ' . $this->table . ' ' .
            'WHERE NOT dynamically_registered ' .
            'ORDER BY client_name ASC',
            array()
        )->fetchAll(PDO::FETCH_ASSOC);
    }

}

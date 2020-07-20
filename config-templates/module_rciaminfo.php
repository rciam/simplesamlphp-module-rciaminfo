<?php
/**
 * Example configuration for SimpleSAMLphp RCIAM Info module.
 *
 * @author Nicolas Liampotis <nliam@grnet.gr>
 */

$config = [

    /*
     * Configuration options for database-based service store.
     */
    'store' => [

        /*
         * Name of the table containing service information
         * @default 'client_details'
         */
        'table' => 'client_details',

        /*
         * Configuration for SimpleSAML\Database.
         * If not specified, the global SimpleSAML\Database config is used.
         * @see SimpleSAML\Database
         */
        'pdo' => [
            /*
             * Database connection string.
             * Ensure that you have the required PDO database driver
             * installed for your connection string.
             * Examples:
             * mysql:host=localhost;port=3306;dbname=testdb
             * mysql:unix_socket=/tmp/mysql.sock;dbname=testdb
             * pgsql:host=localhost;port=5432;dbname=testdb
             */
            'database.dsn' => 'mysql:host=localhost;dbname=saml',

            /*
             * Database credentials
             */
            'database.username' => 'simplesamlphp',
            'database.password' => 'secret',

            /*
             * (Optional) Table prefix
             */
            'database.prefix' => '',

            /*
             * Whether to use persistent database connections
             */
            'database.persistent' => false,

            /*
             * (Optional) Driver options
             */
            'database.driver_options' => [],

            /*
             * Secondary database server configuration is optional. If you
             * are only running a single database server, leave this blank.
             * If you have a primary/secondary configuration, you can
             * define as many secondary servers as you want here. Secondary
             * servers will be picked at random when executing read queries.
             *
             * Configuration options in the secondaries array are exactly
             * the same as the options for the primary above with the
             * exception of the table prefix.
             */
            'database.slaves' => [
                /*
                [
                    'dsn' => 'mysql:host=mysecondary;dbname=saml',
                    'username' => 'simplesamlphp',
                    'password' => 'secret',
                    'persistent' => false,
                ],
                */
            ],
        ],
    ],

    /*
     * List of Service IDs (SAML SP entityIDs or OIDC Client IDs) to exclude
     * @default []
     */
    'serviceIdExcludeList' => [
        'https://sp1.example.org',
    ],

    /*
     * List of SAML SP metadata sources to exclude
     * @default []
     */
    'metadataSrcExcludeList' => [
        'https://example.org-spMeta.xml',
    ],

    /*
     * List of Configurations relative to Info presentation
     * @default []
     */
    'infoConfig' => [

      /*
       * List of onfigurations for the info table
       * @default []
       */

      'table' => [

        /*
         * Number of rows visible per Table page
         * @default 10
         */

        'length' => 10
      ],
    ],
];

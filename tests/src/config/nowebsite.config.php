<?php

declare(strict_types=1);

/**
 * Contains configuration parameters.
 *
 * You can change the values here or you can create a local.config.php file
 * which contains only the data you want to change.
 * The app will replace the values of config.php with the values of local.config.php.
 *
 * @link    https://phptherightway.com/#configuration_files for recommandations.
 * @link    https://github.com/pbraiders/pomponne for the canonical source repository.
 * @license https://github.com/pbraiders/pomponne/blob/master/LICENSE GNU General Public License v3.0 License.
 */

return [

    'application' => [],

    'service' => [

        // Configure database settings.
        'database' => [
            // Database Name used by PBRaiders.
            'name' => 'the_database_name',
            // Username used to access Database.
            'username' => 'the_user_name',
            // Password used by Username to access Database.
            'password' => 'the_password',
            // The hostname of your Database Server. A port number, Unix socket file path or pipe may be needed as well.
            'host' => 'localhost',
            // Driver
            'driver' => 'mysql',
            // Charset
            'charset' => 'utf8mb4',
            // Collation
            'collation' => 'utf8mb4_unicode_ci',
        ],

        'error' => [
            // If set to 1, the application will use 'whoops error handling library' instead of the default PHP one.
            // Usefull during development.
            'use_whoops' => false,
        ],

        'logger' => [
            // Name of the file where application errors should be logged.
            'error_log' => sprintf('%s/var/log/%s_pbraiders_error.log', \PBR_PATH, date("Ymd")),
        ],

    ],

    // These are various options for php.
    // Do not change unless you know what you are doing!
    'php' => [

        // Default timezone used by all date/time functions
        'date.timezone' => 'Europe/Paris',

        // Maximum amount of memory in bytes that a script is allowed to allocate.
        'memory_limit' => '40M',

        // This sets the maximum time in seconds a script is allowed to run before it is terminated by the parser.
        'max_execution_time' => '60',

        // Sets which PHP errors are reported.
        'error_reporting' => E_ALL & ~E_DEPRECATED & ~E_STRICT,

        // Determines whether errors should be printed to the screen as part of the output or if they should be hidden from the user.
        'display_errors' => '0',

        // Even when display_errors is on, errors that occur during PHP's startup sequence are not displayed.
        // It's strongly recommended to keep display_startup_errors off, except for debugging.
        'display_startup_errors' => '0',

        // Do not log repeated messages.
        'ignore_repeated_errors' => '1',

        // This parameter will show a report of memory leaks detected by the Zend memory manager.
        'report_memleaks' => '0',

        // If enabled, the last error message will always be present in the variable $php_errormsg.
        'track_errors' => '0',

        // If enabled, error messages will include HTML tags.
        'html_errors' => '1',

        // Tells whether script error messages should be logged to the server's error log or error_log.
        'log_errors' => '1',

        // Name of the file where script errors should be logged.
        'error_log' => sprintf('%s/var/log/%s_php_error.log', \PBR_PATH, date("Ymd")),

    ],

];

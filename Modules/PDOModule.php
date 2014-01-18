<?php

namespace Bitblitter\Modules;
use Bitblitter\Stump;


/**
 * PDO database module for Stump
 * Configured for MySQL.
 * Requires config:
 * PDO:
 *  - host
 *  - database
 *  - username
 *  - password
 *
 * Returns a PDO instance that is reused across calls.
 *
 * Register:
 * $app->registerModule('pdo', array('Bitblitter\Modules\PDOModule', 'run'));
 *
 * Use:
 * $pdo = $app->module('pdo');
 *
 * @package Bitblitter\Modules
 *
 */
class PDOModule
{
    /**
     * @var $connection \PDO
     */
    protected static $connection;

    /**
     * Run the module.
     *
     * @param Stump $app
     * @return \PDO
     */
    public static function run(Stump $app)
    {
        $moduleConfig = $app->getConfig('PDO', null);
        if(is_array($moduleConfig))
        {
            self::configure($moduleConfig);
        }
        return self::$connection;
    }

    protected static function configure(array $config)
    {
        $dsn = sprintf('mysql:host=%s;dbname=%s', $config['host'], $config['database']);
        $username = $config['username'];
        $password = $config['password'];
        $options = (isset($config['options']) ? $config['options'] : array());
        self::$connection = new \PDO($dsn, $username, $password, $options);
    }
}
<?php
use Bitblitter\Stump;

require('../Stump.php');
require('../Modules/PDOModule.php');

$config = array(
    'views_dir' => 'views/',
    'error_views' => array(
        'default' => 'errors/error',
        404 => 'errors/not_found',
    ),
    'PDO' => array(
        'host' => 'localhost',
        'database' => 'test',
        'username' => 'root',
        'password' => ''
    )
);

$app = new Stump($config);
$app->registerModule('pdo', array('Bitblitter\Modules\PDOModule','run'));
$app->route('/([^/]+)?', 'indexAction');
$app->run();

function indexAction(Stump $app, $name = 'anonymous')
{
    return 'Hello, '.$name.'!';
}

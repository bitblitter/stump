stump
=====

A minimal PHP framework for very small web applications


Support for:
- Routes (regexp, parenthesis translate as extra parameters for callables), limited to GET
- Configuration (array in constructor, getter/setter)
- templates (php, pass variables to render() function)
- retrieve values from GET and POST arrays with default values if not set
- setting up application shared functions, called 'modules', like a database connection or mailer.
 

Basic Usage
-----------
```php
require_once('Stump.php');


function helloAction(Stump $app, $name = 'unknown')
{
  return "Hello, $name!";
}

$app = new Stump();
$app->route('/hello/(\w+)?', 'helloAction');
$app->run();

```

Modules available:
-----------------

Some usual functions are available to use:
- PDO (PDOModule): provides a configured PDO instance that can be reused through the application.

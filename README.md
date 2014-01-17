stump
=====

A minimal PHP framework for very small web applications

Using idiorm for database access right now.


Support for:
- Routes (regexp, parenthesis translate as extra parameters for callables), limited to GET
- Configuration (array in constructor, getter/setter)
- templates (php, pass variables to render() function)
- retrieve values from GET and POST arrays with default values if not set
 

Usage
-----
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

Aura.Invoker
============

The Aura.Invoker library provides tools to create objects from a factory, then
invoke methods on them based on a parameter value. This is useful for invoking
controller and command object methods based on path-info parameters or command
line arguments.

### Installation and Autoloading

This library is installable via Composer and is registered on Packagist at
<https://packagist.org/packages/aura/invoker>. Installing via Composer will
set up autoloading automatically.

Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Dependencies

As with all Aura libraries, this library has no external dependencies.

### Tests

This library has 100% code coverage. To run the library tests, first install
[PHPUnit][], then go to the library _tests_ directory and issue `phpunit` at
the command line.

[PHPUnit]: http://phpunit.de/manual/

### API Documentation

This library has embedded DocBlock API documentation. To generate the
documentation in HTML, first install [PHPDocumentor][] or [ApiGen][], then go
to the library directory and issue one of the following at the command line:

    # for PHPDocumentor
    phpdoc -d ./src/ -t /path/to/output/
    
    # for ApiGen
    apigen --source=./src/ --destination=/path/to/output/

You can then browse the HTML-formatted API documentation at _/path/to/output_.

[PHPDocumentor]: http://phpdoc.org/docs/latest/for-users/installation.html
[ApiGen]: http://apigen.org/#installation

### PSR Compliance

This library is compliant with [PSR-1][] and [PSR-2][]. If you notice
compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md


Basic Usage
-----------

First, instantiate an `InvokerManager` as the central point for object
creation and method invocation.

```php
<?php
use Aura\Invoker\InvokerManager;
use Aura\Invoker\ObjectFactory;

$invoker = new InvokerManager(new ObjectFactory);
?>
```

Next, get the object factory from the manager, and load it with named
callables to create objects.

```php
<?php
$factory = $invoker->getObjectFactory();
$factory->set('blog', function () {
    return new \Vendor\Package\BlogController;
});
?>
```

Finally, given a set of params (e.g. from a web router) you can now invoke a
method on the object automatically:

```php
<?php
$invoker->setObjectParam('controller');
$invoker->setMethodParam('action');
$params = ['controller' => 'blog', 'action' => 'read', 'id' => '88'];
$result = $invoker->exec($params);
// equivalent to:
// $blog_controller = new BlogController;
// $result = $blog_controller->read('88');
?>
```

Trait Usage
-----------

Sometimes your classes will have an intercessory method that picks an action
to run, either on itself or on another object.  The `InvokerTrait` provides
two methods to help with that.

```php
<?php
namespace Vendor\Package;

use Aura\Invoker\InvokerTrait;

class VendorPackageExecutor
{
    use InvokerTrait;
    
    // $params = [
    //      'action' => 'read',
    //      'id' => 88,
    // ];
    public function exec(array $params)
    {
        if (! isset($params['action'])) {
            $params['action'] = 'index';
        }
        $method = 'action' . $params['action'];
        return $this->invokeMethod($params, $this, $method);
    }
    
    public function actionRead($id = null)
    {
        // ...
    }
}
?>
```

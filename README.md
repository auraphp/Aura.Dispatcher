# Aura.Dispatcher

## Overview

The Aura.Dispatcher library provides tools to map names to dispatchable
objects, then dispatch to those objects using named parameters. This is useful
for invoking controller and command object methods based on path-info
parameters or command line arguments, as well as dispatching to closure-based
controllers and building the objects to be dispatched from factories.

### Installation and Autoloading

This library is installable via Composer and is registered on Packagist at
<https://packagist.org/packages/aura/dispatcher>. Installing via Composer will
set up autoloading automatically.

Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Dependencies

As with all Aura libraries, this library has no external dependencies.

### Tests

[![Build Status](https://travis-ci.org/auraphp/Aura.Dispatcher.png?branch=develop-2)](https://travis-ci.org/auraphp/Aura.Dispatcher)

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


## Basic Usage

REWRITE THIS README ENTIRELY.

Cover the following:

- Dispatch using a param directly (i.e., the param is a closure, an object
  with the right method, or an invokable object)

- Dispatch using a closure named by a param

- Building factories, whether by closures or by invokable object

- Describe the recursive dispatch process
  
### Invoking Closures

TBD.
 
### Adding Dispatchable Objects

First, instantiate an `Dispatcher` as the central point for object
creation and method invocation.

```php
<?php
use Aura\Dispatcher\Dispatcher;

$dispatcher = new Dispatcher;
?>
```

Next, load it with factories that create objects.

```php
<?php
$dispatcher->setObject('blog', function () {
    return new \Vendor\Package\BlogController;
});
?>
```

Finally, given a set of params (e.g. from a web router), you can now use the
dispatcher to create an object via its factory and invoke a method on the created
object:

```php
<?php
$dispatcher->setObjectParam('controller');
$dispatcher->setMethodParam('action');
$params = [
    'controller' => 'blog',
    'action' => 'read',
    'id' => '88'
];
$result = $dispatcher($params);
// equivalent to:
// $blog_controller = new \Vendor\Package\BlogController;
// $result = $blog_controller->read('88');
?>
```

## Trait Usage

Sometimes your classes will have an intercessory method that picks an action
to run, either on itself or on another object. This library provides two
traits to help with that: the _InvokeMethodTrait_ to invoke a method on an
object using named parameters, and _InvokeClosureTrait_ to invoke a closure
using named parameters.  (The _InvokeMethodTrait_ honors protected and private
scopes.)

### InvokeMethodTrait

```php
<?php
namespace Vendor\Package;

use Aura\Dispatcher\InvokeMethodTrait;

class VendorPackageExecutor
{
    use InvokeMethodTrait;
    
    // $params = [
    //      'action' => 'read',
    //      'id' => 88,
    // ];
    public function __invoke(array $params)
    {
        if (! isset($params['action'])) {
            $params['action'] = 'index';
        }
        $method = 'action' . $params['action'];
        return $this->invokeMethod($params, $this, $method);
    }
    
    protected function actionRead($id = null)
    {
        // ...
    }
}
?>
```

### InvokeClosureTrait

TBD.

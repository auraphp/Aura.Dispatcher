# Aura.Dispatcher

## Overview

The Aura.Dispatcher library provides tools to map arbitrary names to
dispatchable objects, then dispatch to those objects using named parameters.
This is useful for invoking controller and command objects based on path-info
parameters or command line arguments, as well as dispatching to closure-based
controllers and building the objects to be dispatched from factories.

## Preliminaries

### Installation and Autoloading

This library is installable via Composer and is registered on Packagist at
<https://packagist.org/packages/aura/dispatcher>. Installing via Composer will
set up autoloading automatically.

Alternatively, download or clone this repository, then require or include its
_autoload.php_ file.

### Dependencies and PHP Version

As with all Aura libraries, this library has no external dependencies.

### Tests

[![Build Status](https://travis-ci.org/auraphp/Aura.Dispatcher.png?branch=develop-2)](https://travis-ci.org/auraphp/Aura.Autoload)

This library has 100% code coverage. To run the library tests, first install
[PHPUnit][], then go to the library _tests_ directory and issue `phpunit` at
the command line.

[PHPUnit]: http://phpunit.de/manual/

### PSR Compliance

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Getting Started

### Overview

First, some sort of routing mechanism (e.g., [Aura.Router][] or a
micro-framework router) creates an array of parameters.

Those parameters then get passed to the dispatcher; it examines them and picks
an object to invoke with those parameters.

The dispatcher will examine the returned result from that first invocation;
if the result is itself callable, the dispatcher will recusrively invoke the
result until a non-callable is returned.

When a non-callable result is returned, the dispatcher stops dispatching and
returns that non-callable result.


### Embedded Closure In Params

We begin with an array of parameters that has a closure embdded in them:

```<?php
$params = [
    'controller' => function ($noun) {
        return "Hello $noun!";
    },
    'noun' => 'World',
];
?>
```

Now we create a dispatcher that will examine the 'controller' param to
determine what object to invoke. (Note that the param name can be anything you
like; 'controller' is only an example.)

```php
<?php
use Aura\Dispatcher\Dispatcher;

$dispatcher = new Dispatcher;
$dispatcher->setObjectParam('controller');
?>
```

Finally, we will invoke the dispatcher with params:

```php
<?php
$result = $dispatcher($params);
echo $result; // Hello World!
?>
```

What happened here? The dispatcher looked at the `controller` param and found
a closure.  It then invoked that closure, matching the param names (in this
case `'noun'`) with the clousre arguments (`$noun`) and returned the result.


### Named Closure In Params

Now let's do the same thing, except this time will we put the closure in the
dispatcher instead of embedding it in the params.  Set a named object into
the dispatcher using `setObject()`:

```
<?php
$dispatcher->setObject('hello_noun', function ($noun) {
    return "Hello $noun!";
});
?>
```

We can then dispatch to that named object by using the name as the value for
the `controller` param:

```
<?php
$params = [
    'controller' => 'hello_noun',
    'noun' => 'World';
];

$result = $dispatcher($params);
echo $result; // Hello World!
?>
```

### Named Invokable Object In Params

### Named Object And Method In Params

### Factoried Object And Method In Params

### Basic Use Cases

REWRITE THIS README ENTIRELY.

Cover the following:

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

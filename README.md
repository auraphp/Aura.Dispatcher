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

First, an external routing mechanism such as [Aura.Router][] or a
micro-framework router creates an array of parameters or an object that
implements [ArrayAccess][].

[Aura.Router]: https://github.com/auraphp/Aura.Router
[ArrayAccess]: http://php.net/ArrayAccess

The parameters are then passed to the dispatcher. It examines them and picks
an object to invoke with those parameters, optionally with a method determined
by the parameters.

The dispatcher then examines the returned result from that first invocation;
if the result is itself a dispatchable object, the dispatcher will recursively
invoke the result until something other than a dispatchable object is
returned.

When a non-dispatchable result is returned, the dispatcher stops dispatching
and returns that non-callable result.

### Closures and Invokable Objects

First, we tell the dispatcher to examine the `controller` parameter to find
the name of the object to dispatch to:

```php
<?php
$dispatcher->setObjectParam('controller');
?>
```

Next, we set a closure object into the dispatcher using `setObject()`:

```php
<?php
$dispatcher->setObject('blog', function ($id) {
    return "Read blog entry $id";
});
?>
```

We can now dispatch to that closure by using the name as the value for
the `controller` parameter:

```php
<?php
$params = [
    'controller' => 'blog',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

The same goes for invokable objects. First, define a class with an
`__invoke()` method:

```php
<?php
class InvokableBlog
{
    public function __invoke($noun)
    {
        return "Read blog entry $id";
    }
}
?>
```

Next, set an instance of the object into the dispatcher:

```php
<?php
$dispatcher->set('blog', new InvokableBlog);
?>
```

Finally, dispatch to the named invokable object (the parameters and logic are
the same as above):

```php
<?php
$params = [
    'controller' => 'blog',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

### Object Method

We can tell the dispatcher to examine the params for a method to call on the
object. This method will take precedence over the `__invoke()` method on an
object, if such a method exists.

First, tell the dispatcher to examine the value of the `action` param to find
the name of the method it should invoke.

```php
<?php
$dispatcher->setMethodParam('action');
?>
```

Next, define the object we will dispatch to; note that the method is `read()`
instead of `__invoke()`.

```php
<?php
class Blog
{
    public function read($id)
    {
        return "Read blog entry $id";
    }
}
?>
```

Then, we set the object into the dispatcher ...

```php
<?php
$dispatcher->set('blog', new Blog);
?>
```

... and finally, we invoke the dispatcher; we have added an `action` parameter
with the name of the method to invoke:

```php
<?php
$params = [
    'controller' => 'blog',
    'action' => 'read',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

### Embedding Objects in Parameters

If you like, you can place dispatchable objects in the parameters directly.
(This is sometimes how micro-framework routers work.) For example, let's put
a closure into the `controller` parameter; when we invoke the dispatcher, it
will invoke the closure directly.

```php
<?php
$params = [
    'controller' => function ($noun) {
        return "Read blog entry $id";
    },
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

The same is true for invokable objects ...

```php
<?php
$params = [
    'controller' => new InvokableBlog,
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

... and for object-methods:


```php
<?php
$params = [
    'controller' => new Blog,
    'action' => 'run',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```


### Lazy Loading

The dispatcher is recursive. After dispatching to the first object, if that
object returns a dispatchable object, the dispatcher will re-dispatch to that
object. It will continue doing this until the returned result is not
a dispatchable object.

Let's turn the above example of an invokable object in the dispatcher into a
lazy-loaded instantiation. All we have to do is wrap the instantiation in
another dispatchable object (in this example, a closure). The benefit of this
is that we can fill the dispatcher with as many objects as we like, and they
won't get instantiated until the dispatcher calls on them.

```php
<?php
$dispatcher->set('blog', function () {
    return new Blog;
});
?>
```

Then we invoke the dispatcher with the same params as before.

```php
<?php
$params = [
    'controller' => 'blog',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

What happens is this:

- The dispatcher finds the 'blog' dispatchable object, sees that it
  is a closure, and invokes it with the params.

- The dispatcher examines the result, sees the result is a dispatchable object,
  and invokes it with the params.

- The dispatcher examines *that* result, sees that it is *not* a callable
  object, and returns the result.


## Refactoring From Less Complex To More Complex Architectures

The dispatcher is sympathetic to the idea that some developers will start out
with micro-framework architectures, and that the architecture will evolve
over time toward a full-stack architecture.

At first, the developer uses embedded closures:

```php
<?php
$dispatcher->setObjectParam('controller');

$params = [
    'controller' => function ($id) {
        return "Read blog post $id";
    },
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

After adding several controllers, the developer is likely to want to put the
routing configurations separate from the controller actions. At this point the
developer may start putting the controller actions directly into the dispatcher:

```php
<?php
$dispatcher->setObject('blog', function ($id) {
    return "Read blog entry $id!";
});

$params = [
    'controller' => 'blog',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

As the number and complexity of controllers continues to grow, the developer
may wish to put the controllers into their own classes, lazy-loading along the
way:

```php
<?php
class Blog
{
    public function __invoke($id)
    {
        return "Read blog entry $id";
    }
}

$dispatcher->setObject('blog', function () {
    return new Blog;
});

$params = [
    'controller' => 'blog',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

Finally, the developer may collect several actions into a single controller,
keeping related functionality all the same class. At this point the developer
should call `setMethodParam()` to tell the dispatcher what method to invoke
on the dispatchable object.

```php
<?php
class Blog
{
    public function browse()
    {
        // ...
    }
    
    public function read($id)
    {
        return "Read blog entry $id";
    }
    
    public function edit($id)
    {
        // ...
    }
    
    public function add()
    {
        // ...
    }
    
    public function delete($id)
    {
        // ...
    }
}

$dispatcher->setMethodParam('action');

$dispatcher->setObject('blog', function () {
    return new Blog;
});

$params = [
    'controller' => 'blog',
    'action' => 'read',
    'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

## Construction-Based Configuration

You can set all dispatchable objects, along with the controller parameter name
and the method parameter name, at construction time. This makes it easier to
configure the dispatcher object in a single call.

```php
<?php
$controller_param = 'controller';
$method_param = 'action';
$objects = [
    'blog' => function () {
        return new BlogController;
    },
    'wiki' => function () {
        return new WikiController;
    },
    'forum' => function () {
        return new ForumController;
    },
];

$dispatcher = new Dispatcher($object, $controller_param, $method_param);
?>
```

## Intercessory Dispatch Methods

Sometimes your classes will have an intercessory method that picks an action
to run, either on itself or on another object. _Aura.Dispatcher_ provides an
_InvokeMethodTrait_ to invoke a method on an object using named parameters.
(The _InvokeMethodTrait_ honors protected and private scopes.)

```php
<?php
use Aura\Dispatcher\InvokeMethodTrait;

class Blog
{
    use InvokeMethodTrait;
    
    // uses a hypthetical Request object to examine the execution context
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function __invoke()
    {
        if (! isset($request->pathinfo['action'])) {
            $request->pathinfo['action'] = 'index';
        }
        $method = 'action' . ucfirst($request->pathinfo['action']);
        return $this->invokeMethod($request->pathinfo, $this, $method);
    }
    
    protected function actionRead($id = null)
    {
        return "Read blog entry $id";
    }
}
?>
```

You can then dispatch to the object as normal, and it will determine its
own logical flow.

```php
<?php
$dispatcher->setObject('blog', function () {
    return new Blog;
});

$params = [
     'controller' => 'blog',
     'action' => 'read',
     'id' => 88,
];

$result = $dispatcher($params);
echo $result; // Read blog entry 88
?>
```

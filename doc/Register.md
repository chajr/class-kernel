Register implementation
====================

Idea of usage
--------------
Register class is used for creating all objects in system, or handling singleton
objects (allow to create many singletons from one class). Register class can also
return information how many objects was created.  
If we have included `Events` and `Benchmark` packages w can use another powerful
register functionality that handle calling some special events and trace methods
when we create objects by Register.

Calling objects and singletons
--------------

### Simple object
To create single instance of object use method `getObject`. That will create and
return object instance. Object is called in `try/catch` block that handle only
basic `Exception`.  
Called in that way objects are not stored in register, only information how many
time class was called is stored.

```php
$object     = Register::getObject('ClassKernel\Data\Object');
$objectData = Register::getObject(
    'ClassKernel\Data\Object',
    ['some_key' => 'some value']
);
```

Method has two arguments:

1. **$name** - that is class namespace, or class name if not using namespaces
2. **$args** - that is an `__constructor` argument or array of arguments (_only one argument is passed into created object_)

### Singleton object
To handle singletons (object will be stored into Register memory) use `getSingleton`
method. That method use `getObject` to create object from class, but created object
will be stored in `$_singletons` variable that is instance of `ClassKernel\Data\Object`.  
All time that we call singleton Register will check that object exists inside of
`$_singletons` variable and create instance if object was not found.

Method has three arguments:

1. **$name** - that is class namespace, or class name if not using namespaces
2. **$args** - that is an `__constructor` argument or array of arguments (_only one argument is passed into created object_)
3. **$instanceName** - optionally we can use own name for singleton instead of class namespace

```php
$objectData = Register::getSingleton(
    'ClassKernel\Data\Object',
    ['some_key' => 'some value']
);
$objectData = Register::getSingleton('ClassKernel\Data\Object');

$object     = Register::getSingleton(
    'ClassKernel\Data\Object',
    ['some_key' => 'some value for special key'],
    'special_key'
);
$object     = Register::getSingleton('special_key');
```

When we want to remove singleton from Register use `destroy` method with class
namespace or key to remove singleton from Register. Inside of class counter will
appear information about removing singleton `destroyed [class namespace or key]`.

Objects information
--------------
To get information about called objects we can use two methods `getRegisteredObjects`
and `getClassCounter`.  

* **getRegisteredObjects** - will return list of called singletons with their namespaces and codes
* **getClassCounter** - will return how many times class was called as object (for normal and singletons objects)

Object errors
--------------
If when Register create object catch some Exception, whole Exception object will
added to `$_error` variable and called special event.  
To handle errors w can use some special Register methods:

* **getErrors** - that will return array of Exceptions
* **hasErrors** - boolean information that register has some errors
* **clearErrors** - that will remove all errors from `$_error` variable

Tracing and events
--------------
Register has ready interface to use events and tracing. By default both options
are disabled. Both of them required special packages with their functionality.  
More detailed description is in that packages documentation.

### Events
To enable Events handling set `$eventDisabled` variable on `false` or lunch`initialize`
method before start using Register that will detect that `ClassEvents\Model\Event`
class exist on system and automatically set variable to false.  
To use create event inside of script you can use:

```php
Register::callEvent('event_name', $parameters);
```

#### List of events inside of Register
Detailed information about events in Register can be fount in that document:

[Register events](/doc/Events.md#register-events "Register events")

### Tracing
The same way we enable tracing. You can use `initialize` to detect that class
`ClassBenchmark\Helper\Tracer` method or set `$tracerDisabled` to false.  
To use tracer you can use:

```php
Register::tracer('some message', debug_backtrace(), 'color in hex value without hash');
```

Some usable methods
--------------
Other usable methods in Register:

* **name2code** - convert module namespace to module code (_give name without first backslash_)

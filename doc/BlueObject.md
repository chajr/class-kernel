BlueObject (as trait and Object implementation)
====================

Idea of usage
--------------
BlueObject is one of main objects, used to store data as object. It allows easily
set and access to data by magical methods. If we try to access data that dos't
exist inside of object, we always get `null` value.

### Storage data
All data in object is stored in special
**protected** `$_DATA` variable and accessible by magic methods or by giving variable
key. Also Objects store original data, before changes.

### Data key naming
Object has build in method that convert keys between underscore and camel case.  
If we use key names ans method attributes we always must use an underscore naming.
Camel case naming is used only if we try to access to data by giving his key name
as method name.

### Trait
Because whole logic is inside of `trait BlueObject` we can inject all logic into
every object that we want to. Also BlueObject `__constructor` has lunch two special
methods `initializeObject` and `afterInitializeObject` that can help with lunch
new object without `__construct` method.  
If we want to have only simple blueObject implementation use for that `Object`
class (_ClassKernel\Data\Object_)

Basic Usage
--------------

### Create Object
Constructor accept only one optional parameter `options` that must be an array,
where we give data to set as **data** key (array or string) and second with data
**type** (_json_, _xml_, _simple_xml_, _serialized_). Default type is `array`.  
Data given in that way into object automatically is treat as original data.

```php
$object = new Object([
    'data' => [
        'variable_key' => 'some data to set'
    ]
]);
```
or
```php
$json       = '{"first_data":"a","second_data":"b","third_data":"c"}';
$objectJson = new Object([
    'data' => $json,
    'type' => 'json'
]);
```

### Xml data
Object can parse xml data in two ways. The fastest is usage as type **simple_xml**,
Object will use `simplexml_load_string` function to convert xml into data. But
that function cannot access to node attributes.  
Second is usage of **xml** as type, that will use `Xml` object (_extending DOMDocument_)
to create xml. That method is slower but accept xml attributes and serialized objects.
Attributes are stored into special array at `@attributes` key value.

### Xml data and object
Inside of xml data we can give serialized objects. To inform Object that data given
in node is serialized object and must be converted to object use special attribute
`serialized_object` in node that will have serialized object. That attribute must
just exist, value of that attribute  has no meaning.

When we don't give any parameter to object, object will be created as empty and
all data given into that object later will be treat as new, non original data.

### Set Data
To set data we have two ways. Give complete data in constructor (that data will
be treed as original data), or by using magical `set*` methods when data key is
part of method name or by using `setData` where we give data key as first method
attribute, and data to set as second attribute.

```php
$object->setData('variable_key', 'some data to set');
```

```php
$object->setVariableKey('some data to set');
```

Add data in that way wil set object as changed and if there was some data at key
we set new data, that data will be copied into original data array and can be
accessible by `getOriginalData` method.

### Get Data
To access data we have similar ways to set data. We can use magical `get*` methods
when data kay is part of method name or by using `getData` where we give data key
as first method attribute.

```php
$object->getData('variable_key', 'some data to set');
```

```php
$object->getVariableKey('some data to set');
```

### Unset Data
To destroy data we have two methods. One will totally remove key from `$_DATA`
array, second wil set key value to null.  
To set data sa null we can use `clearData` method and give key as attribute and
`clear*` giving key as method name. The same way work totally removing key from
`$_DATA` array. Just use `unset` keyword.  
In both cases data will be saved as original data.

Original Data usage
--------------
When we give data in method constructor we have access to special Object ability
that is store data in original data array. Each operation on that data that will
change key value will copy value given in constructor to special array `$_originalDATA`
so that data won't be loosed and we can access to that data with special methods.  
To check that data was changed in object call `hasDataChanged` that will return
`true` if data was changed or use `keyDataChanged` to check that data for given
key was changed.

### Get original Data
When we call some destructive methods on some key, data before change will be copied
into original data. To access that data before change we use `getOriginalData`
method with giving key name as attribute

### Restore Data
So if we store original data we can also restore data into `$_DATA` array. We can
restore data for single key or for whole `$_DATA` array.  
To restore data for whole object use `restoreData` method and for single key the
same method but with key as method attribute.

### Replace Data
Also we have ability to set current data as original data. To do that just use
`replaceDataArrays` and `$_originalDATA` will be replaced by `$_DATA` array and
`_dataChanged` set to false.

Export Object
--------------
As default Object return data as single value key or array of key, values. But
we can export data from object in the same formats as we can put it to Object.  
Accepted formats are `json`, `xml` and `serialized` array. Also we can return
object data in non acceptable as input format that is string. In that export format
we get data separated by coma or by given char.  
Each export method lunch `_prepareData` method to prepare data before export.
By default that method is empty.

### Export as json
To export object data as `json` format just use `toJson` method.

### Export as xml
To export object as data as `xml` format use `toXml` method. That method get three
attributes:

* `$addCdata` - will set data inside of CDATA section (_default true_)
* `$dtd` - will add DTD definition to `<!DOCTYPE root SYSTEM` (_default false_)
* `$version` - set xml version (_default 1.0_)

If we have some objects as key value, they will be automatically serialized and
node with that object will get `serialized_object` attribute to inform Object
to unserialize object on import.

### Export as stdClass
Object data can be converted into stdClass. To do that use `toStdClass` method.
Keys will be converted as variable names.

### Export as serialized string
To export data as serialized string just use `serialize` method. That method have
one optional attribute that inform to skip objects inside of data when its set
on `true`.

### Export as string
When we call `echo` or `print` function on object instance, object will return
string with values separated by coma. That separator is saved in variable and we
can easily change to some other using method `changeSeparator` before `echo` function
and as parameter give values separator, or use method `toString` and as parameter
give separator.  
Remember that if you use `toString`, separator given as parameter will be stored
in variable and each usage of `echo` function will use separator given in `toString`
method.

Compare Objects and keys
--------------
Object has special method allowed to compare object data or single data key value.
To do that use `compareData` with this attributes:

1. **$dataToCheck** - it can be instance of Object, or array or value fof key
2. **$key** - name of key to compare with (_default is null_)
3. **$operator** - compare operator (_default is ===_)
4. **$origin** - if set to `true` use original data to compare (_default is false_)

Available operator  to compare data `==`, `===`, `!=`, `<>`, `!==`, `<`, `>`, `<=`,
`>=`, `instance` (_alis to instanceof_)

Also there was added two magic methods to compare data with `===` and `!==` operators.
To compare that that is the same us `is*` method, with value to be compared and
`not*` method to check that data are different.

Merge Objects
--------------
Object has possibility to merge with other Object by `mergeBlueObject` method.
Method has one attribute that is other Object instance that will be joined into
Object on which we call merge method.

Object errors
--------------
Object has build in simple error handling. All errors are stored inside of `$_errorsList`
variable as array. To check that Object has errors just call `hasErrors` method
that will return `true` if there was some error in object.  
To return list of errors from object call `getObjectError` method. That method has
one optional parameter that is error key (to return some concrete error).  
Also we can clear object error or single error using `clearObjectError` method.
Without parameter will clear all errors and with key only error for given key.

### Possible errors
* **append xml data** - can return error with `xml_load_error` key when try to load xml data
* **working with xml data** - when object catch `DOMException` trying create array from xml data
(code will be `Exception->getCode()` value)
* **try to access wrong magic method** - return `wrong_method` code with class and method name
* **convert array to xml** - when object catch `DOMException` trying create array from xml data
(code will be `Exception->getCode()` value)

Extending Object
--------------
Extending object can be done on two ways. First classic is use `extend`:

```php
use ClassKernel\Data\Object;

class Test extends Object
{

}
```
or use trait to inject logic to other object:

```php
class Test extends OtherClass
{
    use ClassKernel\Base\BlueObject;
}
```

Object has some special methods that are empty and are implemented directly for
extending.

* **initializeObject** - is called at beginning of constructor and take as parameter all options (_as reference_)
* **afterInitializeObject** - is called at end of constructor and loaded data (has no parameters)
* **_prepareData** - is protected method lunched before export data or get data by `getData` or `getOriginalData` methods
on two last examples can have `$key` parameter that is key name for data to return.

Some usable methods
--------------
Other usable public methods inside of Object:

### Magic methods
* **__get** - allow to access object data by using method variable `$object->key_name`
* **__set** - allow to set object data by using method variable `$object->key_name = 'value'`
* **__isset** - used when called function `isset` on object variable `isset($object->key_name)`
will use method `hasData` to return value
* **__unset** - used when called function `unset` on object variable `unset($object->key_name)`
will use method `unsetData` to remove value
* **__set_state** - will return object data (_like getData()_) when use `var_dump` on object `var_dump($object)`

### Normal methods
* **returnSeparator** - return current set separator for return data as string
* **toArray** - return object attributes as array
* **traveler** - allow to change data inside of object by using method or function
  * **$method** - name of method or function to change data
  * **$methodAttributes** - some additional attributes for method or function
  * **$data** - data to change, if null use object data (_default is null_)
  * **$function** - if true call `$method` attribute as function (_default is false_)
  * **$recursive** - change data even in associative array if true (_default is false_)


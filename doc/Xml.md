Using ClassKernel\Data\Xml class
====================

Xml object is an extension of `DOMDocument`, so its have all functionality of that
class. More information about base functionality of Xml class is available at that
url:

[DOMDocument](http://php.net/manual/pl/class.domdocument.php "DOMDocument documentation")

Create Xml
--------------

### Load from file
Use method `loadXmlFile` with that parameters:

* **$url** - url to xml file
* **$parse** - if `true` will parse xml file with its DTD definition (_default is false_)

### Load from string
Use methods from `DOMDocument`.

### Build by methods
Use methods from `DOMDocument` to create Xml nodes.

Save xml
--------------

### As xml file
Use `saveXmlFile` method with this parameters:

* **$url** path with file name to save file
* **$asString** will also return xml string

### Return as string
To return only as string. without save file use `saveXmlFile` method with first
parameter set to `false`.

Object errors
--------------
Xml object can have some errors, all of them are stored inside of `$_error` variable
by string error code. Available error codes are:

* **save_file_error** - when xml file cannot be saved (_in saveXmlFile method_)
* **file_not_exists** - when xml file cannot be found in given path (_in loadXmlFile method_)
* **loading_file_error** - when xml file cannot be loaded (_in loadXmlFile method_)
* **parse_file_error** - when xml structure have some errors (_in loadXmlFile method with parse option set to true_)

To handle errors w can use some special methods:

* **getErrors** - that will return array of Exceptions
* **hasErrors** - boolean information that register has some errors
* **clearErrors** - that will remove all errors from `$_error` variable

Other methods
--------------
Some other usable methods:

* **getFreeId** - generate free numeric id
* **searchByAttribute** - search node for elements that contains element with give attribute
  * **DOMNodeList $node** - node to search in
  * **$value** - attribute value to search
* **checkId** - check that element with given id exists (_as parameter give id value_)
* **getId** - alias to `getElementById`

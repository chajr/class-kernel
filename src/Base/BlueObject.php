<?php
/**
 * trait object to store data or models and allows to easily access to object
 *
 * @package     ClassKernel
 * @subpackage  Base
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-kernel/wiki/ClassKernel_Base_BlueObject BlueObject class documentation
 */
namespace ClassKernel\Base;

use ClassKernel\Data\Object;
use ClassKernel\Data\Xml;
use stdClass;
use DOMException;
use DOMElement;
use Zend\Serializer\Serializer;
use Zend\Serializer\Exception\ExceptionInterface;
use Exception;

trait BlueObject
{
    /**
     * if there was some errors in object, that variable will be set on true
     *
     * @var bool
     */
    protected $_hasErrors = false;

    /**
     * will contain list of all errors that was occurred in object
     *
     * 0 => ['error_key' => 'error information']
     *
     * @var array
     */
    protected $_errorsList = [];

    /**
     * array with main object data
     * @var array
     */
    protected $_DATA = [];

    /**
     * keeps data before changes (set only if some data in $_DATA was changed)
     * @var
     */
    protected $_originalDATA = [];

    /**
     * store all new added data keys, to remove them when in eg. restore original data
     * @var array
     */
    protected $_newKeys = [];

    /**
     * @var array
     */
    protected static $_cacheKeys = [];

    /**
     * @var boolean
     */
    protected $_dataChanged = false;

    /**
     * default constructor options
     *
     * @var array
     */
    protected $_options = [
        'data'              => null,
        'type'              => null,
    ];

    /**
     * name of key prefix for xml node
     * if array key was integer
     *
     * @var string
     */
    protected $_integerKeyPrefix = 'integer_key';

    /**
     * separator for data to return as string
     * 
     * @var string
     */
    protected $_separator = ', ';

    /**
     * store list of rules to validate data
     * keys are searched using regular expression
     * 
     * @var array
     */
    protected $_validationRules = [];

    /**
     * list of callbacks to prepare data before insert into object
     * 
     * @var array
     */
    protected $_dataPreparationCallbacks = [];

    /**
     * list of callbacks to prepare data before return from object
     * 
     * @var array
     */
    protected $_dataRetrieveCallbacks = [];

    /**
     * for array access numeric keys, store last used numeric index
     * used only in case when object is used as array
     * 
     * @var int
     */
    protected $_integerKeysCounter = 0;

    /**
     * allow to turn off/on data validation
     * 
     * @var bool
     */
    protected $_validationOn = true;

    /**
     * allow to turn off/on data preparation
     * 
     * @var bool
     */
    protected $_preparationOn = true;

    /**
     * allow to turn off/on data retrieve
     * 
     * @var bool
     */
    protected $_retrieveOn = true;

    /**
     * create new Blue Object, optionally with some data
     * there are some types we can give to convert data to Blue Object
     * like: json, xml, serialized or stdClass default is array
     *
     * @param array|null $options
     */
    public function __construct($options = [])
    {
        if (array_key_exists('data', $options)) {
            $this->_options = array_merge($this->_options, $options);
            $data           = $this->_options['data'];
        } else {
            $data = $options;
        }

        $this->initializeObject($data);

        switch (true) {
            case $this->_options['type'] === 'json':
                $this->_appendJson($data);
                break;

            case $this->_options['type'] === 'xml':
                $this->_appendXml($data);
                break;

            case $this->_options['type'] === 'simple_xml':
                $this->_appendSimpleXml($data);
                break;

            case $this->_options['type'] === 'serialized':
                $this->_appendSerialized($data);
                break;

            case $data instanceof stdClass:
                $this->_appendStdClass($data);
                break;

            default:
                $this->_appendArray($data);
                break;
        }

        $this->afterInitializeObject();
    }

    /**
     * return from DATA value for given object attribute
     *
     * @param string $key
     * @return mixed
     */

    public function __get($key)
    {
        $key = $this->_convertKeyNames($key);
        return $this->getData($key);
    }

    /**
     * save into DATA value given as object attribute
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $key = $this->_convertKeyNames($key);
        $this->_putData($key, $value);
    }

    /**
     * check that variable exists in DATA table
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        $key = $this->_convertKeyNames($key);
        return $this->hasData($key);
    }

    /**
     * remove given key from DATA
     *
     * @param $key
     */
    public function __unset($key)
    {
        $key = $this->_convertKeyNames($key);
        $this->unsetData($key);
    }

    /**
     * allow to access DATA keys by using special methods
     * like getSomeData() will return $_DATA['some_data'] value or
     * setSomeData('val') will create $_DATA['some_data'] key with 'val' value
     *
     * @param string $method
     * @param array $arguments
     * @return $this|bool|mixed
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case substr($method, 0, 3) === 'get':
                $key = $this->_convertKeyNames(substr($method, 3));
                if (array_key_exists(0, $arguments)) {
                    return $this->getData($key, $arguments[0]);
                }
                return $this->getData($key);

            case substr($method, 0, 3) === 'set':
                $key = $this->_convertKeyNames(substr($method, 3));
                if (array_key_exists(0, $arguments)) {
                    return $this->setData($key, $arguments[0]);
                }
                return $this->setData($key);

            case substr($method, 0, 3) === 'has':
                $key = $this->_convertKeyNames(substr($method, 3));
                return $this->hasData($key);

            case substr($method, 0, 3) === 'not':
                $key = $this->_convertKeyNames(substr($method, 3));
                return $this->_comparator($this->getData($key), $arguments[0], '!==');

            case substr($method, 0, 5) === 'unset':
                $key = $this->_convertKeyNames(substr($method, 5));
                return $this->unsetData($key);

            case substr($method, 0, 5) === 'clear':
                $key = $this->_convertKeyNames(substr($method, 5));
                return $this->clearData($key);

            case substr($method, 0, 7) === 'restore':
                $key = $this->_convertKeyNames(substr($method, 7));
                return $this->restoreData($key);

            case substr($method, 0, 2) === 'is':
                $key = $this->_convertKeyNames(substr($method, 2));
                return $this->_comparator($this->getData($key), $arguments[0], '===');

            default:
                $this->_errorsList['wrong_method'] = get_class($this) . ' - ' . $method;
                $this->_hasErrors = true;
                return false;
        }
    }

    /**
     * return DATA content if try to access object by var_export() function
     *
     * @return mixed
     */
    public function __set_state()
    {
        return $this->getData();
    }

    /**
     * return object data as string representation
     *
     * @return string
     */
    public function __toString()
    {
        $this->_prepareData();
        return implode($this->_separator, $this->getData());
    }

    /**
     * return boolean information that object has some error
     *
     * @return bool
     */
    public function checkErrors()
    {
        return $this->_hasErrors;
    }

    /**
     * return single error by key, ora list of all errors
     *
     * @param string $key
     * @return mixed
     */
    public function returnObjectError($key = null)
    {
        return $this->_genericReturn($key, 'error_list');
    }

    /**
     * remove single error, or all object errors
     *
     * @param string|null $key
     * @return Object
     */
    public function removeObjectError($key = null)
    {
        return $this->_genericDestroy($key, 'error_list');
    }

    /**
     * return serialized object data
     *
     * @param boolean $skipObjects
     * @return string
     */
    public function serialize($skipObjects = false)
    {
        $this->_prepareData();
        $temporaryData  = $this->getData();
        $data           = '';

        if ($skipObjects) {
            $temporaryData = $this->traveler(
                '_skipObject',
                null,
                $temporaryData,
                false,
                true
            );
        }

        try {
            $data = Serializer::serialize($temporaryData);
        } catch (ExceptionInterface $exception) {
            $this->_addException($exception);
        }

        return $data;
    }

    /**
     * allow to set data from serialized string with keep original data
     * 
     * @param string $string
     * @return $this
     */
    public function unserialize($string)
    {
        $this->unsetData();
        $this->__construct([
            'data'  => $string,
            'type'  => 'serialized'
        ]);

        return $this;
    }

    /**
     * return data for given key if exist in object, or all object data
     *
     * @param null|string $key
     * @return mixed
     */
    public function getData($key = null)
    {
        $this->_prepareData($key);
        $data = null;

        if (is_null($key)) {
            $data = $this->_DATA;
        } elseif (array_key_exists($key, $this->_DATA)) {
            $data = $this->_DATA[$key];
        }

        if (!$this->_retrieveOn) {
            return $this->_dataPreparation($key, $data, $this->_dataRetrieveCallbacks);
        }
        return $data;
    }

    /**
     * set some data in object
     * can give pair key=>value or array of keys
     *
     * @param string|array $key
     * @param mixed $data
     * @return Object
     */
    public function setData($key, $data = null)
    {
        if (is_array($key)) {
            foreach ($key as $dataKey => $data) {
                $this->_putData($dataKey, $data);
            }

        } else {
            $this->_putData($key, $data);
        }

        return $this;
    }

    /**
     * return original data for key, before it was changed
     *
     * @param null|string $key
     * @return mixed
     */
    public function returnOriginalData($key = null)
    {
        $this->_prepareData($key);

        $mergedData = array_merge($this->_DATA, $this->_originalDATA);
        $data       = $this->_removeNewKeys($mergedData);

        if (!$key) {
            return $data;
        }

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        return null;
    }

    /**
     * check if data with given key exist in object, or object has some data
     * if key wast given
     *
     * @param null|string $key
     * @return bool
     */
    public function hasData($key = null)
    {
        if (
            (is_null($key) && !empty($this->_DATA))
            || array_key_exists($key, $this->_DATA)
        ) {
            return true;
        }

        return false;
    }

    /**
     * check that given data and data in object with given operator
     * use the same operator like in PHP (eg ===, !=, <, ...)
     * possibility to compare with origin data
     * 
     * if return null, comparator symbol was wrong
     *
     * @param mixed $dataToCheck
     * @param string $operator
     * @param string|null $key
     * @param boolean $origin
     * @return bool|null
     */
    public function compareData($dataToCheck, $key = null, $operator = '===', $origin = null)
    {
        if ($origin) {
            $mergedData = array_merge($this->_DATA, $this->_originalDATA);
            $data       = $this->_removeNewKeys($mergedData);
        } else {
            $data = $this->_DATA;
        }

        if ($dataToCheck instanceof Object) {
            $dataToCheck = $dataToCheck->getData();
        }

        switch (true) {
            case is_null($key):
                return $this->_comparator($dataToCheck, $data, $operator);
            // no break, always will return boolean value

            default:
                if (array_key_exists($key, $data)) {
                    return $this->_comparator($dataToCheck, $data[$key], $operator);
                } else {
                    return false;
                }
            // no break, always will return boolean value
        }
    }

    /**
     * allow to compare data with given operator
     * 
     * @param mixed $dataOrigin
     * @param mixed $dataCheck
     * @param string $operator
     * @return bool|null
     */
    protected function _comparator($dataOrigin, $dataCheck, $operator)
    {
        switch ($operator) {
            case '===':
                return $dataOrigin === $dataCheck;
            // no break, always will return boolean value

            case '!==':
                return $dataOrigin !== $dataCheck;
            // no break, always will return boolean value

            case '==':
                return $dataOrigin !== $dataCheck;
            // no break, always will return boolean value

            case '!=':
            case '<>':
                return $dataOrigin !== $dataCheck;
            // no break, always will return boolean value

            case '<':
                return $dataOrigin < $dataCheck;
            // no break, always will return boolean value

            case '>':
                return $dataOrigin > $dataCheck;
            // no break, always will return boolean value

            case '<=':
                return $dataOrigin <= $dataCheck;
            // no break, always will return boolean value

            case '>=':
                return $dataOrigin >= $dataCheck;
            // no break, always will return boolean value

            case 'instance':
                return $dataOrigin instanceof $dataCheck;
            // no break, always will return boolean value

            default:
                return null;
            // no break, always will return boolean value
        }
    }

    /**
     * destroy key entry in object data, or whole keys
     * automatically set data to original array
     *
     * @param string|null $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        if (is_null($key)) {
            $this->_dataChanged  = true;
            $mergedData          = array_merge($this->_DATA, $this->_originalDATA);
            $this->_originalDATA = $this->_removeNewKeys($mergedData);
            $this->_DATA         = [];

        } elseif (array_key_exists($key, $this->_DATA)) {
            $this->_dataChanged = true;

            if (!array_key_exists($key, $this->_originalDATA)
                && !array_key_exists($key, $this->_newKeys)
            ) {
                $this->_originalDATA[$key] = $this->_DATA[$key];
            }

            unset ($this->_DATA[$key]);
        }

        return $this;
    }

    /**
     * set object key data to null
     *
     * @param string $key
     * @return $this
     */
    public function clearData($key)
    {
        $this->_putData($key, null);
        return $this;
    }

    /**
     * replace changed data by original data
     * set data changed to false only if restore whole data
     *
     * @param string|null $key
     * @return $this
     */
    public function restoreData($key = null)
    {
        if (is_null($key)) {
            $mergedData         = array_merge($this->_DATA, $this->_originalDATA);
            $this->_DATA        = $this->_removeNewKeys($mergedData);
            $this->_dataChanged = false;
        } else {
            if (array_key_exists($key, $this->_originalDATA)) {
                $this->_DATA[$key] = $this->_originalDATA[$key];
            }
        }

        return $this;
    }

    /**
     * this method set current DATA as original data
     * replace original data by DATA and set data changed to false
     *
     * @return $this
     */
    public function replaceDataArrays()
    {
        $this->_originalDATA = $this->_DATA;
        $this->_dataChanged  = false;
        $this->_newKeys      = [];
        return $this;
    }

    /**
     * return object as string
     * each data value separated by coma
     *
     * @param string $separator
     * @return string
     */
    public function toString($separator)
    {
        $this->_separator = $separator;
        $this->_prepareData();
        return $this->__toString();
    }

    /**
     * return current separator
     * 
     * @return string
     */
    public function returnSeparator()
    {
        return $this->_separator;
    }

    /**
     * allow to change default separator
     * 
     * @param string $separator
     * @return Object
     */
    public function changeSeparator($separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * return data as json string
     *
     * @return string
     */
    public function toJson()
    {
        $this->_prepareData();
        return json_encode($this->getData());
    }

    /**
     * return object data as xml representation
     *
     * @param bool $addCdata
     * @param string|boolean $dtd
     * @param string $version
     * @return string
     */
    public function toXml($addCdata = true, $dtd = false, $version = '1.0')
    {
        $this->_prepareData();

        $xml    = new Xml(['version' => $version]);
        $root   = $xml->createElement('root');
        $xml    = $this->_arrayToXml($this->_DATA, $xml, $addCdata, $root);

        $xml->appendChild($root);

        if ($dtd) {
            $dtd = "<!DOCTYPE root SYSTEM '$dtd'>";
        }

        $xml->formatOutput = true;
        $xmlData = $xml->saveXmlFile(false, true);

        if ($xml->hasErrors()) {
            $this->_hasErrors       = true;
            $this->_errorsList[]    = $xml->getError();
            return false;
        }

        return $dtd . $xmlData;
    }

    /**
     * return object as stdClass
     * 
     * @return stdClass
     */
    public function toStdClass()
    {
        $this->_prepareData();
        $data = new stdClass();

        foreach ($this->_DATA as $key => $val) {
            $data->$key = $val;
        }

        return $data;
    }

    /**
     * return object attributes as array
     * without DATA
     *
     * @return mixed
     */
    public function toArray()
    {
        $attributesArray = [];

        foreach ($this as $name => $value) {
            if ($name === '_DATA') {
                continue;
            }
            $attributesArray[$name] = $value;
        }

        return $attributesArray;
    }

    /**
     * return information that some data was changed in object
     *
     * @return bool
     */
    public function dataChanged()
    {
        return $this->_dataChanged;
    }

    /**
     * check that data for given key was changed
     *
     * @param string $key
     * @return bool
     */
    public function keyDataChanged($key)
    {
        $data           = $this->getData($key);
        $originalData   = $this->returnOriginalData($key);

        return $data !== $originalData;
    }

    /**
     * allow to use given method or function for all data inside of object
     *
     * @param array|string|\Closure $function
     * @param mixed $methodAttributes
     * @param mixed $data
     * @param bool $recursive
     * @return array|null
     */
    public function traveler(
        $function,
        $methodAttributes = null,
        $data = null,
        $recursive = false
    ) {
        if (!$data) {
            $data = $this->_DATA;
        }

        foreach ($data as $key => $value) {
            $isRecursive = is_array($value) && $recursive;

            if ($isRecursive) {
                $data[$key] = $this->_recursiveTraveler($function, $methodAttributes, $value, true);
            } else {
                $data[$key] = $this->_callUserFunction($function, $key, $value, $methodAttributes);
            }
        }

        return $data;
    }

    /**
     * allow to change some data in multi level arrays
     *
     * @param mixed $methodAttributes
     * @param mixed $data
     * @param array|string|\Closure $function
     * @return mixed
     */
    protected function _recursiveTraveler($function, $methodAttributes, $data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->_recursiveTraveler($function, $methodAttributes, $value);
            } else {
                $data[$key] = $this->_callUserFunction($function, $key, $value, $methodAttributes);
            }
        }

        return $data;
    }

    /**
     * run given function, method or closure on given data
     *
     * @param array|string|\Closure $function
     * @param string $key
     * @param mixed $value
     * @param mixed $attributes
     * @return mixed
     */
    protected function _callUserFunction($function, $key, $value, $attributes)
    {
        if (is_callable($function)) {
            return call_user_func_array($function, [$key, $value, $this, $attributes]);
        }

        return $value;
    }

    /**
     * allow to join two blue objects into one
     *
     * @param \ClassKernel\Data\Object $object
     * @return $this
     */
    public function mergeBlueObject(Object $object)
    {
        $newData = $object->getData();

        foreach ($newData as $key => $value) {
            $this->setData($key, $value);
        }

        $this->_dataChanged = true;
        return $this;
    }

    /**
     * remove all new keys from given data
     *
     * @param array $data
     * @return array
     */
    protected function _removeNewKeys(array $data)
    {
        foreach ($this->_newKeys as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    /**
     * apply given json data as object data
     *
     * @param string $data
     * @return $this
     */
    protected function _appendJson($data)
    {
        $jsonData = json_decode($data, true);

        if ($jsonData) {
            $this->_DATA = $jsonData;
        }

        return $this;
    }

    /**
     * apply given xml data as object data
     *
     * @param $data string
     * @return $this
     */
    protected function _appendSimpleXml($data)
    {
        $loadedXml      = simplexml_load_string($data);
        $jsonXml        = json_encode($loadedXml);
        $this->_DATA    = json_decode($jsonXml, true);

        return $this;
    }

    /**
     * apply given xml data as object data
     * also handling attributes
     *
     * @param $data string
     * @return $this
     */
    protected function _appendXml($data)
    {
        $xml                        = new Xml();
        $xml->preserveWhiteSpace    = false;
        $bool                       = @$xml->loadXML($data);

        if (!$bool) {
            $this->_errorsList['xml_load_error']    = $data;
            $this->_hasErrors                       = true;
            return $this;
        }

        try {
            $temp                  = $this->_xmlToArray($xml->documentElement);
            $this->_DATA           = $temp;
        } catch (DOMException $exception) {
            $this->_addException($exception);
        }

        return $this;
    }

    /**
     * recurrent function to travel on xml nodes and set their data as object data
     *
     * @param DOMElement $data
     * @return array
     */
    protected function _xmlToArray(DOMElement $data)
    {
        $temporaryData = [];

        /** @var $node DOMElement */
        foreach ($data->childNodes as $node) {
            $nodeName           = $this->_stringToIntegerKey($node->nodeName);
            $nodeData           = [];
            $unSerializedData   = [];

            if ($node->hasAttributes() && $node->getAttribute('serialized_object')) {
                try {
                    $unSerializedData = Serializer::unserialize($node->nodeValue);
                } catch (ExceptionInterface $exception) {
                    $this->_addException($exception);
                }

                $temporaryData[$nodeName] = $unSerializedData;
                continue;
            }

            if ($node->hasAttributes()) {
                foreach ($node->attributes as $key => $value) {
                    $nodeData['@attributes'][$key] = $value->nodeValue;
                }
            }

            if ($node->hasChildNodes()) {
                $childNodesData = [];

                /** @var $childNode DOMElement */
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType === 1) {
                        $childNodesData = $this->_xmlToArray($node);
                    }
                }

                if (!empty($childNodesData)) {
                    $temporaryData[$nodeName] = $childNodesData;
                    continue;
                }
            }

            if (!empty($nodeData)) {
                $temporaryData[$nodeName] = array_merge(
                    [$node->nodeValue],
                    $nodeData
                );
            } else {
                $temporaryData[$nodeName] = $node->nodeValue;
            }
        }

        return $temporaryData;
    }

    /**
     * remove prefix from integer array key
     *
     * @param string $key
     * @return string|integer
     */
    protected function _stringToIntegerKey($key)
    {
        return str_replace($this->_integerKeyPrefix . '_', '', $key);
    }

    /**
     * set data given in constructor
     *
     * @param mixed $data
     * @return $this
     */
    protected function _appendArray($data)
    {
        if (is_array($data)) {
            $this->_DATA = $data;
        } else {
            $this->_DATA['default'] = $data;
        }

        return $this;
    }

    /**
     * get class variables and set them as data
     *
     * @param stdClass $class
     * @return $this
     */
    protected function _appendStdClass(stdClass $class)
    {
        $this->_DATA = get_object_vars($class);
        return $this;
    }
    

    /**
     * set data from serialized string as object data
     * if data is an object set one variable where key is an object class name
     *
     * @param mixed $data
     * @return $this
     */
    protected function _appendSerialized($data)
    {
        try {
            $data = Serializer::unserialize($data);
        } catch (ExceptionInterface $exception) {
            $this->_addException($exception);
        }

        if (is_object($data)) {
            $this->_DATA[get_class($data)] = $data;
        } else {
            $this->_DATA = $data;
        }

        return $this;
    }

    /**
     * check that given data for key is valid and set in object if don't exist or is different
     *
     * @param string $key
     * @param mixed $data
     * @return $this
     */
    protected function _putData($key, $data)
    {
        $bool = $this->_validateDataKey($key, $data);
        if (!$bool) {
            return $this;
        }

        $hasData = $this->hasData($key);
        if (!$this->_preparationOn) {
            $data = $this->_dataPreparation(
                $key,
                $data,
                $this->_dataPreparationCallbacks
            );
        }

        if (!$hasData
            || ($hasData && $this->_comparator($this->_DATA[$key], $data, '!=='))
        ) {
            $this->_changeData($key, $data, $hasData);
        }

        return $this;
    }

    /**
     * insert single key=>value pair into object, with key conversion
     * and set _dataChanged to true
     * also set original data for given key in $this->_originalDATA
     * 
     * @param string $key
     * @param mixed $data
     * @param bool $hasData
     * @return $this
     */
    protected function _changeData($key, $data, $hasData)
    {
        if (!array_key_exists($key, $this->_originalDATA)
            && $hasData
            && !array_key_exists($key, $this->_newKeys)
        ) {
            $this->_originalDATA[$key] = $this->_DATA[$key];
        } else {
            $this->_newKeys[$key] = $key;
        }

        $this->_dataChanged = true;
        $this->_DATA[$key]  = $data;

        return $this;
    }

    /**
     * search validation rule for given key and check data
     * 
     * @param string $key
     * @param mixed $data
     * @return bool
     */
    protected function _validateDataKey($key, $data)
    {
        $dataOkFlag = true;

        if (!$this->_validationOn) {
            return $dataOkFlag;
        }

        foreach ($this->_validationRules as $ruleKey => $ruleValue) {
            if (!preg_match($ruleKey, $key)) {
                continue;
            }

            $bool = $this->_validateData($ruleValue, $key, $data);
            if (!$bool) {
                $dataOkFlag = false;
            }
        }

        return $dataOkFlag;
    }

    /**
     * check data with given rule and set error information
     * 
     * @param string $rule
     * @param string $key
     * @param mixed $data
     * @return bool
     */
    protected function _validateData($rule, $key, $data)
    {
        if (preg_match($rule, $data)) {
            return true;
        }

        $this->_errorsList[] = [
            'message'   => 'validation_mismatch',
            'key'       => $key,
            'data'      => $data,
            'rule'      => $rule,
        ];
        $this->_hasErrors = true;

        return false;
    }

    /**
     * convert given object data key (given as came case method)
     * to proper construction
     *
     * @param string $key
     * @return string
     */
    protected function _convertKeyNames($key)
    {
        if (array_key_exists($key, self::$_cacheKeys)) {
            return self::$_cacheKeys[$key];
        }

        $convertedKey = strtolower(
            preg_replace('/(.)([A-Z0-9])/', "$1_$2", $key)
        );
        self::$_cacheKeys[$key] = $convertedKey;
        return $convertedKey;
    }

    /**
     * recursive method to create structure xml structure of object DATA
     *
     * @param $data
     * @param Xml $xml
     * @param boolean $addCdata
     * @param Xml|DOMElement $parent
     * @return Xml
     */
    protected function _arrayToXml($data, Xml $xml, $addCdata, $parent)
    {
        foreach ($data as $key => $value) {
            $key        = str_replace(' ', '_', $key);
            $attributes = [];
            $data       = '';

            if (is_object($value)) {
                try {
                    $data = Serializer::serialize($value);
                } catch (ExceptionInterface $exception) {
                    $this->_addException($exception);
                }

                $value = [
                    '@attributes' => ['serialized_object' => true],
                    $data
                ];
            }

            try {
                $isArray = is_array($value);

                if ($isArray && array_key_exists('@attributes', $value)) {
                    $attributes = $value['@attributes'];
                    unset ($value['@attributes']);
                }

                if ($isArray) {
                    $parent = $this->_convertArrayDataToXml(
                        $value,
                        $addCdata,
                        $xml,
                        $key,
                        $parent,
                        $attributes
                    );
                    continue;
                }

                $element = $this->_appendDataToNode($addCdata, $xml, $key, $value);
                $parent->appendChild($element);

            } catch (DOMException $exception) {
                $this->_addException($exception);
            }
        }

        return $xml;
    }

    /**
     * convert array DATA value to xml format and return as xml object
     *
     * @param array|string $value
     * @param string $addCdata
     * @param Xml $xml
     * @param string|integer $key
     * @param DOMElement $parent
     * @param array $attributes
     * @return DOMElement
     */
    protected function _convertArrayDataToXml(
        $value,
        $addCdata,
        Xml $xml,
        $key,
        $parent,
        array $attributes
    ) {
        $count      = count($value) === 1;
        $isNotEmpty = !empty($attributes);
        $exist      = array_key_exists(0, $value);

        if ($count && $isNotEmpty && $exist) {
            $children = $this->_appendDataToNode(
                $addCdata,
                $xml,
                $key,
                $value[0]
            );
        } else {
            $children = $xml->createElement(
                $this->_integerToStringKey($key)
            );
            $this->_arrayToXml($value, $xml, $addCdata, $children);
        }
        $parent->appendChild($children);

        foreach ($attributes as $attributeKey => $attributeValue) {
            $children->setAttribute($attributeKey, $attributeValue);
        }

        return $parent;
    }
    /**
     * append data to node
     *
     * @param string $addCdata
     * @param Xml $xml
     * @param string|integer $key
     * @param string $value
     * @return DOMElement
     */
    protected function _appendDataToNode($addCdata, Xml $xml, $key, $value)
    {
        if ($addCdata) {
            $cdata      = $xml->createCDATASection($value);
            $element    = $xml->createElement(
                $this->_integerToStringKey($key)
            );
            $element->appendChild($cdata);
        } else {
            $element = $xml->createElement(
                $this->_integerToStringKey($key),
                $value
            );
        }

        return $element;
    }

    /**
     * if array key is number, convert it to string with set up _integerKeyPrefix
     *
     * @param string|integer $key
     * @return string
     */
    protected function _integerToStringKey($key)
    {
        if (is_numeric($key)) {
            $key = $this->_integerKeyPrefix . '_' . $key;
        }

        return $key;
    }

    /**
     * replace object by string
     *
     * @param $value
     * @return string
     */
    protected function _skipObject($value)
    {
        if (is_object($value)) {
            return '{;skipped_object;}';
        }

        return $value;
    }

    /**
     * set regular expression for key find and validate data
     * 
     * @param string $ruleKey
     * @param string $ruleValue
     * @return $this
     */
    public function putValidationRule($ruleKey, $ruleValue = null)
    {
        return $this->_genericPut($ruleKey, $ruleValue, 'validation');
    }

    /**
     * remove validation rule from list
     * 
     * @param string|null $key
     * @return $this
     */
    public function destroyValidationRule($key = null)
    {
        return $this->_genericDestroy($key, 'validation');
    }

    /**
     * return validation rule or all rules set in object
     * 
     * @param null|string $rule
     * @return mixed
     */
    public function returnValidationRule($rule = null)
    {
        return $this->_genericReturn($rule, 'validation');
    }

    /**
     * common put data method for class data lists
     * 
     * @param string|array $key
     * @param mixed $value
     * @param string $type
     * @return $this
     */
    protected function _genericPut($key, $value, $type)
    {
        $listName = $this->_getCorrectList($type);
        if (!$listName) {
            return $this;
        }

        if (is_array($key)) {
            $this->$listName = array_merge($this->$listName, $key);
        } else {
            $list       = &$this->$listName;
            $list[$key] = $value;
        }

        return $this;
    }

    /**
     * common destroy data method for class data lists
     * 
     * @param string $key
     * @param string $type
     * @return $this
     */
    protected function _genericDestroy($key, $type)
    {
        $listName = $this->_getCorrectList($type);
        if (!$listName) {
            return $this;
        }

        if ($key) {
            $list = &$this->$listName;
            unset ($list[$key]);
        }
        $this->$listName = [];

        return $this;
    }

    /**
     * common return data method for class data lists
     * 
     * @param string $key
     * @param string $type
     * @return mixed|null
     */
    protected function _genericReturn($key, $type)
    {
        $listName = $this->_getCorrectList($type);

        switch (true) {
            case !$listName:
                return null;

            case !$key:
                return $this->$listName;

            case array_key_exists($key, $this->$listName):
                $list = &$this->$listName;
                return $list[$key];

            default:
                return null;
        }
    }

    /**
     * return name of data list variable for given data type
     * 
     * @param string $type
     * @return null|string
     */
    protected function _getCorrectList($type)
    {
        switch ($type) {
            case 'error_list':
                $type = '_errorsList';
                break;

            case 'validation':
                $type = '_validationRules';
                break;

            case 'preparation_callback':
                $type = '_dataPreparationCallbacks';
                break;

            case 'return_callback':
                $type = '_dataRetrieveCallbacks';
                break;

            default:
                $type = null;
        }

        return $type;
    }

    /**
     * return data formatted by given function
     * 
     * @param string $key
     * @param mixed $data
     * @param array $rulesList
     * @return mixed
     */
    protected function _dataPreparation($key, $data, array $rulesList)
    {
        foreach ($rulesList as $ruleKey => $function) {
            if (!preg_match($ruleKey, $key) && $key !== null) {
                continue;
            }

            $data = $this->_callUserFunction($function, $key, $data, null);
        }

        return $data;
    }

    /**
     * set regular expression for key find and validate data
     * 
     * @param string $ruleKey
     * @param string $ruleValue
     * @return $this
     */
    public function putPreparationCallback($ruleKey, $ruleValue = null)
    {
        return $this->_genericPut($ruleKey, $ruleValue, 'preparation_callback');
    }

    /**
     * remove validation rule from list
     * 
     * @param string|null $key
     * @return $this
     */
    public function destroyPreparationCallback($key = null)
    {
        return $this->_genericDestroy($key, 'preparation_callback');
    }

    /**
     * return validation rule or all rules set in object
     * 
     * @param null|string $rule
     * @return mixed
     */
    public function returnPreparationCallback($rule = null)
    {
        return $this->_genericReturn($rule, 'preparation_callback');
    }

    /**
     * set regular expression for key find and validate data
     * 
     * @param string $ruleKey
     * @param string $ruleValue
     * @return $this
     */
    public function putReturnCallback($ruleKey, $ruleValue = null)
    {
        return $this->_genericPut($ruleKey, $ruleValue, 'return_callback');
    }

    /**
     * remove validation rule from list
     * 
     * @param string|null $key
     * @return $this
     */
    public function destroyReturnCallback($key = null)
    {
        return $this->_genericReturn($key, 'return_callback');
    }

    /**
     * return validation rule or all rules set in object
     * 
     * @param null|string $rule
     * @return mixed
     */
    public function returnReturnCallback($rule = null)
    {
        return $this->_genericReturn($rule, 'return_callback');
    }

    /**
     * check that data for given key exists
     * 
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasData($offset);
    }

    /**
     * return data for given key
     * 
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getData($offset);
    }

    /**
     * set data for given key
     * 
     * @param string|null $offset
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $offset = $this->_integerToStringKey($this->_integerKeysCounter++);
        }

        $this->_putData($offset, $value);
        return $this;
    }

    /**
     * remove data for given key
     * 
     * @param string $offset
     * @return $this
     */
    public function offsetUnset($offset)
    {
        $this->unsetData($offset);
        return $this;
    }

    /**
     * return the current element in an array
     * handle data preparation
     * 
     * @return mixed
     */
    public function current()
    {
        current($this->_DATA);
        return $this->getData($this->key());
    }

    /**
     * return the current element in an array
     * 
     * @return mixed
     */
    public function key()
    {
        return key($this->_DATA);
    }

    /**
     * advance the internal array pointer of an array
     * handle data preparation
     * 
     * @return mixed
     */
    public function next()
    {
        next($this->_DATA);
        return $this->getData($this->key());
    }

    /**
     * rewind the position of a file pointer
     * 
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->_DATA);
    }

    /**
     * checks if current position is valid
     * 
     * @return bool
     */
    public function valid()
    {
        return key($this->_DATA) !== null;
    }

    /**
     * allow to stop data validation
     * 
     * @return $this
     */
    public function stopValidation()
    {
        $this->_validationOn = false;
        return $this;
    }

    /**
     * allow to start data validation
     * 
     * @return $this
     */
    public function startValidation()
    {
        $this->_validationOn = true;
        return $this;
    }

    /**
     * allow to stop data preparation before add tro object
     * 
     * @return $this
     */
    public function stopOutputPreparation()
    {
        $this->_preparationOn = false;
        return $this;
    }

    /**
     * allow to start data preparation before add tro object
     * 
     * @return $this
     */
    public function startOutputPreparation()
    {
        $this->_preparationOn = true;
        return $this;
    }

    /**
     * allow to stop data preparation before return them from object
     * 
     * @return $this
     */
    public function stopInputPreparation()
    {
        $this->_retrieveOn = false;
        return $this;
    }

    /**
     * allow to start data preparation before return them from object
     * 
     * @return $this
     */
    public function startInputPreparation()
    {
        $this->_retrieveOn = true;
        return $this;
    }

    /**
     * create exception message and set it in object
     * 
     * @param Exception $exception
     * @return $this
     */
    protected function _addException(Exception $exception)
    {
        $this->_hasErrors = true;
        $this->_errorsList[$exception->getCode()] = [
            'message'   => $exception->getMessage(),
            'line'      => $exception->getLine(),
            'file'      => $exception->getFile(),
            'trace'     => $exception->getTraceAsString(),
        ];

        return $this;
    }

    /**
     * can be overwritten by children objects to start with some special operations
     * as parameter take data given to object by reference
     *
     * @param mixed $data
     */
    public function initializeObject(&$data)
    {
        
    }

    /**
     * can be overwritten by children objects to start with some special
     * operations
     */
    public function afterInitializeObject()
    {
        
    }

    /**
     * can be overwritten by children objects to make some special process on
     * data before return
     * 
     * @param string|null $key
     */
    protected function _prepareData($key = null)
    {
        
    }
}

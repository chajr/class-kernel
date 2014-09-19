<?php
/**
 * trait object to store data or models and allows to easily access to object
 *
 * @package     ClassKernel
 * @subpackage  Base
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */
namespace ClassKernel\Base;

use DOMException;
use DOMElement;
use ClassKernel\Data\DomXml;

trait Object
{
    /**
     * name of key prefix for xml node
     * if array key was integer
     *
     * @var string
     */
    protected $_integerKeyPrefix = 'integer_key';

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
        'indexKeyPrefix'    => null
    ];

    /**
     * create new Blue Object, optionally with some data
     * there are some types we can give to convert data to Blue Object
     * like: json, xml, serialized, default is array
     *
     * @param array|null $options
     */
    public function __construct($options = [])
    {
        if (isset($options['data'])) {
            $this->_options = array_merge($this->_options, $options);
            $data           = $this->_options['data'];
        } else {
            $data = $options;
        }

        $this->initializeObject($data);
        if ($this->_options['indexKeyPrefix']) {
            $this->_integerKeyPrefix = $this->_options['indexKeyPrefix'];
        }

        switch ($this->_options['type']) {
            case 'json':
                $this->_appendJson($data);
                break;

            case 'xml':
                $this->_appendXml($data);
                break;

            case 'serialized':
                $this->_appendSerialized($data);
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
     * like getSomeData() will return _DATA['some_data'] value or
     * setSomeData('val') will create DATA['some_data'] key with 'val' value
     *
     * @param string $method
     * @param array $arguments
     * @return Object|bool|mixed
     */
    public function __call($method, $arguments)
    {
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->_convertKeyNames(substr($method, 3));
                if (isset($arguments[0])) {
                    return $this->getData($key, $arguments[0]);
                }
                return $this->getData($key);

            case 'set':
                $key = $this->_convertKeyNames(substr($method, 3));
                if (isset($arguments[0])) {
                    return $this->setData($key, $arguments[0]);
                }
                return $this->setData($key);

            case 'has':
                $key = $this->_convertKeyNames(substr($method, 3));
                return $this->hasData($key);

            default:
                $methodPrefix = substr($method, 0, 5);

                if ($methodPrefix === 'unset') {
                    $key = $this->_convertKeyNames(substr($method, 5));
                    return $this->unsetData($key);
                }

                if ($methodPrefix === 'clear') {
                    $key = $this->_convertKeyNames(substr($method, 5));
                    return $this->clearData($key);
                }

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
        return implode(', ', $this->getData());
    }

    /**
     * return boolean information that object has some error
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->_hasErrors;
    }

    /**
     * return single error by key, ora list of all errors
     *
     * @param string $key
     * @return mixed
     */
    public function getObjectError($key = null)
    {
        if ($key) {
            return $this->_errorsList[$key];
        }

        return $this->_errorsList;
    }

    /**
     * return value of prefix for integer keys when try to save as xml
     *
     * @return string
     */
    public function getIntegerKeyString()
    {
        return $this->_integerKeyPrefix;
    }

    /**
     * allow to set new prefix for integer key
     *
     * @param string $key
     * @return Object
     */
    public function setIntegerKeyString($key)
    {
        $this->_integerKeyPrefix = (string)$key;
        return $this;
    }

    /**
     * remove single error, or all object errors
     *
     * @param string $key
     * @return Object
     */
    public function clearObjectError($key)
    {
        if ($key) {
            unset ($this->_errorsList[$key]);
        }
        $this->_errorsList = [];

        return $this;
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
        $temporaryData = $this->getData();

        if ($skipObjects) {
            $temporaryData = $this->traveler(
                '_skipObject',
                null,
                $temporaryData,
                false,
                true
            );
        }

        return serialize($temporaryData);
    }

    /**
     * return data for given key if exist in object, or all object data
     *
     * @param null|string $key
     * @return mixed
     */
    public function getData($key = null)
    {
        $this->_prepareData();

        if (!$key) {
            return $this->_DATA;
        }

        if (isset($this->_DATA[$key])) {
            return $this->_DATA[$key];
        }

        return null;
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
        if(is_array($key)) {
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
    public function getOriginalData($key = null)
    {
        $this->_prepareData();

        $mergedData = array_merge($this->_DATA, $this->_originalDATA);
        $data       = $this->_removeNewKeys($mergedData);

        if (!$key) {
            return $data;
        }

        if (isset($data[$key])) {
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
        if (!$key && !empty($this->_DATA)) {
            return true;
        }

        if (isset($this->_DATA[$key])) {
            return true;
        }

        return false;
    }

    /**
     * check that given data and data in object are the same
     * checking by === operator
     * possibility to compare with origin data
     *
     * @param string|array $key
     * @param mixed $dataToCheck
     * @param boolean $origin
     * @return bool
     */
    public function compareData($key, $dataToCheck, $origin = null)
    {
        if (is_array($key)) {
            if ($origin) {
                $mergedData = array_merge($this->_DATA, $this->_originalDATA);
                $data       = $this->_removeNewKeys($mergedData);

                if ($dataToCheck === $data) {
                    return true;
                }
            } else {
                if ($dataToCheck === $this->_DATA) {
                    return true;
                }
            }
        } else {
            if ($origin) {
                $mergedData = array_merge($this->_DATA, $this->_originalDATA);
                $data       = $this->_removeNewKeys($mergedData);

                if (isset($data[$key])) {
                    if ($dataToCheck === $data[$key]) {
                        return true;
                    }
                } else if ($dataToCheck === null) {
                    return true;
                }
            } else {
                if (isset($this->_DATA[$key])) {
                    if ($dataToCheck === $this->_DATA[$key]) {
                        return true;
                    }
                } else if ($dataToCheck === null) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * destroy key entry in object data, or whole keys
     * automatically set data to original array
     *
     * @param string|null $key
     * @return Object
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->_dataChanged  = true;
            $mergedData          = array_merge($this->_DATA, $this->_originalDATA);
            $this->_originalDATA = $this->_removeNewKeys($mergedData);
            $this->_DATA         = [];

        } elseif (isset($this->_DATA[$key])) {
            $this->_dataChanged = true;

            if (!isset($this->_originalDATA[$key]) && !isset($this->_newKeys[$key])) {
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
     * @return Object
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
     * @param string $key
     * @return Object
     */
    public function restoreData($key)
    {
        if ($key === null) {
            $mergedData         = array_merge($this->_DATA, $this->_originalDATA);
            $this->_DATA        = $this->_removeNewKeys($mergedData);
            $this->_dataChanged = false;
        } else {
            if (isset($this->_originalDATA[$key])) {
                $this->_DATA[$key] = $this->_originalDATA[$key];
            }
        }

        return $this;
    }

    /**
     * this method set current DATA as original data
     * replace original data by DATA and set data changed to false
     *
     * @return Object
     */
    public function replaceDataArrays()
    {
        $this->_originalDATA = $this->_DATA;
        $this->_dataChanged  = false;
        return $this;
    }

    /**
     * return object as string
     * each data value separated by coma
     *
     * @return string
     */
    public function toString()
    {
        $this->_prepareData();
        return $this->__toString();
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

        $config = ['version' => $version, 'encoding' => 'UTF-8'];
        $xml    = new DomXml($config);
        $root   = $xml->createElement('root');
        $xml    = $this->_arrayToXml($this->_DATA, $xml, $addCdata, $root);

        $xml->appendChild($root);
        if ($dtd) {
            $dtd = "<!DOCTYPE root SYSTEM '$dtd'>";
        }
        $xml->formatOutput = true;

        return $dtd . $xml->saveXmlFile(false, true);
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
    public function hasDataChanged()
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
        $originalData   = $this->getOriginalData($key);

        return $data != $originalData;
    }

    /**
     * allow to use given method or function for all data inside of object
     *
     * @param string $method
     * @param mixed $methodAttributes
     * @param mixed $data
     * @param bool $function
     * @param bool $recursive
     * @return array|null
     */
    public function traveler(
        $method,
        $methodAttributes   = null,
        $data               = null,
        $function           = false,
        $recursive          = false
    ){
        if (!$data) {
            $data = $this->_DATA;
        }

        foreach ($data as $key => $value) {
            $isRecursive = is_array($value) && $recursive;

            if ($isRecursive) {
                $data[$key] = $this->_recursiveTraveler($method, $methodAttributes, $value, $function);
            } else {
                $data[$key] = $this->_callUserFunction($function, $method, $key, $value, $methodAttributes);
            }
        }

        return $data;
    }

    /**
     * allow to change some data in multi level arrays
     *
     * @param string $method
     * @param mixed $methodAttributes
     * @param mixed $data
     * @param string|boolean $function
     * @return mixed
     */
    protected function _recursiveTraveler($method, $methodAttributes, $data, $function)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->_recursiveTraveler($method, $methodAttributes, $value, $function);
            } else {
                $data[$key] = $this->_callUserFunction($function, $method, $key, $value, $methodAttributes);
            }
        }

        return $data;
    }

    /**
     * run given function or method on given data
     *
     * @param string|boolean $function
     * @param string $method
     * @param string $key
     * @param mixed $value
     * @param mixed $methodAttributes
     * @return mixed
     */
    protected function _callUserFunction($function, $method, $key, $value, $methodAttributes)
    {
        if ($function) {
            return call_user_func($method, $key, $value, $methodAttributes);
        }
        return $this->$method($key, $value, $methodAttributes);
    }

    /**
     * allow to join two blue objects into one
     *
     * @param $object
     * @return Object
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
     * @return $this Object
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
     * @return Object
     */
    protected function _appendXml($data)
    {
        $xml                        = new DomXml();
        $xml->preserveWhiteSpace    = false;
        $bool                       = @$xml->loadXML($data);

        if (!$bool) {
            $this->_errorsList['xml_load_error']    = $data;
            $this->_hasErrors                       = true;
            return $this;
        }

        try{
            $temp                  = $this->_xmlToArray($xml->documentElement);
            $this->_DATA           = $temp;
        } catch (DOMException $exception) {
            $this->_errorsList[$exception->getCode()] = [
                'message'   => $exception->getMessage(),
                'line'      => $exception->getLine(),
                'file'      => $exception->getFile(),
                'trace'     => $exception->getTraceAsString(),
            ];
            $this->_hasErrors = true;
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
            $nodeName = $this->_stringToIntegerKey($node->nodeName);
            $nodeData = [];

            if ($node->hasAttributes() && $node->getAttribute('serialized_object')) {
                $temporaryData[$nodeName] = unserialize($node->nodeValue);
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
     * set data given in constructor
     *
     * @param mixed $data
     * @return Object
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
     * set data from serialized string as object data
     * if data is an object set one variable where key is an object class name
     *
     * @param mixed $data
     * @return Object
     */
    protected function _appendSerialized($data)
    {
        $data = unserialize($data);
        if (is_object($data)) {
            $this->_DATA[get_class($data)] = $data;
        } else {
            $this->_DATA = unserialize($data);
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
     * @return Object
     */
    protected function _putData($key, $data)
    {
        if (!isset($this->_originalDATA[$key])
            && isset($this->_DATA[$key])
            && !isset($this->_newKeys[$key])
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
     * convert given object data key (given as came case method)
     * to proper construction
     *
     * @param string $key
     * @return string
     */
    protected function _convertKeyNames($key)
    {
        if (isset(self::$_cacheKeys[$key])) {
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
     * @param DomXml $xml
     * @param boolean $addCdata
     * @param DomXml|DOMElement $parent
     * @return DomXml
     */
    protected function _arrayToXml($data, DomXml $xml, $addCdata, $parent)
    {
        foreach ($data as $key => $value) {
            $key        = str_replace(' ', '_', $key);
            $attributes = [];

            if (is_object($value)) {
                $value = [
                    '@attributes' => ['serialized_object' => true],
                    serialize($value)
                ];
            }

            try{
                if (isset($value['@attributes'])) {
                    $attributes = $value['@attributes'];
                    unset ($value['@attributes']);
                }

                if (is_array($value)) {
                    $parent = $this->_convertArrayDataToXml(
                        $value, $addCdata, $xml, $key, $parent, $attributes
                    );
                    continue;
                }

                $element = $this->_appendDataToNode($addCdata, $xml, $key, $value);
                $parent->appendChild($element);

            } catch (DOMException $exception) {
                $this->_errorsList[$exception->getCode()] = [
                    'message'   => $exception->getMessage(),
                    'line'      => $exception->getLine(),
                    'file'      => $exception->getFile(),
                    'trace'     => $exception->getTraceAsString(),
                ];
                $this->_hasErrors = true;
            }
        }

        return $xml;
    }

    /**
     * convert array DATA value to xml format and return as xml object
     *
     * @param array|string $value
     * @param string $addCdata
     * @param DomXml $xml
     * @param string|integer $key
     * @param DOMElement $parent
     * @param array $attributes
     * @return DOMElement
     */
    protected function _convertArrayDataToXml(
        $value, $addCdata, DomXml $xml, $key, $parent, array $attributes
    ){
        if (count($value) === 1 && !empty($attributes) && isset($value[0])) {
            $children = $this->_appendDataToNode(
                $addCdata, $xml, $key, $value[0]);
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
     * @param DomXml $xml
     * @param string|integer $key
     * @param string $value
     * @return DOMElement
     */
    protected function _appendDataToNode($addCdata, DomXml $xml, $key, $value)
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
     * can be overwritten by children objects to start with some special operations
     * as parameter take data given to object by reference
     *
     * @param mixed $data
     */
    public function initializeObject(&$data){}

    /**
     * can be overwritten by children objects to start with some special
     * operations
     */
    public function afterInitializeObject(){}

    /**
     * can be overwritten by children objects to make some special process on
     * data before return
     */
    protected function _prepareData(){}
}
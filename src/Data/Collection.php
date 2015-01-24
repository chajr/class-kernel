<?php
/**
 * collection class to store list of Objects or other data in array
 *
 * @package     ClassKernel
 * @subpackage  Data
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-kernel/wiki/ClassKernel_Data_Collection collection usage
 */
namespace ClassKernel\Data;

use Serializable;
use ArrayAccess;
use Iterator;

class Collection implements Serializable, ArrayAccess, Iterator
{
    /**
     * store all collection elements
     * 
     * @var array
     */
    protected $_COLLECTION = [];

    /**
     * store collection element before change
     * 
     * @var array
     */
    protected $_OriginalCollection = [];

    /**
     * default page size
     * 
     * @var int
     */
    protected $_pageSize = 10;

    /**
     * number of current page
     * 
     * @var int
     */
    protected $_currentPage = 1;

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
     * @var boolean
     */
    protected $_dataChanged = false;

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
     * allow to process [section] as array key
     *
     * @var bool
     */
    protected $_processIniSection;

    /**
     * if true loop on collection will iterate on pages, otherwise on elements
     * 
     * @var bool
     */
    protected $_loopByPages = false;

    /**
     * default constructor options
     *
     * @var array
     */
    protected $_options = [
        'data'                  => null,
        'type'                  => null,
        'validation'            => [],
        'preparation'           => [],
        'ini_section'           => false,
    ];

    /**
     * inform append* methods that data was set in object creation
     *
     * @var bool
     */
    protected $_objectCreation = true;

    /**
     * create collection object
     * 
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_options             = array_merge($this->_options, $options);
        $data                       = $this->_options['data'];
        $this->_processIniSection   = $this->_options['ini_section'];

        $this->putValidationRule($this->_options['validation'])
            ->putPreparationCallback($this->_options['preparation'])
            ->initializeObject($data);

        switch (true) {
            case is_array($data):
                $this->appendArray($data);
                break;

            //data provider object to handle lazy data loading for collection

            default:
                break;
        }

        $this->afterInitializeObject();
        $this->_objectCreation = false;
    }

    /**
     * append array as collection elements
     * 
     * @param array $arrayData
     * @return $this
     */
    public function appendArray(array $arrayData)
    {
        foreach ($arrayData as $data) {
            $this->addElement($data);
        }

        if ($this->_objectCreation) {
            return $this->_afterAppendDataToNewObject();
        }
        return $this;
    }

    /**
     * set regular expression collection row, or column
     *
     * @param string $ruleKey
     * @param string $ruleValue
     * @return $this
     */
    public function putValidationRule($ruleKey, $ruleValue = null)
    {
        return $this;
    }

    /**
     * set regular expression for collection row
     *
     * @param string $ruleKey
     * @param callable $ruleValue
     * @return $this
     */
    public function putPreparationCallback($ruleKey, callable $ruleValue = null)
    {
        return $this;
    }

    public function setFilter()
    {
        
    }

    public function getFilters()
    {
        
    }

    public function resetFilters()
    {
        
    }

    public function setOrder()
    {
        
    }

    public function getOrder()
    {
        
    }

    public function getCollection()
    {
        
    }

    public function setCollection()
    {
        
    }

    public function getOriginalCollection()
    {
        
    }

    /**
     * return serialized collection
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize($this->_prepareCollection());
    }

    public function unserialize($string)
    {
        
    }

    /**
     * remove element from collection
     * 
     * @param int $element
     * @return $this
     */
    public function delete($element)
    {
        if ($this->hasElement($element)) {
            unset($this->_COLLECTION[$element]);
        }

        return $this;
    }

    /**
     * prepare collection before return
     * 
     * @param array|null $data
     * @param bool $isSingleElement
     * @return mixed
     */
    protected function _prepareCollection($data = null, $isSingleElement = false)
    {
        if (is_null($data)) {
            $data = $this->_COLLECTION;
        }

        if (!$this->_retrieveOn) {
            return $data;
        }

        //work on collection
        if (!$isSingleElement) {
            //work on elements
        } else {
            //work on single element
        }

        return $data;
    }

    public function initializeObject()
    {
        
    }

    public function afterInitializeObject()
    {
        
    }

    /**
     * add one row element to collection
     * 
     * @param mixed $data
     * @param null|string $type
     * @return $this
     */
    public function addElement($data, $type = null)
    {
        $bool = $this->_validateData($data);
        if (!$bool) {
            return $this;
        }

        if ($this->_preparationOn) {
            $data = $this->_dataPreparation(
                $data,
                $this->_dataPreparationCallbacks
            );
        }

        $this->_COLLECTION[] = $data;

        return $this;
    }

    /**
     * validate data on input
     * 
     * @param mixed $data
     * @return bool
     */
    protected function _validateData($data)
    {
        return true;
    }

    /**
     * allow to prepare input or output data
     * 
     * @param mixed $data
     * @param array $callbacks
     * @return mixed
     */
    protected function _dataPreparation($data, array $callbacks)
    {
        return $data;
    }

    //finished methods

    /**
     * return all elements from collection
     *
     * @return array
     */
    public function getFullCollection()
    {
        return $this->_prepareCollection();
    }

    /**
     * check that given page number can be set up
     *
     * @param int $pageSize
     * @return bool
     */
    protected function _isPageAllowed($pageSize)
    {
        $max = $this->count() >= ($this->getPageSize() * $pageSize);
        $min = $pageSize >= 1;

        if ($max && $min) {
            return true;
        }

        return false;
    }

    /**
     * set next page number
     *
     * @return $this
     */
    public function nextPage()
    {
        if ($this->_isPageAllowed($this->getCurrentPage() +1)) {
            $this->_currentPage++;
        }

        return $this;
    }

    /**
     * set previous page number
     *
     * @return $this
     */
    public function previousPage()
    {
        if ($this->_isPageAllowed($this->getCurrentPage() -1)) {
            $this->_currentPage--;
        }

        return $this;
    }

    /**
     * clear some data after creating new object with data
     *
     * @return $this
     */
    protected function _afterAppendDataToNewObject()
    {
        $this->_dataChanged = false;
        return $this;
    }

    /**
     * return element from collection by given index
     * 
     * @param int $index
     * @return mixed|null
     */
    public function getElement($index)
    {
        if ($this->hasElement($index)) {
            $data = $this->_COLLECTION[$index];
            return $this->_prepareCollection($data, true);
        }

        return null;
    }

    /**
     * alias for delete method
     * 
     * @param int $element
     * @return Collection
     */
    public function removeElement($element)
    {
        //recalculate indexes
        return $this->delete($element);
    }

    /**
     * check that element exist in collection
     * 
     * @param int $index
     * @return bool
     */
    public function hasElement($index)
    {
        return isset($this->_COLLECTION[$index]);
    }

    /**
     * check that data for given key exists
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->hasElement($offset);
    }

    /**
     * return data for given key
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->getElement($offset);
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
        $this->addElement($value, $offset);
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
        return $this->delete($offset);
    }

    /**
     * return the current element in an array
     * handle data preparation
     *
     * @return mixed
     */
    public function current()
    {
        current($this->_COLLECTION);
        return $this->getElement($this->key());
    }

    /**
     * return the current element in an array
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_COLLECTION);
    }

    /**
     * advance the internal array pointer of an array
     * handle data preparation
     *
     * @return mixed
     */
    public function next()
    {
        next($this->_COLLECTION);
        return $this->getElement($this->key());
    }

    /**
     * rewind the position of a file pointer
     *
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->_COLLECTION);
    }

    /**
     * checks if current position is valid
     *
     * @return bool
     */
    public function valid()
    {
        return key($this->_COLLECTION) !== null;
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
     * return first element from collection
     *
     * @return mixed
     */
    public function first()
    {
        $data = reset($this->_COLLECTION);
        return $this->_prepareCollection($data, true);
    }

    /**
     * return last element from collection
     *
     * @return mixed
     */
    public function last()
    {
        $data = end($this->_COLLECTION);
        return $this->_prepareCollection($data, true);
    }

    /**
     * return number of all elements in collection
     *
     * @return int|void
     */
    public function count()
    {
        return count($this->_COLLECTION);
    }

    /**
     * return object data as serialized string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->serialize();
    }

    /**
     * set default size for collection elements on page to return
     *
     * @param int $size
     * @return $this
     */
    public function setPageSize($size)
    {
        $this->_pageSize = $size;
        return $this;
    }

    /**
     * return size for collection elements on page to return
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * return current page number
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * allow to set current page
     *
     * @param int $page
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->_currentPage = $page;
        return $this;
    }

    /**
     * return number of all pages
     *
     * @return float
     */
    public function countPages()
    {
        return ceil($this->count() / $this->getPageSize());
    }

    /**
     * get all elements from first page
     *
     * @return array|mixed
     */
    public function getFirstPage()
    {
        $data = $this->_getPage(1);
        return $this->_prepareCollection($data);
    }

    /**
     * get all elements from last page
     * 
     * @return array|mixed
     */
    public function getLastPage()
    {
        $data = $this->_getPage($this->countPages());
        return $this->_prepareCollection($data);
    }

    /**
     * return elements for page with given index
     * 
     * @param int $index
     * @return array
     */
    protected function _getPage($index)
    {
        $pageSize   = $this->getPageSize();
        $start      = ($index * $pageSize) -$pageSize;
        return array_slice($this->_COLLECTION, $start, $pageSize);
    }

    /**
     * return current page or page with given index
     * return null if page don't exists
     * 
     * @param null|int $index
     * @return mixed|null
     */
    public function getPage($index = null)
    {
        if (!$index) {
            $index = $this->getCurrentPage();
        }

        if (!$this->_isPageAllowed($index)) {
            return null;
        }

        $data = $this->_getPage($index);
        return $this->_prepareCollection($data);
    }

    /**
     * get next page of collection
     * don't change the current page marker
     * 
     * @return array|null
     */
    public function getNextPage()
    {
        $page = $this->getCurrentPage() +1;

        if (!$this->_isPageAllowed($page)) {
            return null;
        }

        return $this->getPage($page);
    }

    /**
     * get previous page of collection
     * don't change the current page marker
     *
     * @return array|null
     */
    public function getPreviousPage()
    {
        $page = $this->getCurrentPage() -1;

        if (!$this->_isPageAllowed($page)) {
            return null;
        }

        return $this->getPage($page);
    }

    /**
     * set loop on collection to iterate on pages, if false on elements
     * 
     * @param bool $bool
     * @return $this
     */
    public function loopPages($bool = true)
    {
        $this->_loopByPages = (bool)$bool;
        return $this;
    }

    /**
     * return information witch loop is used for collection
     * 
     * @return bool
     */
    public function isLoopByPagesEnabled()
    {
        return $this->_loopByPages;
    }
}

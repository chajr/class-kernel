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
     * 
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

    public function __construct()
    {
        
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
     * return first element from collection
     * 
     * @return mixed
     */
    public function first()
    {
        $data = reset($this->_COLLECTION);

        if ($this->_retrieveOn) {
            return $this->_prepareCollection($data);
        }

        return $data;
    }

    /**
     * return last element from collection
     * 
     * @return mixed
     */
    public function last()
    {
        $data = end($this->_COLLECTION);

        if ($this->_retrieveOn) {
            return $this->_prepareCollection($data);
        }

        return $data;
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

    public function setFilter()
    {
        
    }

    public function getFilters()
    {
        
    }

    public function resetFilters()
    {
        
    }

    /**
     * set default size for collection elements to return
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
     * return size for collection elements to return
     * 
     * @return int
     */
    public function getPageSize()
    {
        return $this->_pageSize;
    }

    /**
     * return current page
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

    public function setOrder()
    {
        
    }

    public function getOrder()
    {
        
    }

    public function getCollection()
    {
        
    }

    /**
     * return all elements from collection
     * 
     * @return array
     */
    public function getFullCollection()
    {
        if ($this->_retrieveOn) {
            return $this->_prepareCollection();
        }
        return $this->_COLLECTION;
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
        if ($this->_retrieveOn) {
            $collection = $this->_prepareCollection();
        } else {
            $collection = $this->_COLLECTION;
        }

        return serialize($collection);
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
     * check that given page number can be set up
     * 
     * @param int $pageSize
     * @return bool
     */
    protected function _isPageSizeAllowed($pageSize)
    {
        $max = $this->count() >= ($this->getPageSize() * $pageSize);
        $min = $pageSize > 1;

        if ($max && $min) {
            return true;
        }

        return false;
    }

    public function getPage()
    {
        
    }

    public function getNextPage()
    {
        
    }

    public function getPreviousPage()
    {
        
    }

    /**
     * set next page number
     * 
     * @return $this
     */
    public function nextPage()
    {
        if ($this->_isPageSizeAllowed($this->getCurrentPage() +1)) {
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
        if ($this->_isPageSizeAllowed($this->getCurrentPage() -1)) {
            $this->_currentPage--;
        }

        return $this;
    }

    /**
     * return count of allowed pages
     * 
     * @return float
     */
    public function getPagesCount()
    {
        return ceil($this->count() / $this->getPageSize());
    }

    public function getFirstPage()
    {
        
    }

    public function getLastPage()
    {
        
    }

    /**
     * prepare collection before return
     * 
     * @param mixed|null $data
     * @return mixed
     */
    protected function _prepareCollection($data = null)
    {
        if ($data === null) {
            return $this->_COLLECTION;
        }
        return $data;
    }

    public function initializeObject()
    {
        
    }

    public function afterInitializeObject()
    {
        
    }

    public function addElement($value, $index = null)
    {
        
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

            if ($this->_preparationOn) {
                return $this->_prepareCollection($data);
            }

            return $data;
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
}

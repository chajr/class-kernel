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

class Collection
{
    /**
     * 
     * 
     * @var array
     */
    protected $_COLLECTION = [];

    /**
     * 
     * 
     * @var array
     */
    protected $_OriginalCollection = [];

    /**
     * 
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

    public function __construct()
    {
        
    }

    /**
     * return object data as string representation
     *
     * @return string
     */
    public function __toString()
    {
        $this->_prepareCollection();
    }

    public function first()
    {
        
    }

    public function last()
    {
        
    }

    public function count()
    {
        
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

    public function setPageSize()
    {
        
    }

    public function getPageSize()
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

    public function serialize()
    {
        
    }

    public function userialize()
    {
        
    }

    public function delete()
    {
        
    }

    public function getPage()
    {
        
    }

    public function nextPage()
    {
        
    }

    public function previousPage()
    {
        
    }

    protected function _prepareCollection()
    {
        
    }

    public function initializeObject()
    {
        
    }

    public function afterInitializeObject()
    {
        
    }
}

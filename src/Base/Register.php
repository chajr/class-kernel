<?php
/**
 * Contains all object instances
 *
 * @package     ClassKernel
 * @subpackage  Base
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 */
namespace ClassKernel\Base;

use ClassKernel\Data\Object;
use Exception;

class Register
{
    /**
     * enable or disable tracer methods
     * 
     * @var bool
     */
    public static $tracerDisabled = true;

    /**
     * enable or disable event methods
     *
     * @var bool
     */
    public static $eventDisabled = true;

    /**
     * store information about number of class executions
     *
     * @var array
     */
    protected static $_classCounter = [];

    /**
     * keep all singleton class instances
     * 
     * @var \ClassKernel\Data\Object
     */
    protected static $_singletons;

    /**
     * list of errors in register
     * contains Exception instances or string with message
     * 
     * @var array
     */
    protected static $_error = [];

    /**
     * initialize Register object
     */
    public static function initialize()
    {
        self::$_singletons = new Object();
    }

    /**
     * try to create new object and return it
     *
     * @param string $name
     * @param array $args
     * @return mixed
     */
    public static function getObject($name, $args = [])
    {
        $object = false;

        try {
            if (empty($args)) {
                $object = new $name;
            } else {
                $object = new $name($args);
            }
        } catch (Exception $e) {
            self::$_error[] = $e;
        }

        if ($object) {
            self::setClassCounter(self::name2code($name));
        }

        return $object;
    }

    /**
     * unset all errors in register
     */
    public static function clearErrors()
    {
        self::$_error = [];
    }

    /**
     * return true if there was some errors in register
     * 
     * @return bool
     */
    public static function hasErrors()
    {
        return !empty(self::$_error);
    }

    /**
     * return list of errors
     * 
     * @return array
     */
    public static function getErrors()
    {
        return self::$_error;
    }

    /**
     * return object instance, or create it with sets of arguments
     * optionally when create at instance give an instance name to take by that name instead of class name
     *
     * @param string $class
     * @param array $args
     * @param null|string $instanceName
     * @return object;
     */
    public static function getSingleton($class, $args = [], $instanceName = null)
    {
        $name = $class;
        if ($instanceName) {
            $name = $instanceName;
        }

        $instanceCode = self::name2code($name);

        if (self::$_singletons->hasData($instanceCode)) {
            $instance = self::$_singletons->getData($instanceCode);
        } else {
            $instance = self::_setObject($class, $instanceCode, $args);
        }

        return $instance;
    }

    /**
     * destroy singleton object in register
     *
     * @param string $class
     */
    public static function destroy($class)
    {
        $instanceCode   = self::name2code($class);
        $number         = self::$_classCounter[$class];

        self::$_singletons->unsetData($instanceCode);
        self::$_classCounter[$class] = "destroyed [$number]";
    }

    /**
     * convert module namespace to module code
     * give name without first backslash
     *
     * @param string $module
     * @return string
     */
    public static function name2code($module)
    {
        return implode('_', array_map('strtolower', explode('\\', $module)));
    }

    /**
     * @param string $class
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    protected static function _setObject($class, $name, $args)
    {
        self::tracer('initialize object', debug_backtrace(), '006c94');

        $object = self::getObject($class, $args);

        if ($object) {
            self::$_singletons->setData($name, $object);
        }

        return $object;
    }

    /**
     * return list of registered objects with their codes
     *
     * @return array
     */
    public function getRegisteredObjects()
    {
        $list = [];
        foreach (self::$_singletons->getData() as $name => $class) {
            $list[$name] = get_class($class);
        }

        return $list;
    }

    /**
     * return list of created by Loader::getClass objects and number of executions
     *
     * @return array
     */
    public static function getClassCounter()
    {
        return self::$_classCounter;
    }

    /**
     * increment by 1 class execution
     *
     * @param string $class
     * @return Register
     */
    public static function setClassCounter($class)
    {
        self::$_classCounter[$class] += 1;
    }

    /**
     * alias for ClassBenchmark\Helper\Tracer::marker
     * with checking that class exists and tracer is enabled
     *
     * @param string $message
     * @param array $debugBacktrace
     * @param string $color
     */
    public static function tracer($message, $debugBacktrace = null, $color = '000000')
    {
        if (self::$tracerDisabled) {
            return;
        }

        if (class_exists('Core\Benchmark\Helper\Tracer')) {
            ClassBenchmark\Helper\Tracer::marker([
                $message,
                $debugBacktrace,
                '#' . $color
            ]);
        }
    }

    /**
     * create event
     * create event observer in ini file in that model event_code[class_name] = method
     *
     * @param string $name
     * @param mixed $data
     */
    public static function callEvent($name, $data = [])
    {
        if (self::$eventDisabled) {
            return;
        }

        if (class_exists('Core\Events\Helper\Tracer')) {

        }
    }
}

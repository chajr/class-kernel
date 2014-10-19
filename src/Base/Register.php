<?php
/**
 * Contains all object instances
 *
 * @package     ClassKernel
 * @subpackage  Base
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-kernel/wiki/ClassKernel%5CBase%5CRegister Register class documentation
 */
namespace ClassKernel\Base;

use ClassKernel\Data\Object;
use Exception;
use ReflectionClass;

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
        if (class_exists('ClassEvents\Model\Event')) {
            self::$eventDisabled = false;
        }

        if (class_exists('ClassBenchmark\Helper\Tracer')) {
            self::$tracerDisabled = false;
        }
    }

    /**
     * try to create new object and return it, or return object ReflectionClass instance
     *
     * @param string $name
     * @param array $args
     * @param bool $reflection
     * @return mixed
     */
    public static function getObject($name, $args = [], $reflection = false)
    {
        self::tracer('getObject', debug_backtrace(), '006c94');
        self::callEvent('register_get_object_before', [&$name, &$args]);

        $object = false;

        try {
            if (empty($args)) {
                $object = new $name;
            } else {
                $object = new $name($args);
            }
        } catch (Exception $e) {
            self::$_error[] = $e;
            self::callEvent('register_get_object_exception', [$name, $args, $e]);
        }

        if ($object) {
            self::_setClassCounter(self::name2code($name));
        }

        if ($reflection) {
            $object = new ReflectionClass($object);
        }

        self::callEvent('register_get_object_after', [$name, $args, $object]);
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
        self::tracer('getSingleton', debug_backtrace(), '006c94');
        self::callEvent('register_get_singleton_before', [&$class, &$args, $instanceName]);
        self::_singletonContainer();

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

        self::callEvent(
            'register_get_singleton_after',
            [$class, $args, $instanceName, $instance]
        );
        return $instance;
    }

    /**
     * check that singleton object container was initialized and create if not
     */
    protected static function _singletonContainer()
    {
        if (!self::$_singletons) {
            self::$_singletons = new Object();
        }
    }

    /**
     * destroy singleton object in register
     *
     * @param string $class
     */
    public static function destroy($class)
    {
        $instanceCode = self::name2code($class);

        self::$_singletons->unsetData($instanceCode);
        self::$_classCounter[$class] = "destroyed [$class]";
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
     * create singleton object
     * 
     * @param string $class
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    protected static function _setObject($class, $name, $args)
    {
        self::tracer('_setObject', debug_backtrace(), '006c94');
        self::callEvent('register_set_object_before', [&$class, &$name, &$args]);

        $object = self::getObject($class, $args);

        if ($object) {
            self::$_singletons->setData($name, $object);
        }

        self::callEvent('register_set_object_after', [$class, $name, $args, $object]);
        return $object;
    }

    /**
     * return list of singletons ReflectionClass instances
     * or ReflectionClass for given singleton code
     *
     * @param string|null $singletonKey
     * @return array
     */
    public static function getRegisteredObjects($singletonKey = null)
    {
        if ($singletonKey && self::$_singletons->hasData($singletonKey)) {
            return new ReflectionClass(self::$_singletons->getData($singletonKey));
        }

        $list = [];
        foreach (self::$_singletons->getData() as $name => $class) {
            $list[$name] = new ReflectionClass($class);
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
    protected static function _setClassCounter($class)
    {
        if (!isset(self::$_classCounter[$class])) {
            self::$_classCounter[$class] = 0;
        }

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

        if (class_exists('Core\Events\Model\Event')) {

        }
    }
}

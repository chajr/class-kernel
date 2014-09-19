<?php
/**
 * Contains all object instances
 *
 * @package     Core
 * @subpackage  Blue
 * @author      chajr <chajr@bluetree.pl>
 */
namespace ClassKernel\Base;

use ClassKernel\Data\Object;

class Register extends Object
{
    static $tracerDisabled = true;

    /**
     * store information about number of class executions
     *
     * @var array
     */
    protected $_classCounter = [];

    /**
     * @param string $class
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    public function setObject($class, $name, $args)
    {
        self::tracer('initialize object', debug_backtrace(), '006c94');
        $object = FALSE;

        try {
            $object = Loader::getClass($class, $args);
        } catch (Exception $e) {
            Loader::exceptions($e);
        }

        if ($object) {
            $this->setData($name, $object);
        }

        return $this->getData($name);
    }

    /**
     * return list of registered objects with their codes
     *
     * @return array
     */
    public function getRegisteredObjects()
    {
        $list = [];
        foreach ($this->_DATA as $name => $class) {
            $list[$name] = get_class($class);
        }

        return $list;
    }

    /**
     * return list of created by Loader::getClass objects and number of executions
     *
     * @return array
     */
    public function getClassCounter()
    {
        return $this->_classCounter;
    }

    /**
     * increment by 1 class execution
     *
     * @param string $class
     * @return Register
     */
    public function setClassCounter($class)
    {
        $this->_classCounter[$class] += 1;
        return $this;
    }

    /**
     * alias for ClassBenchmark\Helper\Tracer::marker
     * with checking that class exists and tracer is enabled
     *
     * @param string $message
     * @param array $debugBacktrace
     * @param string $color
     */
    static function tracer($message, $debugBacktrace = null, $color = '000000')
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
}

<?php
/**
 * create basically object to store data or models and allows to easily access to object
 *
 * @package     ClassKernel
 * @subpackage  Data
 * @author      Michał Adamiak    <chajr@bluetree.pl>
 * @copyright   chajr/bluetree
 * @link https://github.com/chajr/class-kernel/wiki/ClassKernel_Base_BlueObject Object class documentation
 */
namespace ClassKernel\Data;

use ClassKernel\Base\BlueObject;
use Serializable;
use ArrayAccess;
use Iterator;

class Object implements Serializable, ArrayAccess, Iterator
{
    use BlueObject;
}

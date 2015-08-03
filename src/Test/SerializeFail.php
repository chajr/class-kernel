<?php

namespace ClassKernel\Test;

class SerializeFail implements \Serializable
{
    public function serialize()
    {
        throw new \Zend\Serializer\Exception\RuntimeException('test exception');
    }

    public function unserialize($string)
    {
        
    }
}

<?php

namespace App\Serializer;

class CircularReferenceHandler
{
    /** @phpstan-ignore-next-line  */
    public function __invoke($object)
    {
        return $object->getId();
    }
}

<?php

namespace App\Serializer;

class DateTimeCallback
{
    public const FORMAT = 'Y-m-d H:i:s';
    public const FORMAT_DAY = 'Y-m-d';

    public function __invoke(
        null|string|\DateTimeInterface $object
    ): null|string|\DateTimeInterface {
        if ($object === null) {
            return null;
        }

        if (!$object instanceof \DateTimeInterface) {
            return $object;
        }

        return $object->format(self::FORMAT);
    }
}

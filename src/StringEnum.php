<?php

namespace TerryLonesski\LaravelStringEnums;

use BenSampo\Enum\Enum;

class StringEnum extends Enum
{
    public function toArray()
    {
        return $this->key;
    }

    public static function parseDatabase($value)
    {
        if (self::hasKey($value)) {
            return self::fromKey($value)->value;
        }
        return parent::parseDatabase($value);
    }
}

<?php

namespace TerryLonesski\LaravelStringEnums;

use BenSampo\Enum\Enum;

class StringEnum extends Enum
{
    public function toArray()
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->key;
    }
}

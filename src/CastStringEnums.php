<?php

namespace TerryLonesski\LaravelStringEnums;

use BenSampo\Enum\Enum;
use BenSampo\Enum\Exceptions\InvalidEnumKeyException;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait CastStringEnums
{
    /**
     * Get a plain attribute (not a relationship).
     *
     * @param string $key
     * @return mixed
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);

        if ($this->hasEnumCast($key)) {
            $value = $this->castToEnum($key, $value);
        }

        return $value;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setAttribute($key, $value)
    {
        if ($value !== null && $this->hasEnumCast($key)) {
            $enum = $this->casts[$key];

            if ($value instanceof $enum) {
                $this->attributes[$key] = $value->value;
            } else {
                /** @var Enum $enum */
                if (is_string($value) && !$enum::hasKey($value)) {
                    throw new InvalidEnumKeyException($key, $enum);
                } elseif (is_integer($value) && !$enum::hasValue($value)) {
                    throw new InvalidEnumMemberException($value, new $enum($value));
                }

                $this->attributes[$key] = $enum::coerce($value)->value;
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Determine whether an attribute should be cast to a enum.
     *
     * @param string $key
     * @return bool
     */
    public function hasEnumCast($key): bool
    {
        // This can happen if this trait is added to the model
        // but no enum casts have been added yet
        if ($this->casts === null) {
            return false;
        }

        return array_key_exists($key, $this->casts);
    }

    /**
     * Casts the given key to an enum instance
     *
     * @param string $key
     * @param mixed $value
     * @return Enum|null
     */
    protected function castToEnum($key, $value): ?Enum
    {
        /** @var Enum $enum */
        $enum = $this->casts[$key];

        if ($value === null || $value instanceof Enum) {
            return $value;
        } else {
            return $enum::fromValue($value);
        }
    }
}

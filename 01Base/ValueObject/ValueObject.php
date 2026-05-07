<?php

declare(strict_types=1);

namespace Base\ValueObject;

abstract class ValueObject
{
    abstract public function equals(self $other): bool;

    public function __toString(): string
    {
        return (string) $this->value();
    }

    abstract protected function value(): mixed;
}

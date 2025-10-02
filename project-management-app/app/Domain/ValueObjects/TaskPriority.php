<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class TaskPriority
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';
    public const URGENT = 'urgent';

    private const VALID_PRIORITIES = [
        self::LOW,
        self::MEDIUM,
        self::HIGH,
        self::URGENT,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_PRIORITIES, true)) {
            throw new InvalidArgumentException("Invalid task priority: {$value}");
        }

        $this->value = $value;
    }

    public static function low(): self
    {
        return new self(self::LOW);
    }

    public static function medium(): self
    {
        return new self(self::MEDIUM);
    }

    public static function high(): self
    {
        return new self(self::HIGH);
    }

    public static function urgent(): self
    {
        return new self(self::URGENT);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(TaskPriority $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

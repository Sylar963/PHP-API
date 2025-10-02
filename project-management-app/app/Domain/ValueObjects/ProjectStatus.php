<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class ProjectStatus
{
    public const PLANNING = 'planning';
    public const IN_PROGRESS = 'in_progress';
    public const ON_HOLD = 'on_hold';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    private const VALID_STATUSES = [
        self::PLANNING,
        self::IN_PROGRESS,
        self::ON_HOLD,
        self::COMPLETED,
        self::CANCELLED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Invalid project status: {$value}");
        }

        $this->value = $value;
    }

    public static function planning(): self
    {
        return new self(self::PLANNING);
    }

    public static function inProgress(): self
    {
        return new self(self::IN_PROGRESS);
    }

    public static function onHold(): self
    {
        return new self(self::ON_HOLD);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public static function cancelled(): self
    {
        return new self(self::CANCELLED);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(ProjectStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

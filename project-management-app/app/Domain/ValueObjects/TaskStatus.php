<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class TaskStatus
{
    public const TODO = 'todo';
    public const IN_PROGRESS = 'in_progress';
    public const IN_REVIEW = 'in_review';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    private const VALID_STATUSES = [
        self::TODO,
        self::IN_PROGRESS,
        self::IN_REVIEW,
        self::COMPLETED,
        self::CANCELLED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Invalid task status: {$value}");
        }

        $this->value = $value;
    }

    public static function todo(): self
    {
        return new self(self::TODO);
    }

    public static function inProgress(): self
    {
        return new self(self::IN_PROGRESS);
    }

    public static function inReview(): self
    {
        return new self(self::IN_REVIEW);
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

    public function equals(TaskStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Role
{
    public const SUPER_ADMIN = 'super_admin';
    public const PROJECT_MANAGER = 'project_manager';
    public const TEAM_LEAD = 'team_lead';
    public const TEAM_MEMBER = 'team_member';
    public const CLIENT = 'client';

    private const VALID_ROLES = [
        self::SUPER_ADMIN,
        self::PROJECT_MANAGER,
        self::TEAM_LEAD,
        self::TEAM_MEMBER,
        self::CLIENT,
    ];

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, self::VALID_ROLES, true)) {
            throw new InvalidArgumentException("Invalid role: {$value}");
        }

        $this->value = $value;
    }

    public static function superAdmin(): self
    {
        return new self(self::SUPER_ADMIN);
    }

    public static function projectManager(): self
    {
        return new self(self::PROJECT_MANAGER);
    }

    public static function teamLead(): self
    {
        return new self(self::TEAM_LEAD);
    }

    public static function teamMember(): self
    {
        return new self(self::TEAM_MEMBER);
    }

    public static function client(): self
    {
        return new self(self::CLIENT);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(Role $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

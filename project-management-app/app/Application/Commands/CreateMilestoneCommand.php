<?php

declare(strict_types=1);

namespace App\Application\Commands;

class CreateMilestoneCommand
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $dueDate,
        public readonly bool $isCompleted = false
    ) {
    }
}

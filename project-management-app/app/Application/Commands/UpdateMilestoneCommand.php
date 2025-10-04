<?php

declare(strict_types=1);

namespace App\Application\Commands;

class UpdateMilestoneCommand
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $dueDate = null,
        public readonly ?bool $isCompleted = null
    ) {
    }
}

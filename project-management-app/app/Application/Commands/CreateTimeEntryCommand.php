<?php

declare(strict_types=1);

namespace App\Application\Commands;

class CreateTimeEntryCommand
{
    public function __construct(
        public readonly string $userId,
        public readonly string $taskId,
        public readonly string $startTime,
        public readonly string $description = '',
        public readonly ?string $endTime = null
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Commands;

class UpdateTimeEntryCommand
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $description = null,
        public readonly ?string $endTime = null
    ) {
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $this->log('UserSeeder ✓ (' . count($users) . ' users)');
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class UserSeeder extends Seeder
{
    /**
     * Data user default yang di-seed.
     * Idempoten: INSERT IGNORE → aman dijalankan berkali-kali.
     * Password di-hash dengan bcrypt.
     */
    private array $users = [
        ['username' => 'admin',          'password' => 'admin123',    'role' => 'admin'],
        ['username' => 'admin_tu',       'password' => 'admin123',    'role' => 'admin_tu'],
        ['username' => 'kepala_sekolah', 'password' => 'pimpinan123', 'role' => 'kepala_sekolah'],
        ['username' => 'pelanggan1',     'password' => 'pelanggan123','role' => 'pelanggan'],
    ];

    public function run(): void
    {
        foreach ($this->users as $data) {
            $this->insertIgnore('users', [
                'username'   => $data['username'],
                'password'   => password_hash($data['password'], PASSWORD_BCRYPT),
                'role'       => $data['role'],
                'is_active'  => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->log('UserSeeder ✓ (' . count($this->users) . ' users)');
    }
}

<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class UserSeeder extends Seeder
{
    /**
     * Data user default yang di-seed.
     * Idempoten: INSERT IGNORE → aman dijalankan berkali-kali.
     * Password di-hash dengan bcrypt.
     *
     * Role yang tersedia: admin, pelanggan, pimpinan
     */
    private array $users = [
        [
            'nama'     => 'Administrator',
            'username' => 'admin',
            'password' => 'admin123',
            'role'     => 'admin',
        ],
        [
            'nama'     => 'Pimpinan Utama',
            'username' => 'pimpinan',
            'password' => 'pimpinan123',
            'role'     => 'pimpinan',
        ],
        [
            'nama'     => 'Pelanggan Satu',
            'username' => 'pelanggan1',
            'password' => 'pelanggan123',
            'role'     => 'pelanggan',
        ],
        [
            'nama'     => 'Pelanggan Dua',
            'username' => 'pelanggan2',
            'password' => 'pelanggan123',
            'role'     => 'pelanggan',
        ],
    ];

    public function run(): void
    {
        foreach ($this->users as $data) {
            $this->insertIgnore('users', [
                'nama'       => $data['nama'],
                'username'   => $data['username'],
                'password'   => password_hash($data['password'], PASSWORD_BCRYPT),
                'role'       => $data['role'],
                'is_active'  => 1,
                'is_deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->log('UserSeeder ✓ (' . count($this->users) . ' users: admin, pimpinan, pelanggan)');
    }
}

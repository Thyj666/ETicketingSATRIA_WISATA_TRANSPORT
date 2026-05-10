<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

/**
 * UserSeeder
 *
 * Seed tabel `users` DAN tabel profil masing-masing role:
 *   - role 'admin'     → tabel `admin`
 *   - role 'pimpinan'  → tabel `pimpinan`
 *   - role 'pelanggan' → tabel `pelanggan`
 *
 * Idempoten: INSERT IGNORE → aman dijalankan berkali-kali.
 * Password di-hash dengan bcrypt.
 */
class UserSeeder extends Seeder
{
    /**
     * Data user default yang di-seed.
     * Setiap entry WAJIB memiliki key: nama, username, password, role.
     * Opsional: email, no_telp, alamat (untuk tabel profil).
     */
    private array $users = [
        [
            'nama'     => 'Administrator',
            'username' => 'admin',
            'password' => 'admin123',
            'role'     => 'admin',
            'email'    => 'admin@example.com',
            'no_telp'  => null,
            'alamat'   => null,
        ],
        [
            'nama'     => 'Pimpinan Utama',
            'username' => 'pimpinan',
            'password' => 'pimpinan123',
            'role'     => 'pimpinan',
            'email'    => 'pimpinan@example.com',
            'no_telp'  => null,
            'alamat'   => null,
        ],
        [
            'nama'     => 'Pelanggan Satu',
            'username' => 'pelanggan1',
            'password' => 'pelanggan123',
            'role'     => 'pelanggan',
            'email'    => 'pelanggan1@example.com',
            'no_telp'  => null,
            'alamat'   => null,
        ],
        [
            'nama'     => 'Pelanggan Dua',
            'username' => 'pelanggan2',
            'password' => 'pelanggan123',
            'role'     => 'pelanggan',
            'email'    => 'pelanggan2@example.com',
            'no_telp'  => null,
            'alamat'   => null,
        ],
    ];

    /** Map role → nama tabel profil */
    private array $profileTable = [
        'admin'     => 'admin',
        'pimpinan'  => 'pimpinan',
        'pelanggan' => 'pelanggan',
    ];

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->users as $data) {
            // ----------------------------------------------------------------
            // 1. Insert ke tabel `users`
            // ----------------------------------------------------------------
            $this->insertIgnore('users', [
                'nama'       => $data['nama'],
                'username'   => $data['username'],
                'password'   => password_hash($data['password'], PASSWORD_BCRYPT),
                'role'       => $data['role'],
                'is_active'  => 1,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // ----------------------------------------------------------------
            // 2. Ambil ID user yang baru saja di-insert (atau sudah ada)
            // ----------------------------------------------------------------
            $userId = $this->findId('users', 'username', $data['username']);

            if ($userId === null) {
                $this->log("UserSeeder ✗ Tidak bisa menemukan user_id untuk username: {$data['username']}");
                continue;
            }

            // ----------------------------------------------------------------
            // 3. Insert ke tabel profil sesuai role (admin / pimpinan / pelanggan)
            // ----------------------------------------------------------------
            $table = $this->profileTable[$data['role']] ?? null;

            if ($table === null) {
                $this->log("UserSeeder ⚠ Role tidak dikenal, skip profil: {$data['role']}");
                continue;
            }

            $this->insertIgnore($table, [
                'user_id'    => $userId,
                'nama'       => $data['nama'],
                'email'      => $data['email'] ?? null,
                'no_telp'    => $data['no_telp'] ?? null,
                'alamat'     => $data['alamat'] ?? null,
                'foto'       => null,
                'is_active'  => 1,
                'is_deleted' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->log('UserSeeder ✓ (' . count($this->users) . ' users + profil: admin, pimpinan, pelanggan)');
    }
}

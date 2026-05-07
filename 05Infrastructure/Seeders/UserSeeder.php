<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Jika admin sudah ada, anggap seeder sudah pernah dijalankan
        if ($this->findId('users', 'username', 'admin') !== null) {
            $this->log('UserSeeder — sudah ada, dilewati.');
            return;
        }

        // Lookup jabatan_id
        $jabAdminTu       = $this->findId('jabatan', 'nama_jabatan', 'Admin TU');
        $jabKepalaSekolah = $this->findId('jabatan', 'nama_jabatan', 'Kepala Sekolah');
        $jabGuruMtk       = $this->findId('jabatan', 'nama_jabatan', 'Guru Matematika');
        $jabGuruBindo     = $this->findId('jabatan', 'nama_jabatan', 'Guru Bahasa Indonesia');
        $jabStaffTu       = $this->findId('jabatan', 'nama_jabatan', 'Staff TU');

        $users = [
            [
                'nama'          => 'Administrator TU',
                'username'      => 'admin',
                'password'      => password_hash('admin123', PASSWORD_BCRYPT),
                'email'         => 'admin@sman7bungo.sch.id',
                'nip'           => '198001012005011001',
                'role'          => 'admin_tu',
                'jabatan_id'    => $jabAdminTu,
                'gaji_pokok'    => 3_200_000,
                'jenis_kelamin' => 'L',
                'is_active'     => 1,
            ],
            [
                'nama'          => 'Drs. H. Ahmad Fauzi',
                'username'      => 'kepala',
                'password'      => password_hash('kepala123', PASSWORD_BCRYPT),
                'email'         => 'kepsek@sman7bungo.sch.id',
                'nip'           => '196805151990031002',
                'role'          => 'kepala_sekolah',
                'jabatan_id'    => $jabKepalaSekolah,
                'gaji_pokok'    => 4_500_000,
                'jenis_kelamin' => 'L',
                'is_active'     => 1,
            ],
            [
                'nama'          => 'Siti Rahmah, S.Pd',
                'username'      => 'guru1',
                'password'      => password_hash('guru123', PASSWORD_BCRYPT),
                'email'         => 'siti@sman7bungo.sch.id',
                'nip'           => '197203142000122001',
                'role'          => 'guru',
                'jabatan_id'    => $jabGuruMtk,
                'gaji_pokok'    => 3_200_000,
                'jenis_kelamin' => 'P',
                'is_active'     => 1,
            ],
            [
                'nama'          => 'Budi Santoso, S.Pd',
                'username'      => 'guru2',
                'password'      => password_hash('guru123', PASSWORD_BCRYPT),
                'email'         => 'budi@sman7bungo.sch.id',
                'nip'           => '197510202003121003',
                'role'          => 'guru',
                'jabatan_id'    => $jabGuruBindo,
                'gaji_pokok'    => 3_200_000,
                'jenis_kelamin' => 'L',
                'is_active'     => 1,
            ],
            [
                'nama'          => 'Dewi Lestari',
                'username'      => 'staff1',
                'password'      => password_hash('staff123', PASSWORD_BCRYPT),
                'email'         => 'dewi@sman7bungo.sch.id',
                'nip'           => '198504172010012005',
                'role'          => 'staff',
                'jabatan_id'    => $jabStaffTu,
                'gaji_pokok'    => 2_200_000,
                'jenis_kelamin' => 'P',
                'is_active'     => 1,
            ],
        ];

        $now = date('Y-m-d H:i:s');
        foreach ($users as $user) {
            $this->insertIgnore('users', array_merge($user, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->log('UserSeeder ✓ (' . count($users) . ' users)');
    }
}

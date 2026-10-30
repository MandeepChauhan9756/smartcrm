<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $email = strtolower(trim((string) env('CRM_ADMIN_EMAIL')));
        $password = (string) env('CRM_ADMIN_PASSWORD');
        $name = trim((string) (env('CRM_ADMIN_NAME') ?: 'CRM Admin'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)
            || strlen($password) < 12
            || $name === '') {
            throw new \RuntimeException(
                'Set CRM_ADMIN_EMAIL, CRM_ADMIN_PASSWORD (12+ characters), '
                . 'and CRM_ADMIN_NAME in .env before seeding.'
            );
        }

        $existing = $this->db->table('users')
            ->where('email', $email)
            ->countAllResults();

        if ($existing > 0) {
            throw new \RuntimeException(
                'Admin email already exists. No account was changed.'
            );
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('users')->insert([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => 'admin',
            'is_active'     => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }
}
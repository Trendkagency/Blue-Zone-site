<?php

namespace App\View\ViewModels;

class UserViewModel
{
    /**
     * Get all administrative users.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Tariq Mansour',
                'email' => 'admin@bluezone.com',
                'mobile' => '+966 50 000 1122',
                'role' => 'Super Admin',
                'role_id' => 1,
                'status' => 'active',
                'last_login_at' => '2026-09-03 14:20:00',
                'created_at' => '2025-10-01',
            ],
            [
                'id' => 2,
                'name' => 'Omar Al-Mansoor',
                'email' => 'omar.m@bluezone.com',
                'mobile' => '+966 55 111 2233',
                'role' => 'Inventory Manager',
                'role_id' => 2,
                'status' => 'active',
                'last_login_at' => '2026-09-02 14:25:12',
                'created_at' => '2025-10-15',
            ],
            [
                'id' => 3,
                'name' => 'Sarah Jenkins',
                'email' => 'sarah.j@bluezone.com',
                'mobile' => '+966 54 222 3344',
                'role' => 'Sales User (POS / Retail)',
                'role_id' => 3,
                'status' => 'active',
                'last_login_at' => '2026-09-01 18:45:00',
                'created_at' => '2025-11-01',
            ],
            [
                'id' => 4,
                'name' => 'Dr. Elena Rostova',
                'email' => 'elena.r@bluezone.com',
                'mobile' => '+966 56 333 4455',
                'role' => 'Content Manager',
                'role_id' => 4,
                'status' => 'active',
                'last_login_at' => '2026-08-31 11:10:05',
                'created_at' => '2026-01-10',
            ],
        ];
    }
}

<?php

namespace App\View\ViewModels;

class CustomerViewModel
{
    /**
     * Get all mock customers.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Dr. Zaid Al-Harbi',
                'email' => 'zaid.harbi@example.com',
                'phone' => '+966 50 123 4567',
                'city' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'orders_count' => 8,
                'total_spent' => 1240.50,
                'tier' => 'Platinum Biohacker',
                'status' => 'active',
                'registered_at' => '2025-11-12',
                'last_order_at' => '2026-09-02',
                'addresses' => [
                    [
                        'id' => 1,
                        'is_default' => true,
                        'title' => 'Primary Residence (Villa)',
                        'recipient' => 'Dr. Zaid Al-Harbi',
                        'street' => 'King Fahd Road, Al Olaya, Villa 42',
                        'city' => 'Riyadh',
                        'country' => 'Saudi Arabia',
                        'postal_code' => '12213',
                        'phone' => '+966 50 123 4567',
                    ],
                    [
                        'id' => 2,
                        'is_default' => false,
                        'title' => 'Executive Medical Office',
                        'recipient' => 'Dr. Zaid Al-Harbi (Medical Suite)',
                        'street' => 'King Khalid Hospital Rd, Suite 402',
                        'city' => 'Riyadh',
                        'country' => 'Saudi Arabia',
                        'postal_code' => '11564',
                        'phone' => '+966 50 123 4567',
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Layla Bint Sultan',
                'email' => 'layla.sultan@example.com',
                'phone' => '+971 55 987 6543',
                'city' => 'Dubai',
                'country' => 'United Arab Emirates',
                'orders_count' => 4,
                'total_spent' => 612.00,
                'tier' => 'Gold Member',
                'status' => 'active',
                'registered_at' => '2026-01-20',
                'last_order_at' => '2026-09-01',
                'addresses' => [
                    [
                        'id' => 3,
                        'is_default' => true,
                        'title' => 'Penthouse Dubai',
                        'recipient' => 'Layla Bint Sultan',
                        'street' => 'Marina Gate 1, Apt 1804',
                        'city' => 'Dubai',
                        'country' => 'United Arab Emirates',
                        'postal_code' => '00000',
                        'phone' => '+971 55 987 6543',
                    ],
                ],
            ],
            [
                'id' => 3,
                'name' => 'Tariq Al-Ghamdi',
                'email' => 'tariq.g@example.com',
                'phone' => '+966 54 332 1199',
                'city' => 'Jeddah',
                'country' => 'Saudi Arabia',
                'orders_count' => 2,
                'total_spent' => 144.90,
                'tier' => 'Silver Protocol',
                'status' => 'active',
                'registered_at' => '2026-06-15',
                'last_order_at' => '2026-08-30',
                'addresses' => [
                    [
                        'id' => 4,
                        'is_default' => true,
                        'title' => 'Home',
                        'recipient' => 'Tariq Al-Ghamdi',
                        'street' => 'Al Hamra District, Al Andalus St',
                        'city' => 'Jeddah',
                        'country' => 'Saudi Arabia',
                        'postal_code' => '23212',
                        'phone' => '+966 54 332 1199',
                    ],
                ],
            ],
        ];
    }

    /**
     * Find customer by ID.
     */
    public static function find(int $id): ?array
    {
        foreach (self::all() as $c) {
            if ($c['id'] === $id) {
                return $c;
            }
        }
        return self::all()[0] ?? null;
    }
}

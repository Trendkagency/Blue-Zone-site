<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'saved_addresses',
        'wishlist',
        'loyalty_points',
        'total_orders',
        'total_spent',
        'status',
        'notification_preferences',
        'email_verified_at',
        'registered_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
            'registered_at' => 'datetime',
            'total_spent' => 'decimal:2',
            'total_orders' => 'integer',
            'loyalty_points' => 'integer',
            'saved_addresses' => 'array',
            'wishlist' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Compute tier based on loyalty points & total spent.
     */
    public function getTierAttribute(): string
    {
        $points = $this->loyalty_points ?? 0;
        $spent = (float) ($this->total_spent ?? 0);

        if ($spent >= 2000 || $points >= 2000) {
            return app()->getLocale() === 'ar' ? 'فئة النخبة البلاتينية (Platinum Biohacker)' : 'Platinum Biohacker';
        }
        if ($spent >= 500 || $points >= 500) {
            return app()->getLocale() === 'ar' ? 'فئة البروتوكول الذهبي (Gold Cohort)' : 'Gold Protocol Cohort';
        }

        return app()->getLocale() === 'ar' ? 'عضو بروتوكول طول العمر (Longevity Member)' : 'Longevity Member';
    }

    /**
     * Get list of saved delivery addresses (with fallback to primary address).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAddressesList(): array
    {
        $addresses = $this->saved_addresses;
        if (!empty($addresses) && is_array($addresses)) {
            return $addresses;
        }

        return [
            [
                'id' => 1,
                'title' => app()->getLocale() === 'ar' ? 'المقر السكني الرئيسي' : 'Primary Residence',
                'recipient' => $this->name,
                'phone' => $this->phone ?? '+966 50 123 4567',
                'street' => $this->address ?? '742 Longevity Way, King Fahd District',
                'city' => $this->city ?? 'Riyadh',
                'country' => $this->country ?? 'Saudi Arabia',
                'postal_code' => $this->postal_code ?? '12271',
                'is_default' => true,
            ],
        ];
    }

    /**
     * Get default delivery address.
     *
     * @return array<string, mixed>
     */
    public function getDefaultAddress(): array
    {
        $list = $this->getAddressesList();
        foreach ($list as $addr) {
            if (!empty($addr['is_default'])) {
                return $addr;
            }
        }
        return $list[0] ?? [];
    }
}

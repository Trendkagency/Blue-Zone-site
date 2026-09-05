<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'invoice_number', 'channel',
        'customer_name', 'customer_email', 'customer_phone', 'customer_id',
        'date', 'status', 'payment_method', 'payment_status',
        'subtotal', 'discount', 'coupon_code', 'shipping', 'tax', 'total',
        'shipping_address', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'shipping_address' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate visual order progress timeline based on current status.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getTimelineAttribute(): array
    {
        $status = strtolower($this->status ?? 'pending');
        $dateStr = $this->date ? $this->date->format('Y-m-d') : now()->toDateString();
        $isAr = app()->getLocale() === 'ar';

        $steps = [
            [
                'step' => 'placed',
                'status' => $isAr ? 'تم استلام وتأكيد الطلب' : 'Order Placed & Verified',
                'timestamp' => "{$dateStr} 09:30 AM",
                'note' => $isAr ? 'تم التحقق من بيانات الدفع والتركيبات المطلوبة.' : 'Verified payment tender and clinical formulation reservation.',
                'completed' => true,
                'icon' => 'fa-clipboard-check',
            ],
            [
                'step' => 'processing',
                'status' => $isAr ? 'التجهيز الصيدلاني المخبري' : 'Clinical Compounding & Quality Check',
                'timestamp' => "{$dateStr} 11:45 AM",
                'note' => $isAr ? 'فحص الجودة والتغليف المحكم ومطابقة الدفعة.' : 'Passed cleanroom batch verification and molecular sealing.',
                'completed' => in_array($status, ['processing', 'shipped', 'delivered'], true),
                'icon' => 'fa-flask-vial',
            ],
            [
                'step' => 'shipped',
                'status' => $isAr ? 'خرج للشحن بسلسلة التبريد' : 'Dispatched with Cold-Chain Courier',
                'timestamp' => "{$dateStr} 02:15 PM",
                'note' => $isAr ? 'شحن مبرد بدرجة حرارة محكومة (15-25°C) مع التتبع المباشر.' : 'Temperature-controlled active dispatch (15-25°C) with real-time GPS tracking.',
                'completed' => in_array($status, ['shipped', 'delivered'], true),
                'icon' => 'fa-truck-fast',
            ],
            [
                'step' => 'delivered',
                'status' => $isAr ? 'تم التسليم بنجاح للعميل' : 'Handed Over & Delivered',
                'timestamp' => "{$dateStr} 05:30 PM",
                'note' => $isAr ? 'تم تسليم الشحنة للعميل المكرم وفق بروتوكول الاستلام.' : 'Signature secured and successfully delivered to customer destination.',
                'completed' => $status === 'delivered',
                'icon' => 'fa-circle-check',
            ],
        ];

        if ($status === 'cancelled') {
            $steps[] = [
                'step' => 'cancelled',
                'status' => $isAr ? 'تم إلغاء الطلب واسترجاع المخزون' : 'Order Cancelled & Restocked',
                'timestamp' => now()->format('Y-m-d H:i'),
                'note' => $isAr ? 'تم إلغاء الطلب واسترجاع المبلغ وعناصر المخزون بنجاح.' : 'Order was cancelled and inventory buffer safely replenished.',
                'completed' => true,
                'icon' => 'fa-circle-xmark',
            ];
        }

        return $steps;
    }
}

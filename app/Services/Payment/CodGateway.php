<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;

class CodGateway implements PaymentGatewayInterface
{
    public function getId(): string
    {
        return 'cod';
    }

    public function getName(): string
    {
        return app()->getLocale() === 'ar' ? 'الدفع عند الاستلام (COD)' : 'Cash on Delivery (COD)';
    }

    public function isEnabled(): bool
    {
        return (bool) Setting::get(
            'payment_cod_enabled',
            Setting::get('enable_cod', config('payment.gateways.cod.enabled', true))
        );
    }

    public function processPayment(Order $order, array $payload = []): array
    {
        $transactionId = 'COD-' . ($order->order_number ?? strtoupper(uniqid()));

        $order->update([
            'payment_method' => 'cod',
            'payment_gateway' => 'cod',
            'payment_status' => 'Pending',
            'status' => 'Confirmed',
            'payment_transaction_id' => $transactionId,
            'payment_payload' => [
                'type' => 'cash_on_delivery',
                'placed_at' => now()->toIso8601String(),
                'extra_fee' => (float) Setting::get('payment_cod_extra_fee', config('payment.gateways.cod.extra_fee', 0)),
            ],
        ]);

        return [
            'success' => true,
            'status' => 'Pending',
            'transaction_id' => $transactionId,
            'redirect_url' => route('customer.checkout.confirmation', $order->order_number),
            'message' => app()->getLocale() === 'ar'
                ? 'تم استلام وتأكيد طلبك بنجاح! سيتم الدفع عند استلام الشحنة.'
                : 'Order confirmed successfully! Payment will be collected upon white-glove delivery.',
            'meta' => [
                'gateway' => 'cod',
            ],
        ];
    }

    public function handleWebhook(Request $request): array
    {
        return [
            'valid' => true,
            'event_type' => 'cod.info',
            'status' => 'acknowledged',
        ];
    }
}

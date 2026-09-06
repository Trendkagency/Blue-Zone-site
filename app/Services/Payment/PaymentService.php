<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Setting;
use InvalidArgumentException;

class PaymentService
{
    /**
     * Cache of instantiated gateway drivers.
     *
     * @var array<string, PaymentGatewayInterface>
     */
    protected static array $instances = [];

    /**
     * Map of available gateway identifiers to driver classes.
     *
     * @var array<string, class-string<PaymentGatewayInterface>>
     */
    protected static array $drivers = [
        'stripe' => StripeGateway::class,
        'cod' => CodGateway::class,
    ];

    /**
     * Resolve a payment gateway driver instance.
     */
    public static function gateway(?string $gateway = null): PaymentGatewayInterface
    {
        $name = strtolower(trim($gateway ?: self::getDefaultGateway()));

        // Map common aliases
        if (in_array($name, ['card', 'credit_card', 'stripe', 'apple_pay'], true)) {
            $name = 'stripe';
        } elseif (in_array($name, ['cod', 'cash_on_delivery', 'cash'], true)) {
            $name = 'cod';
        }

        if (!isset(self::$drivers[$name])) {
            throw new InvalidArgumentException("Unsupported payment gateway: [{$name}].");
        }

        if (!isset(self::$instances[$name])) {
            $class = self::$drivers[$name];
            self::$instances[$name] = new $class();
        }

        return self::$instances[$name];
    }

    /**
     * Get all active and configured payment gateways.
     *
     * @return array<string, array{id: string, name: string, enabled: bool, driver: PaymentGatewayInterface}>
     */
    public static function getActiveGateways(): array
    {
        $active = [];

        foreach (self::$drivers as $id => $class) {
            $instance = self::gateway($id);
            if ($instance->isEnabled()) {
                $active[$id] = [
                    'id' => $id,
                    'name' => $instance->getName(),
                    'enabled' => true,
                    'driver' => $instance,
                ];
            }
        }

        return $active;
    }

    /**
     * Get the configured default gateway.
     */
    public static function getDefaultGateway(): string
    {
        return (string) Setting::get(
            'payment_default_gateway',
            config('payment.default', 'stripe')
        );
    }

    /**
     * Process checkout payment for an order through the specified or default gateway.
     *
     * @param string|null $gateway
     * @param Order $order
     * @param array<string, mixed> $payload
     * @return array{
     *     success: bool,
     *     status: string,
     *     transaction_id?: string|null,
     *     redirect_url?: string|null,
     *     message?: string|null,
     *     meta?: array<string, mixed>
     * }
     */
    public static function process(?string $gateway, Order $order, array $payload = []): array
    {
        $driver = self::gateway($gateway);

        if (!$driver->isEnabled()) {
            throw new InvalidArgumentException(app()->getLocale() === 'ar'
                ? "بوابة الدفع المحددة غير مفعلة حالياً."
                : "The selected payment gateway is currently disabled.");
        }

        return $driver->processPayment($order, $payload);
    }
}

<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Get the identifier key of the gateway (e.g. 'stripe', 'cod').
     */
    public function getId(): string;

    /**
     * Get the user-friendly title of the gateway.
     */
    public function getName(): string;

    /**
     * Determine if the gateway is currently active and ready to take orders.
     */
    public function isEnabled(): bool;

    /**
     * Process checkout payment initialization for an order.
     *
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
    public function processPayment(Order $order, array $payload = []): array;

    /**
     * Verify and handle an incoming webhook request from the gateway.
     *
     * @param Request $request
     * @return array{
     *     valid: bool,
     *     event_type: string,
     *     order_number?: string|null,
     *     transaction_id?: string|null,
     *     status?: string|null,
     *     payload?: array<string, mixed>,
     *     error?: string|null
     * }
     */
    public function handleWebhook(Request $request): array;
}

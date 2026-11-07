<?php

namespace App\Http\Controllers;

use App\Services\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming gateway webhook notification or connectivity check.
     */
    public function handle(Request $request, string $gateway)
    {
        // Handle GET requests (Health check / browser connectivity check)
        if ($request->isMethod('GET')) {
            $supportedGateways = ['stripe', 'tamara', 'tabby', 'hyperpay', 'geidea', 'myfatoorah', 'cod'];
            $gatewayKey = strtolower(trim($gateway));
            $isSupported = in_array($gatewayKey, $supportedGateways, true);

            $payload = [
                'status' => $isSupported ? 'active' : 'unsupported_gateway',
                'gateway' => $gatewayKey,
                'message' => $isSupported
                    ? "Payment webhook listener for [{$gatewayKey}] is active and operational. Webhook event payloads must be transmitted via HTTP POST with valid cryptographic signatures."
                    : "Gateway [{$gateway}] is not registered in the system.",
                'accepted_methods' => ['POST'],
                'endpoint' => $request->url(),
                'timestamp' => now()->toIso8601String(),
                'system' => 'Blue Zone BZ-OS Payment Subsystem',
            ];

            if ($request->expectsJson() || !$request->acceptsHtml()) {
                return response()->json($payload, $isSupported ? 200 : 404);
            }

            return response()->view('payment.webhook_status', [
                'gateway' => $gatewayKey,
                'isSupported' => $isSupported,
                'status' => $isSupported ? 'active' : 'unsupported',
                'endpoint' => $request->url(),
                'timestamp' => now(),
            ], $isSupported ? 200 : 404);
        }

        try {
            $driver = PaymentService::gateway($gateway);
        } catch (\Throwable $e) {
            Log::warning("Received webhook for unknown gateway [{$gateway}]");
            return response()->json([
                'received' => false,
                'error' => "Unsupported gateway: {$gateway}",
            ], 404);
        }

        $result = $driver->handleWebhook($request);

        if (!$result['valid']) {
            Log::warning("Payment webhook verification failed for [{$gateway}]", [
                'error' => $result['error'] ?? 'Unknown verification error',
            ]);

            return response()->json([
                'received' => false,
                'error' => $result['error'] ?? 'Webhook verification failed',
            ], 400);
        }

        Log::info("Payment webhook successfully processed for [{$gateway}]", [
            'event' => $result['event_type'],
            'order_number' => $result['order_number'] ?? null,
            'status' => $result['status'] ?? null,
        ]);

        return response()->json([
            'received' => true,
            'gateway' => $gateway,
            'event' => $result['event_type'],
            'status' => $result['status'] ?? 'processed',
        ], 200);
    }

    /**
     * Admin health check simulation for webhook configuration.
     */
    public function simulate(Request $request): JsonResponse
    {
        $gateway = $request->input('gateway', 'stripe');
        $orderNumber = $request->input('order_number');

        $payload = [
            'id' => 'evt_sim_' . uniqid(),
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id' => 'cs_sim_' . uniqid(),
                    'client_reference_id' => $orderNumber,
                    'metadata' => [
                        'order_number' => $orderNumber,
                    ],
                    'payment_status' => 'paid',
                ],
            ],
        ];

        $simulatedRequest = Request::create(
            "/webhooks/payment/{$gateway}",
            'POST',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );
        $simulatedRequest->query->set('simulate_test', '1');

        return $this->handle($simulatedRequest, $gateway);
    }
}

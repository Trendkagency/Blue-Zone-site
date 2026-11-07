<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    public function test_get_payment_webhook_stripe_returns_200_status()
    {
        $response = $this->get('/webhooks/payment/stripe');

        $response->assertStatus(200);
        $response->assertSee('STRIPE Payment Webhook');
        $response->assertSee('Webhook Listener Active');
    }

    public function test_get_payment_webhook_json_returns_structured_payload()
    {
        $response = $this->getJson('/webhooks/payment/stripe');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'active',
            'gateway' => 'stripe',
            'accepted_methods' => ['POST'],
        ]);
    }

    public function test_get_unsupported_gateway_returns_404()
    {
        $response = $this->getJson('/webhooks/payment/invalid_gateway');

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'unsupported_gateway',
            'gateway' => 'invalid_gateway',
        ]);
    }
}

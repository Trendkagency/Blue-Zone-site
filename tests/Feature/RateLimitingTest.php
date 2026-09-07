<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    public function test_cart_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('cart');
        $this->assertNotNull($limiter);

        $request = Request::create('/cart/add', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(30, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }

    public function test_login_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('login');
        $this->assertNotNull($limiter);

        $request = Request::create('/login', 'POST', ['email' => 'client@bluezone.com']);
        $limit = $limiter($request);

        $this->assertEquals(5, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }

    public function test_register_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('register');
        $this->assertNotNull($limiter);

        $request = Request::create('/register', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(3, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }

    public function test_coupon_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('coupon');
        $this->assertNotNull($limiter);

        $request = Request::create('/cart/coupon', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(5, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }

    public function test_checkout_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('checkout');
        $this->assertNotNull($limiter);

        $request = Request::create('/checkout', 'POST');
        $limit = $limiter($request);

        $this->assertEquals(5, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }

    public function test_polling_rate_limiter_is_configured(): void
    {
        $limiter = RateLimiter::limiter('polling');
        $this->assertNotNull($limiter);

        $request = Request::create('/admin/notifications', 'GET');
        $limit = $limiter($request);

        $this->assertEquals(60, $limit->maxAttempts);
        $this->assertEquals(60, $limit->decaySeconds);
    }
}

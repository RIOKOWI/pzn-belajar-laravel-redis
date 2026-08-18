<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class RateLimitingTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_rate_limit(): void
    {
        $success = RateLimiter::attempt("send-message-1", 5, function(){ // kalo 5x percobaan akan di tolak default selama 60s
            echo "send message 1";
        }, 10); // kalo ini di tolak selama 10s
        self::assertTrue($success);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisTest extends TestCase
{
    
    public function test_ping(): void
    {
        $redis = Redis::command("ping");
        self::assertEquals("PONG", $redis);

        $redis2 = Redis::ping();
        self::assertEquals("PONG", $redis2);
    }

    public function test_string(): void
    {
        Redis::setex("nama", 3, "rio"); // set expired (key,exp,value)
        $response = Redis::get("nama");
        self::assertEquals("rio", $response);
        
        sleep(5);
        
        $response2 = Redis::get("nama");
        self::assertEquals(null, $response2);
    }
}

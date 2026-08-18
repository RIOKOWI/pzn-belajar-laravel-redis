<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // public function test_example(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // Cache
    public function test_cache(): void
    {
        Cache::put("name", "Rio", 2);
        Cache::put("address", "Tionghoa", 2);

        $response = Cache::get("name");
        self::assertEquals("Rio", $response);
        $response = Cache::get("address");
        self::assertEquals("Tionghoa", $response);
        // semua data di atas di simpan di redis
        
        sleep(6); // tunggu 6s agar values dalam key hilang
        
        $result = Cache::get("name");
        self::assertNull($result);
        $result = Cache::get("address");
        self::assertNull($result);
        // cek values lagi
    }
}

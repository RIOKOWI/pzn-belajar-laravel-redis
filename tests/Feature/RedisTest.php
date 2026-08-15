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
        $response = Redis::get("nama"); // ambil value dari key
        self::assertEquals("rio", $response); // cek response
        
        sleep(5); // tunggu 5 detik
        
        $response2 = Redis::get("nama"); // cek value dari key
        self::assertNull($response2); // cek response
    }

    public function test_list(): void
    {
        Redis::del("nama");

        // rpush = masukin value ke key ke sebelah kanan
        Redis::rpush("nama", "Rio");
        Redis::rpush("nama", "Achyar");
        Redis::rpush("nama", "3 Periode"); // ["Rio", "Achyar", "3 Periode"]

        $response = Redis::lrange("nama", 0, -1); // ambil isi semua data list dari awal sampai akhir
        self::assertEquals(["Rio", "Achyar", "3 Periode"], $response); // hasil

        // lpop == ambil + hapus
        self::assertEquals("Rio", Redis::lpop("nama")); // ["Rio", "Achyar", "3 Periode"] == ambil Rio dan hapus
        self::assertEquals("Achyar", Redis::lpop("nama")); // ["Achyar", "3 Periode"] == ambil Achyar dan hapus
        self::assertEquals("3 Periode", Redis::lpop("nama")); // ["3 Periode"] == ambil 3 Periode dan hapus
    }
}

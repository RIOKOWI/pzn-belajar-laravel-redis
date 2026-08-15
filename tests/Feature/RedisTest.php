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
        Redis::setex("names", 3, "rio"); // set expired (key,exp,value)
        $response = Redis::get("names"); // ambil value dari key
        self::assertEquals("rio", $response); // cek response
        
        sleep(5); // tunggu 5 detik
        
        $response2 = Redis::get("names"); // cek value dari key
        self::assertNull($response2); // cek response
    }

    public function test_list(): void
    {
        Redis::del("names");

        // rpush = masukin value ke key ke sebelah kanan
        Redis::rpush("names", "Rio");
        Redis::rpush("names", "Achyar");
        Redis::rpush("names", "3 Periode"); // ["Rio", "Achyar", "3 Periode"]

        $response = Redis::lrange("names", 0, -1); // ambil isi semua data list dari awal sampai akhir
        self::assertEquals(["Rio", "Achyar", "3 Periode"], $response); // hasil

        // lpop == ambil + hapus
        self::assertEquals("Rio", Redis::lpop("names")); // ["Rio", "Achyar", "3 Periode"] == ambil Rio dan hapus
        self::assertEquals("Achyar", Redis::lpop("names")); // ["Achyar", "3 Periode"] == ambil Achyar dan hapus
        self::assertEquals("3 Periode", Redis::lpop("names")); // ["3 Periode"] == ambil 3 Periode dan hapus
    }


    // Set
    public function test_set(): void
    {
        Redis::del("names"); // hapus value di dalam key names

        Redis::sadd("names", "Rio");
        Redis::sadd("names", "Rio"); // kalo ada value yang sama di dalam key data yang sama tidak akan terganti
        Redis::sadd("names", "Achyar");
        Redis::sadd("names", "Achyar"); // kalo ada value yang sama di dalam key data yang sama tidak akan terganti
        Redis::sadd("names", "Cungpruy");
        Redis::sadd("names", "Cungpruy"); // kalo ada value yang sama di dalam key data yang sama tidak akan terganti

        $response = Redis::smembers("names"); // mengembalikan semua value yang ada di dalam key
        self::assertEquals(["Rio", "Achyar", "Cungpruy"], $response);
    }
}

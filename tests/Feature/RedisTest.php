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

    // string
    public function test_string(): void
    {
        Redis::setex("names", 3, "rio"); // set expired (key,exp,value)
        $response = Redis::get("names"); // ambil value dari key
        self::assertEquals("rio", $response); // cek response
        
        sleep(5); // tunggu 5 detik
        
        $response2 = Redis::get("names"); // cek value dari key
        self::assertNull($response2); // cek response
    }

    // List
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

    // Sorted Set
    public function test_sorted_set(): void
    {
        Redis::del("names"); // hapus value di dalam key names
        Redis::zadd("names", 100, "Rio"); // zadd(key, score, value). arti score di sini di urutkan secara asc pada yang mana atinya kalo score rio di paling tinggi berarti rio berada di ujung index/paling akhir
        Redis::zadd("names", 85, "Achyar");
        Redis::zadd("names", 65, "Pruy");

        $response = Redis::zrange("names", 0, -1); // ambil isi semua data list dari awal sampai akhir/ ambil data dari index pertama sampai akhir
        self::assertEquals(["Pruy", "Achyar", "Rio"], $response); // output sesuai skor paling rendah
    }

    // Hash
    public function test_hash(): void
    {
        Redis::del("user:1");
        Redis::hset("user:1", "name", "Rio"); // menambah hset(key, field, name)
        Redis::hset("user:1", "email", "cilok@sunda.com");
        Redis::hset("user:1", "age", 1);
        
        $response = Redis::hgetall("user:1"); // ambil semua isi dari hash
        self::assertEquals([
            "name" => "Rio",
            "email" => "cilok@sunda.com",
            "age" => "1", // integer jadi string
        ], $response);
    }
}

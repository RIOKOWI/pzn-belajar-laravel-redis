# Laravel Redis
## Laravel Redis
- Di kelas Redis, kita sudah tahu bahwa Redis adalah salah satu database In Memory yang paling populer di dunia
- Laravel, sejak awal bisa diintegrasikan dengan baik dengan database Redis, bahkan banyak fitur di Laravel bisa menggunakan Redis, seperti Cache, Session, sampai Rate Limiting
- Di kelas ini, kita akan bahas bagaimana cara menggunakan seluruh struktur data di Redis menggunakan Laravel
- Dan diakhir kelas, kita akan bahas fitur-fitur apa saja di Laravel yang menggunakan Redis

## Menambah Library Predis
- Secara default, Redis menggunakan library phpredis (native menggunakan C)
- `https://github.com/phpredis/phpredis`
- Namun, di beberapa kasus, kadang sulit menginstall library phpredis karena membutuhkan kompilasi bahasa C
- Oleh karena itu, di kelas ini kita akan mengunakan library Predis, yang hanya menggunakan kode PHP
- `https://github.com/predis/predis`
```bash
composer require predis/predis
```

# Redis Facade

## Redis Configuration
- Secara default, semua konfigurasi untuk Redis di aplikasi Laravel terdapat di file `config/database.php`
- Kita bisa ubah konfigurasi redis di file tersebut
- Kita bisa ubah yang defaultnya menggunakan phpredis menjadi predis pada konfigurasi file nya


## Redis Facade
- Untuk menggunakan Redis di Laravel, kita bisa menggunakan Redis Facade yang secara otomatis membaca konfigurasi dari konfigurasi redis di Laravel
- `https://laravel.com/api/10.x/Illuminate/Support/Facades/Redis.html`


## Redis Command
- Untuk mengirim perintah ke Redis, kita bisa menggunakan method command() di Redis Facade
- Atau bisa langsung menggunakan nama method sesuai dengan command di Redis
- Redis Facade akan menggunakan Magic Method untuk mengubah nama method secara otomatis menjadi nama perintah yang dikirim ke Redis

## Docs
[REDIS MAGIC METHOD DICUMENTATION](https://redis.io/docs/latest/commands)

# String
## String
- Struktur data yang sering digunakan di Redis adalah String
- Command yang sering kita gunakan adalah menggunakan set(), setEx(), get(), mGet(), dan lain-lain
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
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
```

# List 
- Sekarang kita akan menggunakan struktur data List 
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
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
```

# Set
## Set
- Sekarang kita akan menggunakan struktur data Set 
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
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
```

# Sorted Set
- Sekarang kita akan menggunakan struktur data Sorted Set
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
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
```

# Hash
- Sekarang kita akan menggunakan struktur data Hash 
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
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
```

# Geo Point
- Sekarang kita akan menggunakan struktur data Geo Point


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
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
    // Geo Point
    public function test_geo_point(): void
    {
        // Bersihkan data merchant sebelumnya agar test selalu dimulai dari kondisi yang konsisten.
        Redis::del("merchant");

        // Menyimpan lokasi merchant menggunakan longitude, latitude, dan nama sebagai member.
        Redis::geoadd("merchant", 106.55959818809123, -6.1615071974291435, "warmindo");

        Redis::geoadd( "merchant", 106.5382170524327, -6.125298631266757, "dimsum" );

        // Menghitung jarak antara Warmindo dan Dimsum dalam satuan kilometer.
        $result = Redis::geodist( "merchant", "warmindo", "dimsum", "km" );

        // Memastikan jarak kedua merchant sesuai dengan hasil yang diharapkan.
        self::assertEquals(4.6699, $result);

        // Mencari merchant dalam radius 6 km dari titik koordinat yang ditentukan.
        $result = Redis::geosearch( "merchant", new FromLonLat( 106.55012551225578, -6.145756971084499 ), new ByRadius(6, "km") );

        // Memastikan kedua merchant ditemukan dalam radius pencarian.
        self::assertEqualsCanonicalizing(["dimsum", "warmindo"],$result);
    }
```

# Hyper Log Log
- Sekarang kita akan menggunakan struktur data Hyper Log Log 
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
    // Hyper Log Log
    public function test_hyper_log_log(): void
    {
        Redis::pfadd("pengunjung", "Rio", "Eko", "Chaniago");
        Redis::pfadd("pengunjung", "Rio", "Rizky", "Budi");
        Redis::pfadd("pengunjung", "Rio", "Agung", "Rafi");

        $total = Redis::pfcount("pengunjung"); // mengitung data unique
        self::assertEquals(7 ,$total); // karna rio redundan jadi di hitungnya satu
    }
```

# Pipeline
- Di kelas Redis, kita pernah belajar tentang pipeline, dimana kita bisa mengirim beberapa perintah secara langsung tanpa harus menunggu balasan satu per satu dari Redis
- Hal ini juga bisa dilakukan menggunakan Laravel
- Kita bisa menggunakan method pipeline(), dimana kita bisa tambahkan callback function yang berisi perintah-perintah yang akan dikerjakan dalam pipeline tersebut
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
    // Pipeline
    public function test_pipeline(): void
    {
        Redis::pipeline(function ($pipeline){ // membuat object pipeline
            $pipeline->setex("name", 5, "Rio"); // setex(key, ttl, value)
            $pipeline->setex("handphone", 5, "097877887");
            $pipeline->setex("address", 5, "ngawi");
        });

        $name = Redis::get("name");
        self::assertEquals("Rio", $name); // cek value
        $hp = Redis::get("handphone");
        self::assertEquals("097877887", $hp);
        $ad = Redis::get("address");
        self::assertEquals("ngawi", $ad);
        
        sleep(6); 
        
        $name = Redis::get("name");
        self::assertEquals(null, $name); // cek null value expired
        $hp = Redis::get("handphone");
        self::assertEquals(null, $hp);
        $ad = Redis::get("address");
        self::assertEquals(null, $ad);

    }
```

# Transaction
- Kita tahu bahwa menggunakan Redis bisa melakukan transaction menggunakan perintah MULTI dan COMMIT
- Untuk melakukan proses Redis Transaction di Laravel, kita tidak perlu melakukan manual MULTI dan COMMIT
- Kita bisa menggunakan method transaction(), dan cara penggunaannya sama seperti method pipeline()
* di File : [RedisTest](tests\Feature\RedisTest.php)
```php
    // Transaction
    public function test_transaction(): void
    {
        Redis::transaction(function ($transaction){
            $transaction->setex("name", 5, "Rio"); // MULTI lalu COMMIT/EXEC
            $transaction->setex("handphone", 5, "097877887");
            $transaction->setex("address", 5, "ngawi");
        });

        $name = Redis::get("name");
        self::assertEquals("Rio", $name); // cek value
        $hp = Redis::get("handphone");
        self::assertEquals("097877887", $hp);
        $ad = Redis::get("address");
        self::assertEquals("ngawi", $ad);
    }
```

# PubSub
- Sekarang kita akan menggunakan fitur PubSub 
* di File : [RedisTest](tests\Feature\RedisTest.php)
* di File : [TestSubscriber Artisan Command](app\Console\Commands\TestSubscriber.php)
## Laravel Command
- Untuk melakukan pengetesan Subscriber, kita akan membuat Laravel Command
- Yaitu fitur untuk membuat perintah berbasis terminal
- Perintah itu nanti akan kita gunakan untuk menjalankan Redis Subscriber
- Kita bisa gunakan perintah :
```bash
php artisan make:command TestSubscriber
```
- Run :
```bash
php artisan app:test-subscriber 
```
- handle subscribe
```php
public function handle()
    {
        Redis::subscribe(['channel-1', 'channel-2'], function(string $messsage){
            echo $messsage, PHP_EOL;
        });
    }
```
- unit test publish
```php
// Pub Sub
    public function test_pub_sub(): void
    {
        for($i = 0; $i < 10; $i++){
            Redis::publish("channel-1", "woi");
            Redis::publish("channel-2", "apa?");
        }

        self::assertTrue(true);
    }
```
- response :
```bash
woi
apa?
woi
apa?
woi
apa?
woi
apa?
woi
apa?
```

# Stream





# Cache
## Laravel Cache
- Laravel memiliki fitur bernama Cache untuk menyimpan data sementara
- Cache biasanya digunakan untuk menyimpan data secara sementar, agar tidak perlu mengambil data dari sumber aslinya (misal database atau aplikasi lain)
- Tujuan Cache adalah mempercepat proses, agar data yang sering diakses, tidak perlu diambil dari sumbernya yang lambat, karena Cache lebih cepat


# Redis
- Larave sebenarnya menyediakan tempat menyimpan Cache banyak, bisa Database, Redis, Memcached dan DynamoDB
- Kita akan coba gunakan Redis sebagai tempat penyimpanan Cache
- Kita bisa ubah konfigurasi Cache di file `config/cache.php`
- Saat melakukan unit test, di phpunit.xml, konfigurasi CACHE_DRIVER diubah menjadi array, pastikan diubah menjadi redis jika ingin menggunakan redis


## Cache Facade
- Melakukan Cache di Laravel cukup mudah, kita bisa menggunakan Cache Facade
- `https://laravel.com/api/10.x/Illuminate/Support/Facades/Cache.html`
- Banyak method yang bisa digunakan ketika menggunakan Cache Facade, intinya untuk menyimpan, mengambil dan menghapus data dari Cache
* di File : [CacheTest](tests\Feature\CacheTest.php)
```php
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
```


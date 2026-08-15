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

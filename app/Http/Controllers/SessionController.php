<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function set(Request $request){
        $name = $request->query("name");
        $request->session()->put($name, [
            "name" => fake()->name(),
            "country" => fake()->country(),
        ]);

        return response()->json([
            "status" => "success"
        ]);
    }

    public function get(Request $request){
        $name = $request->query("name");
        $value = $request->session()->get($name);
        return response()->json($value);
    }


    // session succes saving on redis
    // 127.0.0.1:6379> keys *
    // 1) "laravel_database_laravel_cache_:ePlUu6VtcNYf3LQXQf6CBR4ZuHavp9Yy9Jt93wwt"
    // 127.0.0.1:6379> get laravel_database_laravel_cache_:ePlUu6VtcNYf3LQXQf6CBR4ZuHavp9Yy9Jt93wwt
    // "s:368:\"a:5:{s:6:\"_token\";s:40:\"rwkMczwcUH21GxOniCnIBX4X0uKwRoI7MpP4W0pZ\";s:9:\"_previous\";a:1:{s:3:\"url\";s:43:\"http://127.0.0.1:8000/session/get?name=gggg\";}s:6:\"_flash\";a:2:{s:3:\"old\";a:0:{}s:3:\"new\";a:0:{}}s:4:\"name\";a:2:{s:4:\"name\";s:16:\"Bette Stiedemann\";s:7:\"country\";s:7:\"Morocco\";}s:3:\"rio\";a:2:{s:4:\"name\";s:19:\"Dr. Ova Mosciski MD\";s:7:\"country\";s:11:\"Isle of Man\";}}\";"
    // 127.0.0.1:6379> get laravel_database_laravel_cache_:ePlUu6VtcNYf3LQXQf6CBR4ZuHavp9Yy9Jt93wwt
    // "s:534:\"a:7:{s:6:\"_token\";s:40:\"rwkMczwcUH21GxOniCnIBX4X0uKwRoI7MpP4W0pZ\";s:9:\"_previous\";a:1:{s:3:\"url\";s:45:\"http://127.0.0.1:8000/session/new?name=resbob\";}s:6:\"_flash\";a:2:{s:3:\"old\";a:0:{}s:3:\"new\";a:0:{}}s:4:\"name\";a:2:{s:4:\"name\";s:16:\"Bette Stiedemann\";s:7:\"country\";s:7:\"Morocco\";}s:3:\"rio\";a:2:{s:4:\"name\";s:19:\"Dr. Ova Mosciski MD\";s:7:\"country\";s:11:\"Isle of Man\";}s:7:\"kampret\";a:2:{s:4:\"name\";s:17:\"Tiffany Hermiston\";s:7:\"country\";s:10:\"Kazakhstan\";}s:6:\"resbob\";a:2:{s:4:\"name\";s:13:\"German Murphy\";s:7:\"country\";s:4:\"Fiji\";}}\";"
    // 127.0.0.1:6379> 


}

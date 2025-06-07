<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WeatherService
{
    public function getWeather(string $city)
    {
        $key = config('services.weatherapi.key');
        $url = config('services.weatherapi.base_url');
        $keyCache = 'weather:' . Str::slug($city);
        
        /**
         * Hacemos la peticion a la API sin verificar el certificado SSL por temas de producción
         */
        return Cache::remember($keyCache, now()->addMinutes(10), function () use ($url, $key, $city) {
            $response = Http::withoutVerifying()->get($url, [
                'key' => $key,
                'q' => $city,
                'aqi' => 'no',
                'alerts' => 'no',
            ]);
    
            if ($response->failed()) {
                throw new \Exception('Error al obtener la información.');
            }
    
            /**
             * Aqui retornamos un array con los datos solicitados en las instrucciones:
             * (temperatura, estado del clima, viento, humedad y hora local)
             */
            $data = $response->json();
            return [
                'temperature_c' => $data['current']['temp_c'] ?? null,
                'condition' => $data['current']['condition']['text'] ?? null,
                'wind_mph' => $data['current']['wind_mph'] ?? null,
                'humidity' => $data['current']['humidity'] ?? null,
                'localtime' => $data['location']['localtime'] ?? null,
            ];
        });
    }
}
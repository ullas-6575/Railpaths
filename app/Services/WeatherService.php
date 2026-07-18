<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WeatherService
{
    public function getWeather(string $city): array
    {
        $location = $this->geocode($city);
        return $this->getWeatherForCoordinates($location['latitude'], $location['longitude'], $location['name'], $location['country']);
    }

    public function getWeatherForCoordinates(float $latitude, float $longitude, string $city, string $country = 'Bangladesh'): array
    {
        $key = 'weather:' . md5($latitude . ':' . $longitude);

        return Cache::remember($key, now()->addMinutes(30), function () use ($latitude, $longitude, $city, $country) {
            $response = Http::timeout(8)->retry(2, 250)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m,surface_pressure,is_day',
                'timezone' => 'auto',
            ]);

            if ($response->failed()) {
                throw new RuntimeException('Weather provider is temporarily unavailable.');
            }

            $current = $response->json('current', []);
            if (! array_key_exists('temperature_2m', $current)) {
                throw new RuntimeException('Weather data was not returned for this location.');
            }

            return [
                'city' => $city,
                'country' => $country,
                'temperature' => round((float) $current['temperature_2m'], 1),
                'feels_like' => round((float) $current['apparent_temperature'], 1),
                'humidity' => (int) $current['relative_humidity_2m'],
                'wind_speed' => round((float) $current['wind_speed_10m'], 1),
                'pressure' => round((float) $current['surface_pressure']),
                'code' => (int) $current['weather_code'],
                'is_day' => (int) ($current['is_day'] ?? 1) === 1,
                'description' => $this->description((int) $current['weather_code']),
                'icon' => $this->icon((int) $current['weather_code'], (int) ($current['is_day'] ?? 1) === 1),
                'source' => 'open-meteo',
                'cached' => false,
                'updated_at' => $current['time'] ?? now()->toIso8601String(),
            ];
        });
    }

    private function geocode(string $city): array
    {
        $response = Http::timeout(8)->retry(2, 250)->get('https://geocoding-api.open-meteo.com/v1/search', [
            'name' => $city,
            'count' => 1,
            'language' => 'en',
            'format' => 'json',
        ]);

        $location = $response->json('results.0');
        if ($response->failed() || ! $location) {
            throw new RuntimeException("We couldn't find weather data for {$city}.");
        }

        return [
            'name' => $location['name'],
            'country' => $location['country'] ?? '',
            'latitude' => (float) $location['latitude'],
            'longitude' => (float) $location['longitude'],
        ];
    }

    private function description(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear sky',
            in_array($code, [1, 2, 3], true) => 'Partly cloudy',
            in_array($code, [45, 48], true) => 'Foggy',
            in_array($code, [51, 53, 55, 56, 57], true) => 'Drizzle',
            in_array($code, [61, 63, 65, 66, 67], true) => 'Rain',
            in_array($code, [71, 73, 75, 77], true) => 'Snow',
            in_array($code, [80, 81, 82], true) => 'Rain showers',
            in_array($code, [85, 86], true) => 'Snow showers',
            in_array($code, [95, 96, 99], true) => 'Thunderstorm',
            default => 'Current conditions',
        };
    }

    private function icon(int $code, bool $isDay): string
    {
        if ($code === 0) return $isDay ? '☀️' : '🌙';
        if (in_array($code, [1, 2, 3], true)) return '⛅';
        if (in_array($code, [95, 96, 99], true)) return '⛈️';
        if (in_array($code, [61, 63, 65, 80, 81, 82], true)) return '🌧️';
        if (in_array($code, [71, 73, 75, 77, 85, 86], true)) return '❄️';
        return '🌫️';
    }
}

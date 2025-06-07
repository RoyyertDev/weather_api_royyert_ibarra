<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherController;

Route::get('/login', function () {
    return "<head>
        <title>Bienvenid@</title>
    </head>
    <h1>Hola, bienvenido a la  API de WeatherAPI - By Royyert Ibarra.</h1> 
    <h2>Actualmente no tienes ningun token de acceso.</h2> 
    <h3>Para acceder a las funciones de la API, necesitas crear un usuario y un token de acceso.</h3>
    <p>Puedes hacerlo con los siguientes endpoints:</p>
    <ul>
        <li> [POST] /api/register</li>
        <li> [POST] /api/login</li>
    </ul>
    <p>En el README.md del proyecto en GitHub, encontrarás más información sobre cómo crear un usuario y un token de acceso.</p>
    ";
})->name('login');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::group(['prefix' => 'weather'], function () {
        Route::post('/', [WeatherController::class, 'getWeather']);
        Route::get('/city/{id}', [WeatherController::class, 'show']);
        Route::get('/history', [WeatherController::class, 'getHistory']);
        Route::get('/favorites', [WeatherController::class, 'getFavorites']);
        Route::patch('/favorite/{id}', [WeatherController::class, 'markFavorite']);
    });
});

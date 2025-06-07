<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserQuery;
use App\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WeatherController extends Controller
{
    protected WeatherService $weather;

    public function __construct(WeatherService $weather)
    {
        $this->weather = $weather;
    }

    /**
     * Aqui hacemos las peticiones a la API de WeatherAPI y registramos un historial de peticiones
     */
    public function getWeather(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'city' => 'required|string',
        ], [
            'city.required' => 'La ciudad es obligatoria.',
            'city.string' => 'La ciudad ingresada no es válida.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        DB::beginTransaction();
        try {
          $data = $this->weather->getWeather($request->city);  

          $query = UserQuery::create([
            'user_id' => Auth::id(),
            'city' => $request->city,
            'weather_data' => $data,
            'is_favorite' => false,
          ]);

          DB::commit();

          return response()->json([
              'status' => 'success',
              'weather' => $data,
              'saved' => $query,
          ], 200);
        } catch (\Exception $e) {
          DB::rollBack();
          return response()->json([
              'status' => 'error',
              'message' => $e->getMessage(),
          ], 400);
        }
    }

    /**
     * Aca obtenemos el historial de las consultas del usuario logeado
     */
    public function getHistory()
    {
        try {
            $history = Auth::user()->userQueries;

            return response()->json([
                'status' => 'success',
                'history' => $history,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Indicar una ciudad como favorita
     */
    public function markFavorite($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:user_queries,id',
        ], [
            'id.integer' => 'El id ingresado no es válido.',
            'id.exists' => 'El id ingresado no existe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        try {
            $query = Auth::user()->userQueries()->findOrFail($id);
            $query->update(['is_favorite' => true]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Ciudad marcada como favorita.',
                'query' => $query,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Aca obtenemos las ciudades marcadas como favoritas del usuario autenticado
     */
    public function getFavorites()
    {
        try {
            $favorites = Auth::user()->userQueries()->where('is_favorite', true)->get();

            return response()->json([
                'status' => 'success',
                'favorites' => $favorites,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Aca podemos obtener una ciudad buscada por el usuario autenticado
     */
    public function show($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:user_queries,id',
        ], [
            'id.integer' => 'El id ingresado no es válido.',
            'id.exists' => 'El id ingresado no existe.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }
        try {
            $city = Auth::user()->userQueries()->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'ciudad' => $city,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

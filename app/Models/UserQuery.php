<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'city',
        'weather_data',
        'is_favorite',
    ];

    protected $casts = [
        'weather_data' => 'array',
        'is_favorite' => 'boolean',
        'localtime' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}

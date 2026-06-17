<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chat extends Model
{
    /** @use HasFactory<\Database\Factories\ChatFactory> */
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['title', 'user_id', 'favorite_ai'];

    protected static function boot() {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}

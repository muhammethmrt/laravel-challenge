<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'renewed_at',
        'expired_at',
    ];
    public $timestamps = true;

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    
}


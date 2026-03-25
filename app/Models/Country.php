<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $table = 'lc_countries';

    protected $fillable = [
        'official_name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_id');
    }
}

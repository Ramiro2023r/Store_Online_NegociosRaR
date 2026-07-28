<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $fillable = ['email', 'name', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}

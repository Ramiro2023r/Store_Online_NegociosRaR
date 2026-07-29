<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantPreference extends Model
{
    protected $fillable = ['user_id', 'key', 'value'];

    protected $table = 'assistant_preferences';
}

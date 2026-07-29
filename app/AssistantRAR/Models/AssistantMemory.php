<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantMemory extends Model
{
    protected $fillable = ['user_id', 'key', 'value', 'category'];

    protected $table = 'assistant_memories';
}

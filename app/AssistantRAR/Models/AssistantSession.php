<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantSession extends Model
{
    protected $fillable = ['user_id', 'conversation_id', 'session_token', 'context', 'expires_at'];

    protected $table = 'assistant_sessions';

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'expires_at' => 'datetime',
        ];
    }
}

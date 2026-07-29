<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantToolLog extends Model
{
    protected $fillable = [
        'user_id', 'conversation_id', 'message_id', 'tool_name',
        'arguments', 'status', 'result_message', 'error_code',
        'resource_type', 'resource_id', 'previous_values', 'new_values',
        'ip_address', 'user_agent',
    ];

    protected $table = 'assistant_tool_logs';

    protected function casts(): array
    {
        return [
            'arguments' => 'array',
            'previous_values' => 'array',
            'new_values' => 'array',
        ];
    }
}

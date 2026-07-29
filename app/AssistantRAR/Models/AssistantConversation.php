<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantConversation extends Model
{
    protected $fillable = ['user_id', 'title', 'status'];

    protected $table = 'assistant_conversations';

    public function messages()
    {
        return $this->hasMany(AssistantMessage::class, 'conversation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}

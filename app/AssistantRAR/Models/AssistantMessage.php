<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantMessage extends Model
{
    protected $fillable = ['conversation_id', 'role', 'content', 'metadata'];

    protected $table = 'assistant_messages';

    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function conversation()
    {
        return $this->belongsTo(AssistantConversation::class, 'conversation_id');
    }
}

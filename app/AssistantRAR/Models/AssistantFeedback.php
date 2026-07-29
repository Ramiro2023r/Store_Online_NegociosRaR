<?php

namespace App\AssistantRAR\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantFeedback extends Model
{
    protected $fillable = ['user_id', 'message_id', 'positive', 'comment'];

    protected $table = 'assistant_feedback';
}
